<?php
/**
 * Provider export configurations.
 */
class Provider {
	/**
	 * @var string Database ID
	 */
	var $provider_id = "";

	/**
	 * @var string Provider name
	 */
	var $name = "";
	
	/**
	 * @var string Provider interface name. Current implemented types are:
	 * europemachinery, machinerypark, mascus, facebook, twitter and linkedin.
	 */
	var $type = "";
		
	/**
	 * @var int Redaxo language id. Represents the language, the machines should
	 * be exported.
	 */
	var $clang_id = 0;

	/**
	 * @var string Company name (your company name)
	 */
	var $company_name = "";
		
	/**
	 * @var string Company e-mail address (your e-mail address)
	 */
	var $company_email = "";
	
	/**
	 * @var string Customer number (your customer number of the provider)
	 */
	var $customer_number = "";
	
	/**
	 * @var string FTP server address. Needed if transmission type is FTP.
	 */
	var $ftp_server = "";
	
	/**
	 * @var string FTP server username
	 */
	var $ftp_username = "";
	
	/**
	 * @var string FTP server password
	 */
	var $ftp_password = "";

	/**
	 * @var string FTP filename (including file type, normally .zip)
	 */
	var $ftp_filename = "";

	/**
	 * @var string Media manager type for exporting pictures.
	 */
	var $media_manager_type = "d2u_machinery_list_tile";
	
	/**
	 * @var string Path where attachments can be found.
	 */
	var $attachment_path = "";
	
	/**
	 * @var string App ID of social networks.
	 */
	var $social_app_id = "";	

	/**
	 * @var string App Secret of social networks.
	 */
	var $social_app_secret = "";

	/**
	 * @var string Twitter or LinkedIn OAuth Token. This token is valid until user revokes it.
	 */
	var $social_oauth_token = "";

	/**
	 * @var string Twitter or LinkedIn OAuth Token Secret. This secret is valid until user
	 * revokes it.
	 */
	var $social_oauth_token_secret = "";

	/**
	 * @var string Facebook login email address
	 */
	var $facebook_email = "";

	/**
	 * @var string Facebook page id.
	 */
	var $facebook_pageid = "";

	/**
	 * @var string Linkedin id.
	 */
	var $linkedin_id = "";

	/**
	 * @var string Linkedin group id.
	 */
	var $linkedin_groupid = "";

	/**
	 * @var string Twitter id.
	 */
	var $twitter_id = "";

