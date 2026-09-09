<?php

namespace TobiasKrais\D2UMachinery;

use rex;
use rex_addon;
use rex_clang;
use rex_config;
use rex_sql;
use rex_user;
use rex_yrewrite;

/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

use TobiasKrais\D2UMachinery\Extension;
use TobiasKrais\D2UReferences\Reference;

/**
 * Machine.
 */
class Machine implements \TobiasKrais\D2UHelper\ITranslationHelper, \TobiasKrais\D2UHelper\ITranslateable
{
    /** @var int Machine id */
    public int $machine_id = 0;

    /** @var int Redaxo language id */
    public int $clang_id = 0;

    /** @var string Machine name */
    public string $name = '';

    /** @var array<string> Machine pictures */
    public array $pics = [];

    /** @var Category|false Machine category */
    public Category|false $category = false;

    /** @var Contact Machine contact */
    public Contact|false $contact = false;

    /** @var int[] Usage area IDs */
    public array $usage_area_ids = [];

    /** @var int[] IDs of alternative machines */
    public array $alternative_machine_ids = [];

    /** @var int[] IDs of additional machines */
    public array $additional_machine_ids = [];

    /** @var int[] Machine feature ids */
    public array $feature_ids = [];

    /** @var int[] Machine option ids */
    public array $option_ids = [];

    /** @var string machine accessory ids */
    public string $product_number = '';

    /** @var int[] machine business ids */
    public array $industry_sector_ids = [];

    /** @var int Redaxo article id for additional software information */
    public int $article_id_software = 0;

    /** @var int Redaxo article id for additional service information */
    public int $article_id_service = 0;

    /** @var int[] Array with Redaxo article ids with customer success stories */
    public array $article_ids_references = [];

    /** @var int[] Array with IDs from d2u_references addon */
    public array $reference_ids = [];

    /** @var string Status. Either "online" or "offline". */
    public string $online_status = 'offline';

    /** @var int[] Certificate ids */
    public array $certificate_ids = [];

    /** @var int Agitator type id */
    public int $agitator_type_id = 0;

    /** @var int Max. viscosity in mPas */
    public int $viscosity = 0;

    /** @var string Engine power */
    public string $engine_power = '';

    /** @var bool Is engine power frequency controlled? */
    public bool $engine_power_frequency_controlled = false;

    /** @var int Machine length */
    public int $length = 0;

    /** @var int Machine width */
    public int $width = 0;

    /** @var int Machine height */
    public int $height = 0;

    /** @var int Machine depth */
    public int $depth = 0;

    /** @var string Machine weight */
    public string $weight = '';

    /** @var string Machine operating voltage (v) */
    public string $operating_voltage_v = '';

    /** @var string Machine operating voltage (hz) */
    public string $operating_voltage_hz = '';

    /** @var string Machine operating voltage (a) */
    public string $operating_voltage_a = '';

    /** @var int Sort Priority */
    public int $priority = 0;

    /** @var int[] machine_steel_automation_extension: Automation supply ids */
    public array $automation_supply_ids = [];

    /** @var string Language specific name */
    public string $lang_name = '';

    /** @var string Teaser */
    public string $teaser = '';

    /** @var string Machine description */
    public string $description = '';

    /** @var string Machine benefits (long version) */
    public string $benefits_long = '';

    /** @var string Machine benefits */
    public string $benefits_short = '';

    /** @var array<string> File names of PDF files for the machine */
    public array $pdfs = [];

    /** @var string Machine leaflet (PDF file) */
    public string $leaflet = '';

    /** @var \TobiasKrais\D2UVideos\Video[] Videomanager videos */
    public array $videos = [];

    /** @var string Needs translation update? "no", "yes" or "delete" */
    public string $translation_needs_update = 'delete';

    /** @var string URL der Maschine */
    private string $url = '';

    /* Variables from machine_construction_equipment_extension following */

    /** @var string Airless devices: hose connection (") */
    public string $airless_hose_connection = '';

    /** @var int Airless devices: hose diameter (mm) */
    public int $airless_hose_diameter = 0;

    /** @var int Airless devices: maximum hose length (m) */
    public int $airless_hose_length = 0;

    /** @var string Airless devices: maximum nozzle size */
    public string $airless_nozzle_size = '';

    /** @var string Containers: maximum capacity */
    public string $container_capacity = '';

    /** @var string Containers: capacity unit (kg, l, m³) */
    public string $container_capacity_unit = 'kg';

    /** @var string Containers: connection port */
    public string $container_connection_port = '';

    /** @var string Containers: conveying wave */
    public string $container_conveying_wave = '';

    /** @var string Containers: mixing performance (l/min) */
    public string $container_mixing_performance = '';

    /** @var int Containers: water connection pressure (bar) */
    public int $container_waterconnect_pressure = 0;

    /** @var string Containers: water connection diameter (") */
    public string $container_waterconnect_diameter = '';

    /** @var int Containers: empty container weight (kg) */
    public int $container_weight_empty = 0;

    /** @var string Cutting devices: maximum cutting depth (cm) */
    public string $cutters_cutting_depth = '';

    /** @var int Cutting devices: maximum cutting length (cm) */
    public int $cutters_cutting_length = 0;

    /** @var string Cutting devices: rod length (mm) */
    public string $cutters_rod_length = '';

    /** @var int Tillage machines: beam power on concrete (m²/h) */
    public int $floor_beam_power_on_concrete = 0;

    /** @var int Tillage machines: dust extraction connection size (mm) */
    public int $floor_dust_extraction_connection = 0;

    /** @var string Tillage machines: feedrate (m/min) */
    public string $floor_feedrate = '';

    /** @var string Tillage machines: filter connection (mm) */
    public string $floor_filter_connection = '';

    /** @var string Tillage machines: rotations (min-1) */
    public string $floor_rotations = '';

    /** @var string Tillage machines: working pressure (kg) */
    public string $floor_working_pressure = '';

    /** @var int Tillage machines: working width (mm) */
    public int $floor_working_width = 0;

    /** @var int Grinding machines: grinding plate (cm²) */
    public int $grinder_grinding_plate = 0;

    /** @var int Grinding machines: grinding wheel (mm) */
    public int $grinder_grinding_wheel = 0;

