<?php
/**
 * LinkedIn export.
 */
class SocialExportLinkedIn extends AExport
{
    /** @var string Linkedin API version */
    private string $linkedin_version = '202306';

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
     * @see https://learn.microsoft.com/en-us/linkedin/shared/authentication/authorization-code-flow
     * @param string $code authorization code
     * @return bool true if access token is received correctly
     */
    public function getAccessToken($code)
    {
        $code = rex_request::get('code', 'string');

        $accessTokenUrl = 'https://www.linkedin.com/oauth/v2/accessToken';
        $accessTokenParams = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->provider->social_app_id,
            'client_secret' => $this->provider->social_app_secret,
            'code' => $code,
            'redirect_uri' => $this->getCallbackURL(),
        ];

        $ch = curl_init($accessTokenUrl);
        if ($ch instanceof CurlHandle) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($accessTokenParams));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if (200 === $httpCode && is_string($response)) {
                $accessTokenData = json_decode($response, true);

                // get access token
                if (is_array($accessTokenData) && array_key_exists('access_token', $accessTokenData) && array_key_exists('expires_in', $accessTokenData)) {
                    $this->provider->social_access_token = (string) $accessTokenData['access_token'];
                    $this->provider->social_access_token_valid_until = time() + (int) $accessTokenData['expires_in'];
                    $this->provider->save();

                    $this->log('Access token received. Valid until '. date('d.m.Y H:i:s', $this->provider->social_access_token_valid_until) .'.');
                    sleep(15);
                    return true;
                }
            } else {
                $this->log('Error receiving access token: ' . $response);
            }
        }
        return false;
    }

    /**
     * Get login URL.
     * @see https://learn.microsoft.com/en-us/linkedin/shared/authentication/authorization-code-flow
     * @return string Login url
     */
    public function getLoginURL()
    {
        $callback_url = $this->getCallbackURL();
        if ('company' === $this->provider->linkedin_type) {
            return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->provider->social_app_id,
                'redirect_uri' => $callback_url,
                'scope' => 'rw_organization_admin w_organization_social',
            ]);

        }

        return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id' => $this->provider->social_app_id,
            'redirect_uri' => $callback_url,
            'scope' => 'r_liteprofile w_member_social',
        ]);

    }

    /**
     * Check if access token is set.
     * @return bool true if yes, otherwise false
     */
    public function hasAccessToken()
    {
        if ('' !== $this->provider->social_access_token && $this->provider->social_access_token_valid_until > time()) {
            return true;
        }

        return false;
    }

    /**
     * Check if Linkedin ID is set.
     * @return bool true if yes, otherwise false
     */
    public function hasLinkedinId()
    {
        if ('' !== $this->provider->linkedin_id) {
            return true;
        }

        return false;
    }

    /**
     * Export used machines. For result see log file.
     * @return string Empty string if no error occured.
     */
    public function export()
    {
        $return = '';

        // First set 'delete' flag for all deleted machines
        ExportedUsedMachine::removeAllDeletedFromExport();

        foreach ($this->exported_used_machines as $exported_used_machine) {
            $used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
            // Delete, see https://learn.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/posts-api?view=li-lms-2023-08&tabs=http#delete-posts
            if ('delete' === $exported_used_machine->export_action || 'update' === $exported_used_machine->export_action) {
                if ('' !== $exported_used_machine->provider_import_id) {
                    $deleteUrl = 'https://api.linkedin.com/rest/posts/' . $exported_used_machine->provider_import_id;

                    $headers = [
                        'Authorization: Bearer '. $this->provider->social_access_token,
                        'Content-Type: application/json',
                        'Linkedin-Version: '. $this->linkedin_version,
                        'x-li-format: json',
                    ];

                    $ch = curl_init($deleteUrl);
                    if($ch instanceof CurlHandle) {
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        curl_close($ch);

                        if (204 === $httpCode) {
                            // Deleted post
                            $this->log('Deleted post for "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" from '. $this->provider->linkedin_id .' Linkedin stream.');
                        } else {
                            // Failure deleting
                            $message = 'Failure deleting post for "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" from '. $this->provider->linkedin_id .' Linkedin stream: '. $response;
                            $this->log($message);
                            $return = $message;
                        }
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

            // Post
            if ('add' === $exported_used_machine->export_action) {
                // upload image, see https://learn.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/images-api
                $uploadUrl = 'https://api.linkedin.com/rest/images?action=initializeUpload';
                $requestData = [
                    'initializeUploadRequest' => [
                        'owner' => $this->provider->linkedin_id,
                    ],
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uploadUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData)); /** @phpstan-ignore-line */
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $this->provider->social_access_token,
                    'Linkedin-Version: '. $this->linkedin_version,
                    'Content-Type: application/json',
                ]);
                $response = curl_exec($ch);

                // get asset id
                $assetId = null;
                if(is_string($response)) {
                    $responseArray = json_decode($response, true);
                    if (is_array($responseArray) && array_key_exists('value', $responseArray) && is_array($responseArray['value']) && array_key_exists('image', $responseArray['value'])) {
                        $assetId = $responseArray['value']['image'];

                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if (200 === $httpCode) {
                            // upload picture to url received in response
                            $imagePath = rex_path::media($used_machine->pics[0]);

                            $uploadUrl = (string) $responseArray['value']['uploadUrl'];
                            $imageContent = file_get_contents($imagePath);
                            if('' !== $uploadUrl && is_string($imageContent)) {
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $uploadUrl);
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $imageContent);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                    'Authorization: Bearer ' . $this->provider->social_access_token,
                                    'Content-Type: '. mime_content_type($imagePath),
                                ]);
                                $response = curl_exec($ch);

                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                                curl_close($ch);

                                if (201 === $httpCode) {
                                    $this->log('Image "'. $used_machine->pics[0].'" for used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" uploded. Asset ID: '. $assetId);
                                } else {
                                    // failure uploading image
                                    $message = 'Failure uploading image "'. $used_machine->pics[0].'" for used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'". API answer: '. $response;
                                    $this->log($message);
                                    
                                    $return = $message;
                                }
                            }
                        }
                    }
                }

                // upload post, see https://learn.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/posts-api
                if (null !== $assetId) {
                    $postUrl = 'https://api.linkedin.com/rest/posts';
                    $postData = [
                        'author' => $this->provider->linkedin_id,
                        'commentary' => $used_machine->manufacturer .' '. $used_machine->name .': '. \Sprog\Wildcard::get('d2u_machinery_export_linkedin_details', $this->provider->clang_id),
                        'contentLandingPage' => $used_machine->getUrl(true),
                        'visibility' => 'PUBLIC',
                        'distribution' => [
                            'feedDistribution' => 'MAIN_FEED',
                            'targetEntities' => [],
                            'thirdPartyDistributionChannels' => [],
                        ],
                        'content' => [
                            'article' => [
                                'source' => $used_machine->getUrl(true),
                                'thumbnail' => $assetId,
                                'title' => $used_machine->manufacturer .' '. $used_machine->name,
                                'description' => $used_machine->teaser,
                            ],
                        ],
                        'lifecycleState' => 'PUBLISHED',
                        'isReshareDisabledByAuthor' => false,
                    ];

                    $headers = [
                        'Authorization: Bearer '. $this->provider->social_access_token,
                        'X-Restli-Protocol-Version: 2.0.0',
                        'Linkedin-Version: '. $this->linkedin_version,
                        'Content-Type: application/json',
                    ];

                    $ch = curl_init($postUrl);
                    if($ch instanceof CurlHandle) {
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HEADER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); /** @phpstan-ignore-line */
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if (201 === $httpCode) {
                            // Published successfully
                            $exported_used_machine->export_action = '';
                            $exported_used_machine->export_timestamp = date('Y-m-d H:i:s');

                            // Get post id
                            if (is_string($response)) {
                                $headerLines = explode("\r\n", $response);
                                $xRestliId = null;
                                foreach ($headerLines as $headerLine) {
                                    if (str_starts_with($headerLine, 'x-restli-id:')) {
                                        $xRestliId = trim(substr($headerLine, 12));
                                        break;
                                    }
                                }
                                if (null !== $xRestliId) {
                                    $exported_used_machine->provider_import_id = $xRestliId;
                                }
                                $exported_used_machine->save();

                                $this->log('Post for used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" on stream of '. $this->provider->linkedin_id .' published'. (null !== $xRestliId ? ' (post id: '. $xRestliId .')' : '.'));
                            }
                        } else {
                            // Failure publishing post
                            $message = 'Failure posting used machine "'. trim($used_machine->manufacturer .' '. $used_machine->name) .'" on stream of '. $this->provider->linkedin_id .': '. $response;
                            $this->log($message);
                            $return = $message;
                        }
                    }
                }
            }
        }

        // Clean old logs
        $this->cleanUp();

        return $return;
    }

    /**
     * Get and set Linkedin id.
     * @return bool true if successully received
     */
    public function receiveLinkedinID()
    {
        // get linkedin stream id, see https://learn.microsoft.com/en-us/linkedin/shared/integrations/people/profile-api
        $apiUrl = 'https://api.linkedin.com/v2/me';
        $headers = [
            'Authorization: Bearer ' . $this->provider->social_access_token,
            'Linkedin-Version: '. $this->linkedin_version,
            'Content-Type: application/json',
            'x-li-format: json',
        ];
        if ('company' === $this->provider->linkedin_type) {
            // see https://learn.microsoft.com/en-us/linkedin/marketing/integrations/community-management/organizations/organization-access-control-by-role?
            $apiUrl = 'https://api.linkedin.com/rest/organizationAcls?q=roleAssignee';
            $headers = [
                'Authorization: Bearer ' . $this->provider->social_access_token,
                'Linkedin-Version: '. $this->linkedin_version,
                'X-Restli-Protocol-Version: 2.0.0',
            ];
        }

        $ch = curl_init($apiUrl);
        if($ch instanceof CurlHandle) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if (200 === $httpCode && is_string($response)) {
                // No Linkedin urn id set? fetch default person id or first company id
                $responseData = json_decode($response, true);
                if(is_array($responseData)) {
                    if ('company' === $this->provider->linkedin_type) {
                        if (is_array($responseData['elements']) && array_key_exists(0, $responseData['elements']) && is_array($responseData['elements'][0]) && array_key_exists('organization', $responseData['elements'][0])) {
                            $this->provider->linkedin_id = (string) $responseData['elements'][0]['organization'];
                        } else {
                            $this->log($this->provider->name .': User has no company rights.');
                            return false;
                        }
                    } else {
                        $this->provider->linkedin_id = 'urn:li:person:'. $responseData['id'];
                    }
                    $this->provider->save();
                    $this->log($this->provider->name .': Linkedin ID set. It is: '. $this->provider->linkedin_id);
                    return true;
                }
            }
            else {
                // Access token is invalid
                $this->log($this->provider->name .': Cannot get Linkedin ID: '. $response);
            }
        }

        return false;

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

    /**
     * Validate access token. If access token is invalid, it is deleted in datebase.
     * @return bool true if access token is valid
     */
    public function validateAccessToken()
    {
        // see https://learn.microsoft.com/de-de/linkedin/shared/authentication/token-introspection
        $apiUrl = 'https://www.linkedin.com/oauth/v2/introspectToken';
        $accessTokenParams = [
            'client_id' => $this->provider->social_app_id,
            'client_secret' => $this->provider->social_app_secret,
            'token' => $this->provider->social_access_token,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($accessTokenParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (200 === $httpCode) {
            $this->log($this->provider->name .': Access token is valid.');
            return true;
        }

        if (is_string($response)) {
            $this->log($this->provider->name .': '. json_decode($response));
        }
        $this->log($this->provider->name .': Access token is invalid. Please retry export.');
        $this->provider->social_access_token = '';
        $this->provider->social_access_token_valid_until = 0;
        $this->provider->save();
        return false;

    }
}
