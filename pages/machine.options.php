<?php

$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

if ('' !== $message) {
	echo rex_view::success(rex_i18n::msg($message));
}

if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
	$form = rex_post('form', 'array', []);
	$input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

	$success = true;
	$option = false;
	$optionId = $form['option_id'];
	foreach (rex_clang::getAll() as $rex_clang) {
		if (!$option instanceof Option) {
			$option = new Option($optionId, $rex_clang->getId());
			$option->option_id = $optionId;
			$option->category_ids = is_array($form['category_ids']) ? $form['category_ids'] : [];
			$option->priority = $form['priority'];
			$option->pic = $input_media[1];
			if (rex_addon::get('d2u_videos')->isAvailable() && isset($form['video_id']) && $form['video_id'] > 0) {
				$option->video = new \TobiasKrais\D2UVideos\Video($form['video_id'], (int) rex_config::get('d2u_helper', 'default_lang'));
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
			$optionId = $option->option_id;
		} else {
			$success = false;
		}
	}

	$message = $success ? 'form_saved' : 'form_save_error';
	if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $option) {
		header('Location: '. rex_url::currentBackendPage(['entry_id' => $option->option_id, 'func' => 'edit', 'message' => $message], false));
	} else {
		header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
	}
	exit;
}
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
	$optionId = $entry_id;
	if (0 === $optionId) {
		$form = rex_post('form', 'array', []);
		$optionId = $form['option_id'];
	}
	$option = new Option($optionId, (int) rex_config::get('d2u_helper', 'default_lang'));
	$option->option_id = $optionId;
	$referringMachines = $option->getReferringMachines();

	if (0 === count($referringMachines)) {
		$option->delete(true);
	} else {
		$message = '<ul>';
		foreach ($referringMachines as $referringMachine) {
			$message .= '<li><a href="index.php?page=d2u_machinery/machine/machine&func=edit&entry_id='. $referringMachine->machine_id .'">'. $referringMachine->name.'</a></li>';
		}
		$message .= '</ul>';
		echo rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}
	$func = '';
} elseif ('delete_unused' === $func) {
	$options = Option::getUnused();
	if (is_array($options)) {
		foreach ($options as $option) {
			$option->delete(true);
		}
		echo rex_view::success(count($options) .' ' . rex_i18n::msg('d2u_machinery_options_delete_unused_success') . $message);
	}
	$func = '';
}

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
				        $required = $rex_clang->getId() === (int) rex_config::get('d2u_helper', 'default_lang');
				        $readonlyLang = true;
				        if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
				            $readonlyLang = false;
				        }
				?>
					<fieldset>
						<legend><?= rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"' ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
							    if ($rex_clang->getId() !== (int) rex_config::get('d2u_helper', 'default_lang')) {
							        $optionsTranslations = [];
							        $optionsTranslations['yes'] = rex_i18n::msg('d2u_helper_translation_needs_update');
							        $optionsTranslations['no'] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
							        $optionsTranslations['delete'] = rex_i18n::msg('d2u_helper_translation_delete');
							        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $optionsTranslations, [$option->translation_needs_update], 1, false, $readonlyLang);
							    } else {
							        echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
							    }
							?>
							<script>
								$(document).ready(function() {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});
								$("select[name='form[lang][<?= $rex_clang->getId() ?>][translation_needs_update]']").on('change', function() {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});
							</script>
							<div id="details_clang_<?= $rex_clang->getId() ?>">
								<?php
								    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $option->name, $required, $readonlyLang, 'text');
								    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description]', $option->description, 10, false, $readonlyLang, true);
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
						    $option = new Option($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
						    $readonly = true;
						    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
						        $readonly = false;
						    }
						    \TobiasKrais\D2UHelper\BackendHelper::form_input('header_priority', 'form[priority]', $option->priority, true, $readonly, 'number');
						    \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_helper_picture', '1', $option->pic, $readonly);
						    $options = [];
						    foreach (Category::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $category) {
						        $options[$category->category_id] = $category->name;
						    }
						    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_options_categories', 'form[category_ids][]', $options, $option->category_ids, 10, true, $readonly);
						    if (rex_addon::get('d2u_videos')->isAvailable()) {
						        $optionsVideo = [0 => rex_i18n::msg('d2u_machinery_video_no')];
						        foreach (TobiasKrais\D2UVideos\Video::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $video) {
						            $optionsVideo[$video->video_id] = $video->name;
						        }
						        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_video', 'form[video_id]', $optionsVideo, false !== $option->video ? [$option->video instanceof TobiasKrais\D2UVideos\Video ? $option->video->video_id : 0] : [], 1, false, $readonly);
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
						    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
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
	$query = 'SELECT options.option_id, name, category_ids, priority '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_options AS options '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_options_lang AS lang '
			. 'ON options.option_id = lang.option_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
		. 'ORDER BY name, priority ASC';
	$list = rex_list::factory($query, 1000);
	$list->addTableAttribute('class', 'table-striped table-hover');
	$tdIcon = '<i class="rex-icon  fa-plug"></i>';
	$thIcon = '';
	if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
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
		$listParams = $params['list'];
		$catNames = [];
		$categoryIds = preg_grep('/^\s*$/s', explode('|', $listParams->getValue('category_ids')), PREG_GREP_INVERT);
		if (is_array($categoryIds)) {
			foreach ($categoryIds as $categoryId) {
				$category = new Category($categoryId, (int) rex_config::get('d2u_helper', 'default_lang'));
				if ('' !== $category->name) {
					$catNames[] = $category->name;
				}
			}
		}
		return implode(', ', $catNames);
	});
	$list->setColumnLabel('priority', rex_i18n::msg('header_priority'));
	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
	$list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
	$list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###option_id###']);
	if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
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
	if (false !== Option::getUnused()) {
		echo '<p><a href="'. rex_url::currentBackendPage(['func' => 'delete_unused'], false) .'"><button class="btn btn-save">'. rex_i18n::msg('d2u_machinery_options_delete_unused') .'</button></a></p><br><br>';
	}
}