<?php
/**
 * LinkedIn export.
 */
class SocialExportLinkedIn extends AExport
{
    /** @var string Linkedin API version */
    public string $linkedin_version = '202306';

    /**
     * Constructor. Initializes variables.
     * @param Provider $provider Export Provider
     */
    public function __construct($provider)
    {
        parent::__construct($provider);

        $this->exported_used_machines = ExportedUsedMachine::getAll($provider);
    }

    /**
     * Deletes old log files. Keep latest 10 log files.
     * @return bool true if deletion was successful, false if failure for at least one file occured
     */
    private function cleanUp()
    {
        $this->log('Cleaning up old log files.');
        $return = true;
        // keep only the 10 latest logfiles in addon data folder, delete older ones
        $log_files = glob(rex_path::pluginData('d2u_machinery', 'export') .'*'. $this->provider->type .'.log');
        if (is_array($log_files) && count($log_files) > 10) {
            for ($i = 0; $i < count($log_files) - 10; ++$i) {
                if (false === unlink($log_files[$i])) {
                    $return = false;
                    $this->log('Could not delete old file "'. $log_files[$i] .'".');
                }
            }
        }
        return $return;
    }

    /**
     * Get callback URL.
     * @return string callback URL
     */
    public function getCallbackURL()
    {
        return (\rex_addon::get('yrewrite') instanceof rex_addon && \rex_addon::get('yrewrite')->isAvailable() ? \rex_yrewrite::getCurrentDomain()->getUrl() : \rex::getServer())
            .'redaxo/'. rex_url::currentBackendPage(['func' => 'export', 'provider_id' => $this->provider->provider_id], false);
    }

    /**
     * Get and store access token.
     * @param string $code authorization code
     * @return bool true if access token is received correctly
     */
    public function getAccessToken($code)
    {
        $code = $_GET['code'];

        $accessTokenUrl = 'https://www.linkedin.com/oauth/v2/accessToken';
        $accessTokenParams = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->provider->social_app_id,
            'client_secret' => $this->provider->social_app_secret,
            'code' => $code,
            'redirect_uri' => $this->getCallbackURL(),
        ];
    
        $ch = curl_init($accessTokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($accessTokenParams));
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        curl_close($ch);
    
