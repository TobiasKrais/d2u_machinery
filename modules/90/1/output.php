<?php
if(!function_exists('print_categories')) {
	/**
	 * Prints category list.
	 * @param Category[] $categories Array with category objects.
	 */
	function print_categories($categories) {
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");
		$counter = 0;
		foreach($categories as $category) {
			// Only use used categories
			if(count($category->getMachines()) > 0) {
				print '<div class="col-sm-6 col-md-4 col-lg-3 abstand">';
				print '<a href="'. $category->getURL() .'" class="bluebox">';
				print '<div class="box" data-height-watch>';
				if($category->pic != "" || $category->pic_lang != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						($category->pic_lang != "" ? $category->pic_lang : $category->pic) .'" alt="'. $category->name .'">';
				}
				else {
					print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
				}
				print '<div>'. $category->name .'</div>';
				print '</div>';
				print '</a>';
				print '</div>';
				$counter++;
			}
		}
		
		// If no categories are used
		if($counter == 0) {
			print '<div class="col-12 abstand">';
			print '<p>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_no_machines'. $sprog->getConfig('wildcard_close_tag') .'</p>';
			print '</div>';
		}
	}
}

if(!function_exists('print_consulation_hint')) {
	/**
	 * Prints consulation hint.
	 */
	function print_consulation_hint() {
		$d2u_machinery = rex_addon::get("d2u_machinery");
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");

		print '<div class="col-12">';
		print '<div class="consultation">';
		print '<a href="'. rex_getUrl($d2u_machinery->getConfig('consultation_article_id')) .'">';
		print '<div class="row abstand">';

		print '<div class="col-12 col-md-4 col-lg-3">';
		if($d2u_machinery->getConfig('consultation_pic') != "") {
			print '<img src="'. rex_url::media($d2u_machinery->getConfig('consultation_pic')) .'" alt="">';
		}
		print '</div>';

		print '<div class="col-12 col-md-8 col-lg-9">';
		print '<p>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_consultation_hint'. $sprog->getConfig('wildcard_close_tag') .'</p>';
		if($d2u_machinery->hasConfig("contact_phone") && $d2u_machinery->getConfig("contact_phone") != "") {
			print '<h3>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_form_phone'. $sprog->getConfig('wildcard_close_tag') .' '. $d2u_machinery->getConfig("contact_phone") .'</h3>';
		}
		print '</div>';

		print '</div>';
		print '</a>';
		print '</div>';
		print '</div>';
	}
}

if(!function_exists('print_industry_sectors')) {
	/**
	 * Prints industry sector list.
	 */
	function print_industry_sectors() {
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");

		print '<div class="col-12 abstand">';
		print '<h1>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_industry_sectors'. $sprog->getConfig('wildcard_close_tag') .'</h1>';
		print '</div>';

		foreach (IndustrySector::getAll(rex_clang::getCurrentId()) as $industry_sector) {
			if($industry_sector->online_status == "online" && count($industry_sector->getMachines()) > 0) {
				print '<div class="col-sm-6 col-md-4 col-lg-3 abstand">';
				print '<a href="'. $industry_sector->getURL() .'" class="bluebox">';
				print '<div class="box" data-height-watch>';
				if($industry_sector->pic != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$industry_sector->pic .'" alt="'. $industry_sector->name .'">';
				}
				else {
					print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
				}
				print '<div>'. $industry_sector->name .'</div>';
				print '</div>';
				print '</a>';
				print '</div>';
			}
		}
	}
}

if(!function_exists('print_machines')) {
	/**
	 * Prints machine list.
	 * @param Machine[] $machines Machines.
	 * @param string $title Title for the list
	 */
	function print_machines($machines, $title) {
		$d2u_machinery = rex_addon::get("d2u_machinery");
		if($title != "") {
			print '<div class="col-12 abstand">';
			print '<h1>'. $title.'</h1>';
			print '</div>';
		}

		$counter = 0;
		foreach($machines as $machine) {
			if($machine->online_status == 'online') {
				if($d2u_machinery->hasConfig('show_teaser') && $d2u_machinery->getConfig('show_teaser') == 'show') {
					print '<div class="col-12 col-md-6 col-lg-4 abstand" data-height-watch>';
				}
				else {
					print '<div class="col-sm-6 col-md-4 col-lg-3 abstand" data-height-watch>';
				}
				print '<a href="'. $machine->getURL() .'" class="bluebox">';
				print '<div class="box" data-height-watch>';
				if(count($machine->pics) > 0 && $machine->pics[0] != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$machine->pics[0] .'" alt="'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'">';
				}
				else {
					print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
				}
				print '<div><b>'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'</b></div>';
				if($d2u_machinery->hasConfig('show_teaser') && $d2u_machinery->getConfig('show_teaser') == 'show') {
					print '<div class="teaser">'. nl2br($machine->teaser) .'</div>';
				}
				print '</div>';
				print '</a>';
				print '</div>';
				$counter++;
			}
		}
		
		// Directly forward if only one machine is available
		if($counter == 1) {
			foreach($machines as $machine) {
				if($machine->online_status == 'online') {
					header("Location: ". $machine->getURL(FALSE));
					exit;
				}
			}
		}
	}
}