    /** @var string Grinding machines: working pressure (u/min) */
    public string $grinder_rotational_frequency = '';

    /** @var string Grinding machines: sanding (u/min) */
    public string $grinder_sanding = '';

    /** @var int Grinding machines: vacuum connection (mm) */
    public int $grinder_vacuum_connection = 0;

    /** @var string Pumps and other machines: operating pressure (bar) */
    public string $operating_pressure = '';

    /** @var string Pumps: max. flow volume (l/min) */
    public string $pump_flow_volume = '';

    /** @var int Pumps: max. conveying distance (m) */
    public int $pump_conveying_distance = 0;

    /** @var int Pumps: max. pressure height (m) */
    public int $pump_pressure_height = 0;

    /** @var int Pumps: filling (mm) */
    public int $pump_filling = 0;

    /** @var string Pumps: maximum grain size (mm) */
    public string $pump_grain_size = '';

    /** @var string Pumps: material container (l) */
    public string $pump_material_container = '';

    /** @var int Waste water containers: capacity (l) */
    public int $waste_water_capacity = 0;

    /** @var string Description shown in technical data overview */
    public string $description_technical = '';

    /** @var array<string> Delivery set picture name */
    public array $pictures_delivery_set = [];

    /** @var string Basic delivery set description */
    public string $delivery_set_basic = '';

    /** @var string Conversion delivery set description */
    public string $delivery_set_conversion = '';

    /** @var string Full delivery set description */
    public string $delivery_set_full = '';

    /* Variables from service_options plugin following */

    /** @var int[] Machine service option ids */
    public array $service_option_ids = [];

    /* Variables from equipment plugin following */

    /** @var int[] Machine equipment ids */
    public array $equipment_ids = [];

