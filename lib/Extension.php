<?php

namespace TobiasKrais\D2UMachinery;

use rex_be_controller;
use rex_config;
use rex_plugin;
use rex_plugin_manager;

/**
 * Central management for former plugin-based addon extensions.
 */
final class Extension
{
    public const STATE_ACTIVE = 'active';
    public const STATE_INACTIVE = 'inactive';

    /**
     * @var array<string,array<string,mixed>>
     */
    private const DEFINITIONS = [
        'contacts' => [
            'config' => 'extension_contacts',
            'legacy_plugin' => 'contacts',
            'title' => 'd2u_machinery_contacts',
            'pages' => ['d2u_machinery/contacts'],
            'dependencies' => [],
        ],
        'equipment' => [
            'config' => 'extension_equipment',
            'legacy_plugin' => 'equipment',
            'title' => 'd2u_machinery_equipments',
            'pages' => ['d2u_machinery/machine/equipment'],
            'dependencies' => [],
        ],
        'industry_sectors' => [
            'config' => 'extension_industry_sectors',
            'legacy_plugin' => 'industry_sectors',
            'title' => 'd2u_machinery_industry_sectors',
            'pages' => ['d2u_machinery/industry_sectors'],
            'dependencies' => [],
        ],
        'machine_agitator_extension' => [
            'config' => 'extension_machine_agitator_extension',
            'legacy_plugin' => 'machine_agitator_extension',
            'title' => 'd2u_machinery_agitators',
            'pages' => ['d2u_machinery/machine/agitators'],
            'dependencies' => [],
        ],
        'machine_certificates_extension' => [
            'config' => 'extension_machine_certificates_extension',
            'legacy_plugin' => 'machine_certificates_extension',
            'title' => 'd2u_machinery_certificates',
            'pages' => ['d2u_machinery/machine/certificates'],
            'dependencies' => [],
        ],
        'machine_construction_equipment_extension' => [
            'config' => 'extension_machine_construction_equipment_extension',
            'legacy_plugin' => 'machine_construction_equipment_extension',
            'title' => 'd2u_machinery_construction_equipment',
            'pages' => [],
            'dependencies' => [],
        ],
        'machine_features_extension' => [
            'config' => 'extension_machine_features_extension',
            'legacy_plugin' => 'machine_features_extension',
            'title' => 'd2u_machinery_features',
            'pages' => ['d2u_machinery/machine/features'],
            'dependencies' => [],
        ],
        'machine_options_extension' => [
            'config' => 'extension_machine_options_extension',
            'legacy_plugin' => 'machine_options_extension',
            'title' => 'd2u_machinery_options',
            'pages' => ['d2u_machinery/machine/options'],
            'dependencies' => [],
        ],
        'machine_steel_automation_extension' => [
            'config' => 'extension_machine_steel_automation_extension',
            'legacy_plugin' => null,
            'title' => 'd2u_machinery_steel_automation_extension',
            'pages' => ['d2u_machinery/machine/automation'],
            'dependencies' => ['machine_steel_processing_extension'],
        ],
        'machine_steel_processing_extension' => [
            'config' => 'extension_machine_steel_processing_extension',
            'legacy_plugin' => 'machine_steel_processing_extension',
            'title' => 'd2u_machinery_machine_steel_extension',
            'pages' => ['d2u_machinery/machine/steel_processing'],
            'dependencies' => [],
        ],
        'machine_usage_area_extension' => [
            'config' => 'extension_machine_usage_area_extension',
            'legacy_plugin' => 'machine_usage_area_extension',
            'title' => 'd2u_machinery_usage_areas',
            'pages' => ['d2u_machinery/machine/usage_areas'],
            'dependencies' => [],
        ],
        'production_lines' => [
            'config' => 'extension_production_lines',
            'legacy_plugin' => 'production_lines',
            'title' => 'd2u_machinery_production_lines',
            'pages' => ['d2u_machinery/production_lines'],
            'dependencies' => ['industry_sectors'],
        ],
        'service_options' => [
            'config' => 'extension_service_options',
            'legacy_plugin' => 'service_options',
            'title' => 'd2u_machinery_service_options',
            'pages' => ['d2u_machinery/machine/service_options'],
            'dependencies' => [],
        ],
        'used_machines' => [
            'config' => 'extension_used_machines',
            'legacy_plugin' => 'used_machines',
            'title' => 'd2u_machinery_used_machines',
            'pages' => ['d2u_machinery/used_machines', 'd2u_machinery/used_machines/used_machines'],
            'dependencies' => [],
        ],
        'export' => [
            'config' => 'extension_export',
            'legacy_plugin' => 'export',
            'title' => 'd2u_machinery_export',
            'pages' => ['d2u_machinery/used_machines/export', 'd2u_machinery/used_machines/provider'],
            'dependencies' => ['used_machines'],
        ],
    ];