// Get placeholder wildcard tags and other presets
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');
$d2u_machinery = rex_addon::get("d2u_machinery");

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "category_id" && $url_id > 0) {
	$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
	if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
		$category_id = $url_id;
	}
	$category = new Category($category_id, rex_clang::getCurrentId());
	$child_categories = $category->getChildren();
	if(count($child_categories) > 0) {
		// Print child categories
		print '<div class="col-12 abstand">';
		print '<h1>'. $category->name .'</h1>';
		print '</div>';
		print_categories($child_categories);
	}
	else {
		$machines = $category->getMachines(TRUE);
		print '<div class="col-12">';
		print '<div class="tab-content">';

		// Overview
		print '<div id="tab_overview" class="tab-pane fade show active machine-tab">';
		print '<div class="row" data-match-height>';
		print_machines($machines, "");
		print '</div>';
		print '</div>';

		// Usage Areas
		if(rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable() && $d2u_machinery->getConfig("show_categories_usage_areas", "hide") == "show") {
			print '<div id="tab_usage_areas" class="tab-pane fade machine-tab">';
			print '<div class="row">';
			print '<div class="col-12">';
			if($d2u_machinery->hasConfig("usage_area_header") && $d2u_machinery->getConfig("usage_area_header") == "usage") {
				$usage_areas = UsageArea::getAll(rex_clang::getCurrentId(), $category_id);
				print '<table class="matrix">';
				print '<thead><tr><td></td>';
				foreach($usage_areas as $usage_area) {
					print '<td class="inactive-cell">';
					print '<div class="vertical usage-area-name">'. $usage_area->name .'</div></td>';
				}
				print '</tr></thead>';
				print '<tbody>';
				foreach($machines as $machine) {
					print '<tr><td>';
					print '<a href="'. $machine->getURL() .'"><div class="comparison-header">';
					if(count($machine->pics) > 0 && $machine->pics[0] != "") {
						print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'>';
					}
					else {
						print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
					}
					print '<br><b>'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
					foreach($usage_areas as $usage_area) {
						if(in_array($usage_area->usage_area_id, $machine->usage_area_ids)) {
							print '<td class="active-cell"></td>';
						}
						else {
							print '<td class="inactive-cell"></td>';
						}
					}
					print '</tr>';
				}
				print '</tbody>';
				print '</table>';
			}
			else {
				$usage_area_matrix = $category->getUsageAreaMatrix();
				print '<table class="matrix">';
				print '<thead><tr><td></td>';
				foreach($machines as $machine) {
					print '<td valign="bottom" align="center">';
					print '<a href="'. $machine->getURL() .'"><div class="comparison-header">';
					if(count($machine->pics) > 0 && $machine->pics[0] != "") {
						print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'>';
					}
					else {
						print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
					}
					print '<br><b>'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
				}
				print '</tr></thead>';
				print '<tbody>';
				foreach($usage_area_matrix as $key => $values) {
					print '<tr><td class="inactive-cell usage-area-name">'. $key .'</td>';
					foreach($machines as $machine) {
						if(in_array($machine->machine_id, $values)) {
							print '<td class="active-cell"></td>';
						}
						else {
							print '<td class="inactive-cell"></td>';
						}
					}
					print '</tr>';
				}
				print '</tbody>';
				print '</table>';
			}
			print '</div>';
			print '</div>';
			print '</div>';
		}

		// Technical data
		print '<div id="tab_tech_data" class="tab-pane fade machine-tab">';
		print '<div class="row">';
		print '<div class="col-12">';
		$tech_data_matrix = $category->getTechDataMatrix();
		print '<table class="matrix">';
		print '<thead><tr><td></td><td></td>';
		foreach($machines as $machine) {
			print '<td valign="bottom" align="center">';
			print '<a href="'. $machine->getURL() .'"><div class="comparison-header">';
			if(count($machine->pics) > 0 && $machine->pics[0] != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'>';
			}
			else {
				print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
			}
			print '<br><b>'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
		}
		print '</tr></thead>';
		print '<tbody>';
		foreach($tech_data_matrix as $wildcard => $values) {
			print '<tr>';
			print '<td class="inactive-cell"><b>'. $wildcard .'</b></td>';
			print '<td class="inactive-cell unit"><b>'. $values['unit'] .'</b></td>';
			foreach($machines as $machine) {
				print '<td class="inactive-cell">'. $values['machine_ids'][$machine->machine_id] .'</td>';
			}
			print '</tr>';
		}
		print '</tbody>';
		print '</table>';
		print '</div>';
		print '</div>';
		print '</div>';

		print '</div>';
		print '</div>';
	}
}
else if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || ($url_namespace === "machine_id" && $url_id > 0)) {
	// Print machine
	$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
	if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
		$machine_id = $url_id;
	}
	$machine = new Machine($machine_id, rex_clang::getCurrentId());
	// Redirect if machine is not online
	if($machine->online_status != "online") {
		\rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
	}

	print '<div class="col-12">';
	print '<div class="tab-content">';
	
	// Overview
	print '<div id="tab_overview" class="tab-pane fade show active machine-tab">';
	print '<div class="row">';

	// Text
	print '<div class="col-12 col-md-6">';
	print d2u_addon_frontend_helper::prepareEditorField($machine->description);
	print '<p>&nbsp;</p>';
	print '</div>';

	if(count($machine->pics) > 0) {
		// Picture(s)
		print '<div class="col-12 col-md-6">';
		if(count($machine->pics) == 1) {
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$machine->pics[0] .'" alt="'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'" style="max-width:100%;">';
		}
		else {
			// Slider
			print '<div id="machineCarousel" class="carousel carousel-fade slide" data-ride="carousel" data-pause="hover">';
			// Slider indicators

			print '<ol class="carousel-indicators">';
			for($i = 0; $i < count($machine->pics); $i++) {
				print '<li data-target="#machineCarousel" data-slide-to="'. $i .'"';
				if($i == 0) {
					print 'class="active"';
				}
				print '></li>';
			}
			print '</ol>';

			// Wrapper for slides
			print '<div class="carousel-inner">';
			for($i = 0; $i < count($machine->pics); $i++) {
				print '<div class="carousel-item';
				if($i == 0) {
					print ' active';
				}
				print '">';
				print '<img class="d-block w-100" src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$machine->pics[$i] .'" alt="'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'">';
				print '</div>';
			}
			print '</div>';

			// Left and right controls
			print '<a class="carousel-control-prev" href="#machineCarousel" role="button" data-slide="prev">';
			print '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
			print '<span class="sr-only">Previous</span>';
			print '</a>';
			print '<a class="carousel-control-next" href="#machineCarousel" role="button" data-slide="next">';
			print '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
			print '<span class="sr-only">Next</span>';
			print '</a>';

			print '</div>';
		}
		print '</div>';
	}

	// Video
	if(\rex_addon::get('d2u_videos')->isAvailable()) {
		$videos = array_merge($machine->videos, $machine->category->videos);
		if(count($videos) > 0) {
			print '<div class="col-12'. (count($videos) > 1 ? '' : ' col-lg-6') .'" id="videoplayer">';
			print '<h3>'. $tag_open .'d2u_machinery_video'. $tag_close .'</h3>';
			$videomanager = new Videomanager();
			$videomanager->printVideos($videos);
			print '</div>';
		} // END Video
	}

	// Alternative machines
	if(count($machine->alternative_machine_ids) > 0) {
		$alternative_machines = [];
		foreach($machine->alternative_machine_ids as $alternative_machine_id) {
			$alternative_machine = new Machine($alternative_machine_id, $machine->clang_id);
			if($alternative_machine->online_status == 'online' && $alternative_machine->translation_needs_update != "delete") {
				$alternative_machines[] = $alternative_machine;
			}
		}
		if(count($alternative_machines) > 0) {
			print '<div class="col-12 col-md-6">';
			print '<h3>'. $tag_open .'d2u_machinery_alternative_machines'. $tag_close .'</h3>';
			foreach($alternative_machines as $alternative_machine) {
				print '<a href="'. $alternative_machine->getURL() .'"><div class="downloads">'. ($alternative_machine->lang_name == "" ? $alternative_machine->name : $alternative_machine->lang_name) .'</div></a>';
			}
			print '</div>';
		}
	}
		
	// Downloads
	$pdfs = array_unique(array_merge($machine->pdfs, $machine->category->pdfs));
	if(is_array($pdfs) && count($pdfs) > 0) {
		print '<div class="col-12 col-md-6">';
		print '<h3>'. $tag_open .'d2u_machinery_downloads'. $tag_close .'</h3>';
		foreach($pdfs as $pdf) {
			$media = rex_media::get($pdf);
			
			// Check permissions
			$has_permission = TRUE;
			if(rex_plugin::get('ycom', 'media_auth')->isAvailable() && $media instanceof rex_media) {
				$has_permission = rex_ycom_media_auth::checkPerm(rex_media_manager::create("", $pdf));
			}
			if($has_permission) {
				print '<a href="'. rex_url::media($pdf) .'" target="_blank"><div class="downloads"><span class="fa-icon fa-file-pdf-o"></span> '. ($media instanceof rex_media && trim($media->getTitle()) != "" ? $media->getTitle() : $pdf) .'</div></a>';
			}
		}
		print '</div>';
	}

	// Software
	if($machine->article_id_software > 0) {
		print '<div class="col-12 col-md-6">';
		print '<h3>'. $tag_open .'d2u_machinery_software'. $tag_close .'</h3>';
		$article = rex_article::get($machine->article_id_software, $machine->clang_id);
		if($article instanceof rex_article) {
			print '<a href="'. rex_getUrl($machine->article_id_software, $machine->clang_id).'"><div class="downloads">'. $article->getValue('name') .'</div></a>';
		}
		print '</div>';
	}
		
	// Service
	if($machine->article_id_service > 0) {
		print '<div class="col-12 col-md-6">';
		print '<h3>'. $tag_open .'d2u_machinery_service'. $tag_close .'</h3>';
		$article = rex_article::get($machine->article_id_service, $machine->clang_id);
		if($article instanceof rex_article) {
			print '<a href="'. rex_getUrl($machine->article_id_service, $machine->clang_id).'"><div class="downloads">'. $article->getValue('name') .'</div></a>';
		}
		print '</div>';
	}
		
	// References
	if(count($machine->article_ids_references) > 0) {
		print '<div class="col-12 col-md-6">';
		print '<h3>'. $tag_open .'d2u_machinery_references'. $tag_close .'</h3>';
		foreach($machine->article_ids_references as $article_id_reference) {
			$article = rex_article::get($article_id_reference, $machine->clang_id);
			if($article instanceof rex_article) {
				print '<a href="'. rex_getUrl($article_id_reference, $machine->clang_id) .'"><div class="downloads">'. $article->getValue('name') .'</div></a>';
			}
		}
		print '</ul>';
		print '<p>&nbsp;</p>';
		print '</div>';
	}
	print '</div>'; // END class="row"
	print '</div>'; // END tab overview
	
	// Agitators
	if($machine->agitator_type_id > 0 && $machine->category->show_agitators) {
		print '<div id="tab_agitator" class="tab-pane fade machine-tab">';
		$agitator_type = new AgitatorType($machine->agitator_type_id, $machine->clang_id);
		print '<div class="row">';
		print '<div class="col-12"><h3>'. $agitator_type->name .'</h3><br><br></div>';
		print '</div>';
		$agitators = $agitator_type->getAgitators();
		foreach($agitators as $agitator) {
			print '<div class="row">';
			print '<div class="col-12 col-md-6 col-lg-2">';
			if($agitator->pic != "") {
				print '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $agitator->pic .'" '
					.'data-toggle="lightbox_agitator" data-gallery="example-galleryagitator" data-title="'. $agitator->name .'">';
				print '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='.
						$agitator->pic .'" alt='. $agitator->name .' class="featurepic">';
				print '</a>';
			}
			print '</div>';

			print '<div class="col-12 col-md-6 col-lg-10">';
			print '<div class="block-box">';
			print '<p><b>'. $agitator->name .'</b></p>';
			print d2u_addon_frontend_helper::prepareEditorField($agitator->description);
			print '</div>';
			print '</div>';

			print '<div class="col-12"><div class="deviderline"></div></div>';
			print '</div>';
		}
		print '<script>';
		print "$(document).on('click', '[data-toggle=\"lightbox_agitator\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
		print '</script>';
		print '</div>';
	}

	// Features
	if(count($machine->feature_ids) > 0) {
		print '<div id="tab_features" class="tab-pane fade machine-tab">';
		$features = $machine->getFeatures();
		foreach($features as $feature) {
			print '<div class="row">';
			print '<div class="col-12 col-sm-4 col-md-3 col-lg-2">';
			if($feature->pic != "") {
				print '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $feature->pic .'" '
					.'data-toggle="lightbox_features" data-gallery="example-galleryfeatures" data-title="'. $feature->name.'">';
                print '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='. $feature->pic .'" class="img-fluid featurepic"'
					.' alt="'. $feature->name .'" title="'. $feature->name .'">';
				print '</a>';
			}
			print '</div>';

			print '<div class="col-12 col-sm-8 col-md-9 col-lg-10">';
			print '<div class="block-box">';
			print '<p><b>'. $feature->name .'</b></p>';
			print d2u_addon_frontend_helper::prepareEditorField($feature->description);
			print '</div>';
			print '</div>';

			print '<div class="col-12"><div class="deviderline"></div></div>';
			print '</div>';
		}
		print '<script>';
		print "$(document).on('click', '[data-toggle=\"lightbox_features\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
		print '</script>';
		print '</div>';
	}

	// Technical data
	$tech_datas = $machine->getTechnicalData();
	if(count($tech_datas) > 0) {
		print '<div id="tab_tech_data" class="tab-pane fade machine-tab">';
		print '<div class="row">';
		print '<div class="col-12">';
		print '<table class="techdata">';
		foreach ($tech_datas as $tech_data) {
			print '<tr>';
			print '<td class="description">'. $tech_data["description"] .'</td>';
			print '<td class="unit">'. $tech_data["unit"] .'</td>';
			print '<td class="value">'. $tech_data["value"] .'</td>';
			print '</tr>';
		}
		print '</table>';
		print '</div>';
		print '</div>';
		print '</div>';
	}
	
	// Usage Areas
	if(rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable() && $d2u_machinery->getConfig("show_machine_usage_areas", "hide") == "show") {
		print '<div id="tab_usage_areas" class="tab-pane fade machine-tab">';
		print '<div class="row">';
		print '<div class="col-12">';
	
		$usage_areas = UsageArea::getAll(rex_clang::getCurrentId(), $machine->category->category_id);
		print '<table class="matrix">';
		print '<thead><tr>';
		print '<td></td>';
		print '<td valign="bottom" align="center">';
		print '<a href="'. $machine->getURL() .'"><div class="comparison-header">';
		if(count($machine->pics) > 0 && $machine->pics[0] != "") {
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'>';
		}
		print '<br><b>'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'</b></div></a></td>';
		print '</tr></thead>';
		print '<tbody>';
		foreach($usage_areas as $usage_area) {
			print '<tr>';
			print '<td class="inactive-cell usage-area-name">'. $usage_area->name .'</td>';
			if(in_array($usage_area->usage_area_id, $machine->usage_area_ids)) {
				print '<td class="active-cell"></td>';
			}
			else {
				print '<td class="inactive-cell"></td>';
			}
			print '</tr>';
		}
		print '</tbody>';
		print '</table>';

		print '</div>';
		print '</div>';
		print '</div>';
	}

	// Delivery sets
	if(rex_plugin::get("d2u_machinery", "machine_construction_equipment_extension")->isAvailable() &&
			(strlen($machine->delivery_set_basic) > 5 || strlen($machine->delivery_set_conversion) > 5 || strlen($machine->delivery_set_full) > 5)) {
		print '<div id="tab_delivery_set" class="tab-pane fade machine-tab">';
		print '<div class="row">';
		// Number of available sets
		$number_sets = 0;
		if(strlen($machine->delivery_set_basic) > 5) {
			$number_sets++;
		}
		if(strlen($machine->delivery_set_conversion) > 5) {
			$number_sets++;
		}
		if(strlen($machine->delivery_set_full) > 5) {
			$number_sets++;
		}
		if(count($machine->pictures_delivery_set) > 0) {
			$number_sets++;
		}
		
		$class = "";
		if($number_sets == 2) {
			$class = "col-sm-6";
		}
		if($number_sets == 3) {
			$class = "col-sm-6 col-md-4";
		}
		if($number_sets == 4) {
			$class = "col-sm-6";
		}

		if(strlen($machine->delivery_set_basic) > 5) {
			print '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">'. d2u_addon_frontend_helper::prepareEditorField($machine->delivery_set_basic) .'</div></div></div>';
		}
		if(strlen($machine->delivery_set_conversion) > 5) {
			print '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">'. d2u_addon_frontend_helper::prepareEditorField($machine->delivery_set_conversion) .'</div></div></div>';
		}
		if(strlen($machine->delivery_set_full) > 5) {
			print '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">'. d2u_addon_frontend_helper::prepareEditorField($machine->delivery_set_full) .'</div></div></div>';
		}
		if(count($machine->pictures_delivery_set) > 0) {
			print '<div class="col-12 '. $class .'"><div class="deliverysets"><div class="deliverybox">';
			if(count($machine->pictures_delivery_set) == 1) {
				print '<img src="index.php?rex_media_type=d2u_machinery_construction_equipment_delivery_set_slider&rex_media_file='.
					$machine->pictures_delivery_set[0] .'" alt="'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'" style="max-width:100%;">';
			}
			else {
				// Slider
				print '<div id="deliverysetCarousel" class="carousel carousel-fade slide" data-ride="carousel" data-pause="hover">';
				// Slider indicators

				print '<ol class="carousel-indicators">';
				for($i = 0; $i < count($machine->pictures_delivery_set); $i++) {
					print '<li data-target="#deliverysetCarousel" data-slide-to="'. $i .'"';
					if($i == 0) {
						print 'class="active"';
					}
					print '></li>';
				}
				print '</ol>';

				// Wrapper for slides
				print '<div class="carousel-inner">';
				for($i = 0; $i < count($machine->pictures_delivery_set); $i++) {
					print '<div class="carousel-item';
					if($i == 0) {
						print ' active';
					}
					print '">';
					print '<img class="d-block w-100" src="index.php?rex_media_type=d2u_machinery_construction_equipment_delivery_set_slider&rex_media_file='.
					$machine->pictures_delivery_set[$i] .'" alt="'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'">';
					print '</div>';
				}
				print '</div>';

				// Left and right controls
				print '<a class="carousel-control-prev" href="#deliverysetCarousel" role="button" data-slide="prev">';
				print '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
				print '<span class="sr-only">Previous</span>';
				print '</a>';
				print '<a class="carousel-control-next" href="#deliverysetCarousel" role="button" data-slide="next">';
				print '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
				print '<span class="sr-only">Next</span>';
				print '</a>';

				print '</div>';
			}
			print '</div></div></div>';
		}
		print '</div>';
		print '</div>';
	}

	// Service options
	if(rex_plugin::get("d2u_machinery", "service_options")->isAvailable() && count($machine->service_option_ids) > 0) {
		print '<div id="tab_service_options" class="tab-pane fade machine-tab">';
		$service_options = $machine->getServiceOptions();
		foreach($service_options as $service_option) {
			print '<div class="row">';
			print '<div class="col-12 col-md-6 col-lg-2">';
			if($service_option->picture != "") {
				print '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $feature->pic .'" '
					.'data-toggle="lightbox_service" data-gallery="example-galleryservice" data-title="'. $feature->name.'">';
				print '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='.
						$service_option->picture .'" alt='. $service_option->name .' class="featurepic">';
				print '</a>';
			}
			print '</div>';
			print '<div class="col-12 col-md-6 col-lg-10">';
			print '<div class="block-box">';
			print '<p><b>'. $service_option->name .'</b></p>';
			print d2u_addon_frontend_helper::prepareEditorField($service_option->description);
			print '</div>';
			print '</div>';

			print '<div class="col-12"><div class="deviderline"></div></div>';
			print '</div>';
		}
		print '<script>';
		print "$(document).on('click', '[data-toggle=\"lightbox_service\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
		print '</script>';
		print '</div>';
	}

	// Equipment
	if(rex_plugin::get("d2u_machinery", "equipment")->isAvailable() && count($machine->equipment_ids) > 0) {
		print '<div id="tab_equipment" class="tab-pane fade machine-tab">';

		$equipments = [];
		$equipment_groups = [];
		foreach($machine->equipment_ids as $equipment_id) {
			$equipment = new Equipment($equipment_id, rex_clang::getCurrentId());
			if($equipment->group !== FALSE && !array_key_exists($equipment->group->priority, $equipment_groups)) {
				$equipment_groups[$equipment->group->priority] = $equipment->group;
				$equipments[$equipment->group->group_id] = [];
			}
			if ($equipment->group !== FALSE && array_key_exists($equipment->group->group_id, $equipments)) {
				$equipments[$equipment->group->group_id][$equipment->name] = $equipment;
			}
		}
		ksort($equipment_groups);

		foreach($equipment_groups as $equipment_group) {
			print '<div class="row">';
			print '<div class="col-12 col-sm-4 col-md-3 col-lg-2">';
			if($equipment_group->picture != "") {
				print '<a href="index.php?rex_media_type=d2u_helper_gallery_detail&rex_media_file='. $equipment_group->picture .'" '
					.'data-toggle="lightbox_equipment" data-gallery="example-galleryequipment" data-title="'. $equipment_group->name.'">';
                print '<img src="index.php?rex_media_type=d2u_machinery_features&rex_media_file='. $equipment_group->picture .'" class="img-fluid featurepic"'
					.' alt="'. $equipment_group->name .'" title="'. $equipment_group->name .'">';
				print '</a>';
			}
			print '</div>';

			print '<div class="col-12 col-sm-8 col-md-9 col-lg-10">';
			print '<div class="row">';
			print '<div class="col-12">';
			print '<div class="block-box">';
			print '<h3>'. $equipment_group->name .'</h3>';
			print '<p>'. d2u_addon_frontend_helper::prepareEditorField($equipment_group->description) .'</p>';
			print '</div>';
			print '</div>';
			$current_equipments = $equipments[$equipment_group->group_id];
			ksort($current_equipments);
			foreach($current_equipments as $current_equipment) {
				print '<div class="col-12">';
				print '<div class="block-box"><b>'. $current_equipment->name .'</b> ('. $tag_open .'d2u_machinery_equipment_artno'. $tag_close .' '. $current_equipment->article_number .')</div>';
				print '</div>';
			}
			print '</div>';
			print '</div>';

			print '<div class="col-12"><div class="deviderline"></div></div>';
			print '</div>';
		}
		print '<script>';
		print "$(document).on('click', '[data-toggle=\"lightbox_equipment\"]', function(event) {
				event.preventDefault();
				$(this).ekkoLightbox({
					alwaysShowClose: true
				});
			});";
		print '</script>';
		print "</div>";
	}

	// Contact Request form
	$d2u_machinery = rex_addon::get("d2u_machinery");
	print '<div id="tab_request" class="tab-pane fade machine-tab">';
	print '<div class="row">';
	print '<div class="col-12 col-md-8">';
	$form_data = 'hidden|machine_name|'. $machine->name .'|REQUEST

			html||<h3> '. $tag_open .'d2u_machinery_request'. $tag_close .': '. $machine->name .'</h3><br>
			text|vorname|'. $tag_open .'d2u_machinery_form_vorname'. $tag_close .'
			text|name|'. $tag_open .'d2u_machinery_form_name'. $tag_close .' *|||{"required":"required"}
			text|company|'. $tag_open .'d2u_machinery_form_company'. $tag_close .'
			text|address|'. $tag_open .'d2u_machinery_form_address'. $tag_close .'
			text|zip|'. $tag_open .'d2u_machinery_form_zip'. $tag_close .'
			text|city|'. $tag_open .'d2u_machinery_form_city'. $tag_close .'
			text|country|'. $tag_open .'d2u_machinery_form_country'. $tag_close .'
			text|phone|'. $tag_open .'d2u_machinery_form_phone'. $tag_close .' *|||{"required":"required"}
			text|email|'. $tag_open .'d2u_machinery_form_email'. $tag_close .' *|||{"required":"required"}
			textarea|message|'. $tag_open .'d2u_machinery_form_message'. $tag_close .' *|||{"required":"required"}
			checkbox|please_call|'. $tag_open .'d2u_machinery_form_please_call'. $tag_close .'|0,1|0
			checkbox|privacy_policy_accepted|'. $tag_open .'d2u_machinery_form_privacy_policy'. $tag_close .' *|0,1|0
			php|validate_timer|Spamprotection|<input name="validate_timer" type="hidden" value="'. microtime(true) .'" />|

			html|honeypot||<div class="hide-validation">
			text|mailvalidate|'. $tag_open .'d2u_machinery_form_email'. $tag_close .'||no_db
			validate|compare_value|mailvalidate||!=|'. $tag_open .'d2u_machinery_form_validate_spam_detected'. $tag_close .'|
			html|honeypot||</div>

			html||<br>* '. $tag_open .'d2u_machinery_form_required'. $tag_close .'<br><br>

			submit|submit|'. $tag_open .'d2u_machinery_form_send'. $tag_close .'|no_db

			validate|empty|name|'. $tag_open .'d2u_machinery_form_validate_name'. $tag_close .'
			validate|empty|phone|'. $tag_open .'d2u_machinery_form_validate_phone'. $tag_close .'
			validate|empty|email|'. $tag_open .'d2u_machinery_form_validate_email'. $tag_close .'
			validate|type|email|email|'. $tag_open .'d2u_machinery_form_validate_email'. $tag_close .'
			validate|empty|message|'. $tag_open .'d2u_machinery_form_validate_email'. $tag_close .'
			validate|empty|privacy_policy_accepted|'. $tag_open .'d2u_machinery_form_validate_privacy_policy'. $tag_close .'
			validate|customfunction|validate_timer|d2u_addon_frontend_helper::yform_validate_timer|5|'. $tag_open .'d2u_machinery_form_validate_spambots'. $tag_close .'|

			action|tpl2email|d2u_machinery_machine_request|emaillabel|'. $d2u_machinery->getConfig('request_form_email');

	$yform = new rex_yform;
	$yform->setFormData(trim($form_data));
	$yform->setObjectparams("form_action", $machine->getUrl());
	$yform->setObjectparams("form_anchor", "tab_request");
	$yform->setObjectparams("Error-occured", $tag_open .'d2u_machinery_form_validate_title'. $tag_close);
	$yform->setObjectparams("real_field_names", TRUE);

	// action - showtext
	$yform->setActionField("showtext", [$tag_open .'d2u_machinery_form_thanks'. $tag_close]);

	echo $yform->getForm();
	
	// Google Analytics Event
	if(rex_config::get('d2u_machinery', 'google_analytics_activate', 'false') == 'true' &&
			rex_config::get('d2u_machinery', 'analytics_event_category', '') !== '' &&
			rex_config::get('d2u_machinery', 'analytics_event_action', '') !== '' &&
			rex_request('search_it_build_index', 'int', FALSE) === FALSE) {
		print '<script>'. PHP_EOL;
		print '	$(\'button[type="submit"]\').click(function(e) {'. PHP_EOL;
		print "		ga('send', 'event', {". PHP_EOL;
		print "			eventCategory: '". rex_config::get('d2u_machinery', 'analytics_event_category') ."',". PHP_EOL;
		print "			eventAction:  '". rex_config::get('d2u_machinery', 'analytics_event_action') ."',". PHP_EOL;
		print "			eventLabel: '". $machine->name ."',". PHP_EOL;
		print "			transport: 'beacon'". PHP_EOL;
		print "		});". PHP_EOL;
		print "	});". PHP_EOL;
		print '</script>'. PHP_EOL;
	}

	print '</div>';
	print '</div>';
	print '</div>'; // END tab_request
	
	print '</div>';
	print '</div>';
	print '</div>';
	
	print '<div class="row">';
	print '<div class="col-12">';
	print '<div class="back-list">';
	print '<a href="'. $machine->category->getURL() .'">';
	print '<span class="fa-icon fa-back"></span>&nbsp;'. $tag_open .'d2u_machinery_back_machine_list'. $tag_close;
	print '</a>';
	print '</div>';
	print '</div>';
}
else if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || ($url_namespace === "industry_sector_id" && $url_id > 0))) {
	$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
	if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
		$industry_sector_id = $url_id;
	}
	$industry_sector = new IndustrySector($industry_sector_id, rex_clang::getCurrentId());
	// Print machine list
	print '<div class="col-12">';
	print '<div class="row" data-match-height>';
	print_machines($industry_sector->getMachines(), $industry_sector->name);
	print '</div>';
	print '</div>';
}
else {
	// Categories
	print '<div class="col-12">';
	print '<div class="row" data-match-height>';
	print_categories(Category::getAll(rex_clang::getCurrentId()));
	print '</div>';
	print '</div>';
	
	print '<div class="col-12 abstand"></div>';
	
	if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
		// Industry sectors
		print '<div class="col-12">';
		print '<div class="row" data-match-height>';
		print_industry_sectors();
		print '</div>';
		print '</div>';
	}
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