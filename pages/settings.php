<?php
// save settings
if (filter_input(INPUT_POST, "btn_save") === 'save') {
	$settings = rex_post('settings', 'array', []);

	// Linkmap Link and media needs special treatment
	$link_ids = filter_input_array(INPUT_POST, ['REX_INPUT_LINK'=> ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]);

	$settings['article_id'] = is_array($link_ids) ? $link_ids["REX_INPUT_LINK"][1] : 0;

	$input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
	$input_media_list = rex_post('REX_INPUT_MEDIALIST', 'array', []);
	
	$consultation_pics = preg_grep('/^\s*$/s', explode(",", $input_media_list[1]), PREG_GREP_INVERT);
	$settings['consultation_pic'] = is_array($consultation_pics) && key_exists(0, $consultation_pics) ? $consultation_pics[0] : ''; // backward compatibility
	$settings['consultation_pics'] = $input_media_list[1];
	
	$settings['consultation_article_id'] = is_array($link_ids) ? $link_ids["REX_INPUT_LINK"][2] : 0;

	if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
		$settings['used_machine_article_id_rent'] = is_array($link_ids) ? $link_ids["REX_INPUT_LINK"][3] : 0;
		$settings['used_machine_article_id_sale'] = is_array($link_ids) ? $link_ids["REX_INPUT_LINK"][4] : 0;
	}
	if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
		$settings['industry_sectors_article_id'] = is_array($link_ids) ? $link_ids["REX_INPUT_LINK"][5] : 0;
	}
	if(rex_plugin::get('d2u_machinery', 'production_lines')->isAvailable()) {
		$settings['production_lines_article_id'] = is_array($link_ids) ? $link_ids["REX_INPUT_LINK"][6] : 0;
	}

	// Checkbox also need special treatment if empty
	$settings['lang_wildcard_overwrite'] = array_key_exists('lang_wildcard_overwrite', $settings) ? "true" : "false";
	$settings['show_categories_navi'] = array_key_exists('show_categories_navi', $settings) ? "show" : "hide";
	$settings['show_categories_usage_areas'] = array_key_exists('show_categories_usage_areas', $settings) ? "show" : "hide";
	$settings['show_machines_navi'] = array_key_exists('show_machines_navi', $settings) ? "show" : "hide";
	$settings['show_teaser'] = array_key_exists('show_teaser', $settings) ? "show" : "hide";
	$settings['show_techdata'] = array_key_exists('show_techdata', $settings) ? "show" : "hide";
	$settings['show_machine_usage_areas'] = array_key_exists('show_machine_usage_areas', $settings) ? "show" : "hide";
	if(rex_plugin::get('d2u_machinery', 'export')->isAvailable()) {
		$settings['export_autoexport'] = array_key_exists('export_autoexport', $settings) ? "active" : "inactive";
	}
	if(rex_addon::get("url")->isAvailable()) {
		$settings['short_urls'] = array_key_exists('short_urls', $settings) ? "true" : "false";
		$settings['short_urls_forward'] = array_key_exists('short_urls_forward', $settings) && $settings['short_urls'] === "true" ? "true" : "false";
	}
	$settings['google_analytics_activate'] = array_key_exists('google_analytics_activate', $settings) ? "true" : "false";
	
	// Save settings
	if(rex_config::set("d2u_machinery", $settings)) {
		echo rex_view::success(rex_i18n::msg('form_saved'));

		// Update url schemes
		if(\rex_addon::get('url')->isAvailable()) {
			d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_machine_categories", $settings['article_id']);
			d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_machines", $settings['article_id']);
			if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
				d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_industry_sectors", $settings['industry_sectors_article_id']);
			}
			if(rex_plugin::get('d2u_machinery', 'production_lines')->isAvailable()) {
				d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_production_lines", $settings['production_lines_article_id']);
			}
			if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
				d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_used_machines_rent", $settings['used_machine_article_id_rent']);
				d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_rent", $settings['used_machine_article_id_rent']);		
				d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_used_machines_sale", $settings['used_machine_article_id_sale']);
				d2u_addon_backend_helper::update_url_scheme(\rex::getTablePrefix() ."d2u_machinery_url_used_machine_categories_sale", $settings['used_machine_article_id_sale']);		
			}

			// Update forward cache - wait for packages included to prevent internal Server error
			rex_extension::register('PACKAGES_INCLUDED', function ($params) {
				rex_yrewrite_forward::generatePathFile();
		    });
		}
		
		// Install / update language replacements
		d2u_machinery_lang_helper::factory()->install();
		if(rex_plugin::get('d2u_machinery', 'equipment')->isAvailable()) {
			d2u_machinery_equipment_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'export')->isAvailable()) {
			d2u_machinery_export_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
			d2u_machinery_industry_sectors_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_agitator_extension')->isAvailable()) {
			d2u_machinery_machine_agitator_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
			d2u_machinery_machine_construction_equipment_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_features_extension')->isAvailable()) {
			d2u_machinery_machine_features_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
			d2u_machinery_machine_steel_processing_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
			d2u_machinery_machine_usage_area_extension_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'service_options')->isAvailable()) {
			d2u_machinery_service_options_lang_helper::factory()->install();
		}
		if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
			d2u_machinery_used_machines_lang_helper::factory()->install();
		}
		
		// Install / remove Cronjob
		if(rex_plugin::get('d2u_machinery', 'export')->isAvailable()) {
			$export_cronjob = d2u_machinery_export_cronjob::factory();
			if(rex_config::get('d2u_machinery', 'export_autoexport') === 'active') {
				if(!$export_cronjob->isInstalled()) {
					$export_cronjob->install();
				}
			}
			else {
				$export_cronjob->delete();
			}
		}
	}
	else {
		echo rex_view::error(rex_i18n::msg('form_save_error'));
	}
}

