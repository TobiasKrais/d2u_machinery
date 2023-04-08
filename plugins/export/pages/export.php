<?php

$func = rex_request('func', 'string');
$provider_id = (int) rex_request('provider_id', 'int');
$used_machine_id = rex_request('used_machine_id', 'int');

/*
 * Do actions
 */
if ('online' === $func) {
    // Add to next export
    $export_used_machine = new ExportedUsedMachine($used_machine_id, $provider_id);
    $export_used_machine->addToExport();
} elseif ('offline' === $func) {
    // Remove to next export
    $export_used_machine = new ExportedUsedMachine($used_machine_id, $provider_id);
    $export_used_machine->removeFromExport();
} elseif ('all_online' === $func) {
    // Add all to next export
    ExportedUsedMachine::addAllToExport($provider_id);
} elseif ('all_offline' === $func) {
    // Remove all from next export
    ExportedUsedMachine::removeAllFromExport($provider_id);
} elseif ('export' === $func) {
    // Export
    $provider = new Provider($provider_id);
    $error = $provider->export();
    if ('' !== $error) {
        echo rex_view::error($provider->name .': '. $error);
    } else {
        echo rex_view::success($provider->name .': '. rex_i18n::msg('d2u_machinery_export_success'));
    }
}

// Fetch providers
$providers = Provider::getAll();
$used_machines = UsedMachine::getAll((int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()), true);

echo '<table class="table table-striped table-hover">';
if (count($providers) > 0) {
    echo '<thead>';
    echo '<tr>';
    echo '<th><b>'. rex_i18n::msg('d2u_machinery_machine') .'</b></th>';
    foreach ($providers as $provider) {
        echo '<th><b>'. $provider->name .'</b></th>';
    }
    echo '</tr>';
    echo '<tr>';
    echo '<td>&nbsp;</td>';
    foreach ($providers as $provider) {
        echo '<td>';
        if ($provider->isExportPossible()) {
            echo "<a href='". rex_url::currentBackendPage(['func' => 'export', 'provider_id' => $provider->provider_id]) ."'>"
                . "<button class='btn btn-apply'>". rex_i18n::msg('d2u_machinery_export_start') .'</button></a>';
        }
        echo '</td>';
    }
    echo '</tr>';
    echo '<tr>';
    echo '<td><b>'. rex_i18n::msg('d2u_machinery_export_last_export_date') .'</b></td>';
    foreach ($providers as $provider) {
        echo '<td>';
        if ($provider->getLastExportTimestamp() > 0) {
            echo $provider->getLastExportTimestamp() .'&nbsp;'. rex_i18n::msg('d2u_machinery_export_uhr');
        }
        echo '</td>';
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Only if used machines are available
    if (count($used_machines) > 0) {
        // Possibility to add all machines to export
        echo '<tr>';
        echo '<td><i>'. rex_i18n::msg('d2u_machinery_export_all_online') .'</i></td>';
        foreach ($providers as $provider) {
            echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'all_online', 'provider_id' => $provider->provider_id])
                    .'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
        }
        echo '</tr>';
        // Posibility to remove all machines from export
        echo '<tr>';
        echo '<td><i>'. rex_i18n::msg('d2u_machinery_export_all_offline') .'</i></td>';
        foreach ($providers as $provider) {
            echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'all_offline', 'provider_id' => $provider->provider_id])
                    .'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
        }
        echo '</tr>';
        // How many machines are set for export?
        echo '<tr>';
        echo '<td><i>'. rex_i18n::msg('d2u_machinery_export_number_online') .'</i></td>';
        foreach ($providers as $provider) {
            echo '<td><i>'. $provider->getNumberOnlineUsedMachines() .'</i></td>';
        }
        echo '</tr>';

        foreach ($used_machines as $used_machine) {
            echo '<tr>';
            echo '<td>'. $used_machine->manufacturer .' '. $used_machine->name .'</td>';
            foreach ($providers as $provider) {
                if ('rent' === $used_machine->offer_type && ('europemachinery' === $provider->type || 'machinerypark' === $provider->type || 'mascus' === $provider->type)) {
                    // Rental machines cannot be exported to these providers
                    echo '<td class="rex-table-action"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</td>';
                } else {
                    $exported_used_machine = new ExportedUsedMachine($used_machine->used_machine_id, $provider->provider_id);
                    if ($exported_used_machine->isSetForExport()) {
                        echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'offline', 'provider_id' => $provider->provider_id, 'used_machine_id' => $used_machine->used_machine_id])
                            .'" class="rex-online"><i class="rex-icon rex-icon-online"></i> '. rex_i18n::msg('status_online') .'</a></td>';
                    } else {
                        echo '<td class="rex-table-action"><a href="'. rex_url::currentBackendPage(['func' => 'online', 'provider_id' => $provider->provider_id, 'used_machine_id' => $used_machine->used_machine_id])
                            .'" class="rex-offline"><i class="rex-icon rex-icon-offline"></i> '. rex_i18n::msg('status_offline') .'</a></td>';
                    }
                }
            }
            echo '</tr>';
        }
    }
    echo '</tbody>';
} else {
    echo '<tr><th><b>'. rex_i18n::msg('d2u_immo_export_no_providers_found') .'</b></th></tr>';
}
echo '</table>';
