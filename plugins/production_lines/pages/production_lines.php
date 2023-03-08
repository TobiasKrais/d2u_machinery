<?php
$func = rex_request('func', 'string');
$entry_id = (int) rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// messages
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

// save settings
if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
    $form = rex_post('form', 'array', []);

    // Media fields and links need special treatment
    $input_media_list = rex_post('REX_INPUT_MEDIALIST', 'array', []);
    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

    $success = true;
    $production_line = false;
    $production_line_id = $form['production_line_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (false === $production_line) {
            $production_line = new ProductionLine($production_line_id, $rex_clang->getId());
            $production_line->production_line_id = $production_line_id; // Ensure correct ID in case first language has no object
            $production_line->complementary_machine_ids = $form['complementary_machine_ids'] ?? [];
            $production_line->industry_sector_ids = $form['industry_sector_ids'] ?? [];
            $production_line->line_code = $form['line_code'];
            $production_line->machine_ids = $form['machine_ids'] ?? [];
            $pictures = preg_grep('/^\s*$/s', explode(',', $input_media_list[1]), PREG_GREP_INVERT);
            $production_line->pictures = is_array($pictures) ? $pictures : [];
            $production_line->link_picture = $input_media[1];
            $production_line->usp_ids = $form['usp_ids'] ?? [];
            $production_line->video_ids = $form['video_ids'] ?? [];
            $production_line->online_status = '' === $form['online_status'] ? 'offline' : $form['online_status'];
            if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                $automation_supply_ids = $form['automation_supply_ids'] ?? [];
                $production_line->automation_supply_ids = [];
                foreach ($automation_supply_ids as $automation_supply_id) {
                    if ($automation_supply_id > 0) {
                        $production_line->automation_supply_ids[] = $automation_supply_id;
                    }
                }
            }
        } else {
            $production_line->clang_id = $rex_clang->getId();
        }
        $production_line->description_long = $form['lang'][$rex_clang->getId()]['description_long'];
        $production_line->description_short = $form['lang'][$rex_clang->getId()]['description_short'];
        $production_line->name = $form['lang'][$rex_clang->getId()]['name'];
        $production_line->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
        $production_line->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

        if ('delete' === $production_line->translation_needs_update) {
            $production_line->delete(false);
        } elseif ($production_line->save()) {
            // remember id, for each database lang object needs same id
            $production_line_id = $production_line->production_line_id;
        } else {
            $success = false;
        }
    }

    // message output
    $message = 'form_save_error';
    if ($success) {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $production_line) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $production_line->production_line_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $production_line_id = $entry_id;
    if (0 === $production_line_id) {
        $form = rex_post('form', 'array', []);
        $production_line_id = $form['production_line_id'];
    }
    $production_line = new ProductionLine($production_line_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $production_line->production_line_id = $production_line_id; // Ensure correct ID in case language has no object
    $production_line->delete(true);

    $func = '';
}
// Change online status of machine
elseif ('changestatus' === $func) {
    $production_line = new ProductionLine($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $production_line->production_line_id = $entry_id;
    $production_line->changeStatus();

    header('Location: '. rex_url::currentBackendPage());
    exit;
}

// Eingabeformular
if ('edit' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_machinery_production_lines') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[production_line_id]" value="<?= $entry_id ?>">
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_data_all_lang') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            // Do not use last object from translations, because you don't know if it exists in DB
                            $production_line = new ProductionLine($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            $readonly = true;
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                $readonly = false;
                            }

                            d2u_addon_backend_helper::form_input('d2u_machinery_production_lines_line_code', 'form[line_code]', $production_line->line_code, false, $readonly, 'text');
                            d2u_addon_backend_helper::form_medialistfield('d2u_helper_pictures', 1, $production_line->pictures, $readonly);
                            d2u_addon_backend_helper::form_mediafield('d2u_machinery_production_lines_link_picture', '1', $production_line->link_picture, $readonly);
                            if (\rex_addon::get('d2u_videos')->isAvailable()) {
                                $options = [];
                                foreach (Video::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $video) {
                                    $options[$video->video_id] = $video->name;
                                }
                                d2u_addon_backend_helper::form_select('d2u_machinery_category_videos', 'form[video_ids][]', $options, $production_line->video_ids, 10, true, $readonly);
                            }
                            $option_machines = [];
                            foreach (Machine::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $machine) {
                                $option_machines[$machine->machine_id] = $machine->name;
                            }
                            d2u_addon_backend_helper::form_select('d2u_machinery_meta_machines', 'form[machine_ids][]', $option_machines, $production_line->machine_ids, 10, true, $readonly);
                            d2u_addon_backend_helper::form_select('d2u_machinery_production_lines_complementary_machines', 'form[complementary_machine_ids][]', $option_machines, $production_line->complementary_machine_ids, 10, true, $readonly);
                            if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                                $options_supply = [];
                                foreach (Supply::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $supply) {
                                    $options_supply[$supply->supply_id] = $supply->priority .' - '. $supply->name .' (ID: '. $supply->supply_id .')';
                                }
                                d2u_addon_backend_helper::form_select('d2u_machinery_steel_automation_supplys', 'form[automation_supply_ids][]', $options_supply, $production_line->automation_supply_ids, 4, true, $readonly);
                            }
                            if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                                $options_industry_sectors = [];
                                foreach (IndustrySector::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $industry_sector) {
                                    $options_industry_sectors[$industry_sector->industry_sector_id] = $industry_sector->name;
                                }
                                d2u_addon_backend_helper::form_select('d2u_machinery_industry_sectors', 'form[industry_sector_ids][]', $options_industry_sectors, $production_line->industry_sector_ids, 10, true, $readonly);
                            }
                            $option_usps = [];
                            foreach (USP::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $usp) {
                                $option_usps[$usp->usp_id] = $usp->name;
                            }
                            d2u_addon_backend_helper::form_select('d2u_machinery_production_lines_usp', 'form[usp_ids][]', $option_usps, $production_line->usp_ids, 10, true, $readonly);
                            d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', 'online' === $production_line->online_status, $readonly);
                        ?>
					</div>
				</fieldset>
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $production_line = new ProductionLine($entry_id, $rex_clang->getId());
                        $required = $rex_clang->getId() === (int) (rex_config::get('d2u_helper', 'default_lang')) ? true : false;

                        $readonly_lang = true;
                        if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && \rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
                            $readonly_lang = false;
                        }
                ?>
					<fieldset>
						<legend><?= rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"' ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
                                if ($rex_clang->getId() !== (int) rex_config::get('d2u_helper', 'default_lang')) {
                                    $options_translations = [];
                                    $options_translations['yes'] = rex_i18n::msg('d2u_helper_translation_needs_update');
                                    $options_translations['no'] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
                                    $options_translations['delete'] = rex_i18n::msg('d2u_helper_translation_delete');
                                    d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$production_line->translation_needs_update], 1, false, $readonly_lang);
                                } else {
                                    echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
                                }
                            ?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?= $rex_clang->getId() ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});
							</script>
							<div id="details_clang_<?= $rex_clang->getId() ?>">
								<?php
                                    d2u_addon_backend_helper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $production_line->name, $required, $readonly_lang, 'text');
                                    d2u_addon_backend_helper::form_input('d2u_machinery_machine_teaser', 'form[lang]['. $rex_clang->getId() .'][teaser]', $production_line->teaser, false, $readonly_lang, 'text');
                                    d2u_addon_backend_helper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description_short]', $production_line->description_short, 5, false, $readonly_lang, true);
                                    d2u_addon_backend_helper::form_textarea('d2u_helper_description_long', 'form[lang]['. $rex_clang->getId() .'][description_long]', $production_line->description_long, 5, false, $readonly_lang, true);
                                ?>
							</div>
						</div>
					</fieldset>
				<?php
                    }
                ?>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?= rex_i18n::msg('form_save') ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?= rex_i18n::msg('form_apply') ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?= rex_i18n::msg('form_abort') ?></button>
						<?php
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                echo '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
                            }
                        ?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
        echo d2u_addon_backend_helper::getCSS();
        echo d2u_addon_backend_helper::getJS();
}

if ('' === $func) {
    $query = 'SELECT production_lines.production_line_id, name, line_code, online_status '
        . 'FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS production_lines '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS lang '
            . 'ON production_lines.production_line_id = lang.production_line_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
        . 'ORDER BY name ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-arrows-h"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->setColumnLabel('production_line_id', rex_i18n::msg('id'));
    $list->setColumnLayout('production_line_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->setColumnLabel('line_code', rex_i18n::msg('d2u_machinery_production_lines_line_code'));
    $list->setColumnParams('line_code', ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->removeColumn('online_status');
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###production_line_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
        $list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###production_line_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_production_lines_no_production_lines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_production_lines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
