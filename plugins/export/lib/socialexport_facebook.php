<?php
/**
 * Facebook export.
 */
class SocialExportFacebook extends AExport {
	/**
	 * @var \Facebook\Facebook Facebook object.
	 */
	private $facebook;
	
	/**
	 * Access token
	 */
	private $access_token = "";
	
	/**
	 * Access token
	 */
	private $page_access_token = "";
	
	/**
	 * Constructor. Initializes variables
	 * @param Provider $provider Export Provider
	 */
	public function __construct($provider) {
		parent::__construct($provider);
		
		$this->exported_used_machines = ExportedUsedMachine::getAll($this->provider);
		
		$this->facebook = new \Facebook\Facebook([
			'app_id'  => $this->provider->social_app_id,
			'app_secret' => $this->provider->social_app_secret,
			'default_graph_version' => 'v2.11'
		]);
	}	
	
	/**
	 * Get access token
	 * return string Access Token
	 */
	public function getAccessToken() {
		$helper = $this->facebook->getRedirectLoginHelper();

		try {
			// Logged in: short-lived access token
			// Do not forget to set callback URL: https://stackoverflow.com/questions/47898499/error-on-login-using-facebook-php-sdk-with-strict-mode-enabled
			$accessToken = $helper->getAccessToken($this->getCallbackURL());
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			print \rex_view::error('Graph returned an error: ' . $e->getMessage());
			return FALSE;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			print \rex_view::error('Facebook SDK returned an error: ' . $e->getMessage());
			return FALSE;
		}

		if (! isset($accessToken)) {
			if ($helper->getError()) {
				print \rex_view::error("Error: " . $helper->getError() . "<br>"
					."Error Code: " . $helper->getErrorCode() . "<br>"
					."Error Reason: " . $helper->getErrorReason() . "<br>"
					."Error Description: " . $helper->getErrorDescription());
			} else {
				print \rex_view::error('Bad request');
			}
			return FALSE;
		}

		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->facebook->getOAuth2Client();

		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);

		// Validation (these will throw FacebookSDKException's when they fail)
		$tokenMetadata->validateAppId($this->provider->social_app_id);
		// If you know the user ID this access token belongs to, you can validate it here
		//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();

		if (! $accessToken->isLongLived()) {
			// Exchanges a short-lived access token for a long-lived one
			try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
			} catch (\Facebook\Exceptions\FacebookSDKException $e) {
				print \rex_view::error("<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>");
				return FALSE;
			}
			// Get finally access token
			$this->access_token = $accessToken->getValue();
			
			// Get page access token
			if($this->provider->facebook_pageid != '') {
				$this->page_access_token = $this->getPageAccessToken((string) $accessToken, $this->provider->facebook_pageid);
			}

			$this->provider->social_oauth_token = $this->access_token;
			$this->provider->social_oauth_token_valid_until = $accessToken->getExpiresAt() != null ? strtotime($accessToken->getExpiresAt()) : 0;
			$this->provider->save();
			
