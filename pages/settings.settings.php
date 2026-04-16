<?php

use TobiasKrais\D2UHelper\BackendHelper;
// save settings
if ('save' === filter_input(INPUT_POST, 'btn_save')) {
    $settings = rex_post('settings', 'array', []);
    $previousExtensionStates = \TobiasKrais\D2UMachinery\Extension::getStates();
    $extensionChanges = [
        'activated' => [],
        'deactivated' => [],
        'normalized' => $previousExtensionStates,
    ];

    // Linkmap Link and media needs special treatment
    $link_ids = filter_input_array(INPUT_POST, ['REX_INPUT_LINK' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]);

    $settings['article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][1] : 0;

    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
    $input_media_list = rex_post('REX_INPUT_MEDIALIST', 'array', []);

    $consultation_pics = preg_grep('/^\s*$/s', explode(',', $input_media_list[1]), PREG_GREP_INVERT);
    $settings['consultation_pic'] = is_array($consultation_pics) && array_key_exists(0, $consultation_pics) ? $consultation_pics[0] : ''; // backward compatibility
    $settings['consultation_pics'] = $input_media_list[1];

    $settings['consultation_article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][2] : 0;

    $requestedExtensionStates = [];

    if (\TobiasKrais\D2UMachinery\Extension::isActive('used_machines')) {
        $settings['used_machine_article_id_rent'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][3] : 0;
        $settings['used_machine_article_id_sale'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][4] : 0;
    }
    if (\TobiasKrais\D2UMachinery\Extension::isActive('industry_sectors')) {
        $settings['industry_sectors_article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][5] : 0;
    }
    if (\TobiasKrais\D2UMachinery\Extension::isActive('production_lines')) {
        $settings['production_lines_article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][6] : 0;
    }

    // Checkbox also need special treatment if empty
    $settings['lang_wildcard_overwrite'] = array_key_exists('lang_wildcard_overwrite', $settings) ? 'true' : 'false';
    $settings['show_categories_navi'] = array_key_exists('show_categories_navi', $settings) ? 'show' : 'hide';
    $settings['show_categories_usage_areas'] = array_key_exists('show_categories_usage_areas', $settings) ? 'show' : 'hide';
    $settings['show_machines_navi'] = array_key_exists('show_machines_navi', $settings) ? 'show' : 'hide';
    $settings['show_teaser'] = array_key_exists('show_teaser', $settings) ? 'show' : 'hide';
    $settings['show_techdata'] = array_key_exists('show_techdata', $settings) ? 'show' : 'hide';
    $settings['show_machine_usage_areas'] = array_key_exists('show_machine_usage_areas', $settings) ? 'show' : 'hide';
    if (rex_addon::get('url')->isAvailable()) {
        $settings['short_urls'] = array_key_exists('short_urls', $settings) ? 'true' : 'false';
        $settings['short_urls_forward'] = array_key_exists('short_urls_forward', $settings) && 'true' === $settings['short_urls'] ? 'true' : 'false';
    }
    $settings['google_analytics_activate'] = array_key_exists('google_analytics_activate', $settings) ? 'true' : 'false';
    foreach (\TobiasKrais\D2UMachinery\Extension::getDefinitions() as $extensionKey => $definition) {
        $configKey = \TobiasKrais\D2UMachinery\Extension::getConfigKey($extensionKey);
        $settings[$configKey] = array_key_exists($configKey, $settings) ? \TobiasKrais\D2UMachinery\Extension::STATE_ACTIVE : \TobiasKrais\D2UMachinery\Extension::STATE_INACTIVE;
    }
    $requestedExtensionStates = \TobiasKrais\D2UMachinery\Extension::getRequestedStatesFromSettings($settings);
    if (!($requestedExtensionStates['used_machines'] ?? false)) {
        $requestedExtensionStates['export'] = false;
        $settings[\TobiasKrais\D2UMachinery\Extension::getConfigKey('export')] = \TobiasKrais\D2UMachinery\Extension::STATE_INACTIVE;
    }
    if ($requestedExtensionStates['used_machines'] ?? false) {
        $settings['used_machine_article_id_rent'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][3] : 0;
        $settings['used_machine_article_id_sale'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][4] : 0;
        $settings['used_machines_pic_type'] = $settings['used_machines_pic_type'] ?? (string) rex_config::get('d2u_machinery', 'used_machines_pic_type');
    } else {
        unset($settings['used_machine_article_id_rent'], $settings['used_machine_article_id_sale'], $settings['used_machines_pic_type']);
    }
    if ($requestedExtensionStates['industry_sectors'] ?? false) {
        $settings['industry_sectors_article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][5] : 0;
    } else {
        unset($settings['industry_sectors_article_id']);
    }
    if ($requestedExtensionStates['production_lines'] ?? false) {
        $settings['production_lines_article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][6] : 0;
    } else {
        unset($settings['production_lines_article_id']);
    }
    if ($requestedExtensionStates['machine_usage_area_extension'] ?? false) {
        $settings['usage_area_header'] = $settings['usage_area_header'] ?? (string) rex_config::get('d2u_machinery', 'usage_area_header');
        $settings['show_categories_usage_areas'] = array_key_exists('show_categories_usage_areas', $settings) ? 'show' : 'hide';
        $settings['show_machine_usage_areas'] = array_key_exists('show_machine_usage_areas', $settings) ? 'show' : 'hide';
    } else {
        unset($settings['usage_area_header'], $settings['show_categories_usage_areas'], $settings['show_machine_usage_areas']);
    }
    if ($requestedExtensionStates['export'] ?? false) {
        $settings['export_autoexport'] = array_key_exists('export_autoexport', $settings) ? 'active' : 'inactive';
        $settings['export_failure_email'] = $settings['export_failure_email'] ?? (string) rex_config::get('d2u_machinery', 'export_failure_email');
    } else {
        unset($settings['export_autoexport'], $settings['export_failure_email']);
    }

    $stateChangeSucceeded = true;
    try {
        $extensionChanges = \TobiasKrais\D2UMachinery\Extension::applyStateChanges(
            $previousExtensionStates,
            $requestedExtensionStates
        );
    } catch (Throwable $exception) {
        $stateChangeSucceeded = false;
        echo rex_view::error(rex_i18n::msg('form_save_error') .'<br>'. rex_escape($exception->getMessage()));
        $extensionChanges = [
            'activated' => [],
            'deactivated' => [],
            'normalized' => $previousExtensionStates,
        ];
    }

    // Save settings
    if ($stateChangeSucceeded && rex_config::set('d2u_machinery', $settings)) {
        echo rex_view::success(rex_i18n::msg('form_saved'));
        if (count($extensionChanges['activated']) > 0 || count($extensionChanges['deactivated']) > 0) {
            $messages = [];
            if (count($extensionChanges['activated']) > 0) {
                $messages[] = rex_i18n::msg('d2u_machinery_extensions_activated') .': ' . implode(', ', array_map(static fn ($key) => \TobiasKrais\D2UMachinery\Extension::getTitle($key), $extensionChanges['activated']));
            }
            if (count($extensionChanges['deactivated']) > 0) {
                $messages[] = rex_i18n::msg('d2u_machinery_extensions_deactivated') .': ' . implode(', ', array_map(static fn ($key) => \TobiasKrais\D2UMachinery\Extension::getTitle($key), $extensionChanges['deactivated']));
            }
            echo rex_view::info(implode('<br>', $messages));
        }

        // Update url schemes
        if (\rex_addon::get('url')->isAvailable()) {
            BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_machine_categories', $settings['article_id']);
            BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_machines', $settings['article_id']);
            if (\TobiasKrais\D2UMachinery\Extension::isActive('industry_sectors')) {
                BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_industry_sectors', $settings['industry_sectors_article_id']);
            }
            if (\TobiasKrais\D2UMachinery\Extension::isActive('production_lines')) {
                BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_production_lines', $settings['production_lines_article_id']);
            }
            if (\TobiasKrais\D2UMachinery\Extension::isActive('used_machines')) {
                BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_used_machines_rent', $settings['used_machine_article_id_rent']);
                BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_rent', $settings['used_machine_article_id_rent']);
                BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_used_machines_sale', $settings['used_machine_article_id_sale']);
                BackendHelper::update_url_scheme(\rex::getTablePrefix() .'d2u_machinery_url_used_machine_categories_sale', $settings['used_machine_article_id_sale']);
            }

            // Update forward cache - wait for packages included to prevent internal Server error
            rex_extension::register('PACKAGES_INCLUDED', static function ($params) {
                rex_yrewrite_forward::generatePathFile();
            });
        }

        // Install / update language replacements
        \TobiasKrais\D2UMachinery\LangHelper::factory()->install();

        // Install / remove Cronjob
        if (\TobiasKrais\D2UMachinery\Extension::isActive('export')) {
            $export_cronjob = \TobiasKrais\D2UMachinery\ExportCronjob::factory();
            if ('active' === rex_config::get('d2u_machinery', 'export_autoexport')) {
                if (!$export_cronjob->isInstalled()) {
                    $export_cronjob->install();
                }
            } else {
                $export_cronjob->delete();
            }
        }
    } elseif ($stateChangeSucceeded) {
        echo rex_view::error(rex_i18n::msg('form_save_error'));
    }
}

if (\TobiasKrais\D2UMachinery\Extension::isActive('used_machines') && ((int) rex_config::get('d2u_machinery', 'article_id') === (int) rex_config::get('d2u_machinery', 'used_machine_article_id_rent')
        || (int) rex_config::get('d2u_machinery', 'article_id') === (int) rex_config::get('d2u_machinery', 'used_machine_article_id_rent')
        || (int) rex_config::get('d2u_machinery', 'used_machine_article_id_rent') === (int) rex_config::get('d2u_machinery', 'used_machine_article_id_rent'))) {
    echo rex_view::warning(rex_i18n::msg('d2u_machinery_used_machines_settings_duplicate_article_id'));
}
?>
<form action="<?= rex_url::currentBackendPage() ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_helper_settings') ?></div></header>
		<div class="panel-body">
            <fieldset>
                <legend><small><i class="rex-icon fa-puzzle-piece"></i></small> <?= rex_i18n::msg('d2u_machinery_extensions') ?></legend>
                <div class="panel-body-wrapper slide">
                    <p><?= rex_i18n::msg('d2u_machinery_extensions_notice') ?></p>
                    <p><strong><?= rex_i18n::msg('d2u_machinery_extensions_machine_fields') ?></strong></p>
                    <?php
                        $machineFieldExtensions = [
                            'machine_steel_automation_extension',
                            'machine_features_extension',
                            'machine_options_extension',
                            'service_options',
                            'equipment',
                            'machine_usage_area_extension',
                            'machine_agitator_extension',
                            'machine_steel_processing_extension',
                            'machine_certificates_extension',
                            'machine_construction_equipment_extension',
                        ];
                        $additionalExtensions = [
                            'contacts',
                            'industry_sectors',
                            'production_lines',
                            'used_machines',
                            'export',
                        ];
                        $extensionsWithSettings = ['export', 'industry_sectors', 'production_lines', 'used_machines', 'machine_usage_area_extension'];
                        $exportSettingsEnabled = \TobiasKrais\D2UMachinery\Extension::isActive('used_machines') && \TobiasKrais\D2UMachinery\Extension::isActive('export');
                        $renderExtensionToggle = static function (string $extensionKey) use ($extensionsWithSettings, $exportSettingsEnabled): void {
                            $configKey = \TobiasKrais\D2UMachinery\Extension::getConfigKey($extensionKey);

                            if (in_array($extensionKey, $extensionsWithSettings, true)) {
                                $rowId = 'settings['. $configKey .']';
                                if ('export' === $extensionKey) {
                                    $rowId = 'd2u-machinery-extension-export';
                                }
                                echo '<dl class="rex-form-group form-group" id="'. $rowId .'">';
                                echo '<dt><input class="form-control d2u_helper_toggle" type="checkbox" name="settings['. $configKey .']" value="'. \TobiasKrais\D2UMachinery\Extension::STATE_ACTIVE .'"'. (\TobiasKrais\D2UMachinery\Extension::isActive($extensionKey) ? ' checked="checked"' : '') .' /></dt>';
                                echo '<dd><label>'. rex_i18n::msg(\TobiasKrais\D2UMachinery\Extension::getTitleKey($extensionKey)) .'</label>';
                                echo '<div id="d2u-machinery-'. $extensionKey .'-settings" style="padding: 8px 0 0 28px;">';
                                if ('export' === $extensionKey) {
                                    BackendHelper::form_checkbox('d2u_machinery_export_settings_autoexport', 'settings[export_autoexport]', 'active', 'active' === rex_config::get('d2u_machinery', 'export_autoexport'));
                                    BackendHelper::form_input('d2u_machinery_export_settings_email', 'settings[export_failure_email]', (string) rex_config::get('d2u_machinery', 'export_failure_email'), $exportSettingsEnabled, false, 'email');
                                } elseif ('industry_sectors' === $extensionKey) {
                                    BackendHelper::form_linkfield('d2u_machinery_industry_sectors_article', '5', (int) rex_config::get('d2u_machinery', 'industry_sectors_article_id'), (int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));
                                } elseif ('production_lines' === $extensionKey) {
                                    BackendHelper::form_linkfield('d2u_machinery_production_lines_article', '6', (int) rex_config::get('d2u_machinery', 'production_lines_article_id'), (int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));
                                } elseif ('used_machines' === $extensionKey) {
                                    BackendHelper::form_linkfield('d2u_machinery_used_machines_article_rent', '3', (int) rex_config::get('d2u_machinery', 'used_machine_article_id_rent'), (int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));
                                    BackendHelper::form_linkfield('d2u_machinery_used_machines_article_sale', '4', (int) rex_config::get('d2u_machinery', 'used_machine_article_id_sale'), (int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));
                                    $options_used_machines_show_pics = ['slider' => rex_i18n::msg('d2u_machinery_used_machines_pic_type_slider'), 'lightbox' => rex_i18n::msg('d2u_machinery_used_machines_pic_type_lightbox')];
                                    BackendHelper::form_select('d2u_machinery_used_machines_pic_type', 'settings[used_machines_pic_type]', $options_used_machines_show_pics, [(string) rex_config::get('d2u_machinery', 'used_machines_pic_type')]);
                                } elseif ('machine_usage_area_extension' === $extensionKey) {
                                    $optionsUsageAreaHeader = ['machines' => rex_i18n::msg('d2u_machinery_usage_areas_settings_by_machines'), 'usage' => rex_i18n::msg('d2u_machinery_usage_areas_settings_by_usage_areas')];
                                    BackendHelper::form_select('d2u_machinery_usage_areas_settings_header', 'settings[usage_area_header]', $optionsUsageAreaHeader, [(string) rex_config::get('d2u_machinery', 'usage_area_header')]);
                                    BackendHelper::form_checkbox('d2u_machinery_settings_categories_show_usage_areas', 'settings[show_categories_usage_areas]', 'show', 'show' === rex_config::get('d2u_machinery', 'show_categories_usage_areas'));
                                    BackendHelper::form_checkbox('d2u_machinery_settings_machine_show_usage_areas', 'settings[show_machine_usage_areas]', 'show', 'show' === rex_config::get('d2u_machinery', 'show_machine_usage_areas'));
                                }
                                echo '</div>';
                                echo '</dd>';
                                echo '</dl>';

                                return;
                            }

                            BackendHelper::form_checkbox(
                                \TobiasKrais\D2UMachinery\Extension::getTitleKey($extensionKey),
                                'settings['. $configKey .']',
                                \TobiasKrais\D2UMachinery\Extension::STATE_ACTIVE,
                                \TobiasKrais\D2UMachinery\Extension::isActive($extensionKey)
                            );
                        };

                        foreach ($machineFieldExtensions as $extensionKey) {
                            $renderExtensionToggle($extensionKey);
                        }
                    ?>
                    <p><strong><?= rex_i18n::msg('d2u_machinery_extensions_additional_features') ?></strong></p>
                    <?php
                        foreach ($additionalExtensions as $extensionKey) {
                            $renderExtensionToggle($extensionKey);
                        }
                    ?>
                    <p><strong><?= rex_i18n::msg('d2u_machinery_extensions_deactivate_warning') ?></strong></p>
                    <script>
                        function setSubSettingsState(extensionKey, isEnabled) {
                            const settingsBlock = $('#d2u-machinery-' + extensionKey + '-settings');

                            settingsBlock.find('input, select, textarea, button').prop('disabled', !isEnabled);

                            if (isEnabled) {
                                settingsBlock.show();
                            } else {
                                settingsBlock.hide();
                            }
                        }

                        function toggleExtensionSubSettings(extensionKey) {
                            const checkbox = $('input[name="settings\\[extension_' + extensionKey + '\]"]');
                            setSubSettingsState(extensionKey, checkbox.is(':checked'));
                        }

                        function toggleExportExtensionVisibility() {
                            const usedMachinesCheckbox = $('input[name="settings\\[extension_used_machines\\]"]');
                            const exportRow = $('#d2u-machinery-extension-export');
                            const exportCheckbox = $('input[name="settings\\[extension_export\\]"]');
                            const isVisible = usedMachinesCheckbox.is(':checked');

                            if (isVisible) {
                                exportCheckbox.prop('disabled', false);
                                exportRow.show();
                            } else {
                                exportCheckbox.prop('checked', false);
                                exportCheckbox.prop('disabled', true);
                                setSubSettingsState('export', false);
                                exportRow.hide();
                            }

                            if (isVisible) {
                                toggleExtensionSubSettings('export');
                            }
                        }

                        ['export', 'industry_sectors', 'production_lines', 'used_machines', 'machine_usage_area_extension'].forEach(function(extensionKey) {
                            toggleExtensionSubSettings(extensionKey);
                            $('input[name="settings\\[extension_' + extensionKey + '\]"]').on('change', function() {
                                toggleExtensionSubSettings(extensionKey);
                            });
                        });

                        toggleExportExtensionVisibility();
                        $('input[name="settings\\[extension_used_machines\\]"]').on('change', function() {
                            toggleExportExtensionVisibility();
                            toggleExtensionSubSettings('used_machines');
                        });
                        $('input[name="settings\\[extension_export\\]"]').on('change', function() {
                            toggleExtensionSubSettings('export');
                        });
                    </script>
                </div>
            </fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-database"></i></small> <?= rex_i18n::msg('d2u_helper_settings') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        BackendHelper::form_input('d2u_machinery_settings_request_form_email', 'settings[request_form_email]', (string) rex_config::get('d2u_machinery', 'request_form_email'), true, false, 'email');
                        BackendHelper::form_input('d2u_machinery_settings_contact_phone', 'settings[contact_phone]', (string) rex_config::get('d2u_machinery', 'contact_phone'), true, false);
                        $consultation_pics = '' !== rex_config::get('d2u_machinery', 'consultation_pics', '') ? preg_grep('/^\s*$/s', explode(',', (string) rex_config::get('d2u_machinery', 'consultation_pics')), PREG_GREP_INVERT) : ('' !== rex_config::get('d2u_machinery', 'consultation_pic', '') ? [rex_config::get('d2u_machinery', 'consultation_pic')] : []);
                        BackendHelper::form_imagelistfield('d2u_machinery_settings_consultation_pics', 1, is_array($consultation_pics) ? $consultation_pics : [], false);
                        BackendHelper::form_linkfield('d2u_machinery_settings_consultation_article', '2', (int) rex_config::get('d2u_machinery', 'consultation_article_id'), (int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));
                        if (rex_addon::get('url')->isAvailable()) {
                            BackendHelper::form_checkbox('d2u_machinery_settings_short_urls', 'settings[short_urls]', 'true', 'true' === rex_config::get('d2u_machinery', 'short_urls'));
                            BackendHelper::form_checkbox('d2u_machinery_settings_short_urls_forward', 'settings[short_urls_forward]', 'true', 'true' === rex_config::get('d2u_machinery', 'short_urls_forward'));
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
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?= rex_i18n::msg('d2u_helper_lang_replacements') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        BackendHelper::form_checkbox('d2u_helper_lang_wildcard_overwrite', 'settings[lang_wildcard_overwrite]', 'true', 'true' === rex_config::get('d2u_machinery', 'lang_wildcard_overwrite'));
                        foreach (rex_clang::getAll() as $rex_clang) {
                            echo '<dl class="rex-form-group form-group">';
                            echo '<dt><label>'. $rex_clang->getName() .'</label></dt>';
                            echo '<dd>';
                            echo '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
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
						foreach ($replacement_options as $key => $value) {
							$selected = $value === rex_config::get('d2u_machinery', 'lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
							echo '<option value="'. $value .'"'. $selected .'>'. rex_i18n::msg('d2u_helper_lang_replacements_install') .' '. rex_i18n::msg($key) .'</option>';
						}
						echo '</select>';
						echo '</dl>';
					}
                    ?>
				</div>
			</fieldset>
            <fieldset>
                <legend><small><i class="rex-icon rex-icon-open-category"></i></small> <?= rex_i18n::msg('d2u_helper_categories') ?></legend>
                <div class="panel-body-wrapper slide">
                    <?php
                        $optionsCategorySort = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
                        BackendHelper::form_select('d2u_helper_sort', 'settings[default_category_sort]', $optionsCategorySort, [(string) rex_config::get('d2u_machinery', 'default_category_sort')]);
                        BackendHelper::form_checkbox('d2u_machinery_settings_show_teaser', 'settings[show_teaser]', 'show', 'show' === rex_config::get('d2u_machinery', 'show_teaser'));
                        BackendHelper::form_checkbox('d2u_machinery_settings_categories_navi', 'settings[show_categories_navi]', 'show', 'show' === rex_config::get('d2u_machinery', 'show_categories_navi'));
                    ?>
                </div>
            </fieldset>
            <fieldset>
                <legend><small><i class="rex-icon rex-icon-module"></i></small> <?= rex_i18n::msg('d2u_machinery_meta_machines') ?></legend>
                <div class="panel-body-wrapper slide">
                    <?php
                        BackendHelper::form_linkfield('d2u_machinery_settings_article', '1', (int) rex_config::get('d2u_machinery', 'article_id'), (int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()));
                        $optionsMachineSort = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
                        BackendHelper::form_select('d2u_helper_sort', 'settings[default_machine_sort]', $optionsMachineSort, [(string) rex_config::get('d2u_machinery', 'default_machine_sort')]);
                        BackendHelper::form_checkbox('d2u_machinery_settings_show_tech_data', 'settings[show_techdata]', 'show', 'show' === rex_config::get('d2u_machinery', 'show_techdata'));
                        BackendHelper::form_checkbox('d2u_machinery_settings_machines_navi', 'settings[show_machines_navi]', 'show', 'show' === rex_config::get('d2u_machinery', 'show_machines_navi'));
                    ?>
                </div>
            </fieldset>
			<fieldset>
				<legend><small><i class="rex-icon fa-google"></i></small> <?= rex_i18n::msg('d2u_machinery_settings_analytics') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        BackendHelper::form_checkbox('d2u_machinery_settings_analytics_activate', 'settings[google_analytics_activate]', 'true', 'true' === rex_config::get('d2u_machinery', 'google_analytics_activate'));
                        BackendHelper::form_input('d2u_machinery_settings_analytics_event_category', 'settings[analytics_event_category]', (string) rex_config::get('d2u_machinery', 'analytics_event_category'), false, false, 'text');
                        BackendHelper::form_input('d2u_machinery_settings_analytics_event_action', 'settings[analytics_event_action]', (string) rex_config::get('d2u_machinery', 'analytics_event_action'), false, false, 'text');
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
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?= rex_i18n::msg('form_save') ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>
<?php
    echo BackendHelper::getCSS();
    echo BackendHelper::getJS();
    echo BackendHelper::getJSOpenAll();
