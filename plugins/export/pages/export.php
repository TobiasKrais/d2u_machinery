<?php
$func = rex_request('func', 'string');
$provider_id = rex_request('provider_id', 'int');
$used_machine_id = rex_request('used_machine_id', 'int');

/*
 * Do actions
 */
if ($func == 'online') {
	// Add to next export
	$export_used_machine = new ExportedUsedMachine($used_machine_id, $provider_id);
	$export_used_machine->addToExport();
}
else if ($func == 'offline') {
	// Remove to next export
	$export_used_machine = new ExportedUsedMachine($used_machine_id, $provider_id);
	$export_used_machine->removeFromExport();
}
else if ($func == 'all_online') {
	// Add all to next export
	ExportedUsedMachine::addAllToExport($provider_id);
}
else if ($func == 'all_offline') {
	// Remove all from next export
	ExportedUsedMachine::removeAllFromExport($provider_id);
}
else if ($func == 'export') {
	// Export
	$provider = new Provider($provider_id);
	$error = $provider->export();
	if($error != "") {
		print rex_view::error($provider->name .": ". $error);
	}
	else {
		print rex_view::success($provider->name .": ". rex_i18n::msg('d2u_machinery_export_success'));
	}
}

// Fetch providers
$providers = Provider::getAll();
$used_machines = UsedMachine::getAll(rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId()), TRUE);

print '<table class="table table-striped table-hover">';
if(count($providers) > 0) {
	print "<thead>";
	print "<tr>";
	print "<th><b>". rex_i18n::msg('d2u_machinery_machine') ."</b></th>";
	foreach ($providers as $provider) {
		print "<th><b>". $provider->name ."</b></th>";
	}
	print "</tr>";
	print "<tr>";
	print "<td>&nbsp;</td>";
	foreach ($providers as $provider) {
		print "<td>";
		if($provider->isExportPossible()) {
			print "<a href='". rex_url::currentBackendPage(array('func'=>'export', 'provider_id'=>$provider->provider_id)) ."'>"
				. "<button class='btn btn-apply'>". rex_i18n::msg('d2u_machinery_export_start') ."</button></a>";
		}
		print "</td>";
	}
	print "</tr>";
	print "<tr>";
	print "<td><b>". rex_i18n::msg('d2u_machinery_export_last_export_date') ."</b></td>";
	foreach ($providers as $provider) {
		print "<td>";
		if($provider->getLastExportTimestamp() > 0) {
			print date("d.m.Y H:i", $provider->getLastExportTimestamp()) ." ". rex_i18n::msg('d2u_machinery_export_uhr');
		}
		print "</td>";
	}
	print "</tr>";print "</thead>";
	print "<tbody>";

	// Only if used machines are available
	if(count($used_machines) > 0) {
		// Possibility to add all machines to export
		print "<tr>";
		print '<td><i>'. rex_i18n::msg('d2u_machinery_export_all_online') ."</i></td>";
		foreach ($providers as $provider) {
			print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'all_online', 'provider_id'=>$provider->provider_id))
					.'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
		}
		print "</tr>";
		// Posibility to remove all machines from export
		print "<tr>";
		print "<td><i>". rex_i18n::msg('d2u_machinery_export_all_offline') ."</i></td>";
		foreach ($providers as $provider) {
			print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'all_offline', 'provider_id'=>$provider->provider_id))
					.'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
		}
		print "</tr>";
		// How many machines are set for export?
		print "<tr>";
		print "<td><i>". rex_i18n::msg('d2u_machinery_export_number_online') ."</i></td>";
		foreach ($providers as $provider) {
			print '<td><i>'. $provider->getNumberOnlineUsedMachines() ."</i></td>";
		}
		print "</tr>";

		foreach($used_machines as $used_machine) {
			print "<tr>";
			print "<td>". $used_machine->manufacturer ." ". $used_machine->name ."</td>";
			foreach ($providers as $provider) {
				if($used_machine->offer_type == "rent" && ($provider->type == "europemachinery" || $provider->type == "machinerypark" || $provider->type == "mascus")) {
					// Rental machines cannot be exported to these providers
					print '<td class="rex-table-action"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</td>';
				}
				else {
					$exported_used_machine = new ExportedUsedMachine($used_machine->used_machine_id, $provider->provider_id);
					if($exported_used_machine->isSetForExport()) {
						print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'offline', 'provider_id'=>$provider->provider_id, 'used_machine_id'=>$used_machine->used_machine_id))
							.'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
					}
					else {
						print '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(array('func'=>'online', 'provider_id'=>$provider->provider_id, 'used_machine_id'=>$used_machine->used_machine_id))
							.'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
					}
				}
			}
			print "</tr>";
		}
	}
	print "</tbody>";
}
else {
	print '<tr><th><b>'. rex_i18n::msg('d2u_immo_export_no_providers_found') .'</b></th></tr>';
}
print "</table>";