			return (string) $accessToken;
		}
	}

	/**
	 * Get access token for page
	 * @param string $access_token Access token for Facebook user
	 * @param int $page_id ID of Facebook page
	 * return string|boolean Access Token or FALSE if failure occured
	 */
	private function getPageAccessToken($access_token, $page_id) {
		// Get page access token
		if($page_id != '') {
			try {
				// Returns a `Facebook\FacebookResponse` object
				$response = $this->facebook->get(
					'/'. $page_id .'?fields=access_token',
					$access_token
				);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				print \rex_view::error('Graph returned an error: ' . $e->getMessage());
				return FALSE;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				print \rex_view::error('Facebook SDK returned an error: ' . $e->getMessage());
				return FALSE;
			}
			$jsonGraphNode = $response->getGraphNode()->asJson();
			$json = json_decode($jsonGraphNode);
			return $json->access_token;
		}

		return FALSE;
	}

	/**
	 * Get callback URL.
	 * @return string Callback URL
	 */
	private function getCallbackURL() {
		return 	\rex::getServer() .'redaxo/'. \rex_url::currentBackendPage(['func'=>'export', 'facebook_return'=>'true', 'provider_id'=>$this->provider->provider_id], FALSE);
	}
	
	/**
	 * Get login URL
	 * @return string Login url
	 */
	function getLoginURL() {
		$helper = $this->facebook->getRedirectLoginHelper();
		$permissions = ['email', 'manage_pages', 'publish_actions', 'publish_pages'];
		return $helper->getLoginUrl($this->getCallbackURL(), $permissions);
	}

	/**
	 * Get Logout URL
	 * @return string Logout URL
	 */
	function getLogoutURL() {
		return $this->facebook->getLogoutUrl();
	}

	/**
	 * Has valid access token?
	 * return boolean TRUE if valid access token is available
	 */
	public function hasAccessToken() {
		if($this->provider->social_oauth_token != "") {
			$access_token = new \Facebook\Authentication\AccessToken($this->provider->social_oauth_token);
			if($access_token->isExpired()) { 
				// Access Token is expired -> delete it
				$this->provider->social_oauth_token = "";
				$this->provider->social_oauth_token_valid_until = "";
				$this->provider->save();
			}
			else {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Checks if somebody is logged in.
	 * @return boolean TRUE is somebody is logged in, otherwise FALSE
	 */
	function isAnybodyLoggedIn() {
		try {
			// Returns a `\Facebook\FacebookResponse` object
			$response = $this->facebook->get('/me?fields=id,name', $this->provider->social_oauth_token);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			print \rex_view::error('Graph returned an error: ' . $e->getMessage());
			return FALSE;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			print \rex_view::error('Facebook SDK returned an error: ' . $e->getMessage());
			return FALSE;
		}

		if($response->getGraphUser() instanceof \Facebook\GraphNodes\GraphUser) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Is user mentioned in provider settings logged in?
	 * @return boolean TRUE id yes, otherwise FALSE.
	 */
	public function isUserLoggedIn() {
		try {
			// Returns a `\Facebook\FacebookResponse` object
			$response = $this->facebook->get('/me?fields=id,name,email', $this->provider->social_oauth_token);
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			print \rex_view::error('Graph returned an error: ' . $e->getMessage());
			return FALSE;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			print \rex_view::error('Facebook SDK returned an error: ' . $e->getMessage());
			return FALSE;
		}

		$user = $response->getGraphUser();
	  	if($this->provider->facebook_email != "" && $user['email'] == $this->provider->facebook_email) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Export used machines.
	 * @return string Error message
	 */
	public function export() {
		// First remove all deleted machines from exports, otherwise an empty post would be added after deletion
		ExportedUsedMachine::removeAllDeletedFromExport();
		
		foreach($this->exported_used_machines as $exported_used_machine) {
			$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
			// Delete from wall
			if($exported_used_machine->export_action == "delete" || $exported_used_machine->export_action == "update") {
				if($exported_used_machine->provider_import_id != "") {
					try {
						if($this->provider->facebook_pageid == "") {
							// Delete on default wall
							$this->facebook->delete('/'. $exported_used_machine->provider_import_id, [], $this->provider->social_oauth_token);
						}
						else {
							// Post on page
							$this->facebook->delete('/'. $exported_used_machine->provider_import_id, [], $this->page_access_token);
						}
					} catch(\Facebook\Exceptions\FacebookResponseException $e) {
						return 'Graph returned an error: ' . $e->getMessage();
					} catch(\Facebook\Exceptions\FacebookSDKException $e) {
						return 'Facebook SDK returned an error: ' . $e->getMessage();
					}
				}

				// delete in database
				if($exported_used_machine->export_action == "delete") {
					$exported_used_machine->delete();
				}
				else {
					$exported_used_machine->export_action = "add";
					$exported_used_machine->provider_import_id = "";
				}
			}

			// Post on wall
			if($exported_used_machine->export_action == "add") {
				$news = ['message' => $this->provider->company_name ." ". Sprog\Wildcard::get('d2u_machinery_export_linkedin_offers', $this->provider->clang_id) .": "
						. $used_machine->manufacturer ." ". $used_machine->name,
						'link' => $used_machine->getURL(TRUE),
						// Custom description cannot be added: https://developers.facebook.com/blog/post/2017/06/27/API-Change-Log-Modifying-Link-Previews/
					]; 
				if(count($used_machine->pics) > 0 && $this->provider->facebook_pageid == "") {
					$news['picture'] = \rex::getServer() .'?rex_media_type='. $this->provider->media_manager_type .'&rex_media_file='. $used_machine->pics[0];
				}

				try {
					$response = FALSE;
					if($this->provider->facebook_pageid == "") {
						// Post on default wall
						$response = $this->facebook->post('/me/feed', $news, $this->provider->social_oauth_token);
					}
					else {
						// Post on page
						$response = $this->facebook->post('/'. $this->provider->facebook_pageid .'/feed', $news, $this->page_access_token);
					}
					
					// Only if response is available
					if($response !== FALSE) {
						$graphNode = $response->getGraphNode();
						$exported_used_machine->provider_import_id = $graphNode["id"];
					}
				} catch (\Facebook\Exceptions\FacebookSDKException $e) {
					if($this->provider->facebook_pageid == "") {
						return \rex_i18n::msg("d2u_machinery_export_facebook_upload_failed") ."<br><br><pre>". $e ."</pre>";
					}
					else {
						return \rex_i18n::msg("d2u_machinery_export_facebook_upload_page_failed") ."<br><br><pre>". $e ."</pre>";
					}
				}
				
				// Save results
				$exported_used_machine->export_action = "";
				$exported_used_machine->export_timestamp = time();
				$exported_used_machine->save();
			}
		}
		return "";
	}
}