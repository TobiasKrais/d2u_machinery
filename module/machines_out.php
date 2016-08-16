<?php
if(!function_exists('print_categories')) {
	/**
	 * Prints category list.
	 * @param Category[] $categories Array with category objecty.
	 */
	function print_categories($categories) {
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");

		print '<div class="col-xs-12 abstand">';
		print '<h1>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_our_products'. $sprog->getConfig('wildcard_close_tag') .'</h1>';
		print '</div>';

		foreach($categories as $category) {
			// Only use used categories
			if(count($category->getMachines()) > 0) {
				print '<div class="col-xs-6 col-sm-4 col-md-3 abstand">';
				print '<a href="'. $category->getURL() .'">';
				print '<div class="box">';
				if($category->pic != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$category->pic .'" alt='. $category->name .' style="max-width:100%;">';
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
				print '<div class="box">';
				if($industry_sector->pic != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$industry_sector->pic .'" alt='. $industry_sector->name .' style="max-width:100%;">';
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
				print '<div class="box">';
				if(count($machine->pics) > 0 && $machine->pics[0] != "") {
					print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
						$machine->pics[0] .'" alt='. $machine->name .' style="max-width:100%;">';
				}
				print '<div>'. $machine->name .'</div>';
				print '</div>';
				print '</a>';
				print '</div>';
			}
		}
	}
}

if(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) > 0) {
	$category = new Category(filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT), rex_clang::getCurrentId());
	$child_categories = $category->getChildren();
	if(count($child_categories) > 0) {
		// Print child categories
		print_categories($child_categories);
	}
	else {
		// Print machine list
		print_machines($category->getMachines(), $category->name);
	}
}
else if(filter_input(INPUT_GET, 'machine_id', FILTER_VALIDATE_INT) > 0) {
	// Get placeholder wildcard tags
	$sprog = rex_addon::get("sprog");
	$tag_open = $sprog->getConfig('wildcard_open_tag');
	$tag_close = $sprog->getConfig('wildcard_close_tag');

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
	}
	print '</div>';
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

			html||<br />* '. $tag_open .'d2u_machinery_form_required'. $tag_close .'<br /><br />
			captcha|'. $tag_open .'d2u_machinery_form_captcha'. $tag_close .'|'. $tag_open .'d2u_machinery_form_validate_captcha'. $tag_close .'

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

			action|tpl2email|machine_request|emaillabel|'. $d2u_machinery->getConfig('request_form_email');

	$yform = new rex_yform();
	$yform->setFormData($form_data);
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
	print_machines($industry_sector->getMachines(), $industry_sector->name);
}
else {
	// Categories
	print_categories(Category::getAll(rex_clang::getCurrentId()));
	
	print '<div class="col-xs-12 abstand"></div>';
	
	// Industry sectors
	print_industry_sectors();
}
?>
<script>
	// Allow activation of bootstrap tab via URL
	$(function() {
		var hash = window.location.hash;
		hash && $('ul.nav a[href="' + hash + '"]').tab('show');
	});
</script>