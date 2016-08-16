<?php
// save settings
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	$settings = (array) rex_post('settings', 'array', array());

	// Linkmap Link needs special treatment
	$link_ids = filter_input_array(INPUT_POST, array('REX_INPUT_LINK'=> array('filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY)));
	$link_names = filter_input_array(INPUT_POST, array('REX_LINK_NAME' => array('flags' => FILTER_REQUIRE_ARRAY)));

	$settings['article_id'] = $link_ids["REX_INPUT_LINK"][1];
	$settings['article_name'] = trim($link_names["REX_LINK_NAME"][1]);

	// Checkbox also need special treatment if empty
	if(!array_key_exists('show_categories_usage_area', $settings)) {
		$settings['show_categories_usage_area'] = "hide";
	}
	
	// Save settings
	if(rex_config::set("d2u_machinery", $settings)) {
		echo rex_view::success(rex_i18n::msg('form_saved'));
	}
	else {
		echo rex_view::error(rex_i18n::msg('form_save_error'));
	}
}
?>
<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_meta_settings'); ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><?php echo rex_i18n::msg('d2u_machinery_meta_settings'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_linkfield('d2u_machinery_settings_article', '1', $this->getConfig('article_id'), $this->getConfig('default_lang'));

						// Default language for translations
						if(count(rex_clang::getAll()) > 1) {
							$options = array();
							foreach(rex_clang::getAll() as $rex_clang) {
								$options[$rex_clang->getId()] = $rex_clang->getName();
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_settings_defaultlang', 'settings[default_lang]', $options, array($this->getConfig('default_lang')));
						}
						
						d2u_addon_backend_helper::form_input('d2u_machinery_settings_request_form_email', 'settings[request_form_email]', $this->getConfig('request_form_email'), TRUE, FALSE, 'email')
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><?php echo rex_i18n::msg('d2u_machinery_meta_categories'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_categories_usage_area', 'settings[show_categories_usage_area]', 'show', $this->getConfig('show_categories_usage_area') == 'show')
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