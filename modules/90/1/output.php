<?php
if (!function_exists('print_categories')) {
    /**
     * Prints category list.
     * @param Category[] $categories array with category objects
     */
    function print_categories($categories): void
    {
        // Get placeholder wildcard tags
        $sprog = rex_addon::get('sprog');
        $counter = 0;
        foreach ($categories as $category) {
            // Only use used categories
            if (count($category->getMachines()) > 0) {
                echo '<div class="col-6 col-md-4'. (3 === (int) 'REX_VALUE[1]' ? '' : ' col-lg-3') .' mr-auto ml-auto abstand">'; /** @phpstan-ignore-line */
                echo '<a href="'. $category->getUrl() .'" class="bluebox">';
                echo '<div class="box" data-height-watch>';
                if ('' !== $category->pic || '' !== $category->pic_lang) {
                    echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                        ('' !== $category->pic_lang ? $category->pic_lang : $category->pic) .'" alt="'. $category->name .'">';
                } else {
                    echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
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
        $d2u_machinery = rex_addon::get('d2u_machinery');
        // Get placeholder wildcard tags
        $sprog = rex_addon::get('sprog');

        echo '<div class="col-12">';
        echo '<div class="consultation">';
        echo '<a href="'. rex_getUrl((int) $d2u_machinery->getConfig('consultation_article_id')) .'">';
        echo '<div class="row abstand">';

        echo '<div class="col-12 col-md-4 col-lg-3">';
        if ('' !== $d2u_machinery->getConfig('consultation_pic')) {
            echo '<img src="'. rex_url::media((string) $d2u_machinery->getConfig('consultation_pic')) .'" alt="">';
        }
        echo '</div>';

        echo '<div class="col-12 col-md-8 col-lg-9">';
        echo '<p>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_consultation_hint'. $sprog->getConfig('wildcard_close_tag') .'</p>';
        if ($d2u_machinery->hasConfig('contact_phone') && '' !== $d2u_machinery->getConfig('contact_phone')) {
            echo '<h3>'. $sprog->getConfig('wildcard_open_tag') .'d2u_helper_module_form_phone'. $sprog->getConfig('wildcard_close_tag') .' '. $d2u_machinery->getConfig('contact_phone') .'</h3>';
        }
        echo '</div>';

        echo '</div>';
        echo '</a>';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('print_industry_sectors')) {
    /**
     * Prints industry sector list.
     */
    function print_industry_sectors(): void
    {
        // Get placeholder wildcard tags
        $sprog = rex_addon::get('sprog');

        echo '<div class="col-12 abstand">';
        echo '<h1>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_industry_sectors'. $sprog->getConfig('wildcard_close_tag') .'</h1>';
        echo '</div>';

        foreach (IndustrySector::getAll(rex_clang::getCurrentId()) as $industry_sector) {
            if ('online' === $industry_sector->online_status && count($industry_sector->getMachines()) > 0) {
                echo '<div class="col-sm-6 col-md-4'. (3 === (int) 'REX_VALUE[1]' ? '' : ' col-lg-3') .' mr-auto ml-auto abstand">'; /** @phpstan-ignore-line */
                echo '<a href="'. $industry_sector->getUrl() .'" class="bluebox">';
                echo '<div class="box" data-height-watch>';
                if ('' !== $industry_sector->pic) {
                    echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                        $industry_sector->pic .'" alt="'. $industry_sector->name .'">';
                } else {
                    echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
                }
                echo '<div>'. $industry_sector->name .'</div>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
        }
    }
}

if (!function_exists('print_machines')) {
    /**
     * Prints machine list.
     * @param Machine[] $machines machines
     * @param string $title Title for the list
     */
    function print_machines($machines, $title): void
    {
        $d2u_machinery = rex_addon::get('d2u_machinery');
        if ('' !== $title) {
            echo '<div class="col-12 abstand">';
            echo '<h1>'. $title.'</h1>';
            echo '</div>';
        }

        $counter = 0;
        foreach ($machines as $machine) {
            if ('online' === $machine->online_status) {
                if ($d2u_machinery->hasConfig('show_teaser') && 'show' === $d2u_machinery->getConfig('show_teaser')) {
                    echo '<div class="col-12 col-md-6 col-lg-4 mr-auto ml-auto abstand" data-height-watch>';
                } else {
                    echo '<div class="col-sm-6 col-md-4'. (3 === (int) 'REX_VALUE[1]' ? '' : ' col-lg-3') .' mr-auto ml-auto abstand" data-height-watch>'; /** @phpstan-ignore-line */
                }
                echo '<a href="'. $machine->getUrl() .'" class="bluebox">';
                echo '<div class="box" data-height-watch>';
                if (count($machine->pics) > 0 && '' !== $machine->pics[0]) {
                    echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                        $machine->pics[0] .'" alt="'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'">';
                } else {
                    echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
                }
                echo '<div><b>'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'</b></div>';
                if ($d2u_machinery->hasConfig('show_teaser') && 'show' === (string) $d2u_machinery->getConfig('show_teaser')) {
                    echo '<div class="teaser">'. nl2br($machine->teaser) .'</div>';
                }
                echo '</div>';
                echo '</a>';
                echo '</div>';
                ++$counter;
            }
        }

        // Directly forward if only one machine is available
        if (1 === $counter) {
            foreach ($machines as $machine) {
                if ('online' === $machine->online_status) {
                    header('Location: '. $machine->getUrl(false));
                    exit;
                }
            }
        }
    }
}

// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');
$d2u_machinery = rex_addon::get('d2u_machinery');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();
?>

<div id="d2u_machinery_module_90_1" class="col-12">
    <div class="row">

<?php
if (filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'category_id' === $url_namespace && $url_id > 0) {
    $category_id = (int) filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
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
        print_categories($child_categories);
    } else {
        $machines = $category->getMachines(true);
        echo '<div class="col-12">';
        echo '<div class="tab-content">';

        // Overview
        echo '<div id="tab_overview" class="tab-pane fade show active machine-tab">';
        echo '<div class="row" data-match-height>';
        print_machines($machines, '');
        echo '</div>';
        echo '</div>';

        // Usage Areas
        if (rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable() && 'show' === (string) $d2u_machinery->getConfig('show_categories_usage_areas', 'hide')) {
            echo '<div id="tab_usage_areas" class="tab-pane fade machine-tab">';
            echo '<div class="row">';
            echo '<div class="col-12">';
            if ($d2u_machinery->hasConfig('usage_area_header') && 'usage' === (string) $d2u_machinery->getConfig('usage_area_header')) {
                $usage_areas = UsageArea::getAll(rex_clang::getCurrentId(), $category_id);
                echo '<table class="matrix">';
                echo '<thead><tr><td></td>';
                foreach ($usage_areas as $usage_area) {
                    echo '<td class="inactive-cell">';
                    echo '<div class="vertical usage-area-name">'. $usage_area->name .'</div></td>';
                }
                echo '</tr></thead>';
                echo '<tbody>';
                foreach ($machines as $machine) {
                    echo '<tr><td>';
                    echo '<a href="'. $machine->getUrl() .'"><div class="comparison-header">';
                    if (count($machine->pics) > 0 && '' !== $machine->pics[0]) {
                        echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'>';
                    } else {
                        echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
                    }
                    echo '<br><b>'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
                    foreach ($usage_areas as $usage_area) {
                        if (in_array($usage_area->usage_area_id, $machine->usage_area_ids, true)) {
                            echo '<td class="active-cell"></td>';
                        } else {
                            echo '<td class="inactive-cell"></td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                $usage_area_matrix = $category->getUsageAreaMatrix();
                echo '<table class="matrix">';
                echo '<thead><tr><td></td>';
                foreach ($machines as $machine) {
                    echo '<td valign="bottom" align="center">';
                    echo '<a href="'. $machine->getUrl() .'"><div class="comparison-header">';
                    if (count($machine->pics) > 0 && '' !== $machine->pics[0]) {
                        echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'>';
                    } else {
                        echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
                    }
                    echo '<br><b>'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
                }
                echo '</tr></thead>';
                echo '<tbody>';

                foreach ($usage_area_matrix as $key => $machine_ids) {
                    /** @var int[] $machine_ids */
                    echo '<tr><td class="inactive-cell usage-area-name">'. $key .'</td>';
                    foreach ($machines as $machine) {
                        if (in_array($machine->machine_id, $machine_ids, true)) {
                            echo '<td class="active-cell"></td>';
                        } else {
                            echo '<td class="inactive-cell"></td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        // Technical data
        echo '<div id="tab_tech_data" class="tab-pane fade machine-tab">';
        echo '<div class="row">';
        echo '<div class="col-12">';
        $tech_data_matrix = $category->getTechDataMatrix();
        echo '<table class="matrix">';
        echo '<thead><tr><td></td><td></td>';
        foreach ($machines as $machine) {
            echo '<td valign="bottom" align="center">';
            echo '<a href="'. $machine->getUrl() .'"><div class="comparison-header">';
            if (count($machine->pics) > 0 && '' !== $machine->pics[0]) {
                echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'>';
            } else {
                echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
            }
            echo '<br><b>'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
        }
        echo '</tr></thead>';
        echo '<tbody>';
        foreach ($tech_data_matrix as $wildcard => $values) {
            /** @var array<string> $values */
            echo '<tr>';
            echo '<td class="inactive-cell"><b>'. $wildcard .'</b></td>';
            echo '<td class="inactive-cell unit"><b>'. $values['unit'] .'</b></td>';
            foreach ($machines as $machine) {
                echo '<td class="inactive-cell">'. $values['machine_ids'][$machine->machine_id] .'</td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }
} elseif (filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || ('machine_id' === $url_namespace && $url_id > 0)) {
    // Print machine
    $machine_id = (int) filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $machine_id = $url_id;
    }
    $machine = new Machine($machine_id, rex_clang::getCurrentId());
    // Redirect if machine is not online
    if ('online' !== $machine->online_status) {
        rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
    }

    echo '<div class="col-12">';
    echo '<div class="tab-content">';

    // Overview
    echo '<div id="tab_overview" class="tab-pane fade show active machine-tab">';
    echo '<div class="row">';

    // Text
    echo '<div class="col-12 col-md-6">';
    echo d2u_addon_frontend_helper::prepareEditorField($machine->description);
    echo '<p>&nbsp;</p>';
    echo '</div>';

    if (count($machine->pics) > 0) {
        // Picture(s)
        echo '<div class="col-12 col-md-6">';
        if (1 === count($machine->pics)) {
            echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                $machine->pics[0] .'" alt="'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'" style="max-width:100%;">';
        } else {
            // Slider
            echo '<div id="machineCarousel" class="carousel carousel-fade slide" data-ride="carousel" data-pause="hover">';
            // Slider indicators

            echo '<ol class="carousel-indicators">';
            for ($i = 0; $i < count($machine->pics); ++$i) {
                echo '<li data-target="#machineCarousel" data-slide-to="'. $i .'"';
                if (0 === $i) {
                    echo 'class="active"';
                }
                echo '></li>';
            }
            echo '</ol>';

            // Wrapper for slides
            echo '<div class="carousel-inner">';
            for ($i = 0; $i < count($machine->pics); ++$i) {
                echo '<div class="carousel-item';
                if (0 === $i) {
                    echo ' active';
                }
                echo '">';
                echo '<img class="d-block w-100" src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                $machine->pics[$i] .'" alt="'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'">';
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

    // Video
    if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable()) {
        $videos = array_merge($machine->videos, $machine->category instanceof Category ? $machine->category->videos : []);
        if (count($videos) > 0) {
            echo '<div class="col-12'. (count($videos) > 1 ? '' : ' col-lg-6') .'" id="videoplayer">';
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

            if (count($videos) > 1) {
                // Multiple Videos
                if ((string) rex_config::get('d2u_videos', 'player', 'ultimate') === 'plyr' && rex_addon::get('plyr')->isAvailable()) {
                    $media_filenames = [];
                    $ld_json = '';
                    foreach ($videos as $video) {
                        $media_filenames[] = '' !== $video->redaxo_file_lang ? $video->redaxo_file_lang : $video->redaxo_file;
                        $ld_json .= $video->getLDJSONScript();
                    }
                    echo rex_plyr::outputMediaPlaylist($media_filenames, 'play-large,play,progress,current-time,duration,restart,volume,mute,pip,fullscreen');
                    echo $ld_json;
                    echo '<script src="'. rex_url::base('assets/addons/plyr/plyr_playlist.js') .'"></script>';
                } else {
                    $videomanager = new Videomanager();
                    $videomanager->printVideos($videos);
                }
            } else {
                // Only one video
                $video = $videos[0];
                if ('plyr' === (string) rex_config::get('d2u_videos', 'player', 'ultimate') && rex_addon::get('plyr')->isAvailable()) {
                    $video_filename = '' !== $video->redaxo_file_lang ? $video->redaxo_file_lang : $video->redaxo_file;
                    echo '<div class="row"><div class="col-12">';
                    echo rex_plyr::outputMedia($video_filename, 'play-large,play,progress,current-time,duration,restart,volume,mute,pip,fullscreen', rex_url::media($video->picture));
                    echo '<script src="'. rex_url::base('assets/addons/plyr/plyr_init.js') .'"></script>';
                    echo '</div></div>';
                } else {
                    $videomanager = new Videomanager();
                    $videomanager->printVideo($video);
                }
                echo $video->getLDJSONScript();
            }
            echo '</div>';
        } // END Video
    }

    // Alternative machines
    if (count($machine->alternative_machine_ids) > 0) {
        $alternative_machines = [];
        foreach ($machine->alternative_machine_ids as $alternative_machine_id) {
            $alternative_machine = new Machine($alternative_machine_id, $machine->clang_id);
            if ('online' === $alternative_machine->online_status && 'delete' !== $alternative_machine->translation_needs_update) {
                $alternative_machines[] = $alternative_machine;
            }
        }
        if (count($alternative_machines) > 0) {
            echo '<div class="col-12 col-md-6">';
            echo '<h3>'. $tag_open .'d2u_machinery_alternative_machines'. $tag_close .'</h3>';
            foreach ($alternative_machines as $alternative_machine) {
                echo '<a href="'. $alternative_machine->getUrl() .'"><div class="downloads">'. ('' === $alternative_machine->lang_name ? $alternative_machine->name : $alternative_machine->lang_name) .'</div></a>';
            }
            echo '</div>';
        }
    }

    // Downloads
    $pdfs = array_unique(array_merge($machine->pdfs, $machine->category instanceof Category ? $machine->category->pdfs : []));
    if (count($pdfs) > 0) {
        echo '<div class="col-12 col-md-6">';
        echo '<h3>'. $tag_open .'d2u_machinery_downloads'. $tag_close .'</h3>';
        foreach ($pdfs as $pdf) {
            $media = rex_media::get($pdf);

            // Check permissions
            $has_permission = true;
            if (rex_plugin::get('ycom', 'media_auth')->isAvailable() && $media instanceof rex_media) {
                $has_permission = rex_ycom_media_auth::checkPerm(rex_media_manager::create('', $pdf));
            }
            if ($has_permission) {
                echo '<a href="'. rex_url::media($pdf) .'" target="_blank"><div class="downloads"><span class="fa-icon fa-file-pdf-o"></span> '. ($media instanceof rex_media && '' !== trim($media->getTitle()) ? $media->getTitle() : $pdf) .'</div></a>';
            }
        }
        echo '</div>';
    }

    // Software
    if ($machine->article_id_software > 0) {
        echo '<div class="col-12 col-md-6">';
        echo '<h3>'. $tag_open .'d2u_machinery_software'. $tag_close .'</h3>';
        $article = rex_article::get($machine->article_id_software, $machine->clang_id);
        if ($article instanceof rex_article) {
            echo '<a href="'. rex_getUrl($machine->article_id_software, $machine->clang_id).'"><div class="downloads">'. $article->getValue('name') .'</div></a>';
        }
        echo '</div>';
    }

    // Service
    if ($machine->article_id_service > 0) {
        echo '<div class="col-12 col-md-6">';
        echo '<h3>'. $tag_open .'d2u_machinery_service'. $tag_close .'</h3>';
        $article = rex_article::get($machine->article_id_service, $machine->clang_id);
        if ($article instanceof rex_article) {
            echo '<a href="'. rex_getUrl($machine->article_id_service, $machine->clang_id).'"><div class="downloads">'. $article->getValue('name') .'</div></a>';
        }
        echo '</div>';
    }

    // References
    if (count($machine->article_ids_references) > 0) {
        echo '<div class="col-12 col-md-6">';
        echo '<h3>'. $tag_open .'d2u_machinery_references'. $tag_close .'</h3>';
        foreach ($machine->article_ids_references as $article_id_reference) {
            $article = rex_article::get($article_id_reference, $machine->clang_id);
            if ($article instanceof rex_article) {
                echo '<a href="'. rex_getUrl($article_id_reference, $machine->clang_id) .'"><div class="downloads">'. $article->getValue('name') .'</div></a>';
            }
        }
        echo '</ul>';
        echo '<p>&nbsp;</p>';
        echo '</div>';
    }
    echo '</div>'; // END class="row"
    echo '</div>'; // END tab overview

    // Agitators
    if ($machine->agitator_type_id > 0 && $machine->category instanceof Category && 'show' === $machine->category->show_agitators) {
        echo '<div id="tab_agitator" class="tab-pane fade machine-tab">';
        $agitator_type = new AgitatorType($machine->agitator_type_id, $machine->clang_id);
        echo '<div class="row">';
        echo '<div class="col-12"><h3>'. $agitator_type->name .'</h3><br><br></div>';
        echo '</div>';
        $agitators = $agitator_type->getAgitators();
        foreach ($agitators as $agitator) {
            echo '<div class="row">';
            echo '<div class="col-12 col-md-6 col-lg-2">';
            if ('' !== $agitator->pic) {
                echo '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $agitator->pic .'" '
                    .'data-toggle="lightbox_agitator" data-gallery="example-galleryagitator" data-title="'. $agitator->name .'">';
                echo '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='.
                        $agitator->pic .'" alt='. $agitator->name .' class="featurepic">';
                echo '</a>';
            }
            echo '</div>';

            echo '<div class="col-12 col-md-6 col-lg-10">';
            echo '<div class="block-box">';
            echo '<p><b>'. $agitator->name .'</b></p>';
            echo d2u_addon_frontend_helper::prepareEditorField($agitator->description);
            echo '</div>';
            echo '</div>';

            echo '<div class="col-12"><div class="deviderline"></div></div>';
            echo '</div>';
        }
        echo '<script>';
        echo "$(document).on('click', '[data-toggle=\"lightbox_agitator\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
        echo '</script>';
        echo '</div>';
    }

    // Features
    if (count($machine->feature_ids) > 0) {
        echo '<div id="tab_features" class="tab-pane fade machine-tab">';
        $features = $machine->getFeatures();
        foreach ($features as $feature) {
            echo '<div class="row">';
            echo '<div class="col-12 col-sm-4 col-md-3 col-lg-2">';
            if ('' !== $feature->pic) {
                echo '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $feature->pic .'" '
                    .'data-toggle="lightbox_features" data-gallery="example-galleryfeatures" data-title="'. $feature->name.'">';
                echo '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='. $feature->pic .'" class="img-fluid featurepic"'
                    .' alt="'. $feature->name .'" title="'. $feature->name .'">';
                echo '</a>';
            }
            echo '</div>';

            echo '<div class="col-12 col-sm-8 col-md-9 col-lg-10">';
            echo '<div class="block-box">';
            echo '<p><b>'. $feature->name .'</b></p>';
            echo d2u_addon_frontend_helper::prepareEditorField($feature->description);
            echo '</div>';
            echo '</div>';

            echo '<div class="col-12"><div class="deviderline"></div></div>';
            echo '</div>';
        }
        echo '<script>';
        echo "$(document).on('click', '[data-toggle=\"lightbox_features\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
        echo '</script>';
        echo '</div>';
    }

    // Technical data
    $tech_datas = $machine->getTechnicalData();
    if (count($tech_datas) > 0) {
        echo '<div id="tab_tech_data" class="tab-pane fade machine-tab">';
        echo '<div class="row">';
        echo '<div class="col-12">';
        echo '<table class="techdata">';
        foreach ($tech_datas as $tech_data) {
            /** @var array<string> $tech_data */
            echo '<tr>';
            echo '<td class="description">'. $tech_data['description'] .'</td>';
            echo '<td class="unit">'. $tech_data['unit'] .'</td>';
            echo '<td class="value">'. $tech_data['value'] .'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    // Usage Areas
    if (rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable() && 'show' === (string) $d2u_machinery->getConfig('show_machine_usage_areas', 'hide')) {
        echo '<div id="tab_usage_areas" class="tab-pane fade machine-tab">';
        echo '<div class="row">';
        echo '<div class="col-12">';

        $usage_areas = UsageArea::getAll(rex_clang::getCurrentId(), $machine->category instanceof Category ? $machine->category->category_id : 0);
        echo '<table class="matrix">';
        echo '<thead><tr>';
        echo '<td></td>';
        echo '<td valign="bottom" align="center">';
        echo '<a href="'. $machine->getUrl() .'"><div class="comparison-header">';
        if (count($machine->pics) > 0 && '' !== $machine->pics[0]) {
            echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'>';
        }
        echo '<br><b>'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
        echo '</tr></thead>';
        echo '<tbody>';
        foreach ($usage_areas as $usage_area) {
            echo '<tr>';
            echo '<td class="inactive-cell usage-area-name">'. $usage_area->name .'</td>';
            if (in_array($usage_area->usage_area_id, $machine->usage_area_ids, true)) {
                echo '<td class="active-cell"></td>';
            } else {
                echo '<td class="inactive-cell"></td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    // Delivery sets
    if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable() &&
            (strlen($machine->delivery_set_basic) > 5 || strlen($machine->delivery_set_conversion) > 5 || strlen($machine->delivery_set_full) > 5)) {
        echo '<div id="tab_delivery_set" class="tab-pane fade machine-tab">';
        echo '<div class="row">';
        // Number of available sets
        $number_sets = 0;
        if (strlen($machine->delivery_set_basic) > 5) {
            ++$number_sets;
        }
        if (strlen($machine->delivery_set_conversion) > 5) {
            ++$number_sets;
        }
        if (strlen($machine->delivery_set_full) > 5) {
            ++$number_sets;
        }
        if (count($machine->pictures_delivery_set) > 0) {
            ++$number_sets;
        }

        $class = '';
        if (2 === $number_sets) {
            $class = 'col-sm-6';
        }
        if (3 === $number_sets) {
            $class = 'col-sm-6 col-md-4';
        }
        if (4 === $number_sets) {
            $class = 'col-sm-6';
        }

        if (strlen($machine->delivery_set_basic) > 5) {
            echo '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">'. d2u_addon_frontend_helper::prepareEditorField($machine->delivery_set_basic) .'</div></div></div>';
        }
        if (strlen($machine->delivery_set_conversion) > 5) {
            echo '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">'. d2u_addon_frontend_helper::prepareEditorField($machine->delivery_set_conversion) .'</div></div></div>';
        }
        if (strlen($machine->delivery_set_full) > 5) {
            echo '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">'. d2u_addon_frontend_helper::prepareEditorField($machine->delivery_set_full) .'</div></div></div>';
        }
        if (count($machine->pictures_delivery_set) > 0) {
            echo '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">';
            if (1 === count($machine->pictures_delivery_set)) {
                echo '<img src="index.php?rex_media_type=d2u_machinery_construction_equipment_delivery_set_slider&rex_media_file='.
                    $machine->pictures_delivery_set[0] .'" alt="'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'" style="max-width:100%;">';
            } else {
                // Slider
                echo '<div id="deliverysetCarousel" class="carousel carousel-fade slide" data-ride="carousel" data-pause="hover">';
                // Slider indicators

                echo '<ol class="carousel-indicators">';
                for ($i = 0; $i < count($machine->pictures_delivery_set); ++$i) {
                    echo '<li data-target="#deliverysetCarousel" data-slide-to="'. $i .'"';
                    if (0 === $i) {
                        echo 'class="active"';
                    }
                    echo '></li>';
                }
                echo '</ol>';

                // Wrapper for slides
                echo '<div class="carousel-inner">';
                for ($i = 0; $i < count($machine->pictures_delivery_set); ++$i) {
                    echo '<div class="carousel-item';
                    if (0 === $i) {
                        echo ' active';
                    }
                    echo '">';
                    echo '<img class="d-block w-100" src="index.php?rex_media_type=d2u_machinery_construction_equipment_delivery_set_slider&rex_media_file='.
                    $machine->pictures_delivery_set[$i] .'" alt="'. ('' === $machine->lang_name ? $machine->name : $machine->lang_name) .'">';
                    echo '</div>';
                }
                echo '</div>';

                // Left and right controls
                echo '<a class="carousel-control-prev" href="#deliverysetCarousel" role="button" data-slide="prev">';
                echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                echo '<span class="sr-only">Previous</span>';
                echo '</a>';
                echo '<a class="carousel-control-next" href="#deliverysetCarousel" role="button" data-slide="next">';
                echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                echo '<span class="sr-only">Next</span>';
                echo '</a>';

                echo '</div>';
            }
            echo '</div></div></div>';
        }
        echo '</div>';
        echo '</div>';
    }

    // Service options
    if (rex_plugin::get('d2u_machinery', 'service_options')->isAvailable() && count($machine->service_option_ids) > 0) {
        echo '<div id="tab_service_options" class="tab-pane fade machine-tab">';
        $service_options = $machine->getServiceOptions();
        foreach ($service_options as $service_option) {
            echo '<div class="row">';
            echo '<div class="col-12 col-md-6 col-lg-2">';
            if ('' !== $service_option->picture) {
                echo '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $service_option->picture .'" '
                    .'data-toggle="lightbox_service" data-gallery="example-galleryservice" data-title="'. $service_option->name.'">';
                echo '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='.
                        $service_option->picture .'" alt='. $service_option->name .' class="featurepic">';
                echo '</a>';
            }
            echo '</div>';
            echo '<div class="col-12 col-md-6 col-lg-10">';
            echo '<div class="block-box">';
            echo '<p><b>'. $service_option->name .'</b></p>';
            echo d2u_addon_frontend_helper::prepareEditorField($service_option->description);
            echo '</div>';
            echo '</div>';

            echo '<div class="col-12"><div class="deviderline"></div></div>';
            echo '</div>';
        }
        echo '<script>';
        echo "$(document).on('click', '[data-toggle=\"lightbox_service\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
        echo '</script>';
        echo '</div>';
    }

    // Equipment
    if (rex_plugin::get('d2u_machinery', 'equipment')->isAvailable() && count($machine->equipment_ids) > 0) {
        echo '<div id="tab_equipment" class="tab-pane fade machine-tab">';

        $equipments = [];
        $equipment_groups = [];
        foreach ($machine->equipment_ids as $equipment_id) {
            $equipment = new Equipment($equipment_id, rex_clang::getCurrentId());
            if ($equipment->group instanceof EquipmentGroup && !array_key_exists($equipment->group->priority, $equipment_groups)) {
                $equipment_groups[$equipment->group->priority] = $equipment->group;
                $equipments[$equipment->group->group_id] = [];
            }
            if ($equipment->group instanceof EquipmentGroup && array_key_exists($equipment->group->group_id, $equipments)) {
                $equipments[$equipment->group->group_id][$equipment->name] = $equipment;
            }
        }
        ksort($equipment_groups);
        foreach ($equipment_groups as $equipment_group) {
            echo '<div class="row">';
            echo '<div class="col-12 col-sm-4 col-md-3 col-lg-2">';
            if ('' !== $equipment_group->picture) {
                echo '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $equipment_group->picture .'" '
                    .'data-toggle="lightbox_equipment" data-gallery="example-galleryequipment" data-title="'. $equipment_group->name.'">';
                echo '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='. $equipment_group->picture .'" class="img-fluid featurepic"'
                    .' alt="'. $equipment_group->name .'" title="'. $equipment_group->name .'">';
                echo '</a>';
            }
            echo '</div>';

            echo '<div class="col-12 col-sm-8 col-md-9 col-lg-10">';
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<div class="block-box">';
            echo '<h3>'. $equipment_group->name .'</h3>';
            echo '<p>'. d2u_addon_frontend_helper::prepareEditorField($equipment_group->description) .'</p>';
            echo '</div>';
            echo '</div>';
            $current_equipments = $equipments[$equipment_group->group_id];
            ksort($current_equipments);
            foreach ($current_equipments as $current_equipment) {
                echo '<div class="col-12">';
                echo '<div class="block-box"><b>'. $current_equipment->name .'</b>';
                if ('' !== $current_equipment->article_number) {
                    echo ' ('. $tag_open .'d2u_machinery_equipment_artno'. $tag_close .' '. $current_equipment->article_number .')';
                }
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';

            echo '<div class="col-12"><div class="deviderline"></div></div>';
            echo '</div>';
        }
        echo '<script>';
        echo "$(document).on('click', '[data-toggle=\"lightbox_equipment\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
        echo '</script>';
        echo '</div>';
    }

    // Contact Request form
    $d2u_machinery = rex_addon::get('d2u_machinery');
    echo '<div id="tab_request" class="tab-pane fade machine-tab">';
    echo '<div class="row">';
    echo '<div class="col-12 col-md-8">';
    $form_data = 'hidden|machine_name|'. $machine->name .'|REQUEST

			html||<h3> '. $tag_open .'d2u_machinery_request'. $tag_close .': '. $machine->name .'</h3><br>
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
			validate|customfunction|validate_timer|d2u_addon_frontend_helper::yform_validate_timer|5|'. $tag_open .'d2u_helper_module_form_validate_spambots'. $tag_close .'|

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

			action|tpl2email|d2u_machinery_machine_request|'. ($machine->contact instanceof \D2U_Machinery\Contact ? $machine->contact->email : $d2u_machinery->getConfig('request_form_email'));

    $yform = new rex_yform();
    $yform->setFormData(trim($form_data));
    $yform->setObjectparams('csrf_protection', false);
    $yform->setObjectparams('form_action', $machine->getUrl());
    $yform->setObjectparams('form_anchor', 'tab_request');
    $yform->setObjectparams('form_name', 'd2u_machinery_module_90_1_'. random_int(1, 100));
    $yform->setObjectparams('Error-occured', $tag_open .'d2u_helper_module_form_validate_title'. $tag_close);
    $yform->setObjectparams('real_field_names', true);

    // action - showtext
    $yform->setActionField('showtext', [$tag_open .'d2u_helper_module_form_thanks'. $tag_close]);

    echo $yform->getForm();

    // Google Analytics Event
    if ('true' === (string) rex_config::get('d2u_machinery', 'google_analytics_activate', 'false') &&
            '' !== rex_config::get('d2u_machinery', 'analytics_event_category', '') &&
            '' !== rex_config::get('d2u_machinery', 'analytics_event_action', '') &&
            false === rex_request('search_it_build_index', 'int', false)) {
        echo '<script>'. PHP_EOL;
        echo '	$(\'button[type="submit"]\').click(function(e) {'. PHP_EOL;
        echo "		ga('send', 'event', {". PHP_EOL;
        echo "			eventCategory: '". rex_config::get('d2u_machinery', 'analytics_event_category') ."',". PHP_EOL;
        echo "			eventAction:  '". rex_config::get('d2u_machinery', 'analytics_event_action') ."',". PHP_EOL;
        echo "			eventLabel: '". $machine->name ."',". PHP_EOL;
        echo "			transport: 'beacon'". PHP_EOL;
        echo '		});'. PHP_EOL;
        echo '	});'. PHP_EOL;
        echo '</script>'. PHP_EOL;
    }

    echo '</div>';
    echo '</div>';
    echo '</div>'; // END tab_request

    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-12">';
    echo '<div class="back-list">';
    if ($machine->category instanceof Category) {
        echo '<a href="'. $machine->category->getUrl() .'">';
        echo '<span class="fa-icon fa-back"></span>&nbsp;'. $tag_open .'d2u_machinery_back_machine_list'. $tag_close;
        echo '</a>';
    }
    echo '</div>';
    echo '</div>';
} elseif (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || ('industry_sector_id' === $url_namespace && $url_id > 0))) {
    $industry_sector_id = (int) filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
    if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
        $industry_sector_id = $url_id;
    }
    $industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
    // Print machine list
    echo '<div class="col-12">';
    echo '<div class="row" data-match-height>';
    print_machines($industry_sector->getMachines(), $industry_sector->name);
    echo '</div>';
    echo '</div>';
} else {
    // Categories
    echo '<div class="col-12">';
    echo '<div class="row" data-match-height>';
    print_categories(Category::getAll(rex_clang::getCurrentId()));
    echo '</div>';
    echo '</div>';

    echo '<div class="col-12 abstand"></div>';

    if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
        // Industry sectors
        echo '<div class="col-12">';
        echo '<div class="row" data-match-height>';
        print_industry_sectors();
        echo '</div>';
        echo '</div>';
    }
}

print_consulation_hint();
?>
    </div>
</div>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
</script>