        if ($httpCode === 200) {
            $accessTokenData = json_decode($response, true);
    
            // get access token
            $accessToken = 
            $this->provider->social_access_token = $accessTokenData['access_token'];
            $this->provider->social_access_token_valid_until = time() + $accessTokenData['expires_in'];
            $this->provider->save();

            $this->log('Access token received. Valid until '. date('d.m.Y H:i:s', $this->provider->social_access_token_valid_until) .'.');
            return true;
        } else {
            $this->log('Error receiving access token: ' . $response);
        }
        return false;
    }

    /**
     * Get login URL.
     * @return string Login url
     */
    public function getLoginURL()
    {
        $callback_url = $this->getCallbackURL();
        return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $this->provider->social_app_id,
            'redirect_uri' => $callback_url,
            'scope' => 'r_liteprofile w_member_social' //rw_organization_admin for company streams
        ]);
    }

    /**
     * Check if access token is set and valid.
     * @return bool true if yes, otherwise false
     */
    public function hasAccessToken()
    {
        if ('' !== $this->provider->social_access_token && $this->provider->social_access_token_valid_until > time()) {
            // get linkedin stream id
            $apiUrl = 'https://api.linkedin.com/v2/me';
            $headers = [
                'Authorization: Bearer ' . $this->provider->social_access_token,
                'Linkedin-Version: '. $this->linkedin_version,
                'Content-Type: application/json',
                'x-li-format: json'
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($httpCode === 200) {
                if('' === $this->provider->linkedin_id) {
                    // No Linkedin id set? fetch default id
                    $responseData = json_decode($response, true);
                    $this->provider->linkedin_id = $responseData['id'];
                    $this->provider->save();
                    $this->log($this->provider->name .': Linkedin ID retrieved. It is: '. $this->provider->linkedin_id);
                }

                // Access token is valid
                $this->log($this->provider->name .': Access token checked. Token is valid.');

                return true;
            }
            else {
                // Access token is invalid
                $this->log($this->provider->name .': Access token checked. Token is invalid: '. $response);
            }
        }
        $this->provider->social_access_token = '';
        $this->provider->social_access_token_valid_until = 0;
        $this->provider->save();

        return false;
    }

    /**
     * Export used machines. For result see log file
     * @return false if at least one failure occured, true if complete export was successful
     */
    public function export()
    {
        $return = true;
        
        // First set 'delete' flag for all deleted machines
        ExportedUsedMachine::removeAllDeletedFromExport();

        foreach ($this->exported_used_machines as $exported_used_machine) {
            $used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
            // Delete from stream
            if ('delete' === $exported_used_machine->export_action || 'update' === $exported_used_machine->export_action) {
                if ('' !== $exported_used_machine->provider_import_id) {
                    $deleteUrl = 'https://api.linkedin.com/rest/posts/' . $exported_used_machine->provider_import_id;

                    $headers = [
                        'Authorization: Bearer '. $this->provider->social_access_token,
                        'Content-Type: application/json',
                        'Linkedin-Version: '. $this->linkedin_version,
                        'x-li-format: json'
                    ];
                    
                    $ch = curl_init($deleteUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    
                    curl_close($ch);
                    
                    if ($httpCode === 204) {
                        // Deleted post from stream
                        $this->log('Deleted post for "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" from '. $this->provider->linkedin_id .' Linkedin stream.');
                    } else {
                        // Failure deleting
                        $this->log('Failure deleting post for "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" from '. $this->provider->linkedin_id .' Linkedin stream: '. $response);
                        $return = false;
                    }
                }

                // delete in database
                if ('delete' === $exported_used_machine->export_action) {
                    $exported_used_machine->delete();
                } else {
                    // Export action 'update' means delete first and then add again
                    $exported_used_machine->export_action = 'add';
                    $exported_used_machine->provider_import_id = '';
                    $exported_used_machine->save();
                }
            }

            // Post on stream
            if ('add' === $exported_used_machine->export_action) {
                // upload image
                $uploadUrl = 'https://api.linkedin.com/rest/images?action=initializeUpload';
                $requestData = [
                    'initializeUploadRequest' => [
                        'owner' => 'urn:li:person:'. $this->provider->linkedin_id,
                    ],
                ];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uploadUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $this->provider->social_access_token,
                    'Linkedin-Version: '. $this->linkedin_version,
                    'Content-Type: application/json',
                ]);
                $response = curl_exec($ch);

                // get asset id
                $responseArray = json_decode($response, true);
                $assetId = $responseArray['value']['image'];

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode == 200) {
                    // upload picture to url received in response
                    $imagePath = rex_path::media($used_machine->pics[0]);

                    $uploadUrl = $responseArray['value']['uploadUrl'];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($imagePath));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $this->provider->social_access_token,
                        'Content-Type: '. mime_content_type($imagePath),
                    ]);
                    $response = curl_exec($ch);

                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    curl_close($ch);
                    
                    if ($httpCode == 201) {
                        $this->log('Image "'. $used_machine->pics[0].'" for used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" uploded. Asset ID: '. $assetId);
                    } else {
                        // failure uploading image
                        $this->log('Failure uploading image "'. $used_machine->pics[0].'" for used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'". API answer: '. $response);
                        $return = false;
                    }
                }

                // upload post
                if($assetId !== '') {
                    $postUrl = 'https://api.linkedin.com/rest/posts';
                    $postData = [
                        'author' => 'urn:li:person:'. $this->provider->linkedin_id,
                        'commentary' => $used_machine->manufacturer .' '. $used_machine->name .': '. \Sprog\Wildcard::get('d2u_machinery_export_linkedin_details', $this->provider->clang_id),
                        'contentLandingPage' => $used_machine->getUrl(true),
                        'visibility' => 'PUBLIC',
                        'distribution' => [
                            'feedDistribution' => 'MAIN_FEED',
                            'targetEntities' => [],
                            'thirdPartyDistributionChannels' => []
                        ],
                        'content' => [
                            'article' => [
                                'source' => $used_machine->getUrl(true),
                                'thumbnail' => $assetId,
                                'title' => $used_machine->manufacturer .' '. $used_machine->name,
                                'description' => $used_machine->teaser
                            ]
                        ],
                        'lifecycleState' => 'PUBLISHED',
                        'isReshareDisabledByAuthor' => false
                    ];

                    $headers = [
                        'Authorization: Bearer '. $this->provider->social_access_token,
                        'X-Restli-Protocol-Version: 2.0.0',
                        'Linkedin-Version: '. $this->linkedin_version,
                        'Content-Type: application/json',
                    ];
            
                    $ch = curl_init($postUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($httpCode === 201) {
                        // Published successfully
                        $exported_used_machine->export_action = '';
                        $exported_used_machine->export_timestamp = date('Y-m-d H:i:s');

                        // Get post id

                        if (false) {
                            $exported_used_machine->provider_import_id = $response['id'];
                            $exported_used_machine->save();
                        }

                        $this->log('Post for used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" on stream of '. $this->provider->linkedin_id .' published (post id: '. $xRestliId .')');
                    } else {
                        // Failure publishing post
                        $this->log('Failure posting used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" on stream of '. $this->provider->linkedin_id .': '. $response);
                        $return = false;
                    }
                }
            }
        }

        // Clean old logs
        $this->cleanUp();

        return $return;
    }

    /**
     * Send log file. Recipient is set in addon settings.
     * @return bool true if email was sent, otherwise false
     */
    public function sendImportLog()
    {
        $mail = new rex_mailer();
        $mail->isHTML(false);
        $mail->CharSet = 'utf-8';
        $mail->addAddress((string) rex_config::get('d2u_machinery', 'export_failure_email'));

        $mail->Subject = 'D2U Machinery Linkedin Exportbericht';

        $log_content = file_exists($this->log_file) ? file_get_contents($this->log_file) : '';
        if (false !== $log_content) {
            $mail->Body = $log_content;
            try {
                return $mail->send();
            } catch (Exception $e) {
                $this->log('Error sending log file via email: '. $e->getMessage());
            }
        }

        return false;
    }
}