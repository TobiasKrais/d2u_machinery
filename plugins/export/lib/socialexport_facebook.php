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
		
		$this->facebook = new Facebook(array(
			'appId'  => $this->provider->social_app_id,
			'secret' => $this->provider->social_app_secret
		));
	}	
	
	/**
	 * Get access token
	 * return string Access Token
	 */
	private function getAccessToken() {
		if($this->access_token == "") {
			// Access token for wall
			$this->access_token = $this->facebook->getAccessToken();
			// if page id is given, access token of page is needed
			if($this->provider->facebook_pageid != "") {
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
		}
		return $this->access_token;
	}

	/**
	 * Get login URL
	 * @return string Login url
	 */
	function getLoginURL() {
		if($this->provider->facebook_pageid != "") {
			return $this->facebook->getLoginUrl(array('scope' => 'email, manage_pages, publish_actions, publish_pages'));
		}
		else {
			return $this->facebook->getLoginUrl(array('scope' => 'email, manage_pages, publish_actions'));
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
	 * Checks if somebody is logged in.
	 * @return boolean TRUE is somebody is logged in, otherwise FALSE
	 */
	function isAnybodyLoggedIn() {
		if($this->facebook->getUser()) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Is user mentioned in provider logged in?
	 * @return boolean TRUE id yes, otherwise FALSE.
	 */
	public function isUserLoggedIn() {
		if($this->facebook->getUser()) {
			$facebook_user = $this->facebook->api('/me');

			if($this->provider->facebook_email != "" && $facebook_user['email'] == $this->provider->facebook_email) {
				return true;
			}
			else {
				return false;
			}
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
						$this->facebook->api("/". $exported_used_machine->provider_import_id ,"DELETE");
					} catch (FacebookApiException $e) {
						print $e;
						// Seems to be already deleted
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
						$feedback = $this->facebook->api('/me/feed', 'POST', $news);
					}
					else {
						// Post on page
						$feedback = $this->facebook->api('/'. $this->provider->facebook_pageid .'/feed', 'POST', $news);
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