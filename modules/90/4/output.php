<?php
if (!function_exists('print_used_machine_categories')) {
    /**
     * Prints category list for used machines.
     * @param Category[] $categories array with category objects
     * @param string $offer_type Either "sale" or "rent"
     */
    function print_used_machine_categories($categories, $offer_type = ''): void
    {
        // Get placeholder wildcard tags
        $sprog = rex_addon::get('sprog');
        $number_offers_row = '' == (string) 'REX_VALUE[2]' ? 4 : 'REX_VALUE[2]'; /** @phpstan-ignore-line */
        $counter = 0;
        foreach ($categories as $category) {
            // Only use used categories
            if ('' !== $offer_type) {
                $category->setOfferType($offer_type);
            }
            if ($category->hasUsedMachines(true)) {
                echo '<div class="col-sm-6 col-md-4'. (4 == $number_offers_row ? ' col-lg-3' : '') .' abstand">'; /** @phpstan-ignore-line */
                echo '<a href="'. $category->getUrl() .'">';
                echo '<div class="box" data-height-watch>';
                if ('' !== $category->pic || '' !== $category->pic_lang) {
                    echo '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', '' !== $category->pic_lang ? $category->pic_lang : $category->pic)
                        .'" alt="'. $category->name .'">';
                }
                echo '<div>'. $category->name .'</div>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
                ++$counter;
            }
        }

        // If no categories are used
        if (0 === $counter) {
            echo '<div class="col-12 abstand">';
            echo '<p>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_no_machines'. $sprog->getConfig('wildcard_close_tag') .'</p>';
            echo '</div>';
        }
    }
}

if (!function_exists('print_consulation_hint')) {
    /**
     * Prints consulation hint.
     */
    function print_consulation_hint(): void
    {
        $show_consultation_picture = 'REX_VALUE[1]' === 'true' ? false : true; /** @phpstan-ignore-line */

        $d2u_machinery = rex_addon::get('d2u_machinery');
        // Get placeholder wildcard tags
        $sprog = rex_addon::get('sprog');

        echo '<div class="col-12 col-md-9">';
        echo '<div class="consultation">';
        echo '<a href="'. rex_getUrl((int) $d2u_machinery->getConfig('consultation_article_id')) .'">';
        echo '<div class="abstand">';
        if ($show_consultation_picture && '' !== $d2u_machinery->getConfig('consultation_pic')) { /** @phpstan-ignore-line */
            echo '<img src="'. rex_url::media((string) $d2u_machinery->getConfig('consultation_pic')) .'" alt=""><br><br>';
        }
        echo '<p>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_consultation_hint'. $sprog->getConfig('wildcard_close_tag') .'</p>';
        echo '</div>';
        echo '</a>';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('print_used_machines')) {
    /**
     * Prints used machine list.
     * @param UsedMachine[] $used_machines used Machines
     */
    function print_used_machines($used_machines): void
    {
        $d2u_machinery = rex_addon::get('d2u_machinery');
        $number_offers_row = 'REX_VALUE[2]' === '' ? 4 : 'REX_VALUE[2]'; /** @phpstan-ignore-line */
        $counter = 0;
        foreach ($used_machines as $used_machine) {
            if ('online' !== $used_machine->online_status) {
                continue;
            }
            echo '<div class="col-sm-6 col-md-4'. (4 == $number_offers_row ? ' col-lg-3' : '') .' abstand">'; /** @phpstan-ignore-line */
            echo '<a href="'. $used_machine->getUrl(false) .'">';
            echo '<div class="box" data-height-watch>';
            if (count($used_machine->pics) > 0 && '' !== $used_machine->pics[0]) {
                echo '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', $used_machine->pics[0])	 .'" alt="'. $used_machine->name .'">';
            }
            echo '<div><b>'. $used_machine->manufacturer .' '. $used_machine->name .'</b></div>';
            if ('show' === (string) $d2u_machinery->getConfig('show_teaser', 'hide')) {
                echo '<div class="teaser">'. nl2br($used_machine->teaser) .'</div>';
            }
            echo '</div>';
            echo '</a>';
            echo '</div>';
            ++$counter;
        }

        // Directly forward if only one machine is available
        if (1 === $counter) {
            foreach ($used_machines as $used_machine) {
                if ('online' === $used_machine->online_status) {
                    header('Location: '. $used_machine->getUrl(false));
                    exit;
                }
            }
        }
    }
}

