<?php

if (!\TobiasKrais\D2UMachinery\Extension::guardLegacyPage('equipment')) {
    return;
}

$equipmentPages = [
    'equipment' => [
        'label' => rex_i18n::msg('d2u_machinery_equipments'),
        'path' => rex_path::addon('d2u_machinery', 'pages/machine.equipment.equipments.php'),
    ],
    'equipment_group' => [
        'label' => rex_i18n::msg('d2u_machinery_equipment_groups'),
        'path' => rex_path::addon('d2u_machinery', 'pages/machine.equipment.groups.php'),
    ],
];

$equipmentSubpage = rex_request('equipment_subpage', 'string', 'equipment');
if (!isset($equipmentPages[$equipmentSubpage])) {
    $equipmentSubpage = 'equipment';
}
$equipmentPageParams = ['equipment_subpage' => $equipmentSubpage];

echo \TobiasKrais\D2UMachinery\Extension::renderThirdLevelTabs($equipmentPages, $equipmentSubpage, 'equipment_subpage');

require $equipmentPages[$equipmentSubpage]['path'];