    /**
     * Fetches a machine object from database or creates an empty machine object.
     * @param int $machine_id Database machine id
     * @param int $clang_id Redaxo language id
     */
    public function __construct($machine_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_machines AS machines '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang '
                    .'ON machines.machine_id = lang.machine_id '
                    .'AND clang_id = '. $this->clang_id .' '
                .'WHERE machines.machine_id = '. $machine_id;
        $result = \rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->machine_id = (int) $result->getValue('machine_id');
            $this->name = stripslashes((string) $result->getValue('name'));
            $pics = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('pics')), PREG_GREP_INVERT);
            $this->pics = is_array($pics) ? $pics : [];
            $this->category = new Category((int) $result->getValue('category_id'), $clang_id);
            $alternative_machine_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('alternative_machine_ids')), PREG_GREP_INVERT);
            $this->alternative_machine_ids = is_array($alternative_machine_ids) ? array_map('intval', $alternative_machine_ids) : [];
            $additional_machine_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('additional_machine_ids')), PREG_GREP_INVERT);
            $this->additional_machine_ids = is_array($additional_machine_ids) ? array_map('intval', $additional_machine_ids) : [];
            $this->product_number = (string) $result->getValue('product_number');
            $this->article_id_software = (int) $result->getValue('article_id_software');
            $this->article_id_service = (int) $result->getValue('article_id_service');
            $article_ids_references = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('article_ids_references')), PREG_GREP_INVERT);
            $this->article_ids_references = is_array($article_ids_references) ? $article_ids_references : [];
            $reference_ids = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('reference_ids')), PREG_GREP_INVERT);
            $this->reference_ids = is_array($reference_ids) ? array_map('intval', array_filter($reference_ids, 'is_numeric')) : [];
            $this->online_status = (string) $result->getValue('online_status');
            $this->engine_power = (string) $result->getValue('engine_power');
            $this->engine_power_frequency_controlled = 'true' === (string) $result->getValue('engine_power_frequency_controlled') ? true : false;
            $this->length = (int) $result->getValue('length');
            $this->width = (int) $result->getValue('width');
            $this->height = (int) $result->getValue('height');
            $this->depth = (int) $result->getValue('depth');
            $this->weight = (string) $result->getValue('weight');
            $this->operating_voltage_v = (string) $result->getValue('operating_voltage_v');
            $this->operating_voltage_hz = (string) $result->getValue('operating_voltage_hz');
            $this->operating_voltage_a = (string) $result->getValue('operating_voltage_a');
            $this->lang_name = stripslashes((string) $result->getValue('lang_name'));
            $this->teaser = stripslashes(htmlspecialchars_decode((string) $result->getValue('teaser')));
            $this->description = stripslashes(htmlspecialchars_decode((string) $result->getValue('description')));
            $this->benefits_long = stripslashes(htmlspecialchars_decode((string) $result->getValue('benefits_long')));
            $this->benefits_short = stripslashes(htmlspecialchars_decode((string) $result->getValue('benefits_short')));
            $pdfs = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('pdfs')), PREG_GREP_INVERT);
            $this->pdfs = is_array($pdfs) ? $pdfs : [];
            $this->leaflet = (string) $result->getValue('leaflet');
            $this->priority = (int) $result->getValue('priority');
            if ('' !== (string) $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }

            if (Extension::isActive('contacts')) {
                if ((int) $result->getValue('contact_id') > 0) {
                    $this->contact = new Contact((int) $result->getValue('contact_id'));
                }
            }

            if (Extension::isActive('contacts') && Extension::isActive('equipment')) {
                $equipment_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('equipment_ids')), PREG_GREP_INVERT);
                $this->equipment_ids = is_array($equipment_ids) ? array_map('intval', $equipment_ids) : [];
            }

            if (Extension::isActive('contacts') && Extension::isActive('industry_sectors')) {
                $industry_sector_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('industry_sector_ids')), PREG_GREP_INVERT);
                $this->industry_sector_ids = is_array($industry_sector_ids) ? array_map('intval', $industry_sector_ids) : [];
            }

            if (Extension::isActive('contacts') && Extension::isActive('machine_agitator_extension')) {
                $this->agitator_type_id = (int) $result->getValue('agitator_type_id');
                $this->viscosity = (int) $result->getValue('viscosity');
            }

            if (Extension::isActive('contacts') && Extension::isActive('machine_certificates_extension')) {
                $certificate_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('certificate_ids')), PREG_GREP_INVERT);
                $this->certificate_ids = is_array($certificate_ids) ? array_map('intval', $certificate_ids) : [];
            }

            if (Extension::isActive('contacts') && Extension::isActive('machine_construction_equipment_extension')) {
                $this->airless_hose_connection = (string) $result->getValue('airless_hose_connection');
                $this->airless_hose_diameter = (int) $result->getValue('airless_hose_diameter');
                $this->airless_hose_length = (int) $result->getValue('airless_hose_length');
                $this->airless_nozzle_size = (string) $result->getValue('airless_nozzle_size');
                $this->container_capacity = (string) $result->getValue('container_capacity');
                $this->container_capacity_unit = (string) $result->getValue('container_capacity_unit') ?: 'kg';
                $this->container_mixing_performance = (string) $result->getValue('container_mixing_performance');
                $this->container_waterconnect_pressure = (int) $result->getValue('container_waterconnect_pressure');
                $this->container_waterconnect_diameter = (string) $result->getValue('container_waterconnect_diameter');
                $this->container_weight_empty = (int) $result->getValue('container_weight_empty');
                $this->cutters_cutting_depth = (string) $result->getValue('cutters_cutting_depth');
                $this->cutters_cutting_length = (int) $result->getValue('cutters_cutting_length');
                $this->cutters_rod_length = (string) $result->getValue('cutters_rod_length');
                $this->floor_beam_power_on_concrete = (int) $result->getValue('floor_beam_power_on_concrete');
                $this->floor_dust_extraction_connection = (int) $result->getValue('floor_dust_extraction_connection');
                $this->floor_feedrate = (string) $result->getValue('floor_feedrate');
                $this->floor_filter_connection = (string) $result->getValue('floor_filter_connection');
                $this->floor_rotations = (string) $result->getValue('floor_rotations');
                $this->floor_working_pressure = (string) $result->getValue('floor_working_pressure');
                $this->floor_working_width = (int) $result->getValue('floor_working_width');
                $this->grinder_grinding_plate = (int) $result->getValue('grinder_grinding_plate');
                $this->grinder_grinding_wheel = (int) $result->getValue('grinder_grinding_wheel');
                $this->grinder_rotational_frequency = (string) $result->getValue('grinder_rotational_frequency');
                $this->grinder_sanding = (string) $result->getValue('grinder_sanding');
                $this->grinder_vacuum_connection = (int) $result->getValue('grinder_vacuum_connection');
                $this->operating_pressure = (string) $result->getValue('operating_pressure');
                $this->pump_conveying_distance = (int) $result->getValue('pump_conveying_distance');
                $this->pump_filling = (int) $result->getValue('pump_filling');
                $this->pump_flow_volume = (string) $result->getValue('pump_flow_volume');
                $this->pump_grain_size = (string) $result->getValue('pump_grain_size');
                $this->pump_material_container = (string) $result->getValue('pump_material_container');
                $this->pump_pressure_height = (int) $result->getValue('pump_pressure_height');
                $this->waste_water_capacity = (int) $result->getValue('waste_water_capacity');
                $this->container_connection_port = (string) $result->getValue('container_connection_port');
                $this->container_conveying_wave = (string) $result->getValue('container_conveying_wave');
                $this->description_technical = (string) $result->getValue('description_technical');
                $pictures_delivery_set = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('pictures_delivery_set')), PREG_GREP_INVERT);
                $this->pictures_delivery_set = is_array($pictures_delivery_set) ? $pictures_delivery_set : [];
                $this->delivery_set_basic = stripslashes(htmlspecialchars_decode((string) $result->getValue('delivery_set_basic')));
                $this->delivery_set_conversion = stripslashes(htmlspecialchars_decode((string) $result->getValue('delivery_set_conversion')));
                $this->delivery_set_full = stripslashes(htmlspecialchars_decode((string) $result->getValue('delivery_set_full')));
            }

            if (Extension::isActive('contacts') && Extension::isActive('service_options')) {
                $service_option_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('service_option_ids')), PREG_GREP_INVERT);
                $this->service_option_ids = is_array($service_option_ids) ? array_map('intval', $service_option_ids) : [];
            }

            if (Extension::isActive('contacts') && Extension::isActive('machine_features_extension')) {
                $feature_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('feature_ids')), PREG_GREP_INVERT);
                $this->feature_ids = is_array($feature_ids) ? array_map('intval', $feature_ids) : [];
            }

            if (Extension::isActive('contacts') && Extension::isActive('machine_options_extension')) {
                $option_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('option_ids')), PREG_GREP_INVERT);
                $this->option_ids = is_array($option_ids) ? array_map('intval', $option_ids) : [];
            }

            if (Extension::isActive('machine_steel_automation_extension')) {
                $automation_supply_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('automation_supply_ids')), PREG_GREP_INVERT);
                $this->automation_supply_ids = is_array($automation_supply_ids) ? array_map('intval', $automation_supply_ids) : [];
            }

            if (Extension::isActive('contacts') && Extension::isActive('machine_usage_area_extension')) {
                $usage_area_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('usage_area_ids')), PREG_GREP_INVERT);
                $this->usage_area_ids = is_array($usage_area_ids) ? array_map('intval', $usage_area_ids) : [];
            }

            // Videos
            if (\rex_addon::get('d2u_videos')->isAvailable() && '' !== $result->getValue('video_ids')) {
                $video_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('video_ids')), PREG_GREP_INVERT);
                if (is_array($video_ids)) {
                    foreach ($video_ids as $video_id) {
                        if ($video_id > 0) {
                            $video = new \TobiasKrais\D2UVideos\Video($video_id, $clang_id);
                            if ('' !== $video->getVideoURL()) {
                                $this->videos[$video_id] = $video;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Changes the status of a machine.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->machine_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_machines '
                    ."SET online_status = 'offline' "
                    .'WHERE machine_id = '. $this->machine_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->machine_id > 0) {
                $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_machines '
                    ."SET online_status = 'online' "
                    .'WHERE machine_id = '. $this->machine_id;
                $result = \rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }

        // Don't forget to regenerate URL cache and search_it index
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache();
    }

    /**
     * Deletes the object.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang '
            .'WHERE machine_id = '. $this->machine_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = \rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang '
            .'WHERE machine_id = '. $this->machine_id;
        $result_main = \rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
                .'WHERE machine_id = '. $this->machine_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }

        // Don't forget to regenerate URL cache / search_it index
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache();

        // Delete from YRewrite forward list
        if (rex_addon::get('yrewrite')->isAvailable()) {
            if ($delete_all) {
                foreach (rex_clang::getAllIds() as $clang_id) {
                    $lang_object = new self($this->machine_id, $clang_id);
                    $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                        .'WHERE extern = :extern';
                    $result_forward = \rex_sql::factory();
                    $result_forward->setQuery($query_forward, [':extern' => $lang_object->getUrl(true)]);
                }
            } else {
                $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                    .'WHERE extern = :extern';
                $result_forward = \rex_sql::factory();
                $result_forward->setQuery($query_forward, [':extern' => $this->getUrl(true)]);
            }
        }
    }

    /**
     * Get all machines.
     * @param int $clang_id redaxo clang id
     * @param bool $only_online Show only online machines
     * @return array<Machine> array with Machine objects
     */
    public static function getAll($clang_id, $only_online = false)
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines ';
        if ($only_online) {
            $query .= "WHERE online_status = 'online' ";
        }
        if ('priority' === (string) \rex_addon::get('d2u_machinery')->getConfig('default_machine_sort')) {
            $query .= 'ORDER BY priority ASC';
        } else {
            $query .= 'ORDER BY name ASC';
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machines[] = new self((int) $result->getValue('machine_id'), $clang_id);
            $result->next();
        }
        return $machines;
    }

    /**
     * @api
     * Get Feature objects related to this machine.
     * @return array<int,Feature> array with Feature objects
     */
    public function getFeatures()
    {
        $features = [];
        foreach ($this->feature_ids as $feature_id) {
            $feature = new Feature($feature_id, $this->clang_id);
            $features[$feature->priority] = $feature;
        }
        ksort($features);
        return $features;
    }

    /**
     * @api
     * Get Option objects related to this machine.
     * @return Option[] array with Option objects
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->option_ids as $option_id) {
            $option = new Option($option_id, $this->clang_id);
            $options[$option->priority] = $option;
        }
        ksort($options);
        return $options;
    }

    /**
     * Get reference objects reffering to this machine.
     * @return array<int,Reference> array with Reference objects
     */
    public function getReferences(): array
    {
        if (!rex_addon::get('d2u_references')->isAvailable()) {
            return [];
        }

        $references = [];
        foreach ($this->reference_ids as $reference_id) {
            $reference = new Reference($reference_id, $this->clang_id);
            if ($reference->reference_id > 0) {
                $references[$reference->reference_id] = $reference;
            }
        }
        ksort($references);
        return $references;
    }

    /**
     * Gets the machines referring to this machine as alternative or additional machine.
     * @return Machine[] machines referring to this machine as alternative or additional machine
     */
    public function getReferringMachines()
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE alternative_machine_ids LIKE '%|". (int) $this->machine_id ."|%' OR additional_machine_ids LIKE '%|". (int) $this->machine_id ."|%'";
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machines[] = new self((int) $result->getValue('machine_id'), $this->clang_id);
            $result->next();
        }
        return $machines;
    }

    /**
     * Gets the production lines referring to this machine via a marker on the link picture.
     * @return ProductionLine[] production lines referring to this machine
     */
    public function getReferringProductionLines()
    {
        if (Extension::isActive('production_lines')) {
            $query = 'SELECT production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines '
                .'WHERE markers LIKE \'%"type":"machine","id":'. (int) $this->machine_id .'}%\'';
            $result = \rex_sql::factory();
            $result->setQuery($query);

            $production_lines = [];
            for ($i = 0; $i < $result->getRows(); ++$i) {
                $production_lines[] = new ProductionLine((int) $result->getValue('production_line_id'), $this->clang_id);
                $result->next();
            }
            return $production_lines;
        }

        return [];

    }

    /**
     * Gets the used machines referring to this machine.
     * @return UsedMachine[] used machines referring to this machine
     */
    public function getReferringUsedMachines()
    {
        if (Extension::isActive('used_machines')) {
            $query = 'SELECT used_machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_used_machines '
                .'WHERE machine_id = '. $this->machine_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);

            $used_machines = [];
            for ($i = 0; $i < $result->getRows(); ++$i) {
                $used_machines[] = new UsedMachine((int) $result->getValue('used_machine_id'), $this->clang_id);
                $result->next();
            }
            return $used_machines;
        }

        return [];

    }

    /**
     * Get Service Option objects related to this machine.
     * @param bool $online_only true if only online objects are returned
     * @return ServiceOption[] array with ServiceOption objects
     */
    public function getServiceOptions($online_only = true)
    {
        $service_options = [];
        foreach ($this->service_option_ids as $service_option_id) {
            $service_option = new ServiceOption($service_option_id, $this->clang_id);
            if (($online_only && 'online' === $service_option->online_status) || !$online_only) {
                $service_options[] = $service_option;
            }
        }
        return $service_options;
    }

    /**
     * @api
     * Get supply objects related to this machine.
     * @return Supply[] array with supply objects
     */
    public function getSupplies()
    {
        $supplies = [];
        foreach ($this->automation_supply_ids as $supply_id) {
            $supply_id = new Supply($supply_id, $this->clang_id);
            $supplies[$supply_id->priority] = $supply_id;
        }
        ksort($supplies);
        return $supplies;
    }

    /**
     * Get Technical Data as array.
     * @return mixed[] Array with technical data. Each element is an array itself.
     * First element ist the translation wildcard, second is the value and third
     * the unit.
     */
    public function getTechnicalData()
    {
        $tech_data = [];

        // Max. viscosity
        if (Extension::isActive('machine_agitator_extension') && $this->viscosity > 0) {
            $tech_data[] = [
                'description' => \Sprog\Wildcard::get('d2u_machinery_agitators_viscosity'),
                'value' => $this->viscosity,
                'unit' => \Sprog\Wildcard::get('d2u_machinery_agitators_mpas'),
            ];
        }

        if (Extension::isActive('machine_construction_equipment_extension')) {
            // Operating pressure
            if ('' !== $this->operating_pressure) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_operating_pressure'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_up_to') .' '. $this->operating_pressure,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_bar'),
                ];
            }

            // More following later ...
        }

        // Operating voltage
        if ('' !== $this->operating_voltage_v) {
            $v = $this->operating_voltage_v;
            $h = '' === $this->operating_voltage_hz ? ' / -' : ' / '. $this->operating_voltage_hz;
            $a = '' === $this->operating_voltage_a ? ' / -' : ' / '. $this->operating_voltage_a;
            $tech_data[] = [
                'description' => \Sprog\Wildcard::get('d2u_machinery_operating_voltage'),
                'value' => $v . $h . $a,
                'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_v') .'/'. \Sprog\Wildcard::get('d2u_machinery_unit_hz') .'/'. \Sprog\Wildcard::get('d2u_machinery_unit_a'),
            ];
        }

        // Engine power
        if ('' !== $this->engine_power) {
            $tech_data[] = [
                'description' => \Sprog\Wildcard::get('d2u_machinery_engine_power'),
                'value' => $this->engine_power . ($this->engine_power_frequency_controlled ? ' ('. \Sprog\Wildcard::get('d2u_machinery_engine_power_frequency_controlled') .')' : ''),
                'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_kw'),
            ];
        }

        if (Extension::isActive('machine_construction_equipment_extension')) {
            // Water capacity
            if ($this->waste_water_capacity > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_waste_water_capacity'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->waste_water_capacity,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_l'),
                ];
            }

            // Capacity
            if ('' !== $this->container_capacity) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_container_capacity'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->container_capacity .' '. \Sprog\Wildcard::get('d2u_machinery_unit_'. $this->container_capacity_unit),
                    'unit' => '',
                ];
            }

            // Empty weight
            if ($this->container_weight_empty > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_container_weight_empty'),
                    'value' => $this->container_weight_empty,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_kg'),
                ];
            }

            // Mixing performance
            if ('' !== $this->container_mixing_performance) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_container_mixing_performance'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->container_mixing_performance,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_l_min'),
                ];
            }

            // Flow volume
            if ('' !== $this->pump_flow_volume) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_pump_flow_volume'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->pump_flow_volume,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_l_min'),
                ];
            }

            // Conveying distance
            if ($this->pump_conveying_distance > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_pump_conveying_distance'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->pump_conveying_distance,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_m'),
                ];
            }

            // Pressure height
            if ($this->pump_pressure_height > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_pump_pressure_height'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->pump_pressure_height,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_m'),
                ];
            }

            // Grain size
            if ('' !== $this->pump_grain_size) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_pump_grain_size'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->pump_grain_size,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Nozzle size
            if ('' !== $this->airless_nozzle_size) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_airless_nozzle_size'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->airless_nozzle_size,
                    'unit' => '"',
                ];
            }

            // Nozzle size
            if ('' !== $this->pump_material_container) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_pump_material_container'),
                    'value' => $this->pump_material_container,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_l'),
                ];
            }

            // Filling
            if ($this->pump_filling > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_pump_filling'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_unit_ca') .' '. $this->pump_filling,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Hose connection
            if ('' !== $this->airless_hose_connection) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_airless_hose_connection'),
                    'value' => $this->airless_hose_connection,
                    'unit' => '"',
                ];
            }

            // Hose diameter
            if ($this->airless_hose_diameter > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_airless_hose_diameter'),
                    'value' => $this->airless_hose_diameter,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Hose length
            if ($this->airless_hose_length > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_airless_hose_length'),
                    'value' => $this->airless_hose_length,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_m'),
                ];
            }

            // Grinding plate
            if ($this->grinder_grinding_plate > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_grinder_grinding_plate'),
                    'value' => $this->grinder_grinding_plate,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_cm2'),
                ];
            }

            // Grinding wheel
            if ($this->grinder_grinding_wheel > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_grinder_grinding_wheel'),
                    'value' => $this->grinder_grinding_wheel,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Grinding wheel
            if ('' !== $this->grinder_rotational_frequency) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_grinder_rotational_frequency'),
                    'value' => $this->grinder_rotational_frequency,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_rotations_min'),
                ];
            }

            // Vacuum connection
            if ($this->grinder_vacuum_connection > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_grinder_vacuum_connection'),
                    'value' => $this->grinder_vacuum_connection,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Sanding
            if ('' !== $this->grinder_sanding) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_grinder_sanding'),
                    'value' => $this->grinder_sanding,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_rotations_min'),
                ];
            }

            // Cutting length
            if ($this->cutters_cutting_length > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_cutters_cutting_length'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->cutters_cutting_length,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_cm'),
                ];
            }

            // Cutting depth
            if ('' !== $this->cutters_cutting_depth) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_cutters_cutting_depth'),
                    'value' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_max') .' '. $this->cutters_cutting_depth,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_cm'),
                ];
            }

            // Rod length
            if ('' !== $this->cutters_rod_length) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_cutters_rod_length'),
                    'value' => $this->cutters_rod_length,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Conveying wave
            if ('' !== $this->container_conveying_wave) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_container_conveying_wave'),
                    'value' => $this->container_conveying_wave,
                    'unit' => '',
                ];
            }

            // Water connection
            if ('' !== $this->container_waterconnect_diameter && $this->container_waterconnect_pressure > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_container_waterconnect'),
                    'value' => $this->container_waterconnect_pressure .' ('. $this->container_waterconnect_diameter .'")',
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_bar'),
                ];
            }

            // Connection port
            if ('' !== $this->container_connection_port) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_container_connection_port'),
                    'value' => $this->container_connection_port,
                    'unit' => '',
                ];
            }

            // Machine technique
            if (false !== $this->category) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_machine_technique'),
                    'value' => '<a href="'. \rex_escape($this->category->getUrl(), 'html_attr') .'">'. \rex_escape($this->category->name) .'</a>',
                    'unit' => '',
                ];
            }

            // Working width
            if ($this->floor_working_width > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_floor_working_width'),
                    'value' => $this->floor_working_width,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Working pressure
            if ('' !== $this->floor_working_pressure) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_floor_working_pressure'),
                    'value' => $this->floor_working_pressure,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_kg') .'/'. \Sprog\Wildcard::get('d2u_machinery_unit_cm2'),
                ];
            }

            // Dust extraction connection
            if ($this->floor_dust_extraction_connection > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_floor_dust_extraction_connection'),
                    'value' => $this->floor_dust_extraction_connection,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Feedrate
            if ('' !== $this->floor_feedrate) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_floor_feedrate'),
                    'value' => $this->floor_feedrate,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_m_min'),
                ];
            }

            // Beam power on concrete
            if ($this->floor_beam_power_on_concrete > 0) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_floor_beam_power_on_concrete'),
                    'value' => $this->floor_beam_power_on_concrete,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_m2_h'),
                ];
            }

            // Filter connection
            if ('' !== $this->floor_filter_connection) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_floor_filter_connection'),
                    'value' => $this->floor_filter_connection,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
                ];
            }

            // Rotations
            if ('' !== $this->floor_rotations) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_floor_rotations'),
                    'value' => $this->floor_rotations,
                    'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_min1'),
                ];
            }

            // Technical description
            if ('' !== $this->description_technical) {
                $tech_data[] = [
                    'description' => \Sprog\Wildcard::get('d2u_machinery_construction_equipment_description_technical'),
                    'value' => $this->description_technical,
                    'unit' => '',
                ];
            }
        }

        // Dimensions
        if ($this->length > 0 && $this->width > 0 && $this->height > 0) {
            $tech_data[] = [
                'description' => \Sprog\Wildcard::get('d2u_machinery_dimensions_length_width_height'),
                'value' => $this->length .' x '. $this->width .' x '. $this->height,
                'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
            ];
        } elseif ($this->depth > 0 && $this->width > 0 && $this->height > 0) {
            $tech_data[] = [
                'description' => \Sprog\Wildcard::get('d2u_machinery_dimensions_width_height_depth'),
                'value' => $this->width .' x '. $this->height	.' x '. $this->depth,
                'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
            ];
        } elseif ($this->length > 0) {
            $tech_data[] = [
                'description' => \Sprog\Wildcard::get('d2u_machinery_dimensions_length'),
                'value' => $this->length,
                'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_mm'),
            ];
        }

        // Weight
        if ('' !== $this->weight) {
            $tech_data[] = [
                'description' => \Sprog\Wildcard::get('d2u_machinery_weight'),
                'value' => \Sprog\Wildcard::get('d2u_machinery_unit_ca') .' '. $this->weight,
                'unit' => \Sprog\Wildcard::get('d2u_machinery_unit_kg'),
            ];
        }

        return $tech_data;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Machine[] array with Machine objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT lang.machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang '
                .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS main '
                    .'ON lang.machine_id = main.machine_id '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS target_lang '
                        .'ON main.machine_id = target_lang.machine_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS default_lang '
                        .'ON main.machine_id = default_lang.machine_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.machine_id IS NULL '
                    .'ORDER BY main.name';
            $clang_id = (int) \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('machine_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Returns the URL of this object.
     * @param bool $including_domain true if Domain name should be included
     * @return string URL
     */
    public function getUrl(bool $including_domain = false): string
    {
        if ('' === $this->url) {
            $parameterArray = [];
            $parameterArray['machine_id'] = $this->machine_id;
            $this->url = rex_getUrl((int) rex_config::get('d2u_machinery', 'article_id'), $this->clang_id, $parameterArray, '&');
        }

        if ($including_domain) {
            if (\rex_addon::get('yrewrite') instanceof rex_addon && \rex_addon::get('yrewrite')->isAvailable()) {
                return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
            }

            return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);

        }

        return $this->url;

    }

    /**
     * Translate this machine from a source language into its own (target)
     * language using ai_platform and store the result. The internal name is
     * shared across languages and is not translated.
     * @param int $sourceClangId Redaxo clang id of the source language
     * @return bool true on success
     */
    public function translateFrom(int $sourceClangId): bool
    {
        if ($this->machine_id <= 0 || $sourceClangId === $this->clang_id) {
            return false;
        }

        $source = new self($this->machine_id, $sourceClangId);
        if ($source->machine_id <= 0) {
            return false;
        }

        try {
            $translated = \TobiasKrais\D2UHelper\AiTranslationHelper::translateFields([
                'lang_name' => ['value' => $source->lang_name, 'html' => false],
                'teaser' => ['value' => $source->teaser, 'html' => true],
                'description' => ['value' => $source->description, 'html' => true],
                'benefits_long' => ['value' => $source->benefits_long, 'html' => true],
                'benefits_short' => ['value' => $source->benefits_short, 'html' => true],
            ], $sourceClangId, $this->clang_id);
        } catch (\Throwable $e) {
            \rex_logger::logException($e);
            return false;
        }

        $this->lang_name = $translated['lang_name'];
        $this->teaser = $translated['teaser'];
        $this->description = $translated['description'];
        $this->benefits_long = $translated['benefits_long'];
        $this->benefits_short = $translated['benefits_short'];
        $this->translation_needs_update = 'no';

        // save() returns true on success.
        return $this->save();
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save(): bool
    {
        $error = false;

        // Save the not language specific part
        $pre_save_object = new self($this->machine_id, $this->clang_id);

        $regenerate_urls = false;
        if (0 === $this->machine_id || $pre_save_object !== $this) {
            $main_params = [':name' => $this->name];
            $query = \rex::getTablePrefix() .'d2u_machinery_machines SET '
                    .'name = :name, '
                    .'pics = :pics, '
                    .'category_id = '. ($this->category instanceof Category ? (int) $this->category->category_id : 0) .', '
                    .'alternative_machine_ids = :alternative_machine_ids, '
                    .'additional_machine_ids = :additional_machine_ids, '
                    .'product_number = :product_number, '
                    .'article_id_software = :article_id_software, '
                    .'article_id_service = :article_id_service, '
                    .'article_ids_references = :article_ids_references, '
                    .'reference_ids = :reference_ids, '
                    .'online_status = :online_status, '
                    .'engine_power = :engine_power, '
                    .'engine_power_frequency_controlled = :engine_power_frequency_controlled, '
                    .'length = :length, '
                    .'width = :width, '
                    .'height = :height, '
                    .'depth = :depth, '
                    .'weight = :weight, '
                    .'operating_voltage_v = :operating_voltage_v, '
                    .'operating_voltage_hz = :operating_voltage_hz, '
                    .'operating_voltage_a = :operating_voltage_a ';
            $main_params[':pics'] = implode(',', $this->pics);
            $main_params[':alternative_machine_ids'] = '|'. implode('|', $this->alternative_machine_ids) .'|';
            $main_params[':additional_machine_ids'] = '|'. implode('|', $this->additional_machine_ids) .'|';
            $main_params[':product_number'] = $this->product_number;
            $main_params[':article_id_software'] = $this->article_id_software;
            $main_params[':article_id_service'] = $this->article_id_service;
            $main_params[':article_ids_references'] = implode(',', $this->article_ids_references);
            $main_params[':reference_ids'] = implode(',', $this->reference_ids);
            $main_params[':online_status'] = $this->online_status;
            $main_params[':engine_power'] = $this->engine_power;
            $main_params[':engine_power_frequency_controlled'] = $this->engine_power_frequency_controlled ? 'true' : 'false';
            $main_params[':length'] = $this->length;
            $main_params[':width'] = $this->width;
            $main_params[':height'] = $this->height;
            $main_params[':depth'] = $this->depth;
            $main_params[':weight'] = $this->weight;
            $main_params[':operating_voltage_v'] = $this->operating_voltage_v;
            $main_params[':operating_voltage_hz'] = $this->operating_voltage_hz;
            $main_params[':operating_voltage_a'] = $this->operating_voltage_a;
            if (Extension::isActive('contacts')) {
                $query .= ', contact_id = '. ($this->contact instanceof Contact ? (int) $this->contact->contact_id : 0) .' ';
            }
            if (Extension::isActive('equipment')) {
                $query .= ', equipment_ids = :equipment_ids ';
                $main_params[':equipment_ids'] = '|'. implode('|', $this->equipment_ids) .'|';
            }
            if (Extension::isActive('industry_sectors')) {
                $query .= ', industry_sector_ids = :industry_sector_ids ';
                $main_params[':industry_sector_ids'] = '|'. implode('|', $this->industry_sector_ids) .'|';
            }
            if (Extension::isActive('machine_agitator_extension')) {
                $query .= ', agitator_type_id = '. (int) $this->agitator_type_id .' '
                    .', viscosity = '. (int) $this->viscosity .' ';
            }
            if (Extension::isActive('machine_certificates_extension')) {
                $query .= ', certificate_ids = :certificate_ids ';
                $main_params[':certificate_ids'] = '|'. implode('|', $this->certificate_ids) .'|';
            }
            if (Extension::isActive('machine_construction_equipment_extension')) {
                $query .= ', airless_hose_connection = :airless_hose_connection '
                    .', airless_hose_diameter = '. (int) $this->airless_hose_diameter .' '
                    .', airless_hose_length = '. (int) $this->airless_hose_length .' '
                    .', airless_nozzle_size = :airless_nozzle_size '
                    .', container_capacity = :container_capacity '
                    .', container_capacity_unit = :container_capacity_unit '
                    .', container_mixing_performance = :container_mixing_performance '
                    .', container_waterconnect_pressure = '. (int) $this->container_waterconnect_pressure .' '
                    .', container_waterconnect_diameter = :container_waterconnect_diameter '
                    .', container_weight_empty = '. ($this->container_weight_empty > 0 ? (int) $this->container_weight_empty : 0) .' '
                    .', cutters_cutting_depth = :cutters_cutting_depth '
                    .', cutters_cutting_length = '. ($this->cutters_cutting_length > 0 ? (int) $this->cutters_cutting_length : 0) .' '
                    .', cutters_rod_length = :cutters_rod_length '
                    .', floor_beam_power_on_concrete = '. ($this->floor_beam_power_on_concrete > 0 ? (int) $this->floor_beam_power_on_concrete : 0) .' '
                    .', floor_dust_extraction_connection = '. ($this->floor_dust_extraction_connection > 0 ? (int) $this->floor_dust_extraction_connection : 0) .' '
                    .', floor_feedrate = :floor_feedrate '
                    .', floor_filter_connection = :floor_filter_connection '
                    .', floor_rotations = :floor_rotations '
                    .', floor_working_pressure = :floor_working_pressure '
                    .', floor_working_width = '. (int) $this->floor_working_width .' '
                    .', grinder_grinding_plate = '. (int) $this->grinder_grinding_plate .' '
                    .', grinder_grinding_wheel = '. (int) $this->grinder_grinding_wheel .' '
                    .', grinder_rotational_frequency = :grinder_rotational_frequency '
                    .', grinder_sanding = :grinder_sanding '
                    .', grinder_vacuum_connection = '. (int) $this->grinder_vacuum_connection .' '
                    .', operating_pressure = :operating_pressure '
                    .', pictures_delivery_set = :pictures_delivery_set '
                    .', pump_conveying_distance = '. ($this->pump_conveying_distance > 0 ? (int) $this->pump_conveying_distance : 0) .' '
                    .', pump_filling = '. ($this->pump_filling > 0 ? (int) $this->pump_filling : 0) .' '
                    .', pump_flow_volume = :pump_flow_volume '
                    .', pump_grain_size = :pump_grain_size '
                    .', pump_material_container = :pump_material_container '
                    .', pump_pressure_height = '. ($this->pump_pressure_height > 0 ? (int) $this->pump_pressure_height : 0) .' '
                    .', waste_water_capacity = '. ($this->waste_water_capacity > 0 ? (int) $this->waste_water_capacity : 0) .' ';
                $main_params[':airless_hose_connection'] = $this->airless_hose_connection;
                $main_params[':airless_nozzle_size'] = $this->airless_nozzle_size;
                $main_params[':container_capacity'] = $this->container_capacity;
                $main_params[':container_capacity_unit'] = $this->container_capacity_unit;
                $main_params[':container_mixing_performance'] = $this->container_mixing_performance;
                $main_params[':container_waterconnect_diameter'] = $this->container_waterconnect_diameter;
                $main_params[':cutters_cutting_depth'] = $this->cutters_cutting_depth;
                $main_params[':cutters_rod_length'] = $this->cutters_rod_length;
                $main_params[':floor_feedrate'] = $this->floor_feedrate;
                $main_params[':floor_filter_connection'] = $this->floor_filter_connection;
                $main_params[':floor_rotations'] = $this->floor_rotations;
                $main_params[':floor_working_pressure'] = $this->floor_working_pressure;
                $main_params[':grinder_rotational_frequency'] = $this->grinder_rotational_frequency;
                $main_params[':grinder_sanding'] = $this->grinder_sanding;
                $main_params[':operating_pressure'] = $this->operating_pressure;
                $main_params[':pictures_delivery_set'] = implode(',', $this->pictures_delivery_set);
                $main_params[':pump_flow_volume'] = $this->pump_flow_volume;
                $main_params[':pump_grain_size'] = $this->pump_grain_size;
                $main_params[':pump_material_container'] = $this->pump_material_container;
            }
            if (Extension::isActive('service_options')) {
                $query .= ', service_option_ids = :service_option_ids ';
                $main_params[':service_option_ids'] = '|'. implode('|', $this->service_option_ids) .'|';
            }
            if (Extension::isActive('machine_features_extension')) {
                $query .= ', feature_ids = :feature_ids ';
                $main_params[':feature_ids'] = '|'. implode('|', $this->feature_ids) .'|';
            }
            if (Extension::isActive('machine_options_extension')) {
                $query .= ', option_ids = :option_ids ';
                $main_params[':option_ids'] = '|'. implode('|', $this->option_ids) .'|';
            }
            if (Extension::isActive('machine_steel_automation_extension')) {
                $query .= ', automation_supply_ids = :automation_supply_ids ';
                $main_params[':automation_supply_ids'] = '|'. implode('|', $this->automation_supply_ids) .'|';
            }
            if (Extension::isActive('machine_usage_area_extension')) {
                $query .= ', usage_area_ids = :usage_area_ids ';
                $main_params[':usage_area_ids'] = '|'. implode('|', $this->usage_area_ids) .'|';
            }
            if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && count($this->videos) > 0) {
                $query .= ', video_ids = :video_ids ';
                $main_params[':video_ids'] = '|'. implode('|', array_keys($this->videos)) .'|';
            } else {
                $query .= ", video_ids = '' ";
            }

            if (0 === $this->machine_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE machine_id = '. (int) $this->machine_id;
            }
            $result = \rex_sql::factory();
            $result->setQuery($query, $main_params);
            if (0 === $this->machine_id) {
                $this->machine_id = (int) $result->getLastId();
                $error = $result->hasError();
            }

            if (!$error && $pre_save_object->name !== $this->name) {
                $regenerate_urls = true;
            }
        }
        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_object->priority || 0 === $this->machine_id) {
            $this->setPriority();
        }

        if (false === $error) {
            // Save the language specific part
            $pre_save_object = new self($this->machine_id, $this->clang_id);
            if ($pre_save_object !== $this) {
                $query = 'REPLACE INTO '. \rex::getTablePrefix() .'d2u_machinery_machines_lang SET '
                        .'machine_id = :machine_id, '
                        .'clang_id = :clang_id, '
                        .'lang_name = :lang_name, '
                        .'teaser = :teaser, '
                        .'description = :description, '
                        .'benefits_long = :benefits_long, '
                        .'benefits_short = :benefits_short, '
                        .'leaflet = :leaflet, '
                        .'pdfs = :pdfs, '
                        .'translation_needs_update = :translation_needs_update, '
                        .'updatedate = CURRENT_TIMESTAMP, '
                        .'updateuser = :updateuser ';
                $lang_params = [
                    ':machine_id' => $this->machine_id,
                    ':clang_id' => $this->clang_id,
                    ':lang_name' => $this->lang_name,
                    ':teaser' => htmlspecialchars($this->teaser),
                    ':description' => htmlspecialchars($this->description),
                    ':benefits_long' => htmlspecialchars($this->benefits_long),
                    ':benefits_short' => htmlspecialchars($this->benefits_short),
                    ':leaflet' => $this->leaflet,
                    ':pdfs' => implode(',', $this->pdfs),
                    ':translation_needs_update' => $this->translation_needs_update,
                    ':updateuser' => \rex::getUser() instanceof rex_user ? \rex::getUser()->getLogin() : '',
                ];
                if (Extension::isActive('machine_construction_equipment_extension')) {
                    $query .= ', container_connection_port = :container_connection_port '
                        .', container_conveying_wave = :container_conveying_wave '
                        .', description_technical = :description_technical '
                        .', delivery_set_basic = :delivery_set_basic '
                        .', delivery_set_conversion = :delivery_set_conversion '
                        .', delivery_set_full = :delivery_set_full ';
                    $lang_params[':container_connection_port'] = $this->container_connection_port;
                    $lang_params[':container_conveying_wave'] = $this->container_conveying_wave;
                    $lang_params[':description_technical'] = htmlspecialchars($this->description_technical);
                    $lang_params[':delivery_set_basic'] = htmlspecialchars($this->delivery_set_basic);
                    $lang_params[':delivery_set_conversion'] = htmlspecialchars($this->delivery_set_conversion);
                    $lang_params[':delivery_set_full'] = htmlspecialchars($this->delivery_set_full);
                }
                $result = \rex_sql::factory();
                $result->setQuery($query, $lang_params);
                $error = $result->hasError();

                if (!$error && $pre_save_object->lang_name !== $this->lang_name) {
                    $regenerate_urls = true;
                }
            }
        }

        // Don't forget to regenerate URL cache / search_it index
        \TobiasKrais\D2UHelper\BackendHelper::generateUrlCache();

        return !$error;
    }

    /**
     * Reassigns priorities in database.
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority($delete = false): void
    {
        // Pull prios from database
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            .'WHERE machine_id <> '. $this->machine_id .' ORDER BY priority, machine_id';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        // When priority is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = $result->getRows() + 1;
        }

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machines[] = (int) $result->getValue('machine_id');
            $result->next();
        }
        array_splice($machines, $this->priority - 1, 0, [$this->machine_id]);

        // Save all prios
        foreach ($machines as $prio => $machine_id) {
            $query = 'UPDATE '. \rex::getTablePrefix() .'d2u_machinery_machines '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE machine_id = '. $machine_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);
        }
    }
}
