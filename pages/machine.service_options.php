<?php

use TobiasKrais\D2UMachinery\ServiceOption;

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
	$serviceOption = false;
	$serviceOptionId = $form['service_option_id'];
	foreach (rex_clang::getAll() as $rex_clang) {
		if (false === $serviceOption) {
			$serviceOption = new ServiceOption($serviceOptionId, $rex_clang->getId());
			$serviceOption->service_option_id = $serviceOptionId;
			$serviceOption->picture = $input_media[1];
			$serviceOption->online_status = array_key_exists('online_status', $form) ? 'online' : 'offline';
		} else {
			$serviceOption->clang_id = $rex_clang->getId();
		}
		$serviceOption->name = $form['lang'][$rex_clang->getId()]['name'];
		$serviceOption->description = $form['lang'][$rex_clang->getId()]['description'];
		$serviceOption->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if ('delete' === $serviceOption->translation_needs_update) {
			$serviceOption->delete(false);
		} elseif ($serviceOption->save()) {
			$serviceOptionId = $serviceOption->service_option_id;
		} else {
			$success = false;
		}
	}

	$message = $success ? 'form_saved' : 'form_save_error';
	if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $serviceOption) {
		header('Location: '. rex_url::currentBackendPage(['entry_id' => $serviceOption->service_option_id, 'func' => 'edit', 'message' => $message], false));
	} else {
		header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
	}
	exit;
}
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
	$serviceOptionId = $entry_id;
	if (0 === $serviceOptionId) {
		$form = rex_post('form', 'array', []);
		$serviceOptionId = $form['service_option_id'];
	}
	$serviceOption = new ServiceOption($serviceOptionId, (int) rex_config::get('d2u_helper', 'default_lang'));
	$serviceOption->service_option_id = $serviceOptionId;
	$referringMachines = $serviceOption->getReferringMachines();

	if (0 === count($referringMachines)) {
		$serviceOption->delete(true);
	} else {
		$message = '<ul>';
		foreach ($referringMachines as $referringMachine) {
			$message .= '<li><a href="index.php?page=d2u_machinery/machine/machine&func=edit&entry_id='. $referringMachine->machine_id .'">'. $referringMachine->name.'</a></li>';
		}
		$message .= '</ul>';
		echo rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}

	$func = '';
} elseif ('changestatus' === $func) {
	$serviceOption = new ServiceOption($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
	$serviceOption->service_option_id = $entry_id;
	$serviceOption->changeStatus();
	header('Location: '. rex_url::currentBackendPage());
	exit;
}

if ('edit' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_machinery_service_options') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[service_option_id]" value="<?= $entry_id ?>">
				<?php
				    foreach (rex_clang::getAll() as $rex_clang) {
				        $serviceOption = new ServiceOption($entry_id, $rex_clang->getId());
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
							        \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $optionsTranslations, [$serviceOption->translation_needs_update], 1, false, $readonlyLang);
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
								    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $serviceOption->name, $required, $readonlyLang, 'text');
								    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description]', $serviceOption->description, 10, false, $readonlyLang, true);
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
						    $serviceOption = new ServiceOption($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
						    $readonly = true;
						    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
						        $readonly = false;
						    }
						    \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_helper_picture', '1', $serviceOption->picture, $readonly);
						    \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', 'online' === $serviceOption->online_status, $readonly);
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
	$query = 'SELECT service_options.service_option_id, name, online_status '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_service_options AS service_options '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_machinery_service_options_lang AS lang '
			. 'ON service_options.service_option_id = lang.service_option_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' '
		. 'ORDER BY name ASC';
	$list = rex_list::factory($query, 1000);
	$list->addTableAttribute('class', 'table-striped table-hover');
	$tdIcon = '<i class="rex-icon fa-check-square"></i>';
	$thIcon = '';
	if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
	$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
	$list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###service_option_id###']);
	$list->setColumnLabel('service_option_id', rex_i18n::msg('id'));
	$list->setColumnLayout('service_option_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);
	$list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
	$list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###service_option_id###']);
	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
	$list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
	$list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###service_option_id###']);
	$list->removeColumn('online_status');
	if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###service_option_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###service_option_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}
	$list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_service_options_no_service_options_found'));
	$fragment = new rex_fragment();
	$fragment->setVar('title', rex_i18n::msg('d2u_machinery_service_options'), false);
	$fragment->setVar('content', $list->get(), false);
	echo $fragment->parse('core/page/section.php');
}