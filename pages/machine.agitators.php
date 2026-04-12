<?php

if (!\TobiasKrais\D2UMachinery\Extension::guardLegacyPage('machine_agitator_extension')) {
	return;
}

$agitatorPages = [
	'agitator_type' => [
		'label' => rex_i18n::msg('d2u_machinery_agitator_types'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.agitators.types.php'),
	],
	'agitator' => [
		'label' => rex_i18n::msg('d2u_machinery_agitators'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.agitators.items.php'),
	],
];

$agitatorSubpage = rex_request('agitator_subpage', 'string', 'agitator_type');
if (!isset($agitatorPages[$agitatorSubpage])) {
	$agitatorSubpage = 'agitator_type';
}
$agitatorPageParams = ['agitator_subpage' => $agitatorSubpage];

echo \TobiasKrais\D2UMachinery\Extension::renderThirdLevelTabs($agitatorPages, $agitatorSubpage, 'agitator_subpage');

require $agitatorPages[$agitatorSubpage]['path'];