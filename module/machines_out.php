<?php
if(!function_exists('print_categories')) {
	/**
	 * Prints category list.
	 * @param Category[] $categories Array with category objecty.
	 */
	function print_categories($categories) {
		foreach($categories as $category) {
			// Only use used categories
			if(count($category->getMachines()) > 0) {
				print '<div class="col-xs-6 col-sm-4 col-md-3 abstand">';
				print '<a href="'. $category->getURL() .'">';
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
			}
		}
	}
}

if(!function_exists('print_industry_sectors')) {
	/**
	 * Prints industry sector list.
	 */
	function print_industry_sectors() {
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");

		print '<div class="col-xs-12 abstand">';
		print '<h1>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_industry_sectors'. $sprog->getConfig('wildcard_close_tag') .'</h1>';
		print '</div>';

		foreach (IndustrySector::getAll(rex_clang::getCurrentId()) as $industry_sector) {
			if($industry_sector->online_status == "online" && count($industry_sector->getMachines()) > 0) {
				print '<div class="col-xs-6 col-sm-4 col-md-3 abstand">';
				print '<a href="'. $industry_sector->getURL() .'">';
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
	 * Prints category list.
	 * @param Machine[] $machines Machines.
	 * @param string $title Title for the list
	 */
	function print_machines($machines, $title) {
		print '<div class="col-xs-12 abstand">';
		print '<h1>'. $title.'</h1>';
		print '</div>';

		foreach($machines as $machine) {
			if($machine->online_status == 'online') {
				print '<div class="col-xs-6 col-sm-4 col-md-3 abstand">';
				print '<a href="'. $machine->getURL() .'">';
				print '<div class="box" data-height-watch>';
				if(count($machine->pics) > 0 && $machine->pics[0] != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$machine->pics[0] .'" alt="'. $machine->name .'">';
				}
				else {
					print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
				}
				print '<div><b>'. $machine->name .'</b></div>';
				print '<div class="teaser">'. $machine->teaser .'</div>';
				print '</div>';
				print '</a>';
				print '</div>';
			}
		}
	}
}
// Get placeholder wildcard tags
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) > 0) {
	$category = new Category(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT), rex_clang::getCurrentId());
	$child_categories = $category->getChildren();
	if(count($child_categories) > 0) {
		// Print child categories
		print '<div class="col-xs-12 abstand">';
		print '<h1>'. $category->name .'</h1>';
		print '</div>';
		print_categories($child_categories);
	}
	else {
		$machines = $category->getMachines();
		print '<div class="col-xs-12">';
		print '<div class="tab-content">';

		// Overview
		print '<div id="tab_overview" class="tab-pane fade in active machine-tab">';
		print '<div class="row" data-match-height>';
		print_machines($machines, $category->name);
		print '</div>';
		print '</div>';

		// Usage Areas
		print '<div id="tab_usage_areas" class="tab-pane fade machine-tab">';
		print '<div class="row">';
		print '<div class="col-xs-12">';
		$usage_area_matrix = $category->getUsageAreaMatrix();
		print '<table class="matrix">';
		print '<thead><tr><td></td>';
		foreach($machines as $machine) {
			print '<td valign="bottom" align="center">';
			print '<a href="'. $machine->getURL() .'"><div>';
			if(count($machine->pics) > 0 && $machine->pics[0] != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. $machine->name .'>';
			}
			else {
				print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
			}
			print '<br><b>'. $machine->name .'</b></div></a></td>';
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
		print '</div>';
		print '</div>';
		print '</div>';

		// Technical data
		print '<div id="tab_tech_data" class="tab-pane fade machine-tab">';
		print '<div class="row">';
		print '<div class="col-xs-12">';
		$tech_data_matrix = $category->getTechDataMatrix();
		print '<table class="matrix">';
		print '<thead><tr><td></td>';
		foreach($machines as $machine) {
			print '<td valign="bottom" align="center">';
			print '<a href="'. $machine->getURL() .'"><div>';
			if(count($machine->pics) > 0 && $machine->pics[0] != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.	$machine->pics[0] .'" alt='. $machine->name .'>';
			}
			else {
				print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
			}
			print '<br><b>'. $machine->name .'</b></div></a></td>';
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
else if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT) > 0) {
	// Print machine
	$machine = new Machine(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT), rex_clang::getCurrentId());
	print '<div class="col-xs-12">';
	print '<div class="tab-content">';
	
	// Overview
	print '<div id="tab_overview" class="tab-pane fade in active machine-tab">';
	print '<div class="row">';
	if(count($machine->pics) > 0) {
		// Picture(s)
		print '<div class="col-xs-12 col-sm-6">';
		if(count($machine->pics) == 1) {
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$machine->pics[0] .'" alt='. $machine->name .' style="max-width:100%;">';
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
				print '<div class="item';
				if($i == 0) {
					print ' active';
				}
				print '">';
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$machine->pics[$i] .'" alt='. $machine->name .'>';
				print '</div>';
			}
			print '</div>';

			// Left and right controls
			print '<a class="left carousel-control" href="#machineCarousel" role="button" data-slide="prev">';
			print '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
			print '<span class="sr-only">Previous</span>';
			print '</a>';
			print '<a class="right carousel-control" href="#machineCarousel" role="button" data-slide="next">';
			print '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
			print '<span class="sr-only">Next</span>';
			print '</a>';
			
			print '</div>';
		}
		print '</div>';

		// Text
		print '<div class="col-xs-12 col-sm-6">'. $machine->description .'</div>';
		print '<div class="col-xs-12"></div>';
		
		// Alternative machines
		if(count($machine->alternative_machine_ids) > 0) {
			print '<div class="col-xs-12 col-sm-6">';
			print '<h3>'. $tag_open .'d2u_machinery_alternative_machines'. $tag_close .'</h3>';
			print '<ul>';
			foreach($machine->alternative_machine_ids as $alternative_machine_id) {
				$alternative_machine = new Machine($alternative_machine_id, $machine->clang_id);
				print '<li><a href="'. $alternative_machine->getURL() .'">'. $alternative_machine->name .'</a></li>';
			}
			print '</ul>';
			print '</div>';
		}
		
		// Software
		if($machine->article_id_software > 0) {
			print '<div class="col-xs-12 col-sm-6">';
			print '<h3>'. $tag_open .'d2u_machinery_software'. $tag_close .'</h3>';
			$article = rex_article::get($machine->article_id_software, $machine->clang_id);
			print '<a href="'. rex_getUrl($machine->article_id_software, $machine->clang_id).'">'. $article->getValue('name') .'</a>';
			print '</div>';
		}
		
		// Service
		if($machine->article_id_service > 0) {
			print '<div class="col-xs-12 col-sm-6">';
			print '<h3>'. $tag_open .'d2u_machinery_service'. $tag_close .'</h3>';
			$article = rex_article::get($machine->article_id_service, $machine->clang_id);
			print '<a href="'. rex_getUrl($machine->article_id_service, $machine->clang_id).'">'. $article->getValue('name') .'</a>';
			print '</div>';
		}
		
		// References
		if(count($machine->article_ids_references) > 0) {
			print '<div class="col-xs-12 col-sm-6">';
			print '<h3>'. $tag_open .'d2u_machinery_references'. $tag_close .'</h3>';
			print '<ul>';
			foreach($machine->article_ids_references as $article_id_reference) {
				$article = rex_article::get($article_id_reference, $machine->clang_id);
				print '<li><a href="'. rex_getUrl($article_id_reference, $machine->clang_id) .'">'. $article->getValue('name') .'</a></li>';
			}
			print '</ul>';
			print '</div>';
		}

		// Downloads
		if(count($machine->pdfs) > 0) {
			print '<div class="col-xs-12 col-sm-6">';
			print '<h3>'. $tag_open .'d2u_machinery_downloads'. $tag_close .'</h3>';
			$pdfs = array_unique(array_merge($machine->pdfs, $machine->category->pdfs));
			foreach($pdfs as $pdf) {
				$media = rex_media::get($pdf);
				print '<a href="'. $media->getUrl() .'"><div class="downloads">'. $media->getTitle() .'</div></a>';
			}
			print '</div>';
		}
	}
	print '</div>';
	print '</div>';
	
	// Agitators
	print '<div id="tab_agitator" class="tab-pane fade machine-tab">';
	$agitator_type = new AgitatorType($machine->agitator_type_id, $machine->clang_id);
	print '<div class="row">';
	print '<div class="col-xs-12"><h3>'. $agitator_type->name .'</h3><br><br></div>';
	print '</div>';
	$agitators = $agitator_type->getAgitators();
	foreach($agitators as $agitator) {
		print '<div class="row">';
		print '<div class="col-xs-12 col-sm-6 col-md-2">';
		if($agitator->pic != "") {
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
					$agitator->pic .'" alt='. $agitator->name .' class="featurepic">';
		}
		print '</div>';
		print '<div class="col-xs-12 col-sm-6 col-md-10 col-lg-8">';
		print '<p><b>'. $agitator->name .'</b></p>';
		print $agitator->description;
		print '</div>';
		print '</div>';
	}
	print '</div>';
	
	// Features
	if(count($machine->feature_ids) > 0) {
		print '<div id="tab_features" class="tab-pane fade machine-tab">';
		$features = $machine->getFeatures();
		foreach($features as $feature) {
			print '<div class="row">';
			print '<div class="col-xs-12 col-sm-6 col-md-2">';
			if($feature->pic != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$feature->pic .'" alt='. $feature->title .' class="featurepic">';
			}
			print '</div>';
			print '<div class="col-xs-12 col-sm-6 col-md-10 col-lg-8">';
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
		print '<div class="col-xs-12 col-md-8">';
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
	print '<div class="col-xs-12 col-md-8">';
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
			captcha|'. $tag_open .'d2u_machinery_form_captcha'. $tag_close .'|'. $tag_open .'d2u_machinery_form_validate_captcha'. $tag_close .'|'. substr($machine->getUrl(), 0 , strrpos($machine->getUrl(), '?')) .'

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
else if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable() && filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT) > 0) {
	$industry_sector = new IndustrySector(filter_input(INPUT_GET, 'industry_sector_id', FILTER_VALIDATE_INT), rex_clang::getCurrentId());
	// Print machine list
	print '<div class="col-xs-12">';
	print '<div class="row" data-match-height>';
	print_machines($industry_sector->getMachines(), $industry_sector->name);
	print '</div>';
	print '</div>';
}
else {
	// Categories
	print '<div class="col-xs-12">';
	print '<div class="row" data-match-height>';
	print_categories(Category::getAll(rex_clang::getCurrentId()));
	print '</div>';
	print '</div>';
	
	print '<div class="col-xs-12 abstand"></div>';
	
	// Industry sectors
	print '<div class="col-xs-12">';
	print '<div class="row" data-match-height>';
	print_industry_sectors();
	print '</div>';
	print '</div>';
}
?>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
</script>