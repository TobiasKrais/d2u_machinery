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
				if($category->pic != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$category->pic .'" alt="'. $category->name .'">';
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

		print '<div class="col-12 consultation">';
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
$urlParamKey = "";
if(rex_addon::get("url")->isAvailable()) {
	$url_data = UrlGenerator::getData();
	$urlParamKey = isset($url_data->urlParamKey) ? $url_data->urlParamKey : "";
}

if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "category_id")) {
	$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$category_id = UrlGenerator::getId();
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
		$machines = $category->getMachines();
		print '<div class="col-12">';
		print '<div class="tab-content">';

		// Overview
		print '<div id="tab_overview" class="tab-pane fade in active machine-tab show">';
		print '<div class="row" data-match-height>';
		print_machines($machines, "");
		print '</div>';
		print '</div>';

		// Usage Areas
		if(rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
			print '<div id="tab_usage_areas" class="tab-pane fade machine-tab">';
			print '<div class="row">';
			print '<div class="col-12">';
			if($d2u_machinery->hasConfig("usage_area_header") && $d2u_machinery->getConfig("usage_area_header") == "usage") {
				$usage_areas = UsageArea::getAll(rex_clang::getCurrentId(), $category_id);
				print '<table class="matrix">';
				print '<thead><tr><td></td>';
				foreach($usage_areas as $usage_area) {
					print '<td class="inactive-cell">';
					print '<div class="vertical"><b>'. $usage_area->name .'</b></div></td>';
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
					print '<td valign="top" align="center">';
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
					print '<tr><td class="inactive-cell"><b>'. $key .'</b></td>';
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
		print '<thead><tr><td></td>';
		foreach($machines as $machine) {
			print '<td valign="top" align="center">';
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
		foreach($tech_data_matrix as $key => $values) {
			print '<tr><td class="inactive-cell"><b>'. $key .'</b></td>';
			foreach($machines as $machine) {
				print '<td class="inactive-cell">'. $values[$machine->machine_id] .'</td>';
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
else if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "machine_id")) {
	// Print machine
	$machine_id = filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$machine_id = UrlGenerator::getId();
	}
	$machine = new Machine($machine_id, rex_clang::getCurrentId());
	print '<div class="col-12">';
	print '<div class="tab-content">';
	
	// Overview
	print '<div id="tab_overview" class="tab-pane fade in active machine-tab show">';
	print '<div class="row">';

	// Text
	print '<div class="col-12 col-md-6">';
	print $machine->description;
	print '<p>&nbsp;</p>';
	// Alternative machines
	if(count($machine->alternative_machine_ids) > 0) {
		$alternative_machines = [];
		foreach($machine->alternative_machine_ids as $alternative_machine_id) {
			$alternative_machine = new Machine($alternative_machine_id, $machine->clang_id);
			if($alternative_machine->translation_needs_update != "delete") {
				$alternative_machines[] = $alternative_machine;
			}
		}
		if(count($alternative_machines) > 0) {
			print '<h3>'. $tag_open .'d2u_machinery_alternative_machines'. $tag_close .'</h3>';
			print '<ul>';
			foreach($alternative_machines as $alternative_machine) {
				print '<li><a href="'. $alternative_machine->getURL() .'">'. ($alternative_machine->lang_name == "" ? $alternative_machine->name : $alternative_machine->lang_name) .'</a></li>';
			}
			print '</ul>';
			print '<p>&nbsp;</p>';
		}
	}
		
	// Software
	if($machine->article_id_software > 0) {
		print '<h3>'. $tag_open .'d2u_machinery_software'. $tag_close .'</h3>';
		$article = rex_article::get($machine->article_id_software, $machine->clang_id);
		print '<a href="'. rex_getUrl($machine->article_id_software, $machine->clang_id).'">'. $article->getValue('name') .'</a>';
		print '<p>&nbsp;</p>';
	}
		
	// Service
	if($machine->article_id_service > 0) {
		print '<h3>'. $tag_open .'d2u_machinery_service'. $tag_close .'</h3>';
		$article = rex_article::get($machine->article_id_service, $machine->clang_id);
		print '<a href="'. rex_getUrl($machine->article_id_service, $machine->clang_id).'">'. $article->getValue('name') .'</a>';
		print '<p>&nbsp;</p>';
	}
		
	// References
	if(count($machine->article_ids_references) > 0) {
		print '<h3>'. $tag_open .'d2u_machinery_references'. $tag_close .'</h3>';
		print '<ul>';
		foreach($machine->article_ids_references as $article_id_reference) {
			$article = rex_article::get($article_id_reference, $machine->clang_id);
			print '<li><a href="'. rex_getUrl($article_id_reference, $machine->clang_id) .'">'. $article->getValue('name') .'</a></li>';
		}
		print '</ul>';
		print '<p>&nbsp;</p>';
	}
	print '</div>';
	
	if(count($machine->pics) > 0) {
		// Picture(s)
		print '<div class="col-12 col-md-6">';
		if(count($machine->pics) == 1) {
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$machine->pics[0] .'" alt='. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .' style="max-width:100%;">';
		}
		else {
			// Slider
			print '<div id="machineCarousel" class="carousel slide" data-ride="carousel">';
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
			print '<div class="carousel-inner" role="listbox">';
			for($i = 0; $i < count($machine->pics); $i++) {
				print '<div class="carousel-item';
				if($i == 0) {
					print ' active';
				}
				print '">';
				print '<div class="carousel-img-holder">';
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$machine->pics[$i] .'" alt="'. ($machine->lang_name == "" ? $machine->name : $machine->lang_name) .'">';
				print '</div>';
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
	print '</div>';

	// Downloads
	if(count($machine->pdfs) > 0) {
		print '<div class="col-12 col-md-6">';
		print '<h3>'. $tag_open .'d2u_machinery_downloads'. $tag_close .'</h3>';
		$pdfs = array_unique(array_merge($machine->pdfs, $machine->category->pdfs));
		foreach($pdfs as $pdf) {
			$media = rex_media::get($pdf);
			print '<a href="'. $media->getUrl() .'"><div class="downloads">'. $media->getTitle() .'</div></a>';
		}
		print '</div>';
	}
	print '</div>';
	
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
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$agitator->pic .'" alt='. $agitator->name .' class="featurepic">';
			}
			print '</div>';
			print '<div class="col-12 col-md-6 col-lg-10 col-xl-8">';
			print '<p><b>'. $agitator->name .'</b></p>';
			print $agitator->description;
			print '</div>';
			print '</div>';
		}
		print '</div>';
	}
	
	// Features
	if(count($machine->feature_ids) > 0) {
		print '<div id="tab_features" class="tab-pane fade machine-tab">';
		$features = $machine->getFeatures();
		foreach($features as $feature) {
			print '<div class="row">';
			print '<div class="col-12 col-md-6 col-lg-2">';
			if($feature->pic != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$feature->pic .'" alt='. $feature->title .' class="featurepic">';
			}
			print '</div>';
			print '<div class="col-12 col-md-6 col-lg-10 col-xl-8">';
			print '<p><b>'. $feature->title .'</b></p>';
			print $feature->description;
			print '</div>';
			print '</div>';
		}
		print '</div>';
	}
	
	// Technical data
	print '<div id="tab_tech_data" class="tab-pane fade machine-tab">';
	$tech_data = $machine->getTechnicalData();
	if(count($tech_data) > 0) {
		print '<div class="row">';
		print '<div class="col-12 col-md-8">';
		print '<table class="techdata">';
		foreach ($tech_data as $description_wildcard => $value) {
			print '<tr>';
			print '<td class="description">'. $description_wildcard .'</td>';
			print '<td class="value">'. $value .'</td>';
			print '</tr>';
		}
		print '</table>';
		print '</div>';
		print '</div>';
	}
	print '</div>';
	
	// Contact Request form
	$d2u_machinery = rex_addon::get("d2u_machinery");
	print '<div id="tab_request" class="tab-pane fade machine-tab">';
	print '<div class="row">';
	print '<div class="col-12 col-md-8">';
	$form_data = 'hidden|machine_name|'. $machine->name .'|REQUEST

			text|name|'. $tag_open .'d2u_machinery_form_name'. $tag_close .' *
			text|company|'. $tag_open .'d2u_machinery_form_company'. $tag_close .' *
			text|address|'. $tag_open .'d2u_machinery_form_address'. $tag_close .' *
			text|zip|'. $tag_open .'d2u_machinery_form_zip'. $tag_close .' *
			text|city|'. $tag_open .'d2u_machinery_form_city'. $tag_close .' *
			text|country|'. $tag_open .'d2u_machinery_form_country'. $tag_close .' *
			text|phone|'. $tag_open .'d2u_machinery_form_phone'. $tag_close .' *
			text|email|'. $tag_open .'d2u_machinery_form_email'. $tag_close .' *
			textarea|message|'. $tag_open .'d2u_machinery_form_message'. $tag_close .'

			html||<br>* '. $tag_open .'d2u_machinery_form_required'. $tag_close .'<br><br>
			captcha_calc|'. $tag_open .'d2u_machinery_form_captcha'. $tag_close .'|'. $tag_open .'d2u_machinery_form_validate_captcha'. $tag_close .'|'. rex_getUrl('', '', ['machine_id' => $machine->machine_id]) .'

			submit|submit|'. $tag_open .'d2u_machinery_form_send'. $tag_close .'|no_db

			validate|empty|name|'. $tag_open .'d2u_machinery_form_validate_name'. $tag_close .'
			validate|empty|company|'. $tag_open .'d2u_machinery_form_validate_company'. $tag_close .'
			validate|empty|address|'. $tag_open .'d2u_machinery_form_validate_address'. $tag_close .'
			validate|empty|zip|'. $tag_open .'d2u_machinery_form_validate_zip'. $tag_close .'
			validate|empty|city|'. $tag_open .'d2u_machinery_form_validate_city'. $tag_close .'
			validate|empty|country|'. $tag_open .'d2u_machinery_form_validate_country'. $tag_close .'
			validate|empty|phone|'. $tag_open .'d2u_machinery_form_validate_phone'. $tag_close .'
			validate|empty|email|'. $tag_open .'d2u_machinery_form_validate_email'. $tag_close .'
			validate|email|email|'. $tag_open .'d2u_machinery_form_validate_email_false'. $tag_close .'

			action|tpl2email|d2u_machinery_machine_request|emaillabel|'. $d2u_machinery->getConfig('request_form_email');

	$yform = new rex_yform;
	$yform->setFormData(trim($form_data));
	$yform->setObjectparams("form_action", $machine->getUrl());
	$yform->setObjectparams("form_anchor", "tab_request");
	$yform->setObjectparams("Error-occured", $tag_open .'d2u_machinery_form_validate_title'. $tag_close);

	// action - showtext
	$yform->setActionField("showtext", array($tag_open .'d2u_machinery_form_thanks'. $tag_close));

	echo $yform->getForm();
	print '</div>';
	print '</div>';
	print '</div>';
	
	print '</div>';
	print '</div>';
}
else if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && (filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "industry_sector_id"))) {
	$industry_sector_id = filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$industry_sector_id = UrlGenerator::getId();
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