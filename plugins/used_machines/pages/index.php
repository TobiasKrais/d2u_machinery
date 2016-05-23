<?php
/**
 * Formatiert zwei Strings so, das sie in ein rex_form passen.
 * @param type $label Label
 * @param type $content Inhalt
 */
function raw_field($label, $content) {
	$formated_content = '<dl class="rex-form-group form-group">';
	$formated_content .= '<dt><label class="control-label" for="rex-375-group-gruppen-default-sender-name">'. $label .'</label></dt>';
	$formated_content .= '<dd style="padding-top: 7px;">'. $content .'</dd>';
	$formated_content .= '</dl>';

	return $formated_content;
}

echo rex_view::title($this->i18n('d2u_machinery_used_machines'));

rex_be_controller::includeCurrentPageSubPath();
?>