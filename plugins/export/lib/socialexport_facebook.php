<?php
/**
 * Facebook export.
 */
class SocialExportFacebook extends AExport {
	/**
	 * Facebook object.
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
		
		$this->facebook = new Facebook\Facebook(array(
			'app_id'  => $this->provider->social_app_id,
			'app_secret' => $this->provider->social_app_secret
		));
	}	
	
	/**
	 * Get access token
	 * return string Access Token
	 */
	private function getAccessToken() {
		if($this->access_token == "") {
			$helper = $this->facebook->getRedirectLoginHelper();
print_r($helper);
			// Access token for wall
			$this->access_token = $helper->getAccessToken();
			// if page id is given, access token of page is needed
/*	TODO	if($this->provider->facebook_pageid != "") {
				// Get accounts (pages)
				$facebook_accounts = $this->facebook->api('/me/accounts');
				foreach($facebook_accounts as $facebook_accounts_data) {
					foreach($facebook_accounts_data as $facebook_account) {
						if($facebook_account["id"] == $this->provider->facebook_pageid) {
							$this->access_token = $facebook_account["access_token"];
						}
					}
				}
			}
 * 
 */
		}
		return $this->access_token;
	}

	/**
	 * Get login URL
	 * @return string Facebook login url
	 */
	function getLoginURL() {
		$helper = $this->facebook->getRedirectLoginHelper();
		if($this->provider->facebook_pageid != "") {
			$permissions = ['email', 'manage_pages', 'publish_actions', 'publish_pages'];
			return $helper->getLoginUrl(rex_url::currentBackendPage(array('func'=>'export', 'provider_id'=>$this->provider->provider_id)), $permissions);
		}
		else {
			$permissions = ['email', 'manage_pages', 'publish_actions'];
			return $helper->getLoginUrl(rex_url::currentBackendPage(array('func'=>'export', 'provider_id'=>$this->provider->provider_id)), $permissions);
		}
	}

	/**
	 * Get Logout URL
	 * @return string Facebook logout URL
	 */
	function getLogoutURL() {
		return $this->facebook->getLogoutUrl();
	}

	/**
	 * Checks if somebody is logged in.
	 * @return boolean TRUE is somebody is logged in, otherwise FALSE
	 */
	function isAnybodyLoggedIn() {
		if($this->facebook->get('/me', $this->getAccessToken())) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Is the Facebook user mentioned in prociver logged in?
	 * @return boolean TRUE id yes, otherwise FALSE.
	 */
	public function isUserLoggedIn() {
		$response = $this->facebook->get('/me', $this->getAccessToken());
		$facebook_user = $response->getGraphUser();
		if($this->provider->facebook_email != "" && $facebook_user['email'] == $this->provider->facebook_email) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Export used machines.
	 * @return string Error message
	 */
	public function export() {
		foreach($this->exported_used_machines as $exported_used_machine) {
			$used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
			// Delete from wall
			if($exported_used_machine->export_action == "delete" || $exported_used_machine->export_action == "update") {
				if($exported_used_machine->provider_import_id != "") {
					try {
						if($this->provider->facebook_pageid == "") {
							// Delete on default wall
							$this->facebook->delete('/me/feed', '/'. $exported_used_machine->provider_import_id, $this->getAccessToken());
						}
						else {
							// Delete on pare
							$this->facebook->delete('/'. $this->provider->facebook_pageid .'/feed', '/'. $exported_used_machine->provider_import_id, $this->getAccessToken());
						}
					} catch (FacebookApiException $e) {
						// Seems to be already deleted
					}
				}

				// delete in database
				if($used_machine->export_action == "delete") {
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
						'name' => $used_machine->manufacturer ." ". $used_machine->name, // heading
						'link' => $used_machine->getURL(TRUE),
						// 'caption' => 'E.g.: Small heading',
						'description' => $used_machine->getExtendedTeaser()]; // post description
				if(count($used_machine->pics) > 0) {
					$news['picture'] = rex::getServer() .'?rex_media_type='. $this->provider->media_manager_type .'&rex_media_file='. $used_machine->pics[0];
				}

				try {
					$feedback = [];
					if($this->provider->facebook_pageid == "") {
						// Post on default wall
						$feedback = $this->facebook->post('/me/feed', $news, $this->getAccessToken());
					}
					else {
						// Post on page
						$feedback = $this->facebook->post('/'. $this->provider->facebook_pageid .'/feed', $news, $this->getAccessToken());
					}
					$exported_used_machine->provider_import_id = $feedback["id"];
				} catch (FacebookApiException $e) {
					return rex_i18n::msg("d2u_machinery_export_facebook_upload_failed") ." ". $e;
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