    /**
     * @return array<string,array<string,mixed>>
     */
    public static function getDefinitions(): array
    {
        return self::DEFINITIONS;
    }

    public static function getConfigKey(string $key): string
    {
        return (string) self::requireDefinition($key)['config'];
    }

    public static function getTitleKey(string $key): string
    {
        return (string) self::requireDefinition($key)['title'];
    }

    public static function getTitle(string $key): string
    {
        return \rex_i18n::msg(self::getTitleKey($key));
    }

    public static function getLegacyPluginName(string $key): ?string
    {
        $legacyPlugin = self::requireDefinition($key)['legacy_plugin'] ?? null;
        if (!is_string($legacyPlugin) || '' === $legacyPlugin) {
            return null;
        }

        return $legacyPlugin;
    }

    public static function isActive(string $key): bool
    {
        $configKey = self::getConfigKey($key);
        if (rex_config::has('d2u_machinery', $configKey)) {
            return self::STATE_ACTIVE === (string) rex_config::get('d2u_machinery', $configKey);
        }

        return self::isLegacyPluginInstalled($key);
    }

    /**
     * @return array<string,bool>
     */
    public static function getStates(): array
    {
        $states = [];
        foreach (array_keys(self::DEFINITIONS) as $key) {
            $states[$key] = self::isActive($key);
        }

        return $states;
    }

    /**
     * @return array<int,string>
     */
    public static function getDeactivationCascade(string $key): array
    {
        self::requireDefinition($key);

        $cascade = [];
        self::collectDeactivationCascade($key, $cascade);

        return array_keys($cascade);
    }

    /**
     * @return array<int,string>
     */
    public static function getActivationCascade(string $key): array
    {
        self::requireDefinition($key);

        $cascade = [];
        self::collectActivationCascade($key, $cascade);

        return array_keys($cascade);
    }

    public static function ensureConfigInitialized(): void
    {
        foreach (array_keys(self::DEFINITIONS) as $key) {
            $configKey = self::getConfigKey($key);
            if (!rex_config::has('d2u_machinery', $configKey)) {
                rex_config::set('d2u_machinery', $configKey, self::isLegacyPluginInstalled($key) ? self::STATE_ACTIVE : self::STATE_INACTIVE);
            }
        }
    }

    public static function migrateLegacyStates(?string $fromVersion = null): void
    {
        $hadExtensionConfig = self::hasAnyExtensionConfig();
        self::ensureConfigInitialized();

        if (
            ((null !== $fromVersion && \rex_version::compare($fromVersion, '1.6.0', '<')) || !$hadExtensionConfig)
            && self::isActive('machine_steel_processing_extension')
        ) {
            rex_config::set('d2u_machinery', self::getConfigKey('machine_steel_automation_extension'), self::STATE_ACTIVE);
        }
    }

