<?php

if (!\TobiasKrais\D2UMachinery\Extension::guardLegacyPage('machine_steel_processing_extension')) {
	return;
}

$steelProcessingPages = [
	'supply' => [
		'label' => rex_i18n::msg('d2u_machinery_steel_supply'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.steel_processing.supply.php'),
	],
	'material' => [
		'label' => rex_i18n::msg('d2u_machinery_steel_material_class'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.steel_processing.material.php'),
	],
	'procedure' => [
		'label' => rex_i18n::msg('d2u_machinery_steel_procedures'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.steel_processing.procedure.php'),
	],
	'process' => [
		'label' => rex_i18n::msg('d2u_machinery_steel_processes'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.steel_processing.process.php'),
	],
	'profile' => [
		'label' => rex_i18n::msg('d2u_machinery_steel_profiles'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.steel_processing.profile.php'),
	],
	'tool' => [
		'label' => rex_i18n::msg('d2u_machinery_steel_tools'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.steel_processing.tool.php'),
	],
	'welding' => [
		'label' => rex_i18n::msg('d2u_machinery_steel_welding'),
		'path' => rex_path::addon('d2u_machinery', 'pages/machine.steel_processing.welding.php'),
	],
];

$steelProcessingSubpage = rex_request('steel_processing_subpage', 'string', 'supply');
if (!isset($steelProcessingPages[$steelProcessingSubpage])) {
	$steelProcessingSubpage = 'supply';
}
$steelProcessingPageParams = ['steel_processing_subpage' => $steelProcessingSubpage];

echo \TobiasKrais\D2UMachinery\Extension::renderThirdLevelTabs($steelProcessingPages, $steelProcessingSubpage, 'steel_processing_subpage');

require $steelProcessingPages[$steelProcessingSubpage]['path'];