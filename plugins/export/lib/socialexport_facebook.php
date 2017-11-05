<?php
/**
 * Facebook export.
 */
class SocialExportFacebook extends AExport {
	/**
	 * @var Facebook\Facebook Facebook object.
	 */
	private $facebook;
	
	/**
	 * Access token
	 */
	private $access_token = "";
	
	/**
	 * Constructor. Initializes variables
	 * @param Provider $provider Export Provider
	 */
	public function __construct($provider) {
		parent::__construct($provider);
		
		$this->exported_used_machines = ExportedUsedMachine::getAll($this->provider);
		
		$this->facebook = new Facebook\Facebook([
			'app_id'  => $this->provider->social_app_id,
			'app_secret' => $this->provider->social_app_secret
		]);
	}	
	
	/**
	 * Get access token
	 * return string Access Token
	 */
	public function getAccessToken() {
		$helper = $this->facebook->getRedirectLoginHelper();

		try {
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			print rex_view::error('Graph returned an error: ' . $e->getMessage());
			return FALSE;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			print rex_view::error('Facebook SDK returned an error: ' . $e->getMessage());
			return FALSE;
		}

		if (! isset($accessToken)) {
			if ($helper->getError()) {
				print rex_view::error("Error: " . $helper->getError() . "<br>"
					."Error Code: " . $helper->getErrorCode() . "<br>"
					."Error Reason: " . $helper->getErrorReason() . "<br>"
					."Error Description: " . $helper->getErrorDescription());
			} else {
				print rex_view::error('Bad request');
			}
			return FALSE;
		}

		// Logged in: short-lived access token
		$accessToken = $accessToken->getValue();

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
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				print rex_view::error("<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>");
				return FALSE;
			}

			$this->access_token = $accessToken->getValue();
			$this->provider->social_oauth_token = $this->access_token;
			$this->provider->social_oauth_token_valid_until = $accessToken->getExpiresAt() != null ? strtotime($accessToken->getExpiresAt()) : 0;
			$this->provider->save();
		}
	}

	/**
	 * Get login URL
	 * @return string Login url
	 */
	function getLoginURL() {
		$helper = $this->facebook->getRedirectLoginHelper();
		$callback_url = rex::getServer() .'redaxo/'. rex_url::currentBackendPage(['func'=>'export', 'provider_id'=>$this->provider->provider_id, 'facebook_return'=>'true'], FALSE);

		if($this->provider->facebook_pageid != "") {
			$permissions = ['email', 'manage_pages', 'publish_actions', 'publish_pages']; // Optional permissions
			return $helper->getLoginUrl($callback_url, $permissions);
		}
		else {
			$permissions = ['email', 'manage_pages', 'publish_actions']; // Optional permissions
			return $helper->getLoginUrl($callback_url, $permissions);
		}
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
			$access_token = new Facebook\Authentication\AccessToken($this->provider->social_oauth_token);
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
			// Returns a `Facebook\FacebookResponse` object
			$response = $this->facebook->get('/me?fields=id,name');
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			print rex_view::error('Graph returned an error: ' . $e->getMessage());
			return FALSE;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			print rex_view::error('Facebook SDK returned an error: ' . $e->getMessage());
			return FALSE;
		}

		if($response->getGraphUser() instanceof Facebook\GraphNodes\GraphUser) {
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
			// Returns a `Facebook\FacebookResponse` object
			$response = $this->facebook->get('/me?fields=id,name,email');
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			print rex_view::error('Graph returned an error: ' . $e->getMessage());
			return FALSE;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			print rex_view::error('Facebook SDK returned an error: ' . $e->getMessage());
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
						$this->facebook->delete('/me/feed', ['object' => $exported_used_machine->provider_import_id]);
					} catch(Facebook\Exceptions\FacebookResponseException $e) {
						return 'Graph returned an error: ' . $e->getMessage();
					} catch(Facebook\Exceptions\FacebookSDKException $e) {
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
				$news = ['access_token' => $this->getAccessToken(),
						// 'message' => 'E.g.: We offer:',
						
						'link' => $used_machine->getURL(TRUE),
						// 'caption' => 'E.g.: Small heading',
						'child_attachments' => [
							'name' => $used_machine->manufacturer ." ". $used_machine->name, // heading
							'description' => $used_machine->getExtendedTeaser(), // post description
						],
					]; 
				if(count($used_machine->pics) > 0) {
					$news['child_attachments']['picture'] = rex::getServer() .'?rex_media_type='. $this->provider->media_manager_type .'&rex_media_file='. $used_machine->pics[0];
				}

				try {
					$feedback = [];
					if($this->provider->facebook_pageid == "") {
						// Post on default wall
						$feedback = $this->facebook->post('/me/feed', $news);
					}
					else {
						// Post on page
						$feedback = $this->facebook->post('/'. $this->provider->facebook_pageid .'/feed', $news);
					}
					$exported_used_machine->provider_import_id = $feedback["id"];
				} catch (FacebookApiException $e) {
					if($this->provider->facebook_pageid == "") {
						return rex_i18n::msg("d2u_machinery_export_facebook_upload_failed") ." ". $e;
					}
					else {
						return rex_i18n::msg("d2u_machinery_export_facebook_upload_page_failed") ." ". $e;
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