    public static function runLegacyVersionMigrations(string $fromVersion): void
    {
        if (\rex_version::compare($fromVersion, '1.2.6', '<')) {
            self::convertUnixTimestampColumn(\rex::getTablePrefix() .'d2u_machinery_machines_lang', 'updatedate');
            self::convertUnixTimestampColumn(\rex::getTablePrefix() .'d2u_machinery_categories_lang', 'updatedate');
            self::convertUnixTimestampColumn(\rex::getTablePrefix() .'d2u_machinery_used_machines_lang', 'updatedate');
            self::convertUnixTimestampColumn(\rex::getTablePrefix() .'d2u_machinery_service_options_lang', 'updatedate');
            self::convertUnixTimestampColumn(\rex::getTablePrefix() .'d2u_machinery_industry_sectors_lang', 'updatedate');
            self::convertUnixTimestampColumn(\rex::getTablePrefix() .'d2u_machinery_export_machines', 'export_timestamp');
        }
    }

    /**
     * @param array<string,bool> $previousStates
     * @param array<string,bool> $requestedStates
     * @return array{activated: array<int,string>, deactivated: array<int,string>, normalized: array<string,bool>}
     */
    public static function applyStateChanges(array $previousStates, array $requestedStates): array
    {
        $normalizedStates = self::normalizeStates($requestedStates);
        $activated = [];
        $deactivated = [];

        foreach (array_keys(self::DEFINITIONS) as $key) {
            if (($previousStates[$key] ?? false) || !($normalizedStates[$key] ?? false)) {
                continue;
            }

            $legacyPlugin = self::getLegacyPluginName($key);
            if (null !== $legacyPlugin && rex_plugin::exists('d2u_machinery', $legacyPlugin)) {
                self::installLegacyPlugin($key);
            } else {
                self::runLegacyScript($key, 'install.php');
            }
            $activated[] = $key;
        }

        foreach (array_reverse(array_keys(self::DEFINITIONS)) as $key) {
            if (!($previousStates[$key] ?? false) || ($normalizedStates[$key] ?? false)) {
                continue;
            }

            $legacyPlugin = self::getLegacyPluginName($key);
            if (null !== $legacyPlugin && rex_plugin::exists('d2u_machinery', $legacyPlugin)) {
                $plugin = rex_plugin::get('d2u_machinery', $legacyPlugin);
                if ($plugin instanceof rex_plugin && $plugin->isInstalled()) {
                    self::uninstallLegacyPlugin($key);
                } else {
                    self::runLegacyScript($key, 'uninstall.php');
                }
            } else {
                self::runLegacyScript($key, 'uninstall.php');
            }
            $deactivated[] = $key;
        }

        foreach (array_reverse(array_keys(self::DEFINITIONS)) as $key) {
            if ($normalizedStates[$key] ?? false) {
                continue;
            }
            if (in_array($key, $deactivated, true)) {
                continue;
            }

            self::runLegacyScript($key, 'uninstall.php');
        }

        foreach ($normalizedStates as $key => $state) {
            rex_config::set('d2u_machinery', self::getConfigKey($key), $state ? self::STATE_ACTIVE : self::STATE_INACTIVE);
        }

        return [
            'activated' => $activated,
            'deactivated' => $deactivated,
            'normalized' => $normalizedStates,
        ];
    }

    public static function installLegacyPlugin(string $key): void
    {
        $legacyPlugin = self::getLegacyPluginName($key);
        if (null === $legacyPlugin || !rex_plugin::exists('d2u_machinery', $legacyPlugin)) {
            return;
        }

        $plugin = rex_plugin::get('d2u_machinery', $legacyPlugin);
        if (!$plugin instanceof rex_plugin) {
            return;
        }

        $manager = rex_plugin_manager::factory($plugin);
        if (!$plugin->isInstalled()) {
            if (!$manager->install()) {
                throw new \RuntimeException($manager->getMessage());
            }

            return;
        }

        if (!$plugin->isAvailable() && !$manager->activate()) {
            throw new \RuntimeException($manager->getMessage());
        }
    }

    public static function uninstallLegacyPlugin(string $key): void
    {
        $legacyPlugin = self::getLegacyPluginName($key);
        if (null === $legacyPlugin || !rex_plugin::exists('d2u_machinery', $legacyPlugin)) {
            return;
        }

        $plugin = rex_plugin::get('d2u_machinery', $legacyPlugin);
        if (!$plugin instanceof rex_plugin || !$plugin->isInstalled()) {
            return;
        }

        $manager = rex_plugin_manager::factory($plugin);
        if (!$manager->uninstall()) {
            throw new \RuntimeException($manager->getMessage());
        }
    }

