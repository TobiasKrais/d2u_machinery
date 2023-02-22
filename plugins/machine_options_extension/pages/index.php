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
    $option = false;
    $option_id = $form['option_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (false === $option) {
            $option = new Option($option_id, $rex_clang->getId());
            $option->option_id = $option_id; // Ensure correct ID in case first language has no object
            $option->category_ids = $form['category_ids'];
            $option->priority = $form['priority'];
            $option->pic = $input_media[1];
            if (\rex_addon::get('d2u_videos')->isAvailable() && isset($form['video_id']) && $form['video_id'] > 0) {
                $option->video = new Video($form['video_id'], (int) rex_config::get('d2u_helper', 'default_lang'));
            } else {
                $option->video = false;
            }
        } else {
            $option->clang_id = $rex_clang->getId();
        }
        $option->name = $form['lang'][$rex_clang->getId()]['name'];
        $option->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
        $option->description = $form['lang'][$rex_clang->getId()]['description'];

        if ('delete' === $option->translation_needs_update) {
            $option->delete(false);
        } elseif ($option->save()) {
            // remember id, for each database lang object needs same id
            $option_id = $option->option_id;
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
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $option) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $option->option_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $option_id = $entry_id;
    if (0 === $option_id) {
        $form = rex_post('form', 'array', []);
        $option_id = $form['option_id'];
    }
    $option = new Option($option_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $option->option_id = $option_id; // Ensure correct ID in case language has no object

    // Check if object is used
    $referring_machines = $option->getReferringMachines();

    // If not used, delete
    if (0 === count($referring_machines)) {
        $option->delete(true);
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
// Delete unused
elseif ('delete_unused' === $func) {
    $options = Option::getUnused();
    if (is_array($options)) {
        foreach ($options as $option) {
            $option->delete(true);
        }

        echo rex_view::success(count($options) .' ' . rex_i18n::msg('d2u_machinery_options_delete_unused_success') . $message);
    }

    $func = '';
}

// Eingabeformular
if ('edit' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_machinery_options') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[option_id]" value="<?= $entry_id ?>">
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $option = new Option($entry_id, $rex_clang->getId());
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
                                    d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$option->translation_needs_update], 1, false, $readonly_lang);
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
                                    d2u_addon_backend_helper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $option->name, $required, $readonly_lang, 'text');
                                    d2u_addon_backend_helper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description]', $option->description, 10, false, $readonly_lang, true);
                                ?>
							</div>
						</div>
					</fieldset>
				<?php
                    }
                ?>
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_data_all_lang') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            // Do not use last object from translations, because you don't know if it exists in DB
                            $option = new Option($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            $readonly = true;
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                $readonly = false;
                            }

                            d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $option->priority, true, $readonly, 'number');
                            d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $option->pic, $readonly);

                            $options = [];
                            foreach (Category::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $category) {
                                $options[$category->category_id] = $category->name;
                            }
                            d2u_addon_backend_helper::form_select('d2u_machinery_options_categories', 'form[category_ids][]', $options, $option->category_ids, 10, true, $readonly);

                            if (\rex_addon::get('d2u_videos')->isAvailable()) {
                                $options_video = [0 => rex_i18n::msg('d2u_machinery_video_no')];
                                foreach (Video::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $video) {
                                    $options_video[$video->video_id] = $video->name;
                                }
                                d2u_addon_backend_helper::form_select('d2u_machinery_video', 'form[video_id]', $options_video, false !== $option->video ? [$option->video instanceof Video ? $option->video->video_id : 0] : [], 1, false, $readonly);
                            }
                        ?>
					</div>
				</fieldset>
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
    $query = 'SELECT options.option_id, name, category_ids, priority '
        . 'FROM '. \rex::getTablePrefix() .'d2u_machinery_options AS options '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_options_lang AS lang '
            . 'ON options.option_id = lang.option_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
        . 'ORDER BY name, priority ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon  fa-plug"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###option_id###']);

    $list->setColumnLabel('option_id', rex_i18n::msg('id'));
    $list->setColumnLayout('option_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###option_id###']);

    $list->setColumnLabel('category_ids', rex_i18n::msg('d2u_helper_categories'));
    $list->setColumnFormat('category_ids', 'custom', static function ($params) {
        $list_params = $params['list'];
        $cat_names = [];
        $category_ids = preg_grep('/^\s*$/s', explode('|', $list_params->getValue('category_ids')), PREG_GREP_INVERT);
        if (is_array($category_ids)) {
            foreach ($category_ids as $category_id) {
                $category = new Category($category_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                if ('' !== $category->name) {
                    $cat_names[] = $category->name;
                }
            }
        }
        return implode(', ', $cat_names);
    });

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###option_id###']);

    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###option_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_options_no_options_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_options'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');

    // Delete unused options
    if (false !== Option::getUnused()) {
        echo '<p><a href="'. rex_url::currentBackendPage(['func' => 'delete_unused'], false) .'"><button class="btn btn-save">'. rex_i18n::msg('d2u_machinery_options_delete_unused') .'</button></a></p><br><br>';
    }
}
