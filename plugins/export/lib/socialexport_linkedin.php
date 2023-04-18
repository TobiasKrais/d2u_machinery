<?php
/**
 * LinkedIn export.
 */
class SocialExportLinkedIn extends AExport
{
    /** @var OAuth LinkedIn OAuth object */
    private OAuth $oauth;

    /**
     * Constructor. Initializes variables.
     * @param Provider $provider Export Provider
     */
    public function __construct($provider)
    {
        parent::__construct($provider);

        $this->exported_used_machines = ExportedUsedMachine::getAll($provider);
        $this->oauth = new OAuth($provider->social_app_id, $provider->social_app_secret);

        if ($this->hasAccessToken()) {
            $this->oauth->setToken($provider->social_oauth_token, $provider->social_oauth_token_secret);
        }
    }

    /**
     * Get callback URL.
     * @return string callback URL
     */
    private function getCallbackURL()
    {
        return (\rex_addon::get('yrewrite') instanceof rex_addon && \rex_addon::get('yrewrite')->isAvailable() ? \rex_yrewrite::getCurrentDomain()->getUrl() : \rex::getServer())
            .'redaxo/'. rex_url::currentBackendPage(['func' => 'export', 'provider_id' => $this->provider->provider_id], false);
    }

    /**
     * Get and set access token.
     * @param string $verifier_pin OAuth verifier pin
     * @return string error message
     */
    public function getAccessToken($verifier_pin)
    {
        $session = rex_request::session('linkedin', 'array');
        if (is_array($session) && array_key_exists('requesttoken', $session) && array_key_exists('requesttoken_secret', $session)) {
            $requesttoken = $session['requesttoken'];
            $requesttoken_secret = $session['requesttoken_secret'];
            rex_request::setSession('linkedin', null);

            try {
                // now set the token so we can get our access token
                $this->oauth->setToken($requesttoken, $requesttoken_secret);

                // get the access token now that we have the verifier pin
                $at_info = $this->oauth->getAccessToken('https://api.linkedin.com/uas/oauth/accessToken', '', $verifier_pin);
                // store in DB
                if (is_array($at_info)) {
                    $this->provider->social_oauth_token = $at_info['oauth_token'];
                    $this->provider->social_oauth_token_secret = $at_info['oauth_token_secret'];
                    $this->provider->social_oauth_token_valid_until = time() + $at_info['oauth_expires_in'];
                    $this->provider->save();

                    // set the access token so we can make authenticated requests
                    $this->oauth->setToken($this->provider->social_oauth_token, $this->provider->social_oauth_token_secret);
                }
            } catch (OAuthException $e) {
                return $e->getMessage();
            }
        }
        return '';
    }

    /**
     * Get login URL.
     * @return string Login url
     */
    public function getLoginURL()
    {
        $session = rex_request::session('linkedin', 'array');
        if (is_array($session) && array_key_exists('requesttoken', $session)) {
            return 'https://www.linkedin.com/uas/oauth/authenticate?oauth_token='. $session['requesttoken'];
        }
        return '';
    }

    /**
     * Get and set request token.
     * @return string error message
     */
    public function getRequestToken()
    {
        try {
            $rt_info = $this->oauth->getRequestToken('https://api.linkedin.com/uas/oauth/requestToken?scope=r_basicprofile+r_emailaddress+w_share', $this->getCallbackURL());
            if (is_array($rt_info)) {
                rex_request::setSession('linkedin', ['requesttoken' => $rt_info['oauth_token'], 'requesttoken_secret' => $rt_info['oauth_token_secret']]);
            }
        } catch (OAuthException $e) {
            return $e->getMessage();
        }
        return '';
    }

    /**
     * Check if access token is set.
     * @return bool true if yes, otherwise false
     */
    public function hasAccessToken()
    {
        if ('' !== $this->provider->social_oauth_token && '' !== $this->provider->social_oauth_token_secret) {
            if ($this->provider->social_oauth_token_valid_until > time()) {
                return true;
            }

            $this->provider->social_oauth_token = '';
            $this->provider->social_oauth_token_secret = '';
            $this->provider->social_oauth_token_valid_until = 0;
            $this->provider->save();

        }
        return false;
    }

