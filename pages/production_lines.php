<?php

if (!\TobiasKrais\D2UMachinery\Extension::guardLegacyPage('production_lines')) {
    return;
}

$productionLinesPages = [
    'production_lines' => [
        'label' => rex_i18n::msg('d2u_machinery_production_lines'),
        'path' => rex_path::addon('d2u_machinery', 'pages/production_lines.lines.php'),
    ],
    'usps' => [
        'label' => rex_i18n::msg('d2u_machinery_production_lines_usp'),
        'path' => rex_path::addon('d2u_machinery', 'pages/production_lines.usps.php'),
    ],
];

$productionLinesSubpage = rex_request('production_lines_subpage', 'string', 'production_lines');
if (!isset($productionLinesPages[$productionLinesSubpage])) {
    $productionLinesSubpage = 'production_lines';
}
$productionLinesPageParams = ['production_lines_subpage' => $productionLinesSubpage];

echo \TobiasKrais\D2UMachinery\Extension::renderThirdLevelTabs($productionLinesPages, $productionLinesSubpage, 'production_lines_subpage');

require $productionLinesPages[$productionLinesSubpage]['path'];