$d2u_machinery = rex_addon::get('d2u_machinery');
$picture_type = $d2u_machinery->getConfig('used_machines_pic_type', 'slider');
// Get placeholder wildcard tags
$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = TobiasKrais\D2UHelper\FrontendHelper::getUrlNamespace();
$url_id = TobiasKrais\D2UHelper\FrontendHelper::getUrlId();

if (filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_rent_category_id' === $url_namespace
        || filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_sale_category_id' === $url_namespace) {
    $category_id = (int) (filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 ? filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT));
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $category_id = $url_id;
    }
    $category = new Category($category_id, rex_clang::getCurrentId());
    $child_categories = $category->getChildren();
    if (count($child_categories) > 0) {
        // Print child categories
        echo '<div class="col-12 abstand">';
        echo '<h1>'. $category->name .'</h1>';
        echo '</div>';
        print_used_machine_categories($child_categories);
    } else {
        $used_machines = $category->getUsedMachines();
        echo '<div class="col-12">';
        echo '<div class="tab-content">';

        // Overview
        echo '<div id="tab_overview" class="tab-pane fade in active machine-tab show">';
        echo '<div class="row" data-match-height>';
        print_used_machines($used_machines);
        echo '</div>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }
} elseif ((filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_rent_machine_id' === $url_namespace)
        || (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'used_sale_machine_id' === $url_namespace)) {
    // Print used machine
    $used_machine_id = (int) (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 ? filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT));
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $used_machine_id = $url_id;
    }
    $used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
    // Redirect if object is not online
    if ('online' !== $used_machine->online_status) {
        rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
    }
    echo '<div class="col-12">';
    echo '<div class="tab-content">';

    // Overview
    echo '<div id="tab_overview" class="tab-pane fade show active machine-tab">';
    echo '<div class="row">';
    if (count($used_machine->pics) > 0) {
        // Slider picture(s)
        echo '<div class="col-12 col-md-6 abstand">';
        if ('lightbox' === $picture_type || 1 === count($used_machine->pics)) {
            echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                $used_machine->pics[0] .'" alt='. $used_machine->name .' style="max-width:100%;">';
        } else {
            // Slider
            echo '<div id="machineCarousel" class="carousel carousel-fade slide" data-ride="carousel">';
            // Slider indicators
            echo '<ol class="carousel-indicators">';
            for ($i = 0; $i < count($used_machine->pics); ++$i) {
                echo '<li data-target="#machineCarousel" data-slide-to="'. $i .'"';
                if (0 === $i) {
                    echo ' class="active"';
                }
                echo '></li>';
            }
            echo '</ol>';

            // Wrapper for slides
            echo '<div class="carousel-inner" role="listbox">';
            for ($i = 0; $i < count($used_machine->pics); ++$i) {
                echo '<div class="carousel-item';
                if (0 === $i) {
                    echo ' active';
                }
                echo '">';
                echo '<div class=".carousel-img-holder">';
                echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                $used_machine->pics[$i] .'" alt='. $used_machine->name .'>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';

            // Left and right controls
            echo '<a class="carousel-control-prev" href="#machineCarousel" role="button" data-slide="prev">';
            echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
            echo '<span class="sr-only">Previous</span>';
            echo '</a>';
            echo '<a class="carousel-control-next" href="#machineCarousel" role="button" data-slide="next">';
            echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
            echo '<span class="sr-only">Next</span>';
            echo '</a>';

            echo '</div>';
        }
        echo '</div>';
    }

    // Text
    echo '<div class="col-12 col-md-6">'. TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($used_machine->description) .'</div>';
    if ('slider' === $picture_type && count($used_machine->pics) > 0) {
        echo '<div class="col-12">&nbsp;</div>';
    }

    echo '<div class="col-12 col-md-6">';
    echo '<div class="row">'; // START details row

    // Video
    if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable()) {
        if (count($used_machine->videos) > 0) {
            echo '<div class="col-12" id="videoplayer">';
            echo '<h3>'. $tag_open .'d2u_machinery_video'. $tag_close .'</h3>';

            if ('plyr' === (string) rex_config::get('d2u_videos', 'player', 'ultimate') && rex_addon::get('plyr')->isAvailable()) {
                if (!function_exists('loadJsPlyr')) {
                    function loadJsPlyr(): void
                    {
                        echo '<script src="'. rex_url::base('assets/addons/plyr/vendor/plyr/dist/plyr.min.js') .'"></script>';
                    }
                }
                loadJsPlyr();
            }

            if (count($used_machine->videos) > 1) {
                // Multiple Videos
                if (rex_config::get('d2u_videos', 'player', 'ultimate') === 'plyr' && rex_addon::get('plyr')->isAvailable()) {
                    $media_filenames = [];
                    $ld_json = '';
                    foreach ($used_machine->videos as $video) {
                        $media_filenames[] = '' !== $video->redaxo_file_lang ? $video->redaxo_file_lang : $video->redaxo_file;
                        $ld_json .= $video->getLDJSONScript();
                    }
                    echo rex_plyr::outputMediaPlaylist($media_filenames, 'play-large,play,progress,current-time,duration,restart,volume,mute,pip,fullscreen');
                    echo $ld_json;
                    echo '<script src="'. rex_url::base('assets/addons/plyr/plyr_playlist.js') .'"></script>';
                } else {
                    $videomanager = new \TobiasKrais\D2UVideos\Videomanager();
                    $videomanager->printVideos($used_machine->videos);
                }
            } else {
                // Only one video
                $video = $used_machine->videos[array_key_first($used_machine->videos)];
                if ('plyr' === (string) rex_config::get('d2u_videos', 'player', 'ultimate') && rex_addon::get('plyr')->isAvailable()) {
                    $video_filename = '' !== $video->redaxo_file_lang ? $video->redaxo_file_lang : $video->redaxo_file;
                    echo '<div class="row"><div class="col-12">';
                    echo rex_plyr::outputMedia($video_filename, 'play-large,play,progress,current-time,duration,restart,volume,mute,pip,fullscreen', rex_url::media($video->getPreviewPictureFilename()));
                    echo '<script src="'. rex_url::base('assets/addons/plyr/plyr_init.js') .'"></script>';
                    echo '</div></div>';
                } else {
                    $videomanager = new \TobiasKrais\D2UVideos\Videomanager();
                    $videomanager->printVideo($video);
                }
                echo $video->getLDJSONScript();
            }
            echo '</div>';
        } // END Video
    }

    // Link to machine
    if ($used_machine->machine instanceof Machine) {
        echo '<div class="col-12">';
        echo '<p><b>'. $tag_open .'d2u_machinery_used_machines_link_machine'. $tag_close .':</b>';
        echo '<a href="'. $used_machine->machine->getUrl() .'">'. $used_machine->machine->name .'</a></p>';
        echo '</div>';
    }
    // Availability
    echo '<div class="col-12">';
    echo '<p><b>'. $tag_open .'d2u_machinery_used_machines_availible_from'. $tag_close .':</b> ';
    if ('' !== $used_machine->availability && $used_machine->availability > date('Y-m-d')) {
        if ($d2u_machinery->hasConfig('lang_replacement_'. rex_clang::getCurrentId()) && 'german' === (string) $d2u_machinery->getConfig('lang_replacement_'. rex_clang::getCurrentId())) {
            if (date_create($used_machine->availability) instanceof DateTime) {
                echo date_format(date_create($used_machine->availability), 'd.m.Y');
            }
        } else {
            if (date_create($used_machine->availability) instanceof DateTime) {
                echo date_format(date_create($used_machine->availability), 'Y/m/d');
            }
        }
    } else {
        echo $tag_open .'d2u_machinery_used_machines_availability'. $tag_close;
    }
    echo '</p></div>';
    // Year built
    if ($used_machine->year_built > 0) {
        echo '<div class="col-12">';
        echo '<p><b>'. $tag_open .'d2u_machinery_used_machines_year_built'. $tag_close .':</b> '. $used_machine->year_built .'</p>';
        echo '</div>';
    }
    // Price
    echo '<div class="col-12">';
    echo '<p><b>'. $tag_open .'d2u_machinery_used_machines_price'. $tag_close .':</b> ';
    if (0 !== $used_machine->price) {
        echo $used_machine->price .' '. $used_machine->currency_code;
        if ($used_machine->vat > 0) {
            echo ' ('. $used_machine->vat .'% '.$tag_open .'d2u_machinery_used_machines_vat_included'. $tag_close .')';
        } else {
            echo ' ('. $tag_open .'d2u_machinery_used_machines_vat_excluded'. $tag_close .')';
        }
    } else {
        echo $tag_open .'d2u_machinery_used_machines_price_on_request'. $tag_close;
    }
    echo '</p></div>';
    // Location
    if ('' !== $used_machine->location) {
        echo '<div class="col-12">';
        echo '<p><b>'. $tag_open .'d2u_machinery_used_machines_location'. $tag_close .':</b> '. $used_machine->location .'</p>';
        echo '</div>';
    }
    // External URL
    if ('' !== $used_machine->external_url && false !== filter_var($used_machine->external_url, FILTER_VALIDATE_URL)) {
        echo '<div class="col-12">';
        echo '<p><b>'. $tag_open .'d2u_machinery_used_machines_external_url'. $tag_close .':</b> <a href="'. $used_machine->external_url .'" target="_blank">'. $used_machine->external_url .'</a></p>';
        echo '</div>';
    }
    // Downloads
    if (count($used_machine->downloads) > 0) {
        echo '<div class="col-12 col-md-6">';
        echo '<ul class="download-list">';
        foreach ($used_machine->downloads as $document) {
            $rex_document = rex_media::get($document);
            if ($rex_document instanceof rex_media) {
                $filesize = 0;
                if (false !== filesize(rex_path::media() .'/'. $document)) {
                    $filesize = round(filesize(rex_path::media() .'/'. $document) / 1024 ** 2, 2);
                }
                $filetype = strtoupper(pathinfo(rex_path::media() . PATH_SEPARATOR . $document, PATHINFO_EXTENSION));

                // Check permissions
                $has_permission = true;
                if (rex_plugin::get('ycom', 'media_auth')->isAvailable()) {
                    $has_permission = rex_ycom_media_auth::checkPerm(rex_media_manager::create('', $document));
                }
                if ($has_permission) {
                    echo '<li>';
                    if ('pdf' === $filetype) {
                        echo '<span class="icon pdf"></span> ';
                    } else {
                        echo '<span class="icon file"></span> ';
                    }
                    echo '<a href="'. rex_url::media($document) .'" target="_blank">'.
                            $rex_document->getTitle() .' <span>('. $filetype .($filesize > 0 ? ', '. $filesize .' MB' : '') .')</span></a></li>';
                }
            }
        }
        echo '</ul>';
        echo '</div>';
    }

    echo '</div>'; // END details row
    echo '</div>';

    echo '</div>'; // End row
    echo '</div>'; // End tab

    // Lightbox pictures tab
    if ('lightbox' === $picture_type && count($used_machine->pics) > 0) {
        echo '<div id="tab_pics" class="tab-pane fade machine-tab">';
        echo '<div class="row">';
        $type_thumb = 'd2u_helper_gallery_thumb';
        $type_detail = 'd2u_helper_gallery_detail';
        $lightbox_id = random_int(0, getrandmax());

        echo '<div class="col-12">';
        echo '<div class="row">';
        foreach ($used_machine->pics as $pic) {
            $media = rex_media::get($pic);
            echo '<a href="index.php?rex_media_type='. $type_detail .'&rex_media_file='. $pic .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
            if ($media instanceof rex_media) {
                echo ' data-title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box"';
            if ($media instanceof rex_media) {
                echo ' alt="'. $media->getValue('title') .'" title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '<script>';
        echo "$(document).on('click', '[data-toggle=\"lightbox". $lightbox_id ."\"]', function(event) {";
        echo 'event.preventDefault();';
        echo '$(this).ekkoLightbox({ alwaysShowClose: true	});';
        echo '});';
        echo '</script>';
        echo '</div>'; // End row
        echo '</div>'; // End tab
    }

    // Contact Request form
    $d2u_machinery = rex_addon::get('d2u_machinery');
    echo '<div id="tab_request" class="tab-pane fade machine-tab">';
    echo '<div class="row">';
    echo '<div class="col-12 col-md-8 abstand">';
    $form_data = 'hidden|machine_name|'. $used_machine->manufacturer .' '. $used_machine->name .' (Gebrauchtmaschine)|REQUEST

			html||<h3> '. $tag_open .'d2u_machinery_request'. $tag_close .': '. $used_machine->name .'</h3><br>
			text|vorname|'. $tag_open .'d2u_helper_module_form_first_name'. $tag_close .'
			text|name|'. $tag_open .'d2u_helper_module_form_last_name'. $tag_close .' *|||{"required":"required"}
			text|company|'. $tag_open .'d2u_machinery_form_company'. $tag_close .'
			text|address|'. $tag_open .'d2u_helper_module_form_street'. $tag_close .'
			text|zip|'. $tag_open .'d2u_helper_module_form_zip'. $tag_close .'
			text|city|'. $tag_open .'d2u_helper_module_form_city'. $tag_close .'
			text|country|'. $tag_open .'d2u_helper_module_form_country'. $tag_close .'
			text|phone|'. $tag_open .'d2u_helper_module_form_phone'. $tag_close .' *|||{"required":"required"}
			text|email|'. $tag_open .'d2u_helper_module_form_email'. $tag_close .' *|||{"required":"required"}
			textarea|message|'. $tag_open .'d2u_helper_module_form_message'. $tag_close .' *|||{"required":"required"}
			checkbox|please_call|'. $tag_open .'d2u_helper_module_form_please_call'. $tag_close .'|0,1|0
			checkbox|privacy_policy_accepted|'. $tag_open .'d2u_helper_module_form_privacy_policy'. $tag_close .' *|0,1|0';
    if (rex_addon::get('yform_spam_protection')->isAvailable()) {
        $form_data .= '
			spam_protection|honeypot|Bitte nicht ausfüllen|'. $tag_open .'d2u_helper_module_form_validate_spam_detected'. $tag_close .'|0';
    } else {
        $form_data .= '
			php|validate_timer|Spamprotection|<input name="validate_timer" type="hidden" value="'. microtime(true) .'" />|
			validate|customfunction|validate_timer|TobiasKrais\D2UHelper\FrontendHelper::yform_validate_timer|5|'. $tag_open .'d2u_helper_module_form_validate_spambots'. $tag_close .'|

			html|honeypot||<div class="mail-validate hide">
			text|mailvalidate|'. $tag_open .'d2u_helper_module_form_email'. $tag_close .'||no_db
			validate|compare_value|mailvalidate||!=|'. $tag_open .'d2u_helper_module_form_validate_spam_detected'. $tag_close .'|
			html|honeypot||</div>';
    }
    $form_data .= '
			html||<br>* '. $tag_open .'d2u_helper_module_form_required'. $tag_close .'<br><br>

			submit|submit|'. $tag_open .'d2u_helper_module_form_send'. $tag_close .'|no_db

			validate|empty|name|'. $tag_open .'d2u_helper_module_form_validate_name'. $tag_close .'
			validate|empty|phone|'. $tag_open .'d2u_helper_module_form_validate_phone'. $tag_close .'
			validate|empty|email|'. $tag_open .'d2u_helper_module_form_validate_email'. $tag_close .'
			validate|type|email|email|'. $tag_open .'d2u_helper_module_form_validate_email'. $tag_close .'
			validate|empty|message|'. $tag_open .'d2u_helper_module_form_validate_message'. $tag_close .'
			validate|empty|privacy_policy_accepted|'. $tag_open .'d2u_helper_module_form_validate_privacy_policy'. $tag_close .'

			action|tpl2email|d2u_machinery_machine_request|'. ($used_machine->contact instanceof \D2U_Machinery\Contact ? $used_machine->contact->email : $d2u_machinery->getConfig('request_form_email'));

    $yform = new rex_yform();
    $yform->setFormData(trim($form_data));
    $yform->setObjectparams('form_action', $used_machine->getUrl());
    $yform->setObjectparams('form_anchor', 'tab_request');
    $yform->setObjectparams('form_name', 'd2u_machinery_module_90_4_'. $this->getCurrentSlice()->getId()); /** @phpstan-ignore-line */
    $yform->setObjectparams('Error-occured', $tag_open .'d2u_helper_module_form_validate_title'. $tag_close);
    $yform->setObjectparams('real_field_names', true);

    // action - showtext
    $yform->setActionField('showtext', [$tag_open .'d2u_helper_module_form_thanks'. $tag_close]);

    echo $yform->getForm();

    // Google Analytics Event
    if ('true' === rex_config::get('d2u_machinery', 'google_analytics_activate', 'false') &&
            '' !== rex_config::get('d2u_machinery', 'analytics_event_category', '') &&
            '' !== rex_config::get('d2u_machinery', 'analytics_event_action', '') &&
            false === rex_request('search_it_build_index', 'int', false)) {
        echo '<script>'. PHP_EOL;
        echo '	$(\'button[type="submit"]\').click(function(e) {'. PHP_EOL;
        echo "		ga('send', 'event', {". PHP_EOL;
        echo "			eventCategory: '". rex_config::get('d2u_machinery', 'analytics_event_category') ."',". PHP_EOL;
        echo "			eventAction:  '". rex_config::get('d2u_machinery', 'analytics_event_action') ."',". PHP_EOL;
        echo "			eventLabel: '". $used_machine->manufacturer .' '. $used_machine->name ."',". PHP_EOL;
        echo "			transport: 'beacon'". PHP_EOL;
        echo '		});'. PHP_EOL;
        echo '	});'. PHP_EOL;
        echo '</script>'. PHP_EOL;
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
    echo '</div>';
} else {
    // Categories
    echo '<div class="col-12">';
    echo '<div class="tab-content">';

    $current_article = rex_article::getCurrent();
    $class_active = ' active show';
    if ($current_article instanceof rex_article && $current_article->getId() === (int) $d2u_machinery->getConfig('used_machine_article_id_sale')) {
        // Sale offers
        echo '<div id="tab_sale" class="tab-pane fade machine-tab'. $class_active .'">';
        $class_active = '';
        echo '<div class="row" data-match-height>';
        print_used_machine_categories(Category::getAll(rex_clang::getCurrentId()), 'sale');
        echo '</div>';
        echo '</div>';
    }

    if ($current_article instanceof rex_article && $current_article->getId() === (int) $d2u_machinery->getConfig('used_machine_article_id_rent')) {
        // Rental offers
        echo '<div id="tab_rent" class="tab-pane fade machine-tab'. $class_active .'">';
        echo '<div class="row" data-match-height>';
        print_used_machine_categories(Category::getAll(rex_clang::getCurrentId()), 'rent');
        echo '</div>';
        echo '</div>';
    }

    if ($current_article instanceof rex_article && $current_article->getId() !== (int) $d2u_machinery->getConfig('used_machine_article_id_rent') && $current_article->getId() !== (int) $d2u_machinery->getConfig('used_machine_article_id_sale')) {
        echo '<p>'. $tag_open .'d2u_machinery_used_machines_config_error'. $tag_close .'</p>';
    }

    echo '</div>';
    echo '<div class="col-12 abstand"></div>';
    echo '</div>';

}

print_consulation_hint();
?>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
</script>