    /**
     * Is user mentioned in provider logged in?
     * @return bool|string true id yes, false if no or a string with error message
     */
    public function isUserLoggedIn()
    {
        try {
            // Fetch id from LinkedIn
            $api_url = 'https://api.linkedin.com/v1/people/~:(id,email-address)';
            $this->oauth->fetch($api_url, [], OAUTH_HTTP_METHOD_GET);
            $response = $this->oauth->getLastResponse();

            $linkedin_email = '';
            $xml = new DOMDocument();
            $xml->loadXML($response);
            $persons = $xml->getElementsByTagName('person');
            foreach ($persons as $person) {
                $email = $person->getElementsByTagName('email-address');
                if (null !== $email->item(0) && null !== $email->item(0)->nodeValue) {
                    $linkedin_email = $email->item(0)->nodeValue;
                }
            }
            if ('' === $linkedin_email) {
                return rex_i18n::msg('d2u_machinery_export_linkedin_mail_failed');
            }
            if (strtolower($linkedin_email) !== strtolower($this->provider->linkedin_email)) {
                rex_request::setSession('linkedin', null);
                return rex_i18n::msg('d2u_machinery_export_linkedin_login_again');
            }

            return true;

        } catch (OAuthException $e) {
            return 'Error: '. $e->getMessage() .'<br />';
        }
    }

