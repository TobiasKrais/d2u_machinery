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
    $feature = false;
    $feature_id = $form['feature_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (false === $feature) {
            $feature = new Feature($feature_id, $rex_clang->getId());
            $feature->feature_id = $feature_id; // Ensure correct ID in case first language has no object
            $feature->category_ids = is_array($form['category_ids']) ? array_map('intval', $form['category_ids']) : [];
            $feature->priority = $form['priority'];
            $feature->pic = $input_media[1];
            if (\rex_addon::get('d2u_videos')->isAvailable() && isset($form['video_id']) && $form['video_id'] > 0) {
                $feature->video = new \TobiasKrais\D2UVideos\Video($form['video_id'], (int) rex_config::get('d2u_helper', 'default_lang'));
            } else {
                $feature->video = false;
            }
        } else {
            $feature->clang_id = $rex_clang->getId();
        }
        $feature->name = $form['lang'][$rex_clang->getId()]['name'];
        $feature->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
        $feature->description = $form['lang'][$rex_clang->getId()]['description'];

        if ('delete' === $feature->translation_needs_update) {
            $feature->delete(false);
        } elseif ($feature->save()) {
            // remember id, for each database lang object needs same id
            $feature_id = $feature->feature_id;
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
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $feature) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $feature->feature_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $feature_id = $entry_id;
    if (0 === $feature_id) {
        $form = rex_post('form', 'array', []);
        $feature_id = $form['feature_id'];
    }
    $feature = new Feature($feature_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $feature->feature_id = $feature_id; // Ensure correct ID in case language has no object

    // Check if object is used
    $referring_machines = $feature->getReferringMachines();

    // If not used, delete
    if (0 === count($referring_machines)) {
        $feature->delete(true);
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
    $features = Feature::getUnused();
    if (is_array($features)) {
        foreach ($features as $feature) {
            $feature->delete(true);
        }

        echo rex_view::success(count($features) .' ' . rex_i18n::msg('d2u_machinery_features_delete_unused_success') . $message);
    }

    $func = '';
}

// Eingabeformular
if ('edit' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_machinery_features') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[feature_id]" value="<?= $entry_id ?>">
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $feature = new Feature($entry_id, $rex_clang->getId());
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
                                    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$feature->translation_needs_update], 1, false, $readonly_lang);
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
                                    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $feature->name, $required, $readonly_lang, 'text');
                                    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description]', $feature->description, 10, false, $readonly_lang, true);
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
                            $feature = new Feature($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            $readonly = true;
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                $readonly = false;
                            }

                            \TobiasKrais\D2UHelper\BackendHelper::form_input('header_priority', 'form[priority]', $feature->priority, true, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_helper_picture', '1', $feature->pic, $readonly);

                            $options = [];
                            foreach (Category::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $category) {
                                $options[$category->category_id] = $category->name;
                            }
                            \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_features_categories', 'form[category_ids][]', $options, $feature->category_ids, 10, true, $readonly);

                            if (\rex_addon::get('d2u_videos')->isAvailable()) {
                                $options_video = [0 => rex_i18n::msg('d2u_machinery_video_no')];
                                foreach (TobiasKrais\D2UVideos\Video::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $video) {
                                    $options_video[$video->video_id] = $video->name;
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_video', 'form[video_id]', $options_video, $feature->video instanceof TobiasKrais\D2UVideos\Video ? [$feature->video->video_id] : [], 1, false, $readonly);
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
        echo \TobiasKrais\D2UHelper\BackendHelper::getCSS();
        echo \TobiasKrais\D2UHelper\BackendHelper::getJS();
}

if ('' === $func) {
    $query = 'SELECT features.feature_id, name, category_ids, priority '
        . 'FROM '. \rex::getTablePrefix() .'d2u_machinery_features AS features '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_features_lang AS lang '
            . 'ON features.feature_id = lang.feature_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
        . 'ORDER BY name, priority ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon  fa-plug"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###feature_id###']);

    $list->setColumnLabel('feature_id', rex_i18n::msg('id'));
    $list->setColumnLayout('feature_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###feature_id###']);

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
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###feature_id###']);

    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###feature_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_features_no_features_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_features'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');

    // Delete unused features
    if (false !== Feature::getUnused()) {
        echo '<p><a href="'. rex_url::currentBackendPage(['func' => 'delete_unused'], false) .'"><button class="btn btn-save">'. rex_i18n::msg('d2u_machinery_features_delete_unused') .'</button></a></p><br><br>';
    }
}