    public static function hideInactiveBackendPages(): void
    {
        foreach (self::DEFINITIONS as $key => $definition) {
            if (self::isActive($key)) {
                continue;
            }

            foreach ($definition['pages'] as $pageId) {
                $page = rex_be_controller::getPageObject($pageId);
                if (null !== $page) {
                    $page->setHidden(true);
                }
            }
        }
    }

    /**
     * @param array<string,mixed> $page
     * @return array<string,mixed>
     */
    public static function removeInactivePagesFromNavigation(array $page): array
    {
        $subpages = $page['subpages'] ?? [];
        if (!is_array($subpages)) {
            return $page;
        }

        self::unsetSubpage($subpages, ['machine', 'subpages', 'equipment'], self::isActive('equipment'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'agitators'], self::isActive('machine_agitator_extension'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'certificates'], self::isActive('machine_certificates_extension'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'features'], self::isActive('machine_features_extension'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'options'], self::isActive('machine_options_extension'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'automation'], self::isActive('machine_steel_automation_extension'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'steel_processing'], self::isActive('machine_steel_processing_extension'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'service_options'], self::isActive('service_options'));
        self::unsetSubpage($subpages, ['machine', 'subpages', 'usage_areas'], self::isActive('machine_usage_area_extension'));

        self::unsetSubpage($subpages, ['contacts'], self::isActive('contacts'));
        self::unsetSubpage($subpages, ['industry_sectors'], self::isActive('industry_sectors'));
        self::unsetSubpage($subpages, ['production_lines'], self::isActive('production_lines'));
        self::unsetSubpage($subpages, ['used_machines'], self::isActive('used_machines'));

        if (self::isActive('used_machines')) {
            self::unsetSubpage($subpages, ['used_machines', 'subpages', 'export'], self::isActive('export'));
            self::unsetSubpage($subpages, ['used_machines', 'subpages', 'provider'], self::isActive('export'));
        }

        $page['subpages'] = $subpages;

        return $page;
    }

    public static function guardLegacyPage(string $key): bool
    {
        if (self::isActive($key)) {
            return true;
        }

        echo \rex_view::warning(sprintf((string) \rex_i18n::msg('d2u_machinery_extension_disabled_notice'), self::getTitle($key)));

        return false;
    }

    /**
     * @param array<string,array{label:string,path:string}> $pages
     */
    public static function renderThirdLevelTabs(array $pages, string $activePage, string $requestKey): string
    {
        $tabItems = [];
        foreach ($pages as $key => $page) {
            $tabItems[] = sprintf(
                '<li%s><a href="%s">%s</a></li>',
                $key === $activePage ? ' class="active"' : '',
                \rex_url::currentBackendPage([$requestKey => $key], false),
                \rex_escape($page['label'])
            );
        }

        return '<div class="nav rex-page-nav"><ul class="nav nav-tabs">'. implode('', $tabItems) .'</ul></div>';
    }

    /**
     * @param array<string,mixed> $settings
     * @return array<string,bool>
     */
    public static function getRequestedStatesFromSettings(array $settings): array
    {
        $states = [];
        foreach (array_keys(self::DEFINITIONS) as $key) {
            $configKey = self::getConfigKey($key);
            $states[$key] = self::STATE_ACTIVE === ($settings[$configKey] ?? self::STATE_INACTIVE);
        }

        return $states;
    }

    /**
     * @param array<string,bool> $states
     * @return array<string,bool>
     */
    private static function normalizeStates(array $states): array
    {
        foreach (array_keys(self::DEFINITIONS) as $key) {
            $states[$key] = (bool) ($states[$key] ?? false);
        }

        foreach (array_keys(self::DEFINITIONS) as $key) {
            if (!($states[$key] ?? false)) {
                continue;
            }

            foreach (self::getDependencies($key) as $dependency) {
                $states[$dependency] = true;
            }
        }

        return $states;
    }