	/**
	 * Fetches the object from database.
	 * @param int $provider_id Object id
	 */
	public function __construct($provider_id) {
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_machinery_export_provider WHERE provider_id = ". $provider_id;

		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			// Wenn Verbindung ueber Redaxo hergestellt wurde
			$this->provider_id = $result->getValue("provider_id");
			$this->name = $result->getValue("name");
			$this->type = $result->getValue("type");
			$this->clang_id = $result->getValue("clang_id");
			$this->customer_number = $result->getValue("customer_number");
			$this->ftp_server = $result->getValue("ftp_server");
			$this->ftp_username = $result->getValue("ftp_username");
			$this->ftp_password = $result->getValue("ftp_password");
			$this->ftp_filename = $result->getValue("ftp_filename");
			$this->company_name = $result->getValue("company_name");
			$this->company_email = $result->getValue("company_email");
			$this->media_manager_type = $result->getValue("media_manager_type");
			$this->social_app_id = $result->getValue("social_app_id");
			$this->social_app_secret = $result->getValue("social_app_secret");
			$this->social_oauth_token = $result->getValue("social_oauth_token");
			$this->social_oauth_token_secret = $result->getValue("social_oauth_token_secret");
			$this->facebook_email = $result->getValue("facebook_email");
			$this->facebook_pageid = $result->getValue("facebook_pageid");
			$this->linkedin_id = $result->getValue("linkedin_id");
			$this->linkedin_groupid = $result->getValue("linkedin_groupid");
			$this->twitter_id = $result->getValue("twitter_id");
		}
	}

	/**
	 * Deletes the object.
	 */
	public function delete() {
		// First delete exported used machines
		$exported_used_machines = ExportedUsedMachine::getAll($this);
		foreach($exported_used_machines as $exported_used_machine) {
			$exported_used_machine->delete();
		}
		
		// Next delete object
		$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_machinery_export_provider "
			."WHERE provider_id = ". $this->provider_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
	}

	/**
	 * Exports used machines for provider types europemachinery, machinerypark
	 * and mascus (ftp based exports). Export starts only, if changes were made
	 * or last export ist older than a week.
	 * @return string HTML formatted string with success or failure message.
	 */
	public static function autoexport() {
		$providers = Provider::getAll();
		$message = [];
		
		$error = FALSE;
		
		foreach($providers as $provider) {
			if($provider->isExportNeeded() || $provider->getLastExportTimestamp() < strtotime("-1 week")) {
				if($provider->type == "europemachinery") {
					$europemachinery = new EuropeMachinery($provider);
					$europemachinery_error = $europemachinery->export();
					if($europemachinery_error != "") {
						$message[] = $provider->name .": ". $europemachinery_error;
						print $provider->name .": ". $europemachinery_error ."; ";
						$error = TRUE;
					}
					else {
						$message[] = $provider->name .": ". rex_i18n::msg('d2u_machinery_export_success');
					}
				}
				else if($provider->type == "machinerypark") {
					$machinerypark = new MachineryPark($provider);
					$machinerypark_error = $machinerypark->export();
					if($machinerypark_error != "") {
						$message[] = $provider->name .": ". $machinerypark_error;
						print $provider->name .": ". $machinerypark_error ."; ";
						$error = TRUE;
					}
					else {
						$message[] = $provider->name .": ". rex_i18n::msg('d2u_machinery_export_success');
					}
				}
				else if($provider->type == "mascus") {
					$mascus = new Mascus($provider);
					$mascus_error = $mascus->export();
					if($mascus_error != "") {
						$message[] = $provider->name .": ". $mascus_error;
						print $provider->name .": ". $mascus_error ."; ";
						$error = TRUE;
					}
					else {
						$message[] = $provider->name .": ". rex_i18n::msg('d2u_machinery_export_success');
					}
				}
			}
		}
		
		// Send report
		$d2u_machinery = rex_addon::get("d2u_machinery");
		if($d2u_machinery->hasConfig('export_failure_email') && $error) {
			$mail = new rex_mailer();
			$mail->IsHTML(true);
			$mail->CharSet = "utf-8";
			$mail->AddAddress(trim($d2u_machinery->getConfig('export_failure_email')));
			$mail->Subject = rex_i18n::msg('d2u_machinery_export_failure_report');
			$mail->Body = implode("<br>", $message);
			$mail->Send();
		}
		
		if($error) {
			return false;
		}
		else {
			print rex_i18n::msg('d2u_machinery_export_success');
			return true;
		}
	}

	/**
	 * Exports used machines for the provider.
	 */
	public function export() {
		if($this->type == "europemachinery") {
			$europemachinery = new EuropeMachinery($this);
			return $europemachinery->export();
		}
		else if($this->type == "machinerypark") {
			$machinerypark = new MachineryPark($this);
			return $machinerypark->export();
		}
		else if($this->type == "mascus") {
			$mascus = new Mascus($this);
			return $mascus->export();
		}
		else if($this->type == "facebook") {
			// Check requirements
			if (!function_exists('curl_init')) {
				return rex_i18n::msg('d2u_machinery_export_failure_curl');
			}
			else if (!function_exists('json_decode')) {
				return rex_i18n::msg('d2u_machinery_export_failure_json');				
			}
			
			// Export
			$facebook = new SocialExportFacebook($this);
			if($facebook->isUserLoggedIn()) {
				return $facebook->export();
			}
			else {
				if($facebook->isAnybodyLoggedIn()) {
					// Wrong user logged in: logout first
					header("Location: ". $facebook->getLogoutURL());
				}
				else {
					// If not logged in, go to log in page
					header("Location: ". $facebook->getLoginURL());
				}
				exit;
			}
		}
		else if($this->type == "twitter") {
			return "Schnittstelle ist noch nicht programmiert.";
		}
		else if($this->type == "linkedin") {
			// Check requirements
			if (!function_exists('curl_init')) {
				return rex_i18n::msg('d2u_machinery_export_failure_curl');
			}
			else if (!class_exists('oauth')) {
				return rex_i18n::msg('d2u_machinery_export_failure_oauth');				
			}
			$linkedin = new SocialExportLinkedIn($this);
			if($linkedin->isUserLoggedIn()) {
				return $linkedin->export();
			}
			else {
				if($linkedin->isAnybodyLoggedIn()) {
					// Wrong user logged in: logout first
					$linkedin->logout();
				}
				// If not logged in, go to log in page
				header("Location: ". $linkedin->getLoginURL());
				exit;
			}
		}
	}
	
	/**
	 * Get all providers.
	 * @return Provider[] Array with Provider objects.
	 */
	public static function getAll() {
		$query = "SELECT provider_id FROM ". rex::getTablePrefix() ."d2u_machinery_export_provider "
			."ORDER BY name";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$providers = array();
		for($i = 0; $i < $result->getRows(); $i++) {
			$providers[] = new Provider($result->getValue("provider_id"));
			$result->next();
		}
		return $providers;
	}
	
	/**
	 * Checks if an export is needed. This is the case if:
	 * a) A machine needs to be deleted from export
	 * b) A machine is updated after the last export
	 * @return int Timestamp of latest machine update.
	 */
	private function isExportNeeded() {
		$query = "SELECT export_action FROM ". rex::getTablePrefix() ."d2u_machinery_export_machines "
			."WHERE provider_id = ". $this->provider_id ." AND export_action = 'delete'";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0) {
			return TRUE;
		}
		
		$query = "SELECT used_machines.updatedate, export.export_timestamp FROM ". rex::getTablePrefix() ."d2u_machinery_used_machines_lang AS used_machines "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_machinery_export_machines AS export ON used_machines.used_machine_id = export.used_machine_id "
			."WHERE provider_id = ". $this->provider_id ." AND clang_id = ". $this->clang_id ." "
			."ORDER BY used_machines.updatedate DESC LIMIT 0, 1";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		if($result->getRows() > 0 && $result->getValue("updatedate") > $result->getValue("export_timestamp")) {
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Get last export timestamp.
	 * @return int Timestamp of last successful export.
	 */
	public function getLastExportTimestamp() {
		$query = "SELECT export_timestamp FROM ". rex::getTablePrefix() ."d2u_machinery_export_machines "
			."WHERE provider_id = ". $this->provider_id ." "
			."ORDER BY export_timestamp DESC LIMIT 0, 1";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$time = 0;
		if($result->getRows() > 0) {
			$time = $result->getValue("export_timestamp");
		}
		return $time;
	}

	/**
	 * Counts the number of online UsedMachines for this provider.
	 * @return int Number of online UsedMachines
	 */
	public function getNumberOnlineUsedMachines() {
		$query = "SELECT COUNT(*) as number FROM ". rex::getTablePrefix() ."d2u_machinery_export_machines WHERE provider_id = ". $this->provider_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		return $result->getValue("number");
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$d2u_machinery = rex_addon::get('d2u_machinery');
		$this->clang_id = $this->clang_id == 0 ? $d2u_machinery->getConfig('default_lang') : $this->clang_id;

		$query = rex::getTablePrefix() ."d2u_machinery_export_provider SET "
				."name = '". $this->name ."', "
				."type = '". $this->type ."', "
				."clang_id = ". $this->clang_id .", "
				."company_name = '". $this->company_name ."', "
				."company_email = '". $this->company_email ."', "
				."customer_number = '". $this->customer_number ."', "
				."media_manager_type = '". $this->media_manager_type ."', "
				."ftp_server = '". $this->ftp_server ."', "
				."ftp_username = '". $this->ftp_username ."', "
				."ftp_password = '". $this->ftp_password ."', "
				."ftp_filename = '". $this->ftp_filename ."', "
				."social_app_id = '". $this->social_app_id ."', "
				."social_app_secret = '". $this->social_app_secret ."', "
				."social_oauth_token = '". $this->linkedin_oauth_token ."', "
				."social_oauth_token_secret = '". $this->linkedin_oauth_token_secret ."', "
				."facebook_email = '". $this->facebook_email ."', "
				."facebook_pageid = '". $this->facebook_pageid ."', "
				."linkedin_id = '". $this->linkedin_id ."', "
				."linkedin_groupid = '". $this->linkedin_groupid ."', "
				."twitter_id = '". $this->twitter_id ."' ";

		if($this->provider_id == 0) {
			$query = "INSERT INTO ". $query;
		}
		else {
			$query = "UPDATE ". $query ." WHERE provider_id = ". $this->provider_id;
		}

		$result = rex_sql::factory();
		$result->setQuery($query);
		if($this->provider_id == 0) {
			$this->provider_id = $result->getLastId();
		}

		if($result->hasError()) {
			return FALSE;
		}
		
		return TRUE;
	}
}