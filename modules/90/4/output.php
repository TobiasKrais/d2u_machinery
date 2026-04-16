<?php

use TobiasKrais\D2UMachinery\Category;
use TobiasKrais\D2UMachinery\Machine;
use TobiasKrais\D2UMachinery\UsedMachine;

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
            echo '<p>'. \Sprog\Wildcard::get('d2u_machinery_no_machines') .'</p>';
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
        echo '<p>'. \Sprog\Wildcard::get('d2u_machinery_consultation_hint') .'</p>';
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
            echo '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', $used_machine->pics[0]) .'" alt='. $used_machine->name .' style="max-width:100%;">';
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
                echo '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', $used_machine->pics[$i]) .'" alt='. $used_machine->name .'>';
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
            echo '<h3>'. \Sprog\Wildcard::get('d2u_machinery_video') .'</h3>';
            $videomanager = new \TobiasKrais\D2UVideos\Videomanager();
            if (count($used_machine->videos) > 1) {
                $videomanager->printVideos($used_machine->videos);
            } else {
                $video = $used_machine->videos[array_key_first($used_machine->videos)];
                $videomanager->printVideo($video);
                echo $video->getLDJSONScript();
            }
            echo '</div>';
        } // END Video
    }

    // Link to machine
    if ($used_machine->machine instanceof Machine) {
        echo '<div class="col-12">';
        echo '<p><b>'. \Sprog\Wildcard::get('d2u_machinery_used_machines_link_machine') .':</b>';
        echo '<a href="'. $used_machine->machine->getUrl() .'">'. $used_machine->machine->name .'</a></p>';
        echo '</div>';
    }
    // Availability
    echo '<div class="col-12">';
    echo '<p><b>'. \Sprog\Wildcard::get('d2u_machinery_used_machines_availible_from') .':</b> ';
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
        echo \Sprog\Wildcard::get('d2u_machinery_used_machines_availability');
    }
    echo '</p></div>';
    // Year built
    if ($used_machine->year_built > 0) {
        echo '<div class="col-12">';
        echo '<p><b>'. \Sprog\Wildcard::get('d2u_machinery_used_machines_year_built') .':</b> '. $used_machine->year_built .'</p>';
        echo '</div>';
    }
    // Price
    echo '<div class="col-12">';
    echo '<p><b>'. \Sprog\Wildcard::get('d2u_machinery_used_machines_price') .':</b> ';
    if (0 !== $used_machine->price) {
        echo $used_machine->price .' '. $used_machine->currency_code;
        if ($used_machine->vat > 0) {
            echo ' ('. $used_machine->vat .'% '.\Sprog\Wildcard::get('d2u_machinery_used_machines_vat_included') .')';
        } else {
            echo ' ('. \Sprog\Wildcard::get('d2u_machinery_used_machines_vat_excluded') .')';
        }
    } else {
        echo \Sprog\Wildcard::get('d2u_machinery_used_machines_price_on_request');
    }
    echo '</p></div>';
    // Location
    if ('' !== $used_machine->location) {
        echo '<div class="col-12">';
        echo '<p><b>'. \Sprog\Wildcard::get('d2u_machinery_used_machines_location') .':</b> '. $used_machine->location .'</p>';
        echo '</div>';
    }
    // External URL
    if ('' !== $used_machine->external_url && false !== filter_var($used_machine->external_url, FILTER_VALIDATE_URL)) {
        echo '<div class="col-12">';
        echo '<p><b>'. \Sprog\Wildcard::get('d2u_machinery_used_machines_external_url') .':</b> <a href="'. $used_machine->external_url .'" target="_blank">'. $used_machine->external_url .'</a></p>';
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
            echo '<a href="'. rex_media_manager::getUrl($type_detail, $pic) .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
            if ($media instanceof rex_media) {
                echo ' data-title="'. $media->getValue('title') .'"';
            }
            echo '>';
            echo '<img src="'. rex_media_manager::getUrl($type_thumb, $pic) .'" class="img-fluid gallery-pic-box"';
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

			html||<h3> '. \Sprog\Wildcard::get('d2u_machinery_request') .': '. $used_machine->name .'</h3><br>
			text|vorname|'. \Sprog\Wildcard::get('d2u_helper_module_form_first_name') .'
			text|name|'. \Sprog\Wildcard::get('d2u_helper_module_form_last_name') .' *|||{"required":"required"}
			text|company|'. \Sprog\Wildcard::get('d2u_machinery_form_company') .'
			text|address|'. \Sprog\Wildcard::get('d2u_helper_module_form_street') .'
			text|zip|'. \Sprog\Wildcard::get('d2u_helper_module_form_zip') .'
			text|city|'. \Sprog\Wildcard::get('d2u_helper_module_form_city') .'
			text|country|'. \Sprog\Wildcard::get('d2u_helper_module_form_country') .'
			text|phone|'. \Sprog\Wildcard::get('d2u_helper_module_form_phone') .' *|||{"required":"required"}
			text|email|'. \Sprog\Wildcard::get('d2u_helper_module_form_email') .' *|||{"required":"required"}
			textarea|message|'. \Sprog\Wildcard::get('d2u_helper_module_form_message') .' *|||{"required":"required"}
			checkbox|please_call|'. \Sprog\Wildcard::get('d2u_helper_module_form_please_call') .'|0,1|0
			checkbox|privacy_policy_accepted|'. \Sprog\Wildcard::get('d2u_helper_module_form_privacy_policy') .' *|0,1|0';
    if (rex_addon::get('yform_spam_protection')->isAvailable()) {
        $form_data .= '
			spam_protection|honeypot|Bitte nicht ausfüllen|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_spam_detected') .'|0';
    } else {
        $form_data .= '
			php|validate_timer|Spamprotection|<input name="validate_timer" type="hidden" value="'. microtime(true) .'" />|
			validate|customfunction|validate_timer|TobiasKrais\D2UHelper\FrontendHelper::yform_validate_timer|5|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_spambots') .'|

			html|honeypot||<div class="mail-validate hide">
			text|mailvalidate|'. \Sprog\Wildcard::get('d2u_helper_module_form_email') .'||no_db
			validate|compare_value|mailvalidate||!=|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_spam_detected') .'|
			html|honeypot||</div>';
    }
    $form_data .= '
			html||<br>* '. \Sprog\Wildcard::get('d2u_helper_module_form_required') .'<br><br>

			submit|submit|'. \Sprog\Wildcard::get('d2u_helper_module_form_send') .'|no_db

			validate|empty|name|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_name') .'
			validate|empty|phone|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_phone') .'
			validate|empty|email|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_email') .'
			validate|type|email|email|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_email') .'
			validate|empty|message|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_message') .'
			validate|empty|privacy_policy_accepted|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_privacy_policy') .'

            action|tpl2email|d2u_machinery_machine_request|'. ($used_machine->contact instanceof \TobiasKrais\D2UMachinery\Contact ? $used_machine->contact->email : $d2u_machinery->getConfig('request_form_email'));

    $yform = new rex_yform();
    $yform->setFormData(trim($form_data));
    $yform->setObjectparams('form_action', $used_machine->getUrl());
    $yform->setObjectparams('form_anchor', 'tab_request');
    $yform->setObjectparams('form_name', 'd2u_machinery_module_90_4_'. $this->getCurrentSlice()->getId()); /** @phpstan-ignore-line */
    $yform->setObjectparams('Error-occured', \Sprog\Wildcard::get('d2u_helper_module_form_validate_title'));
    $yform->setObjectparams('real_field_names', true);

    // action - showtext
    $yform->setActionField('showtext', [\Sprog\Wildcard::get('d2u_helper_module_form_thanks')]);

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
        echo '<p>'. \Sprog\Wildcard::get('d2u_machinery_used_machines_config_error') .'</p>';
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