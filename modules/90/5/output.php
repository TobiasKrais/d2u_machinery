<?php
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

print_consulation_hint();
?>