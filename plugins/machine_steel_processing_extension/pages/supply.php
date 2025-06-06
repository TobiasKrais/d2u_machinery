<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// messages
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

// save settings
if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
    $form = rex_post('form', 'array', []);

    // Media fields and links need special treatment
    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

    $success = true;
    $supply = false;
    $supply_id = $form['supply_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (false === $supply) {
            $supply = new Supply($supply_id, $rex_clang->getId());
            $supply->supply_id = $supply_id; // Ensure correct ID in case first language has no object
            $supply->priority = $form['priority'];
            $supply->pic = $input_media[1];
            if (\rex_addon::get('d2u_videos')->isAvailable() && isset($form['video_id']) && $form['video_id'] > 0) {
                $supply->video = new \TobiasKrais\D2UVideos\Video($form['video_id'], (int) rex_config::get('d2u_helper', 'default_lang'));
            } else {
                $supply->video = false;
            }
        } else {
            $supply->clang_id = $rex_clang->getId();
        }
        $supply->name = $form['lang'][$rex_clang->getId()]['name'];
        $supply->description = $form['lang'][$rex_clang->getId()]['description'];
        $supply->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

        if ('delete' === $supply->translation_needs_update) {
            $supply->delete(false);
        } elseif ($supply->save()) {
            // remember id, for each database lang object needs same id
            $supply_id = $supply->supply_id;
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
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $supply) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $supply->supply_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $supply_id = $entry_id;
    if (0 === $supply_id) {
        $form = rex_post('form', 'array', []);
        $supply_id = $form['supply_id'];
    }
    $supply = new Supply($supply_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $supply->supply_id = $supply_id; // Ensure correct ID in case language has no object

    // Check if object is used
    $referring_machines = $supply->getReferringMachines();

    // If not used, delete
    if (0 === count($referring_machines)) {
        $supply->delete();
    } else {
        $message = '<ul>';
        foreach ($referring_machines as $referring_machine) {
            $message .= '<li><a href="index.php?page=d2u_machinery/machine&func=edit&entry_id='. $referring_machine->machine_id .'">'. $referring_machine->name.'</a></li>';
        }
        $message .= '</ul>';

        echo rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
    }

    $func = '';
}
// Change online status of machine
elseif ('changestatus' === $func) {
    $supply = new Supply($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $supply->supply_id = $entry_id; // Ensure correct ID in case language has no object
    $supply->changeStatus();

    header('Location: '. rex_url::currentBackendPage());
    exit;
}

// Eingabeformular
if ('edit' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_machinery_steel_supply') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[supply_id]" value="<?= $entry_id ?>">
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_data_all_lang') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            // Do not use last object from translations, because you don't know if it exists in DB
                            $supply = new Supply($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            $readonly = true;
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                $readonly = false;
                            }
                            \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', 'online' === $supply->online_status, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('header_priority', 'form[priority]', $supply->priority, true, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_helper_picture', '1', $supply->pic, $readonly);
                            if (\rex_addon::get('d2u_videos')->isAvailable()) {
                                $options_video = [0 => rex_i18n::msg('d2u_machinery_video_no')];
                                foreach (TobiasKrais\D2UVideos\Video::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $video) {
                                    $options_video[$video->video_id] = $video->name;
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_video', 'form[video_id]', $options_video, false !== $supply->video ? [$supply->video instanceof TobiasKrais\D2UVideos\Video ? $supply->video->video_id : 0] : [], 1, false, $readonly);
                            }
                        ?>
					</div>
				</fieldset>
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $supply = new Supply($entry_id, $rex_clang->getId());
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
                                    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$supply->translation_needs_update], 1, false, $readonly_lang);
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
                                    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $supply->name, $required, $readonly_lang, 'text');
                                    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description]', $supply->description, 5, false, $readonly_lang, true);
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
        echo \TobiasKrais\D2UHelper\BackendHelper::getCSS();
        echo \TobiasKrais\D2UHelper\BackendHelper::getJS();
}

if ('' === $func) {
    $query = 'SELECT supplys.supply_id, name, online_status, priority '
        . 'FROM '. \rex::getTablePrefix() .'d2u_machinery_steel_supply AS supplys '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_steel_supply_lang AS lang '
            . 'ON supplys.supply_id = lang.supply_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
        . 'ORDER BY priority ASC ';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-stack-overflow"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###supply_id###']);

    $list->setColumnLabel('supply_id', rex_i18n::msg('id'));
    $list->setColumnLayout('supply_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###supply_id###']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###supply_id###']);

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->removeColumn('online_status');
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###supply_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
        $list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###supply_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_steel_supply_no_supplys_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_steel_supply'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
