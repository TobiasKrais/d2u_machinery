<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', []);

	$contact = new \D2U_Machinery\Contact($form['contact_id']);
	$contact->name = $form['name'];
	$contact->picture = $input_media[1];
	$contact->phone = $form['phone'];
	$contact->email = $form['email'];

	// message output
	$message = 'form_save_error';
	if($contact->save() == 0) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $contact !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$contact->contact_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$contact_id = $entry_id;
	if($contact_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$contact_id = $form['contact_id'];
	}
	$contact = new D2U_Machinery\Contact($contact_id);
	
	// Check if object is used
	$uses_machines = $contact->getMachines();
	
	// If not used, delete
	if(count($uses_machines) == 0) {
		$contact = new D2U_Machinery\Contact($contact_id);
		$contact->delete();
	}
	else {
		$message = '<ul>';
		foreach($uses_machines as $uses_machine) {
			$message .= '<li><a href="index.php?page=d2u_machinery/machine&func=edit&entry_id='. $uses_machine->machine_id .'">'. $uses_machine->name.'</a></li>';
		}
		$message .= '</ul>';

		print rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}
	
	$func = '';
}

// Eingabeformular
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_machinery_contacts_contacts'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[contact_id]" value="<?php echo $entry_id; ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_machinery_contacts_contact'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$contact = new D2U_Machinery\Contact($entry_id);
							$readonly = TRUE;
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
								$readonly = FALSE;
							}
							
							d2u_addon_backend_helper::form_input('d2u_helper_name', 'form[name]', $contact->name, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_machinery_contacts_email', 'form[email]', $contact->email, TRUE, $readonly, 'email');
							d2u_addon_backend_helper::form_input('d2u_machinery_contacts_phone', 'form[phone]', $contact->phone, FALSE, $readonly);
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $contact->picture, $readonly);
						?>
					</div>
				</fieldset>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?php echo rex_i18n::msg('form_save'); ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?php echo rex_i18n::msg('form_apply'); ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?php echo rex_i18n::msg('form_abort'); ?></button>
						<?php
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
								print '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
							}
						?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<script>
		jQuery(document).ready(function($) {
			$('legend').each(function() {
				$(this).addClass('open');
				$(this).next('.panel-body-wrapper.slide').slideToggle();
			});
		});
	</script>
	<?php
		print d2u_addon_backend_helper::getCSS();
}

if ($func == '') {
	$query = 'SELECT contact_id, name, email '
		. 'FROM '. rex::getTablePrefix() .'d2u_machinery_contacts '
		. 'ORDER BY name ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-user"></i>';
 	$thIcon = "";
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###contact_id###']);

    $list->setColumnLabel('contact_id', rex_i18n::msg('id'));
    $list->setColumnLayout('contact_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###contact_id###']);

    $list->setColumnLabel('email', rex_i18n::msg('d2u_machinery_contacts_email'));
    $list->setColumnParams('email', ['func' => 'edit', 'entry_id' => '###contact_id###']);

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###contact_id###']);

	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_machinery[edit_data]')) {
		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###contact_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_contacts_no_contacts_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_contacts'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}