if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable() && (intval(rex_config::get('d2u_machinery', 'article_id')) === intval(rex_config::get('d2u_machinery', 'used_machine_article_id_rent'))
		|| intval(rex_config::get('d2u_machinery', 'article_id')) === intval(rex_config::get('d2u_machinery', 'used_machine_article_id_rent'))
		|| intval(rex_config::get('d2u_machinery', 'used_machine_article_id_rent')) === intval(rex_config::get('d2u_machinery', 'used_machine_article_id_rent')))) {
	echo rex_view::warning(rex_i18n::msg('d2u_machinery_used_machines_settings_duplicate_article_id'));
}
?>
<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_helper_settings'); ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-database"></i></small> <?php echo rex_i18n::msg('d2u_helper_settings'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_input('d2u_machinery_settings_request_form_email', 'settings[request_form_email]', strval(rex_config::get('d2u_machinery', 'request_form_email')), TRUE, FALSE, 'email');
						d2u_addon_backend_helper::form_input('d2u_machinery_settings_contact_phone', 'settings[contact_phone]', strval(rex_config::get('d2u_machinery', 'contact_phone')), TRUE, FALSE);
						$consultation_pics = rex_config::get('d2u_machinery', 'consultation_pics','') !== '' ? preg_grep('/^\s*$/s', explode(",", strval(rex_config::get('d2u_machinery', 'consultation_pics'))), PREG_GREP_INVERT) : (rex_config::get('d2u_machinery', 'consultation_pic', '') !== '' ? [rex_config::get('d2u_machinery', 'consultation_pic')] : []);
						d2u_addon_backend_helper::form_medialistfield('d2u_machinery_settings_consultation_pics', 1, is_array($consultation_pics) ? $consultation_pics : [], FALSE);
						d2u_addon_backend_helper::form_linkfield('d2u_machinery_settings_consultation_article', '2', intval(rex_config::get('d2u_machinery', 'consultation_article_id')), intval(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId())));
						if(rex_addon::get("url")->isAvailable()) {
							d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_short_urls', 'settings[short_urls]', 'true', rex_config::get('d2u_machinery', 'short_urls') === 'true');
							d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_short_urls_forward', 'settings[short_urls_forward]', 'true', rex_config::get('d2u_machinery', 'short_urls_forward') === 'true');
							?>
							<script>
								function changeTypeShortURLs() {
									if($('input[name="settings\\[short_urls\\]"]').is(':checked')) {
										$('#settings\\[short_urls_forward\\]').fadeIn();
									}
									else {
										$('#settings\\[short_urls_forward\\]').hide();
									}
								}

								// On init
								changeTypeShortURLs();
								// On change
								$('input[name="settings\\[short_urls\\]"]').on('change', function() {
									changeTypeShortURLs();
								});
							</script>
							<?php
						}
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?php echo rex_i18n::msg('d2u_helper_lang_replacements'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_checkbox('d2u_helper_lang_wildcard_overwrite', 'settings[lang_wildcard_overwrite]', 'true', rex_config::get('d2u_machinery', 'lang_wildcard_overwrite') === 'true');
						foreach(rex_clang::getAll() as $rex_clang) {
							print '<dl class="rex-form-group form-group">';
							print '<dt><label>'. $rex_clang->getName() .'</label></dt>';
							print '<dd>';
							print '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
							$replacement_options = [
								'd2u_helper_lang_english' => 'english',
								'd2u_helper_lang_chinese' => 'chinese',
								'd2u_helper_lang_czech' => 'czech',
								'd2u_helper_lang_dutch' => 'dutch',
								'd2u_helper_lang_french' => 'french',
								'd2u_helper_lang_german' => 'german',
								'd2u_helper_lang_italian' => 'italian',
								'd2u_helper_lang_polish' => 'polish',
								'd2u_helper_lang_portuguese' => 'portuguese',
								'd2u_helper_lang_russian' => 'russian',
								'd2u_helper_lang_spanish' => 'spanish',
							];
							foreach($replacement_options as $key => $value) {
								$selected = $value === rex_config::get('d2u_machinery', 'lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
								print '<option value="'. $value .'"'. $selected .'>'. rex_i18n::msg('d2u_helper_lang_replacements_install') .' '. rex_i18n::msg($key) .'</option>';
							}
							print '</select>';
							print '</dl>';
						}
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-open-category"></i></small> <?php echo rex_i18n::msg('d2u_helper_categories'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						$options_category_sort = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
						d2u_addon_backend_helper::form_select('d2u_helper_sort', 'settings[default_category_sort]', $options_category_sort, [strval(rex_config::get('d2u_machinery', 'default_category_sort'))]);
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_show_teaser', 'settings[show_teaser]', 'show', rex_config::get('d2u_machinery', 'show_teaser') === 'show');
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_categories_navi', 'settings[show_categories_navi]', 'show', rex_config::get('d2u_machinery', 'show_categories_navi') === 'show');
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-module"></i></small> <?php echo rex_i18n::msg('d2u_machinery_meta_machines'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_linkfield('d2u_machinery_settings_article', '1', intval(rex_config::get('d2u_machinery', 'article_id')), intval(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId())));
						$options_machine_sort = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
						d2u_addon_backend_helper::form_select('d2u_helper_sort', 'settings[default_machine_sort]', $options_machine_sort, [strval(rex_config::get('d2u_machinery', 'default_machine_sort'))]);
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_show_tech_data', 'settings[show_techdata]', 'show', rex_config::get('d2u_machinery', 'show_techdata') === 'show');
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_machines_navi', 'settings[show_machines_navi]', 'show', rex_config::get('d2u_machinery', 'show_machines_navi') === 'show');
					?>
				</div>
			</fieldset>
			<?php
				if(rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-industry"></i></small> <?php echo rex_i18n::msg('d2u_machinery_industry_sectors'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_industry_sectors_article', '5', intval(rex_config::get('d2u_machinery', 'industry_sectors_article_id')), intval(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId())));
						?>
					</div>
				</fieldset>
			<?php
				}
				if(rex_plugin::get('d2u_machinery', 'production_lines')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-arrows-h"></i></small> <?php echo rex_i18n::msg('d2u_machinery_production_lines'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_production_lines_article', '6', intval(rex_config::get('d2u_machinery', 'production_lines_article_id')), intval(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId())));
						?>
					</div>
				</fieldset>
			<?php
				}
				if(rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-truck"></i></small> <?php echo rex_i18n::msg('d2u_machinery_used_machines'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_used_machines_article_rent', '3', intval(rex_config::get('d2u_machinery', 'used_machine_article_id_rent')), intval(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId())));
							d2u_addon_backend_helper::form_linkfield('d2u_machinery_used_machines_article_sale', '4', intval(rex_config::get('d2u_machinery', 'used_machine_article_id_sale')), intval(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId())));
							$options_used_machines_show_pics = ['slider' => rex_i18n::msg('d2u_machinery_used_machines_pic_type_slider'), 'lightbox' => rex_i18n::msg('d2u_machinery_used_machines_pic_type_lightbox')];
							d2u_addon_backend_helper::form_select('d2u_machinery_used_machines_pic_type', 'settings[used_machines_pic_type]', $options_used_machines_show_pics, [strval(rex_config::get('d2u_machinery', 'used_machines_pic_type'))]);
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
							$options_usage_area_header = ['machines' => rex_i18n::msg('d2u_machinery_usage_areas_settings_by_machines'), 'usage' => rex_i18n::msg('d2u_machinery_usage_areas_settings_by_usage_areas')];
							d2u_addon_backend_helper::form_select('d2u_machinery_usage_areas_settings_header', 'settings[usage_area_header]', $options_usage_area_header, [strval(rex_config::get('d2u_machinery', 'usage_area_header'))]);
							d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_categories_show_usage_areas', 'settings[show_categories_usage_areas]', 'show', rex_config::get('d2u_machinery', 'show_categories_usage_areas') === 'show');
							d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_machine_show_usage_areas', 'settings[show_machine_usage_areas]', 'show', rex_config::get('d2u_machinery', 'show_machine_usage_areas') === 'show');
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
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_export_settings_autoexport', 'settings[export_autoexport]', 'active', rex_config::get('d2u_machinery', 'export_autoexport') === 'active');
						d2u_addon_backend_helper::form_input('d2u_machinery_export_settings_email', 'settings[export_failure_email]', strval(rex_config::get('d2u_machinery', 'export_failure_email')), TRUE, FALSE, 'email');
						?>
					</div>
				</fieldset>
			<?php
				}
			?>
			<fieldset>
				<legend><small><i class="rex-icon fa-google"></i></small> <?php echo rex_i18n::msg('d2u_machinery_settings_analytics'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_checkbox('d2u_machinery_settings_analytics_activate', 'settings[google_analytics_activate]', 'true', rex_config::get('d2u_machinery', 'google_analytics_activate') === 'true');
						d2u_addon_backend_helper::form_input('d2u_machinery_settings_analytics_event_category', 'settings[analytics_event_category]', strval(rex_config::get('d2u_machinery', 'analytics_event_category')), FALSE, FALSE, 'text');
						d2u_addon_backend_helper::form_input('d2u_machinery_settings_analytics_event_action', 'settings[analytics_event_action]', strval(rex_config::get('d2u_machinery', 'analytics_event_action')), FALSE, FALSE, 'text');
					?>
					<script>
						function changeType() {
							if($('input[name="settings\\[google_analytics_activate\\]"]').is(':checked')) {
								$('#settings\\[analytics_event_category\\]').fadeIn();
								$('#settings\\[analytics_event_action\\]').fadeIn();
							}
							else {
								$('#settings\\[analytics_event_category\\]').hide();
								$('#settings\\[analytics_event_action\\]').hide();
							}
						}

						// On init
						changeType();
						// On change
						$('input[name="settings\\[google_analytics_activate\\]"]').on('change', function() {
							changeType();
						});
					</script>
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
<?php
	print d2u_addon_backend_helper::getCSS();
	print d2u_addon_backend_helper::getJS();
	print d2u_addon_backend_helper::getJSOpenAll();