    /**
     * @return array<int,string>
     */
    private static function getDependencies(string $key): array
    {
        return self::requireDefinition($key)['dependencies'];
    }

    private static function hasAnyExtensionConfig(): bool
    {
        foreach (array_keys(self::DEFINITIONS) as $key) {
            if (rex_config::has('d2u_machinery', self::getConfigKey($key))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string,bool> $cascade
     */
    private static function collectDeactivationCascade(string $key, array &$cascade): void
    {
        if (isset($cascade[$key])) {
            return;
        }

        foreach (array_keys(self::DEFINITIONS) as $candidate) {
            if (in_array($key, self::getDependencies($candidate), true)) {
                self::collectDeactivationCascade($candidate, $cascade);
            }
        }

        $cascade[$key] = true;
    }

    /**
     * @param array<string,bool> $cascade
     */
    private static function collectActivationCascade(string $key, array &$cascade): void
    {
        if (isset($cascade[$key])) {
            return;
        }

        foreach (self::getDependencies($key) as $dependency) {
            self::collectActivationCascade($dependency, $cascade);
        }

        $cascade[$key] = true;
    }

    private static function isLegacyPluginInstalled(string $key): bool
    {
        $legacyPlugin = self::getLegacyPluginName($key);
        if (null === $legacyPlugin) {
            return false;
        }
        $plugin = rex_plugin::get('d2u_machinery', $legacyPlugin);

        return $plugin instanceof rex_plugin && $plugin->isAvailable();
    }

    private static function convertUnixTimestampColumn(string $table, string $column): void
    {
        $sql = \rex_sql::factory();
        $sql->setQuery('SHOW TABLES LIKE "'. $table .'"');
        if (0 === $sql->getRows()) {
            return;
        }

        $sql->setQuery('SHOW COLUMNS FROM '. $table .' LIKE "'. $column .'"');
        if (0 === $sql->getRows()) {
            return;
        }

        $type = strtolower((string) $sql->getValue('Type'));
        if (str_contains($type, 'datetime')) {
            return;
        }

        $tmpColumn = $column .'_new';
        $sql->setQuery('SHOW COLUMNS FROM '. $table .' LIKE "'. $tmpColumn .'"');
        if ($sql->getRows() > 0) {
            $sql->setQuery('ALTER TABLE '. $table .' DROP '. $tmpColumn);
        }

        $sql->setQuery('ALTER TABLE '. $table .' ADD COLUMN `'. $tmpColumn .'` DATETIME NOT NULL AFTER `'. $column .'`;');
        $sql->setQuery('UPDATE '. $table .' SET `'. $tmpColumn .'` = FROM_UNIXTIME(`'. $column .'`);');
        $sql->setQuery('ALTER TABLE '. $table .' DROP `'. $column .'`;');
        $sql->setQuery('ALTER TABLE '. $table .' CHANGE `'. $tmpColumn .'` `'. $column .'` DATETIME NOT NULL;');
    }

    private static function runLegacyScript(string $key, string $script): void
    {
        if ('machine_steel_automation_extension' === $key) {
            return;
        }

        $scriptPath = dirname(__DIR__) .'/'. $script;
        if (file_exists($scriptPath)) {
            $d2uMachineryAction = $key;
            include $scriptPath;
        }
    }

    /**
     * @return array<string,mixed>
     */
    private static function requireDefinition(string $key): array
    {
        if (!array_key_exists($key, self::DEFINITIONS)) {
            throw new \InvalidArgumentException('Unknown d2u_machinery extension: '. $key);
        }

        return self::DEFINITIONS[$key];
    }

    /**
     * @param array<string,mixed> $items
     * @param array<int,string> $path
     */
    private static function unsetSubpage(array &$items, array $path, bool $keep): void
    {
        if ($keep || [] === $path) {
            return;
        }

        $key = array_shift($path);
        if (null === $key || !array_key_exists($key, $items)) {
            return;
        }

        if ([] === $path) {
            unset($items[$key]);

            return;
        }

        if (!is_array($items[$key])) {
            return;
        }

        self::unsetSubpage($items[$key], $path, $keep);
    }
}