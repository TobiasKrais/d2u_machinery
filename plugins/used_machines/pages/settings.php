<?php
// save settings
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	$settings = (array) rex_post('settings', 'array', array());

	// Linkmap Link need special treatment
	$link_ids = filter_input_array(INPUT_POST, array('REX_INPUT_LINK'=> array('filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY)));
	$link_names = filter_input_array(INPUT_POST, array('REX_LINK_NAME' => array('flags' => FILTER_REQUIRE_ARRAY)));

	$settings['article_id'] = $link_ids["REX_INPUT_LINK"][1];
	$settings['article_name'] = trim($link_names["REX_LINK_NAME"][1]);
	
	// Save settings
	if(rex_config::set("d2u_machinery/used_machines", $settings)) {
		echo rex_view::success(rex_i18n::msg('d2u_machinery_settings_changes_saved'));
	}
	else {
		echo rex_view::error(rex_i18n::msg('d2u_machinery_settings_changes_not_saved'));
	}
}
?>
<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_used_machines_settings'); ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><?php echo rex_i18n::msg('d2u_machinery_used_machines_settings'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_linkfield('d2u_machinery_used_machines_article', '1', $this->getConfig('article_name'), $this->getConfig('article_id'));
					?>
				</div>
			</fieldset>
		</div>
		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?php echo rex_i18n::msg('form_save'); ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>