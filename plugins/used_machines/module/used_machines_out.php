<?php
if(!function_exists('print_used_machine_categories')) {
	/**
	 * Prints category list for used machines.
	 * @param Category[] $categories Array with category objects.
	 */
	function print_used_machine_categories($categories) {
		foreach($categories as $category) {
			// Only use used categories
			if(count($category->getUsedMachines()) > 0) {
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

if(!function_exists('print_consulation_hint')) {
	/**
	 * Prints consulation hint.
	 */
	function print_consulation_hint() {
		$d2u_machinery = rex_addon::get("d2u_machinery");
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");

		print '<div class="col-xs-12 col-sm-8 col-md-12">';
		print '<br><br><br><br><br>';
		print '<a href="'. rex_getUrl($d2u_machinery->getConfig('consultation_article_id')) .'">';
		print '<div class="abstand">';
		if($d2u_machinery->getConfig('consultation_pic') != "") {
			print '<img src="'. rex_url::media($d2u_machinery->getConfig('consultation_pic')) .'" alt=""><br><br>';
		}
		print '<p>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_consultation_hint'. $sprog->getConfig('wildcard_close_tag') .'</p>';
		print '</div>';
		print '</a>';
		print '</div>';
	}
}

if(!function_exists('print_used_machines')) {
	/**
	 * Prints category list.
	 * @param Machine[] $used_machines Machines.
	 * @param string $title Title for the list
	 */
	function print_used_machines($used_machines, $title) {
		print '<div class="col-xs-12 abstand">';
		print '<h1>'. $title.'</h1>';
		print '</div>';

		foreach($used_machines as $used_machine) {
			if($used_machine->online_status == 'online') {
				print '<div class="col-xs-6 col-sm-4 col-md-3 abstand">';
				print '<a href="'. $used_machine->getURL() .'">';
				print '<div class="box" data-height-watch>';
				if(count($used_machine->pics) > 0 && $used_machine->pics[0] != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$used_machine->pics[0] .'" alt="'. $used_machine->name .'">';
				}
				else {
					print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
				}
				print '<div><b>'. $used_machine->name .'</b></div>';
				print '<div class="teaser">'. nl2br($used_machine->teaser) .'</div>';
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
$urlParamKey = "";
if(rex_addon::get("url")->isAvailable()) {
	$url_data = UrlGenerator::getData();
	$urlParamKey = $url_data->urlParamKey;
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
		print '<div class="col-xs-12 abstand">';
		print '<h1>'. $category->name .'</h1>';
		print '</div>';
		print_used_machine_categories($child_categories);
	}
	else {
		$used_machines = $category->getUsedMachines();
		print '<div class="col-xs-12">';
		print '<div class="tab-content">';

		// Overview
		print '<div id="tab_overview" class="tab-pane fade in active machine-tab">';
		print '<div class="row" data-match-height>';
		print_used_machines($used_machines, $category->name);
		print '</div>';
		print '</div>';

		print '</div>';
		print '</div>';
	}
}
else if(filter_input(INPUT_GET, 'used_machine_id', FILTER_VALIDATE_INT) > 0 || (rex_addon::get("url")->isAvailable() && $urlParamKey === "used_machine_id")) {
	// Print used machine
	$used_machine_id = filter_input(INPUT_GET, 'used_machine_id', FILTER_VALIDATE_INT);
	if(rex_addon::get("url")->isAvailable() && UrlGenerator::getId() > 0) {
		$used_machine_id = UrlGenerator::getId();
	}
	$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
	print '<div class="col-xs-12">';
	print '<div class="tab-content">';
	
	// Overview
	print '<div id="tab_overview" class="tab-pane fade in active machine-tab">';
	print '<div class="row">';
	if(count($used_machine->pics) > 0) {
		// Picture(s)
		print '<div class="col-xs-12 col-sm-6">';
		if(count($used_machine->pics) == 1) {
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$used_machine->pics[0] .'" alt='. $used_machine->name .' style="max-width:100%;">';
		}
		else {
			// Slider
			print '<div id="machineCarousel" class="carousel slide" data-ride="carousel">';
			// Slider indicators
			print '<ol class="carousel-indicators">';
			for($i = 0; $i < count($used_machine->pics); $i++) {
				print '<li data-target="#machineCarousel" data-slide-to="'. $i .'"';
				if($i == 0) {
					print 'class="active"';
				}
				print '></li>';
			}
			print '</ol>';

			// Wrapper for slides
			print '<div class="carousel-inner" role="listbox">';
			for($i = 0; $i < count($used_machine->pics); $i++) {
				print '<div class="item';
				if($i == 0) {
					print ' active';
				}
				print '">';
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$used_machine->pics[$i] .'" alt='. $used_machine->name .'>';
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
		print '<div class="col-xs-12 col-sm-6">'. $used_machine->description .'</div>';
		print '<div class="col-xs-12"></div>';
		
		// Link to machine TODO
		if($used_machine->machine !== FALSE) {
			print '<div class="col-xs-12 col-sm-6">';
			print '<h3>'. $tag_open .'d2u_machinery_service'. $tag_close .'</h3>';
			$article = rex_article::get($used_machine->article_id_service, $used_machine->clang_id);
			print '<a href="'. rex_getUrl($used_machine->article_id_service, $used_machine->clang_id).'">'. $article->getValue('name') .'</a>';
			print '</div>';
		}
	}
	print '</div>';
	print '</div>';
	
	// Agitators
	if($used_machine->agitator_type_id > 0) {
		print '<div id="tab_agitator" class="tab-pane fade machine-tab">';
		$agitator_type = new AgitatorType($used_machine->agitator_type_id, $used_machine->clang_id);
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
	}
	
	// Features
	if(count($used_machine->feature_ids) > 0) {
		print '<div id="tab_features" class="tab-pane fade machine-tab">';
		$features = $used_machine->getFeatures();
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
	$tech_data = $used_machine->getTechnicalData();
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
	$form_data = 'hidden|machine_name|'. $used_machine->name .'|REQUEST

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
			captcha_calc|'. $tag_open .'d2u_machinery_form_captcha'. $tag_close .'|'. $tag_open .'d2u_machinery_form_validate_captcha'. $tag_close .'|'. rex_getUrl('', '', ['used_machine_id' => $used_machine->used_machine_id]) .'

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
	$yform->setObjectparams("form_action", $used_machine->getUrl());
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
	print '<div class="col-xs-12">';
	print '<div class="row" data-match-height>';
	print_used_machines($industry_sector->getMachines(), $industry_sector->name);
	print '</div>';
	print '</div>';
}
else {
	// Categories
	print '<div class="col-xs-12">';
	print '<div class="row" data-match-height>';
	print_used_machine_categories(Category::getAll(rex_clang::getCurrentId()));
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

print_consulation_hint();
?>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
</script>