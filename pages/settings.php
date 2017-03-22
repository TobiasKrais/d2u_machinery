<?php
// save settings
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	$settings = (array) rex_post('settings', 'array', array());

	// Linkmap Link and media needs special treatment
	$link_ids = filter_input_array(INPUT_POST, array('REX_INPUT_LINK'=> array('filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY)));

	$settings['article_id'] = $link_ids["REX_INPUT_LINK"][1];

	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', array());
	$settings['consultation_pic'] = $input_media['consultation_pic'];
	$settings['consultation_article_id'] = $link_ids["REX_INPUT_LINK"][2];

	if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
		$settings['used_machine_article_id_rent'] = $link_ids["REX_INPUT_LINK"][3];
		$settings['used_machine_article_id_sale'] = $link_ids["REX_INPUT_LINK"][4];
	}

	// Checkbox also need special treatment if empty
	$settings['show_categories_usage_area'] = array_key_exists('show_categories_usage_area', $settings) ? "show" : "hide";
	$settings['show_teaser'] = array_key_exists('show_teaser', $settings) ? "show" : "hide";
	$settings['show_techdata'] = array_key_exists('show_techdata', $settings) ? "show" : "hide";
	if(rex_plugin::get('d2u_machinery', 'export')->isAvailable()) {
		$settings['export_autoexport'] = array_key_exists('export_autoexport', $settings) ? "active" : "inactive";
	}
	
	// Save settings
	if(rex_config::set("d2u_machinery", $settings)) {
		echo rex_view::success(rex_i18n::msg('form_saved'));

		// Update url schemes
		if(rex_addon::get('url')->isAvailable()) {
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_machinery_url_machine_categories", $settings['article_id']);
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_machinery_url_machines", $settings['article_id']);
			if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
				d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_machinery_url_industry_sectors", $settings['article_id']);
			}
			if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
				d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent", $settings['used_machine_article_id_rent']);
				d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent", $settings['used_machine_article_id_rent']);		
				d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale", $settings['used_machine_article_id_sale']);
				d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale", $settings['used_machine_article_id_sale']);		
			}
			UrlGenerator::generatePathFile([]);
		}
		
		// Install / update language replacements
		d2u_machinery_lang_helper::factory()->install();
		if(rex_plugin::get('d2u_machinery', 'export')->isAvailable()) {
			export_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
			industry_sectors_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_agitator_extension')->isAvailable()) {
			machine_agitator_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_features_extension')->isAvailable()) {
			machine_features_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
			machine_usage_area_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
			used_machines_lang_helper::factory()->install();
		}
		
		// Install / remove Cronjob
		if(rex_plugin::get('d2u_machinery', 'export')->isAvailable()) {
			if($this->getConfig('export_autoexport') == 'active') {
				if(!export_backend_helper::autoexportIsInstalled()) {
					export_backend_helper::autoexportInstall();
				}
			}
			else {
				export_backend_helper::autoexportDelete();
			}
		}
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
				<legend><small><i class="rex-icon rex-icon-database"></i></small> <?php echo rex_i18n::msg('d2u_machinery_meta_settings'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						// Default language for translations
						if(count(rex_clang::getAll()) > 1) {
							$lang_options = array();
							foreach(rex_clang::getAll() as $rex_clang) {
								$lang_options[$rex_clang->getId()] = $rex_clang->getName();
							}
							d2u_addon_backend_helper::form_select('d2u_machinery_settings_defaultlang', 'settings[default_lang]', $lang_options, array($this->getConfig('default_lang')));
						}
						
						d2u_addon_backend_helper::form_input('d2u_machinery_settings_request_form_email', 'settings[request_form_email]', $this->getConfig('request_form_email'), TRUE, FALSE, 'email');
						d2u_addon_backend_helper::form_input('d2u_machinery_settings_contact_phone', 'settings[contact_phone]', $this->getConfig('contact_phone'), TRUE, FALSE);
						d2u_addon_backend_helper::form_mediafield('d2u_machinery_settings_consultation_pic', 'consultation_pic', $this->getConfig('consultation_pic'));
						d2u_addon_backend_helper::form_linkfield('d2u_machinery_settings_consultation_article', '2', $this->getConfig('consultation_article_id'), $this->getConfig('default_lang'));
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?php echo rex_i18n::msg('d2u_machinery_settings_lang_replacements'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						foreach(rex_clang::getAll() as $rex_clang) {
							print '<dl class="rex-form-group form-group">';
							print '<dt><label>'. $rex_clang->getName() .'</label></dt>';
							print '<dd>';
							print '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
							$replacement_options = array(
								'd2u_machinery_settings_german' => 'german',
								'd2u_machinery_settings_english' => 'english'
							);
							foreach($replacement_options as $key => $value) {
								$selected = $value == $this->getConfig('lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
								print '<option value="'. $value .'"'. $selected .'>'. rex_i18n::msg('d2u_machinery_settings_lang_replacements_install') .' '. rex_i18n::msg($key) .'</option>';
							}
							print '</select>';
							print '</dl>';
						}
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-open-category"></i></small> <?php echo rex_i18n::msg('d2u_machinery_meta_categories'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						$options = array('name' => rex_i18n::msg('d2u_machinery_name'), 'priority' => rex_i18n::msg('header_priority'));
						d2u_addon_backend_helper::form_select('d2u_machinery_settings_default_sort', 'settings[default_category_sort]', $options, array($this->getConfig('default_category_sort')));
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_categories_usage_area', 'settings[show_categories_usage_area]', 'show', $this->getConfig('show_categories_usage_area') == 'show')
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-module"></i></small> <?php echo rex_i18n::msg('d2u_machinery_meta_machines'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_linkfield('d2u_machinery_settings_article', '1', $this->getConfig('article_id'), $this->getConfig('default_lang'));
						$options = array('name' => rex_i18n::msg('d2u_machinery_name'), 'priority' => rex_i18n::msg('header_priority'));
						d2u_addon_backend_helper::form_select('d2u_machinery_settings_default_sort', 'settings[default_machine_sort]', $options, array($this->getConfig('default_machine_sort')));
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_show_teaser', 'settings[show_teaser]', 'show', $this->getConfig('show_teaser') == 'show');
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_show_tech_data', 'settings[show_techdata]', 'show', $this->getConfig('show_techdata') == 'show');
					?>
				</div>
			</fieldset>
			<?php
				if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-truck"></i></small> <?php echo rex_i18n::msg('d2u_machinery_used_machines'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_used_machines_article_rent', '3', $this->getConfig('used_machine_article_id_rent'), $this->getConfig('default_lang'));
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_used_machines_article_sale', '4', $this->getConfig('used_machine_article_id_sale'), $this->getConfig('default_lang'));
						?>
					</div>
				</fieldset>
			<?php
				}
			?>
			<?php
				if(rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-codepen"></i></small> <?php echo rex_i18n::msg('d2u_machinery_usage_areas'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
						$options_usage_area_header = array('machines' => rex_i18n::msg('d2u_machinery_usage_areas_settings_by_machines'), 'usage' => rex_i18n::msg('d2u_machinery_usage_areas_settings_by_usage_areas'));
						d2u_addon_backend_helper::form_select('d2u_machinery_usage_areas_settings_header', 'settings[usage_area_header]', $options_usage_area_header, array($this->getConfig('usage_area_header')));
						?>
					</div>
				</fieldset>
			<?php
				}
			?>
			<?php
				if(rex_plugin::get('d2u_machinery', 'export')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-cloud-upload"></i></small> <?php echo rex_i18n::msg('d2u_machinery_category_export'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_export_settings_autoexport', 'settings[export_autoexport]', 'active', $this->getConfig('export_autoexport') == 'active');
						d2u_addon_backend_helper::form_input('d2u_machinery_export_settings_email', 'settings[export_failure_email]', $this->getConfig('export_failure_email'), TRUE, FALSE, 'email');
						?>
					</div>
				</fieldset>
			<?php
				}
			?>
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