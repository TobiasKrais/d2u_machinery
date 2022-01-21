<?php
if(!function_exists('print_used_machine_categories')) {
	/**
	 * Prints category list for used machines.
	 * @param Category[] $categories Array with category objects.
	 * @param string $offer_type Either "sale" or "rent"
	 */
	function print_used_machine_categories($categories, $offer_type = "") {
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");
		$number_offers_row = "REX_VALUE[2]" == "" ? 4 : "REX_VALUE[2]";
		$counter = 0;
		foreach($categories as $category) {
			// Only use used categories
			if($offer_type != "") {
				$category->setOfferType($offer_type);
			}
			if(count($category->getUsedMachines(TRUE)) > 0) {
				print '<div class="col-sm-6 col-md-4'. ($number_offers_row == 4 ? ' col-lg-3' : '') .' abstand">';
				print '<a href="'. $category->getURL() .'">';
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
		$show_consultation_picture = "REX_VALUE[1]" == 'true' ? FALSE : TRUE;

		$d2u_machinery = rex_addon::get("d2u_machinery");
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");

		print '<div class="col-12 col-md-9 consultation">';
		print '<a href="'. rex_getUrl($d2u_machinery->getConfig('consultation_article_id')) .'">';
		print '<div class="abstand">';
		if($show_consultation_picture && $d2u_machinery->getConfig('consultation_pic') != "") {
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
	 * Prints used machine list.
	 * @param UsedMachine[] $used_machines Used Machines.
	 */
	function print_used_machines($used_machines) {
		$d2u_machinery = rex_addon::get("d2u_machinery");
		$number_offers_row = "REX_VALUE[2]" == "" ? 4 : "REX_VALUE[2]";
		$counter = 0;
		foreach($used_machines as $used_machine) {
			if($used_machine->online_status != 'online') {
				continue;
			}
			print '<div class="col-sm-6 col-md-4'. ($number_offers_row == 4 ? ' col-lg-3' : '') .' abstand">';
			print '<a href="'. $used_machine->getURL(FALSE) .'">';
			print '<div class="box" data-height-watch>';
			if(count($used_machine->pics) > 0 && $used_machine->pics[0] != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
					$used_machine->pics[0] .'" alt="'. $used_machine->name .'">';
			}
			else {
				print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
			}
			print '<div><b>'. $used_machine->manufacturer .' '. $used_machine->name .'</b></div>';
			if($d2u_machinery->getConfig('show_teaser', 'hide') == 'show') {
				print '<div class="teaser">'. nl2br($used_machine->teaser) .'</div>';
			}
			print '</div>';
			print '</a>';
			print '</div>';
			$counter++;
		}
		
		// Directly forward if only one machine is available
		if($counter == 1) {
			foreach($used_machines as $used_machine) {
				if($used_machine->online_status == 'online') {
					header("Location: ". $used_machine->getURL(FALSE));
					exit;
				}
			}
		}
	}
}

$d2u_machinery = rex_addon::get("d2u_machinery");
$picture_type = $d2u_machinery->getConfig('used_machines_pic_type', 'slider');
// Get placeholder wildcard tags
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

if(filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_category_id"
		|| filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_category_id") {
	$category_id = filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_rent_category_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_sale_category_id', FILTER_VALIDATE_INT);
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
		print_used_machine_categories($child_categories);
	}
	else {
		$used_machines = $category->getUsedMachines();
		print '<div class="col-12">';
		print '<div class="tab-content">';

		// Overview
		print '<div id="tab_overview" class="tab-pane fade in active machine-tab show">';
		print '<div class="row" data-match-height>';
		print_used_machines($used_machines);
		print '</div>';
		print '</div>';

		print '</div>';
		print '</div>';
	}
}
else if((filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_rent_machine_id")
		|| (filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 || $url_namespace === "used_sale_machine_id")) {
	// Print used machine
	$used_machine_id = filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT, ['options' => ['default'=> 0]]) > 0 ? filter_input(INPUT_GET, 'used_sale_machine_id', FILTER_VALIDATE_INT) : filter_input(INPUT_GET, 'used_rent_machine_id', FILTER_VALIDATE_INT);
	if(\rex_addon::get("url")->isAvailable() && $url_id > 0) {
		$used_machine_id = $url_id;
	}
	$used_machine = new UsedMachine($used_machine_id, rex_clang::getCurrentId());
	// Redirect if object is not online
	if($used_machine->online_status != "online") {
		\rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
	}
	print '<div class="col-12">';
	print '<div class="tab-content">';
	
	// Overview
	print '<div id="tab_overview" class="tab-pane fade show active machine-tab">';
	print '<div class="row">';
	if(count($used_machine->pics) > 0) {
		// Slider picture(s)
		print '<div class="col-12 col-md-6 abstand">';
		if($picture_type == 'lightbox' || count($used_machine->pics) == 1) {
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$used_machine->pics[0] .'" alt='. $used_machine->name .' style="max-width:100%;">';
		}
		else {
			// Slider
			print '<div id="machineCarousel" class="carousel carousel-fade slide" data-ride="carousel">';
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
				print '<div class="carousel-item';
				if($i == 0) {
					print ' active';
				}
				print '">';
				print '<div class=".carousel-img-holder">';
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$used_machine->pics[$i] .'" alt='. $used_machine->name .'>';
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

	// Text
	print '<div class="col-12 col-md-6">'. d2u_addon_frontend_helper::prepareEditorField($used_machine->description) .'</div>';
	if($picture_type == 'slider' && count($used_machine->pics) > 0) {
		print '<div class="col-12">&nbsp;</div>';
	}
		
	print '<div class="col-12 col-md-6">';
	print '<div class="row">'; // START details row

	// Video
	if(\rex_addon::get('d2u_videos')->isAvailable()) {
		if(is_array($used_machine->videos) && count($used_machine->videos) > 0) {
			print '<div class="col-12'. (count($used_machine->videos) > 1 ? '' : ' col-lg-6') .'" id="videoplayer">';
			print '<h3>'. $tag_open .'d2u_machinery_video'. $tag_close .'</h3>';
			$videomanager = new Videomanager();
			$videomanager->printVideos($used_machine->videos);
			print '</div>';
		} // END Video
	}

	// Link to machine
	if($used_machine->machine !== FALSE) {
		print '<div class="col-12">';
		print '<p><b>'. $tag_open .'d2u_machinery_used_machines_link_machine'. $tag_close .':</b>';
		print '<a href="'. $used_machine->machine->getURL() .'">'. $used_machine->machine->name .'</a></p>';
		print '</div>';
	}
	// Availability
	print '<div class="col-12">';
	print '<p><b>'. $tag_open .'d2u_machinery_used_machines_availible_from'. $tag_close .':</b> ';
	if($used_machine->availability != "" && $used_machine->availability > date("Y-m-d")) {
		if($d2u_machinery->hasConfig('lang_replacement_'. rex_clang::getCurrentId()) && $d2u_machinery->getConfig('lang_replacement_'. rex_clang::getCurrentId()) == "german") {
			print date_format(date_create($used_machine->availability),"d.m.Y");
		}
		else {
			print date_format(date_create($used_machine->availability),"Y/m/d");
		}
	}
	else {
		print $tag_open .'d2u_machinery_used_machines_availability'. $tag_close;
	}
	print '</p></div>';
	// Year built
	if($used_machine->year_built > 0) {
		print '<div class="col-12">';
		print '<p><b>'. $tag_open .'d2u_machinery_used_machines_year_built'. $tag_close .':</b> '. $used_machine->year_built .'</p>';
		print '</div>';
	}
	// Price
	print '<div class="col-12">';
	print '<p><b>'. $tag_open .'d2u_machinery_used_machines_price'. $tag_close .':</b> ';
	if($used_machine->price != "0.00") {
		print $used_machine->price .' '. $used_machine->currency_code;
		if($used_machine->vat > 0) {
			print ' ('. $used_machine->vat .'% '.$tag_open .'d2u_machinery_used_machines_vat_included'. $tag_close .')';
		}
		else {
			print ' ('. $tag_open .'d2u_machinery_used_machines_vat_excluded'. $tag_close .')';
		}
	}
	else {
		print $tag_open .'d2u_machinery_used_machines_price_on_request'. $tag_close;
	}
	print '</p></div>';
	// Location
	if($used_machine->location != "") {
		print '<div class="col-12">';
		print '<p><b>'. $tag_open .'d2u_machinery_used_machines_location'. $tag_close .':</b> '. $used_machine->location .'</p>';
		print '</div>';
	}
	// External URL
	if($used_machine->external_url != "" && filter_var($used_machine->external_url, FILTER_VALIDATE_URL)) {
		print '<div class="col-12">';
		print '<p><b>'. $tag_open .'d2u_machinery_used_machines_external_url'. $tag_close .':</b> <a href="'. $used_machine->external_url .'" target="_blank">'. $used_machine->external_url .'</a></p>';
		print '</div>';
	}
	//Downloads
	if(count($used_machine->downloads) > 0) {
		print '<div class="col-12 col-md-6">';
		print '<ul class="download-list">';
		foreach($used_machine->downloads as $document) {
			$rex_document = rex_media::get($document);
			if($rex_document instanceof rex_media) {
				$filesize = round(filesize(rex_path::media() .'/'. $document) / pow(1024, 2), 2);
				$filetype = strtoupper(pathinfo(rex_path::media() . PATH_SEPARATOR . $document, PATHINFO_EXTENSION));
				
				// Check permissions
				$has_permission = TRUE;
				if(rex_plugin::get('ycom', 'media_auth')->isAvailable()) {
					$has_permission = rex_ycom_media_auth::checkPerm(rex_media_manager::create("", $document));
				}
				if($has_permission) {
					print '<li>';
					if($filetype == 'pdf') {
						print '<span class="icon pdf"></span> ';
					}
					else {
						print '<span class="icon file"></span> ';
					}
					print '<a href="'. rex_url::media($document) .'" target="_blank">'.
							$rex_document->getTitle() .' <span>('. $filetype .', '. $filesize .' MB)</span></a></li>';
				}
			}
		}
		print '</ul>';
		print '</div>';
	}
	
	print '</div>'; // END details row
	print '</div>';

	print '</div>'; // End row
	print '</div>'; // End tab

	// Lightbox pictures tab
	if($picture_type == 'lightbox' && count($used_machine->pics) > 0) {
		print '<div id="tab_pics" class="tab-pane fade machine-tab">';
		print '<div class="row">';
		$type_thumb = "d2u_helper_gallery_thumb";
		$type_detail = "d2u_helper_gallery_detail";
		$lightbox_id = rand();
		
		print '<div class="col-12">';
		print '<div class="row">';
		foreach($used_machine->pics as $pic) {
			$media = rex_media::get($pic);
			print '<a href="index.php?rex_media_type='. $type_detail .'&rex_media_file='. $pic .'" data-toggle="lightbox'. $lightbox_id .'" data-gallery="example-gallery'. $lightbox_id .'" class="col-6 col-sm-4 col-lg-3"';
			if($media instanceof rex_media) {
				print ' data-title="'. $media->getValue('title') .'"';
			}
			print '>';
			print '<img src="index.php?rex_media_type='. $type_thumb .'&rex_media_file='. $pic .'" class="img-fluid gallery-pic-box"';
			if($media instanceof rex_media) {
				print ' alt="'. $media->getValue('title') .'" title="'. $media->getValue('title') .'"';
			}
			print '>';
			print '</a>';
		}
		print '</div>';
		print '</div>';
		print "<script>";
		print "$(document).on('click', '[data-toggle=\"lightbox". $lightbox_id ."\"]', function(event) {";
		print "event.preventDefault();";
		print "$(this).ekkoLightbox({ alwaysShowClose: true	});";
		print "});";
		print "</script>";		
		print '</div>'; // End row
		print '</div>'; // End tab
	}

	// Contact Request form
	$d2u_machinery = rex_addon::get("d2u_machinery");
	print '<div id="tab_request" class="tab-pane fade machine-tab">';
	print '<div class="row">';
	print '<div class="col-12 col-md-8 abstand">';
	$form_data = 'hidden|machine_name|'. $used_machine->manufacturer .' '. $used_machine->name .' (Gebrauchtmaschine)|REQUEST

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
	if(rex_addon::get('yform_spam_protection')->isAvailable()) {
		$form_data .= '
			spam_protection|honeypot|Bitte nicht ausfüllen|'. $tag_open .'d2u_helper_module_form_validate_spam_detected'. $tag_close .'|0';					
	}
	else {
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

			action|tpl2email|d2u_machinery_machine_request|'. ($used_machine->contact ? $used_machine->contact->email : $d2u_machinery->getConfig('request_form_email'));

	$yform = new rex_yform;
	$yform->setFormData(trim($form_data));
	$yform->setObjectparams("form_action", $used_machine->getUrl());
	$yform->setObjectparams("form_anchor", "tab_request");
	$yform->setObjectparams("Error-occured", $tag_open .'d2u_helper_module_form_validate_title'. $tag_close);
	$yform->setObjectparams("real_field_names", TRUE);

	// action - showtext
	$yform->setActionField("showtext", [$tag_open .'d2u_helper_module_form_thanks'. $tag_close]);

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
		print "			eventLabel: '". $used_machine->manufacturer .' '. $used_machine->name ."',". PHP_EOL;
		print "			transport: 'beacon'". PHP_EOL;
		print "		});". PHP_EOL;
		print "	});". PHP_EOL;
		print '</script>'. PHP_EOL;
	}

	print '</div>';
	print '</div>';
	print '</div>';
	
	print '</div>';
	print '</div>';
}
else {
	// Categories
	print '<div class="col-12">';
	print '<div class="tab-content">';

	$current_article = rex_article::getCurrent();
	$class_active = ' active show';
	if($current_article->getId() == $d2u_machinery->getConfig('used_machine_article_id_sale')) {
		// Sale offers
		print '<div id="tab_sale" class="tab-pane fade machine-tab'. $class_active .'">';
		$class_active = "";
		print '<div class="row" data-match-height>';
		print_used_machine_categories(Category::getAll(rex_clang::getCurrentId()), "sale");
		print '</div>';
		print '</div>';
	}

	if($current_article->getId() == $d2u_machinery->getConfig('used_machine_article_id_rent')) {
		// Rental offers
		print '<div id="tab_rent" class="tab-pane fade machine-tab'. $class_active .'">';
		print '<div class="row" data-match-height>';
		print_used_machine_categories(Category::getAll(rex_clang::getCurrentId()), "rent");
		print '</div>';
		print '</div>';
	}
	
	if($current_article->getId() != $d2u_machinery->getConfig('used_machine_article_id_rent') && $current_article->getId() != $d2u_machinery->getConfig('used_machine_article_id_sale')) {
		print "<p>". $tag_open .'d2u_machinery_used_machines_config_error'. $tag_close ."</p>";
	}

	print '</div>';
	print '</div>';
	
	print '<div class="col-12 abstand"></div>';
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