    /**
     * Export used machines.
     * @return string Error message, empty if no error occured
     */
    public function export()
    {
        // First remove all deleted
        ExportedUsedMachine::removeAllDeletedFromExport();

        foreach ($this->exported_used_machines as $exported_used_machine) {
            $used_machine = new UsedMachine($exported_used_machine->used_machine_id, $this->provider->clang_id);
            // Delete from stream
            if ('delete' === $exported_used_machine->export_action || 'update' === $exported_used_machine->export_action) {
                // State April 2015: deleting is not supported
                /*
                if($exported_used_machine->provider_import_id !== "") {
                    try {
                        $this->oauth->fetch($exported_used_machine->provider_import_id, false, OAUTH_HTTP_METHOD_DELETE);
                    } catch (OAuthException $e) {
                    }
                }
                */

                // delete in database
                if ('delete' === $exported_used_machine->export_action) {
                    $exported_used_machine->delete();
                } else {
                    $exported_used_machine->export_action = 'add';
                    $exported_used_machine->provider_import_id = '';
                }
            }

            // Post on wall
            if ('add' === $exported_used_machine->export_action) {
                // Create XML for LinkedIn Social Stream
                // Documentation: https://developer.linkedin.com/documents/share-api
                // <?xml version="1.0" encoding="UTF-8">
                $xml = new DOMDocument('1.0', 'UTF-8');
                $xml->formatOutput = true;

                // Post on Social Stream: prepare XML
                if ('' === $this->provider->linkedin_groupid) {
                    // <share>
                    $share = $xml->createElement('share');

                    // <comment>Bester Kran auf dem Markt</comment>
                    $comment = $xml->createElement('comment');
                    $comment->appendChild($xml->createTextNode(\Sprog\Wildcard::get('d2u_machinery_export_linkedin_comment_text', $this->provider->clang_id)));
                    $share->appendChild($comment);

                    // <content>
                    $content = $xml->createElement('content');

                    // <title>Potain IGO 32</title>
                    $title = $xml->createElement('title');
                    $title->appendChild($xml->createTextNode($used_machine->manufacturer .' '. $used_machine->name));
                    $content->appendChild($title);

                    // <description>Price: on request; ... </description>
                    $description = $xml->createElement('description');
                    $description->appendChild($xml->createTextNode($used_machine->getExtendedTeaser()));
                    $content->appendChild($description);

                    // <submitted-url>http://www.meier-krantechnik.de/de/produkte/gebrauchte-krane?action=detail&item=13</submitted-url>
                    $submitted_url = $xml->createElement('submitted-url');
                    $submitted_url->appendChild($xml->createTextNode($used_machine->getUrl(true)));
                    $content->appendChild($submitted_url);

                    // <submitted-image-url>http://www.meier-krantechnik.de/index.php?rex_img_type=d2u_baumaschinen_list&amp;rex_img_file=sjjdc_826.jpg</submitted-image-url>
                    if (count($used_machine->pics) > 0) {
                        $submitted_image_url = $xml->createElement('submitted-image-url');
                        $submitted_image_url->appendChild($xml->createTextNode((\rex_addon::get('yrewrite') instanceof rex_addon && \rex_addon::get('yrewrite')->isAvailable() ? \rex_yrewrite::getCurrentDomain()->getUrl() : \rex::getServer())
                            .'index.php?rex_media_type='. $this->provider->media_manager_type .'&rex_media_file='. $used_machine->pics[0]));
                        $content->appendChild($submitted_image_url);
                    }

                    // </content>
                    $share->appendChild($content);

                    // <visibility>
                    $visibility = $xml->createElement('visibility');

                    // <code>anyone</code>
                    $code = $xml->createElement('code');
                    $code->appendChild($xml->createTextNode('anyone'));
                    $visibility->appendChild($code);

                    // </visibility>
                    $share->appendChild($visibility);

                    // </share>
                    $xml->appendChild($share);
                }
                // Post on group stream: prepare XML
                else {
                    $title_text = $this->provider->company_name .' '. \Sprog\Wildcard::get('d2u_machinery_export_linkedin_offers', $this->provider->clang_id) .': '
                        . $used_machine->name;
                    $summary_text = \Sprog\Wildcard::get('d2u_machinery_export_linkedin_details', $this->provider->clang_id) .' '. $used_machine->getUrl(true);

                    // <post>
                    $post = $xml->createElement('post');

                    // <title>Potain IGO 32</title>
                    $title = $xml->createElement('title');
                    $title->appendChild($xml->createTextNode($title_text));
                    $post->appendChild($title);

                    // <summary>Price: on request; ... </summary>
                    $summary = $xml->createElement('summary');
                    $summary->appendChild($xml->createTextNode($summary_text));
                    $post->appendChild($summary);

                    // </share>
                    $xml->appendChild($post);
                }

                // Let's post it
                try {
                    $api_url = 'https://api.linkedin.com/v1/people/~/shares';
                    if ('' !== $this->provider->linkedin_groupid) {
                        $api_url = 'https://api.linkedin.com/v1/groups/'. $this->provider->linkedin_groupid .'/posts';
                    }

                    $this->oauth->fetch($api_url, [$xml->saveXML()], OAUTH_HTTP_METHOD_POST, ['Content-Type' => 'text/xml']);

                    // Getting stream id
                    $response_headers = self::http_parse_headers((string) $this->oauth->getLastResponseHeaders());
                    if (count($response_headers) > 0) {
                        if (isset($response_headers['Location'])) {
                            $exported_used_machine->provider_import_id = $response_headers['Location'];
                        }
                        // Save results
                        $exported_used_machine->export_action = '';
                        $exported_used_machine->export_timestamp = date('Y-m-d H:i:s');
                        $exported_used_machine->save();
                    }
                } catch (OAuthException $e) {
                    return rex_i18n::msg('d2u_machinery_export_linkedin_upload_failed') .' '. $e;
                }
            }
        }

        return '';
    }

    /**
     * Parses HTTP headers into an associative array.
     * @param string $r string containing HTTP headers
     * @return string[] returns an array on success or false on failure
     */
    private static function http_parse_headers($r)
    {
        $o = [];
        $s = explode("\r\n", substr($r, false !== stripos($r, "\r\n") ? stripos($r, "\r\n") : 0));
        foreach ($s as $h) {
            [$v, $val] = explode(': ', $h);
            if ('' === $v) {
                continue;
            }
            $o[$v] = $val;
        }
        return $o;
    }

    /**
     * Logout by cleaning LinkedIn session vars and removing access token.
     */
    public function logout(): void
    {
        rex_request::setSession('linkedin', null);

        $this->provider->social_oauth_token = '';
        $this->provider->social_oauth_token_secret = '';
    }
}
