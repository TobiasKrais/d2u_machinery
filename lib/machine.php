<?php
/**
 * Redaxo D2U Machines Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine.
 */
class Machine implements \D2U_Helper\ITranslationHelper
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

    /** @var \D2U_Machinery\Contact Machine contact */
    public \D2U_Machinery\Contact|false $contact = false;

    /** @var int[] Usage area IDs */
    public array $usage_area_ids = [];

    /** @var int[] IDs of alternative machines */
    public array $alternative_machine_ids = [];

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

    /** @var Process[] machine_steel_processing_extension: Process objects */
    public array $processes = [];

    /** @var Procedure[] machine_steel_processing_extension: Procedure objects */
    public array $procedures = [];

    /** @var Material[] machine_steel_processing_extension: Material objects */
    public array $materials = [];

    /** @var Tool[] machine_steel_processing_extension: Tool objects */
    public array $tools = [];

    /** @var string machine_steel_processing_extension: Automation - Supply single stroke (mm) */
    public string $automation_supply_single_stroke = '';

    /** @var string machine_steel_processing_extension: Automation - Multiple single stroke (mm) */
    public string $automation_supply_multi_stroke = '';

    /** @var string machine_steel_processing_extension: Automation - Feed rate range (mm/min) */
    public string $automation_feedrate = '';

    /** @var string machine_steel_processing_extension: Automation - Feed rate range for saw blades (mm/min) */
    public string $automation_feedrate_sawblade = '';

    /** @var int machine_steel_processing_extension: Automation - Rush leader flyback (mm/min) */
    public int $automation_rush_leader_flyback = 0;

    /** @var Automation[] machine_steel_processing_extension: Automation grade objects */
    public array $automation_automationgrades = [];

    /** @var int[] machine_steel_processing_extension: Automation supply ids */
    public array $automation_supply_ids = [];

    /** @var string machine_steel_processing_extension: Workspace (mm x mm or only mm) */
    public string $workspace = '';

    /** @var string machine_steel_processing_extension: Workspace square material (mm x mm or only mm) */
    public string $workspace_square = '';

    /** @var string machine_steel_processing_extension: Workspace flat material (mm x mm or only mm) */
    public string $workspace_flat = '';

    /** @var string machine_steel_processing_extension: Workspace plates (mm x mm or only mm) */
    public string $workspace_plate = '';

    /** @var string machine_steel_processing_extension: Workspace for profiles (mm x mm or only mm) */
    public string $workspace_profile = '';

    /** @var string machine_steel_processing_extension: Workspace for angle steels (mm x mm x mm) */
    public string $workspace_angle_steel = '';

    /** @var string machine_steel_processing_extension: Workspace for round materials (mm x mm or only mm) */
    public string $workspace_round = '';

    /** @var string machine_steel_processing_extension: Minimum Workspace (mm x mm x mm or mm x mm or only mm) */
    public string $workspace_min = '';

    /** @var string machine_steel_processing_extension: sheet width range (mm) */
    public string $sheet_width = '';

    /** @var string machine_steel_processing_extension: sheet length range (mm) */
    public string $sheet_length = '';

    /** @var string machine_steel_processing_extension: sheet thickness ragne (mm) */
    public string $sheet_thickness = '';

    /** @var string machine_steel_processing_extension: number of tool changer locations */
    public string $tool_changer_locations = '';

    /** @var int machine_steel_processing_extension: number of drilling units from below */
    public int $drilling_unit_below = 0;

    /** @var string machine_steel_processing_extension: number of vertical drilling units */
    public string $drilling_unit_vertical = '';

    /** @var string machine_steel_processing_extension: number of horizontal drilling units */
    public string $drilling_unit_horizontal = '';

    /** @var string machine_steel_processing_extension: drilling whole diameter (range in mm) */
    public string $drilling_diameter = '';

    /** @var string machine_steel_processing_extension: number of drilling tools per axis */
    public string $drilling_tools_axis = '';

    /** @var string machine_steel_processing_extension: axis driver power */
    public string $drilling_axis_drive_power = '';

    /** @var string machine_steel_processing_extension: drilling speed (rpm) */
    public string $drilling_rpm_speed = '';

    /** @var string machine_steel_processing_extension: saw blade diameter (mm, sometimes mm x mm) */
    public string $saw_blade = '';

    /** @var string machine_steel_processing_extension: saw band dimensions (mm x mm x mm) */
    public string $saw_band = '';

    /** @var string machine_steel_processing_extension: saw band tilt range (°) */
    public string $saw_band_tilt = '';

    /** @var string machine_steel_processing_extension: saw cutting speed range (mm) */
    public string $saw_cutting_speed = '';

    /** @var string machine_steel_processing_extension: saw miter (°) */
    public string $saw_miter = '';

    /** @var int machine_steel_processing_extension: Max. bevel angle (°). */
    public int $bevel_angle = 0;

    /** @var string machine_steel_processing_extension: punching diameter range (mm) */
    public string $punching_diameter = '';

    /** @var int machine_steel_processing_extension: punching power */
    public int $punching_power = 0;

    /** @var string machine_steel_processing_extension: number of punching tools */
    public string $punching_tools = '';

    /** @var int machine_steel_processing_extension: angle steel single cut */
    public int $shaving_unit_angle_steel_single_cut = 0;

    /** @var Profile[] machine_steel_processing_extension: Profile area objects */
    public array $profiles = [];

    /** @var string machine_steel_processing_extension: carrier width (mm) */
    public string $carrier_width = '';

    /** @var string machine_steel_processing_extension: carrier height (mm) */
    public string $carrier_height = '';

    /** @var int machine_steel_processing_extension: carrier weight (kg) */
    public int $carrier_weight = 0;

    /** @var string machine_steel_processing_extension: flange thickness min. / max. (mm). */
    public string $flange_thickness = '';

    /** @var string machine_steel_processing_extension: web thickness min. / max. (mm). */
    public string $web_thickness = '';

    /** @var string machine_steel_processing_extension: component length min. / max. (mm). */
    public string $component_length = '';

    /** @var int machine_steel_processing_extension: component weight (kg) */
    public int $component_weight = 0;

    /** @var Welding[] machine_steel_processing_extension: welding process objects */
    public array $weldings = [];

    /** @var int machine_steel_processing_extension: welding thickness ((a) mm) */
    public int $welding_thickness = 0;

    /** @var string machine_steel_processing_extension: welding_wire_thickness (mm) */
    public string $welding_wire_thickness = '';

    /** @var string machine_steel_processing_extension: beam machine continuous opening (mm) */
    public string $beam_continuous_opening = '';

    /** @var int machine_steel_processing_extension: number of turbines */
    public int $beam_turbines = 0;

    /** @var string machine_steel_processing_extension: beam machine power per turbine (kW) */
    public string $beam_turbine_power = '';

    /** @var string machine_steel_processing_extension: beam machine number of color guns */
    public string $beam_color_guns = '';

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

    /** @var Video[] Videomanager videos */
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

    /** @var string Containers: maximum capacity (kg) */
    public string $container_capacity = '';

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
            $this->product_number = (string) $result->getValue('product_number');
            $this->article_id_software = (int) $result->getValue('article_id_software');
            $this->article_id_service = (int) $result->getValue('article_id_service');
            $article_ids_references = preg_grep('/^\s*$/s', explode(',', (string) $result->getValue('article_ids_references')), PREG_GREP_INVERT);
            $this->article_ids_references = is_array($article_ids_references) ? $article_ids_references : [];
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

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'contacts')->isAvailable()) {
                if ((int) $result->getValue('contact_id') > 0) {
                    $this->contact = new D2U_Machinery\Contact((int) $result->getValue('contact_id'));
                }
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'equipment')->isAvailable()) {
                $equipment_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('equipment_ids')), PREG_GREP_INVERT);
                $this->equipment_ids = is_array($equipment_ids) ? array_map('intval', $equipment_ids) : [];
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                $industry_sector_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('industry_sector_ids')), PREG_GREP_INVERT);
                $this->industry_sector_ids = is_array($industry_sector_ids) ? array_map('intval', $industry_sector_ids) : [];
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'machine_agitator_extension')->isAvailable()) {
                $this->agitator_type_id = (int) $result->getValue('agitator_type_id');
                $this->viscosity = (int) $result->getValue('viscosity');
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'machine_certificates_extension')->isAvailable()) {
                $certificate_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('certificate_ids')), PREG_GREP_INVERT);
                $this->certificate_ids = is_array($certificate_ids) ? array_map('intval', $certificate_ids) : [];
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
                $this->airless_hose_connection = (string) $result->getValue('airless_hose_connection');
                $this->airless_hose_diameter = (int) $result->getValue('airless_hose_diameter');
                $this->airless_hose_length = (int) $result->getValue('airless_hose_length');
                $this->airless_nozzle_size = (string) $result->getValue('airless_nozzle_size');
                $this->container_capacity = (string) $result->getValue('container_capacity');
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

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'service_options')->isAvailable()) {
                $service_option_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('service_option_ids')), PREG_GREP_INVERT);
                $this->service_option_ids = is_array($service_option_ids) ? array_map('intval', $service_option_ids) : [];
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'machine_features_extension')->isAvailable()) {
                $feature_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('feature_ids')), PREG_GREP_INVERT);
                $this->feature_ids = is_array($feature_ids) ? array_map('intval', $feature_ids) : [];
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'machine_options_extension')->isAvailable()) {
                $option_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('option_ids')), PREG_GREP_INVERT);
                $this->option_ids = is_array($option_ids) ? array_map('intval', $option_ids) : [];
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                $process_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('process_ids')), PREG_GREP_INVERT);
                if (is_array($process_ids)) {
                    foreach ($process_ids as $process_id) {
                        $this->processes[$process_id] = new Process($process_id, $this->clang_id);
                    }
                }
                $procedure_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('procedure_ids')), PREG_GREP_INVERT);
                if (is_array($procedure_ids)) {
                    foreach ($procedure_ids as $procedure_id) {
                        $this->procedures[$procedure_id] = new Procedure($procedure_id, $this->clang_id);
                    }
                }
                $material_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('material_ids')), PREG_GREP_INVERT);
                if (is_array($material_ids)) {
                    foreach ($material_ids as $material_id) {
                        $this->materials[$material_id] = new Material($material_id, $this->clang_id);
                    }
                }
                $tool_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('tool_ids')), PREG_GREP_INVERT);
                if (is_array($tool_ids)) {
                    foreach ($tool_ids as $tool_id) {
                        $this->tools[$tool_id] = new Tool($tool_id, $this->clang_id);
                    }
                }
                $this->automation_supply_single_stroke = (string) $result->getValue('automation_supply_single_stroke');
                $this->automation_supply_multi_stroke = (string) $result->getValue('automation_supply_multi_stroke');
                $this->automation_feedrate = (string) $result->getValue('automation_feedrate');
                $this->automation_feedrate_sawblade = (string) $result->getValue('automation_feedrate_sawblade');
                $this->automation_rush_leader_flyback = (int) $result->getValue('automation_rush_leader_flyback');
                $automation_automationgrade_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('automation_automationgrade_ids')), PREG_GREP_INVERT);
                if (is_array($automation_automationgrade_ids)) {
                    foreach ($automation_automationgrade_ids as $automation_automationgrade_id) {
                        $this->automation_automationgrades[$automation_automationgrade_id] = new Automation($automation_automationgrade_id, $this->clang_id);
                    }
                }
                $automation_supply_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('automation_supply_ids')), PREG_GREP_INVERT);
                $this->automation_supply_ids = is_array($automation_supply_ids) ? array_map('intval', $automation_supply_ids) : [];
                $this->workspace = (string) $result->getValue('workspace');
                $this->workspace_square = (string) $result->getValue('workspace_square');
                $this->workspace_flat = (string) $result->getValue('workspace_flat');
                $this->workspace_plate = (string) $result->getValue('workspace_plate');
                $this->workspace_profile = (string) $result->getValue('workspace_profile');
                $this->workspace_angle_steel = (string) $result->getValue('workspace_angle_steel');
                $this->workspace_round = (string) $result->getValue('workspace_round');
                $this->workspace_min = (string) $result->getValue('workspace_min');
                $this->sheet_width = (string) $result->getValue('sheet_width');
                $this->sheet_length = (string) $result->getValue('sheet_length');
                $this->sheet_thickness = (string) $result->getValue('sheet_thickness');
                $this->tool_changer_locations = (string) $result->getValue('tool_changer_locations');
                $this->drilling_unit_below = (int) $result->getValue('drilling_unit_below');
                $this->drilling_unit_vertical = (string) $result->getValue('drilling_unit_vertical');
                $this->drilling_unit_horizontal = (string) $result->getValue('drilling_unit_horizontal');
                $this->drilling_diameter = (string) $result->getValue('drilling_diameter');
                $this->drilling_tools_axis = (string) $result->getValue('drilling_tools_axis');
                $this->drilling_axis_drive_power = (string) $result->getValue('drilling_axis_drive_power');
                $this->drilling_rpm_speed = (string) $result->getValue('drilling_rpm_speed');
                $this->saw_blade = (string) $result->getValue('saw_blade');
                $this->saw_band = (string) $result->getValue('saw_band');
                $this->saw_band_tilt = (string) $result->getValue('saw_band_tilt');
                $this->saw_cutting_speed = (string) $result->getValue('saw_cutting_speed');
                $this->saw_miter = (string) $result->getValue('saw_miter');
                $this->bevel_angle = (int) $result->getValue('bevel_angle');
                $this->punching_diameter = (string) $result->getValue('punching_diameter');
                $this->punching_power = (int) $result->getValue('punching_power');
                $this->punching_tools = (string) $result->getValue('punching_tools');
                $this->shaving_unit_angle_steel_single_cut = (int) $result->getValue('shaving_unit_angle_steel_single_cut');
                $profile_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('profile_ids')), PREG_GREP_INVERT);
                if (is_array($profile_ids)) {
                    foreach ($profile_ids as $profile_id) {
                        $this->profiles[$profile_id] = new Profile($profile_id, $this->clang_id);
                    }
                }
                $this->carrier_width = (string) $result->getValue('carrier_width');
                $this->carrier_height = (string) $result->getValue('carrier_height');
                $this->carrier_weight = (int) $result->getValue('carrier_weight');
                $this->flange_thickness = (string) $result->getValue('flange_thickness');
                $this->web_thickness = (string) $result->getValue('web_thickness');
                $this->component_length = (string) $result->getValue('component_length');
                $this->component_weight = (int) $result->getValue('component_weight');
                $welding_process_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('welding_process_ids')), PREG_GREP_INVERT);
                if (is_array($welding_process_ids)) {
                    foreach ($welding_process_ids as $welding_process_id) {
                        $this->weldings[$welding_process_id] = new Welding($welding_process_id, $this->clang_id);
                    }
                }
                $this->welding_thickness = (int) $result->getValue('welding_thickness');
                $this->welding_wire_thickness = (string) $result->getValue('welding_wire_thickness');
                $this->beam_continuous_opening = (string) $result->getValue('beam_continuous_opening');
                $this->beam_turbines = (int) $result->getValue('beam_turbines');
                $this->beam_turbine_power = (string) $result->getValue('beam_turbine_power');
                $this->beam_color_guns = (string) $result->getValue('beam_color_guns');
            }

            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
                $usage_area_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('usage_area_ids')), PREG_GREP_INVERT);
                $this->usage_area_ids = is_array($usage_area_ids) ? array_map('intval', $usage_area_ids) : [];
            }

            // Videos
            if (rex_plugin::get('d2u_machinery', 'contacts') instanceof rex_plugin && \rex_addon::get('d2u_videos')->isAvailable() && '' !== $result->getValue('video_ids')) {
                $video_ids = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('video_ids')), PREG_GREP_INVERT);
                if (is_array($video_ids)) {
                    foreach ($video_ids as $video_id) {
                        if ($video_id > 0) {
                            $video = new Video($video_id, $clang_id);
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
        \d2u_addon_backend_helper::generateUrlCache();
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
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
                .'WHERE machine_id = '. $this->machine_id;
            $result = \rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }

        // Don't forget to regenerate URL cache / search_it index
        \d2u_addon_backend_helper::generateUrlCache();

        // Delete from YRewrite forward list
        if (rex_addon::get('yrewrite')->isAvailable()) {
            if ($delete_all) {
                foreach (rex_clang::getAllIds() as $clang_id) {
                    $lang_object = new self($this->machine_id, $clang_id);
                    $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                        ."WHERE extern = '". $lang_object->getUrl(true) ."'";
                    $result_forward = \rex_sql::factory();
                    $result_forward->setQuery($query_forward);
                }
            } else {
                $query_forward = 'DELETE FROM '. \rex::getTablePrefix() .'yrewrite_forward '
                    ."WHERE extern = '". $this->getUrl(true) ."'";
                $result_forward = \rex_sql::factory();
                $result_forward->setQuery($query_forward);
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
     * @return Feature[] array with Feature objects
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
     * Gets the machines referring to this machine as alternate machine.
     * @return Machine[] machines referring to this machine as alternate machine
     */
    public function getReferringMachines()
    {
        $query = 'SELECT machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            ."WHERE alternative_machine_ids LIKE '%|". $this->machine_id ."|%'";
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
     * Gets the production lines referring to this machine.
     * @return ProductionLine[] production lines referring to this machine
     */
    public function getReferringProductionLines()
    {
        if (rex_plugin::get('d2u_machinery', 'production_lines')->isAvailable()) {
            $query = 'SELECT production_line_id FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines '
                ."WHERE machine_ids LIKE '%|". $this->machine_id ."|%' OR complementary_machine_ids LIKE '%|". $this->machine_id ."|%'";
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
        if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
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
     * Get Technical Data as array.
     * @return mixed[] Array with technical data. Each element is an array itself.
     * First element ist the translation wildcard, second is the value and third
     * the unit.
     */
    public function getTechnicalData()
    {
        $tech_data = [];

        // Get placeholder wildcard tags
        $sprog = rex_addon::get('sprog');
        $tag_open = $sprog->getConfig('wildcard_open_tag');
        $tag_close = $sprog->getConfig('wildcard_close_tag');

        // Max. viscosity
        if (rex_plugin::get('d2u_machinery', 'machine_agitator_extension')->isAvailable() && $this->viscosity > 0) {
            $tech_data[] = [
                'description' => $tag_open . 'd2u_machinery_agitators_viscosity' . $tag_close,
                'value' => $this->viscosity,
                'unit' => $tag_open . 'd2u_machinery_agitators_mpas' . $tag_close,
            ];
        }

        if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
            // Operating pressure
            if ('' !== $this->operating_pressure) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_operating_pressure' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_up_to' . $tag_close .' '. $this->operating_pressure,
                    'unit' => $tag_open . 'd2u_machinery_unit_bar' . $tag_close,
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
                'description' => $tag_open . 'd2u_machinery_operating_voltage' . $tag_close,
                'value' => $v . $h . $a,
                'unit' => $tag_open . 'd2u_machinery_unit_v' . $tag_close .'/'. $tag_open . 'd2u_machinery_unit_hz' . $tag_close .'/'. $tag_open . 'd2u_machinery_unit_a' . $tag_close,
            ];
        }

        // Engine power
        if ('' !== $this->engine_power) {
            $tech_data[] = [
                'description' => $tag_open . 'd2u_machinery_engine_power' . $tag_close,
                'value' => $this->engine_power . ($this->engine_power_frequency_controlled ? ' ('. $tag_open . 'd2u_machinery_engine_power_frequency_controlled' . $tag_close .')' : ''),
                'unit' => $tag_open . 'd2u_machinery_unit_kw' . $tag_close,
            ];
        }

        if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
            // Water capacity
            if ($this->waste_water_capacity > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_waste_water_capacity' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->waste_water_capacity,
                    'unit' => $tag_open . 'd2u_machinery_unit_l' . $tag_close,
                ];
            }

            // Capacity
            if ('' !== $this->container_capacity) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_container_capacity' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->container_capacity,
                    'unit' => $tag_open . 'd2u_machinery_unit_kg' . $tag_close,
                ];
            }

            // Empty weight
            if ($this->container_weight_empty > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_container_weight_empty' . $tag_close,
                    'value' => $this->container_weight_empty,
                    'unit' => $tag_open . 'd2u_machinery_unit_kg' . $tag_close,
                ];
            }

            // Mixing performance
            if ('' !== $this->container_mixing_performance) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_container_mixing_performance' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->container_mixing_performance,
                    'unit' => $tag_open . 'd2u_machinery_unit_l_min' . $tag_close,
                ];
            }

            // Flow volume
            if ('' !== $this->pump_flow_volume) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_pump_flow_volume' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->pump_flow_volume,
                    'unit' => $tag_open . 'd2u_machinery_unit_l_min' . $tag_close,
                ];
            }

            // Conveying distance
            if ($this->pump_conveying_distance > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_pump_conveying_distance' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->pump_conveying_distance,
                    'unit' => $tag_open . 'd2u_machinery_unit_m' . $tag_close,
                ];
            }

            // Pressure height
            if ($this->pump_pressure_height > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_pump_pressure_height' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->pump_pressure_height,
                    'unit' => $tag_open . 'd2u_machinery_unit_m' . $tag_close,
                ];
            }

            // Grain size
            if ('' !== $this->pump_grain_size) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_pump_grain_size' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->pump_grain_size,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Nozzle size
            if ('' !== $this->airless_nozzle_size) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_airless_nozzle_size' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->airless_nozzle_size,
                    'unit' => '"',
                ];
            }

            // Nozzle size
            if ('' !== $this->pump_material_container) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_pump_material_container' . $tag_close,
                    'value' => $this->pump_material_container,
                    'unit' => $tag_open . 'd2u_machinery_unit_l' . $tag_close,
                ];
            }

            // Filling
            if ($this->pump_filling > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_pump_filling' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_unit_ca' . $tag_close .' '. $this->pump_filling,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Hose connection
            if ('' !== $this->airless_hose_connection) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_airless_hose_connection' . $tag_close,
                    'value' => $this->airless_hose_connection,
                    'unit' => '"',
                ];
            }

            // Hose diameter
            if ($this->airless_hose_diameter > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_airless_hose_diameter' . $tag_close,
                    'value' => $this->airless_hose_diameter,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Hose length
            if ($this->airless_hose_length > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_airless_hose_length' . $tag_close,
                    'value' => $this->airless_hose_length,
                    'unit' => $tag_open . 'd2u_machinery_unit_m' . $tag_close,
                ];
            }

            // Grinding plate
            if ($this->grinder_grinding_plate > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_grinder_grinding_plate' . $tag_close,
                    'value' => $this->grinder_grinding_plate,
                    'unit' => $tag_open . 'd2u_machinery_unit_cm2' . $tag_close,
                ];
            }

            // Grinding wheel
            if ($this->grinder_grinding_wheel > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_grinder_grinding_wheel' . $tag_close,
                    'value' => $this->grinder_grinding_wheel,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Grinding wheel
            if ('' !== $this->grinder_rotational_frequency) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_grinder_rotational_frequency' . $tag_close,
                    'value' => $this->grinder_rotational_frequency,
                    'unit' => $tag_open . 'd2u_machinery_unit_rotations_min' . $tag_close,
                ];
            }

            // Vacuum connection
            if ($this->grinder_vacuum_connection > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_grinder_vacuum_connection' . $tag_close,
                    'value' => $this->grinder_vacuum_connection,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Sanding
            if ('' !== $this->grinder_sanding) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_grinder_sanding' . $tag_close,
                    'value' => $this->grinder_sanding,
                    'unit' => $tag_open . 'd2u_machinery_unit_rotations_min' . $tag_close,
                ];
            }

            // Cutting length
            if ($this->cutters_cutting_length > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_cutters_cutting_length' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->cutters_cutting_length,
                    'unit' => $tag_open . 'd2u_machinery_unit_cm' . $tag_close,
                ];
            }

            // Cutting depth
            if ('' !== $this->cutters_cutting_depth) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_cutters_cutting_depth' . $tag_close,
                    'value' => $tag_open . 'd2u_machinery_construction_equipment_max' . $tag_close .' '. $this->cutters_cutting_depth,
                    'unit' => $tag_open . 'd2u_machinery_unit_cm' . $tag_close,
                ];
            }

            // Rod length
            if ('' !== $this->cutters_rod_length) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_cutters_rod_length' . $tag_close,
                    'value' => $this->cutters_rod_length,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Conveying wave
            if ('' !== $this->container_conveying_wave) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_container_conveying_wave' . $tag_close,
                    'value' => $this->container_conveying_wave,
                    'unit' => '',
                ];
            }

            // Water connection
            if ('' !== $this->container_waterconnect_diameter && $this->container_waterconnect_pressure > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_container_waterconnect' . $tag_close,
                    'value' => $this->container_waterconnect_pressure .' ('. $this->container_waterconnect_diameter .'")',
                    'unit' => $tag_open . 'd2u_machinery_unit_bar' . $tag_close,
                ];
            }

            // Connection port
            if ('' !== $this->container_connection_port) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_container_connection_port' . $tag_close,
                    'value' => $this->container_connection_port,
                    'unit' => '',
                ];
            }

            // Machine technique
            if (false !== $this->category) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_machine_technique' . $tag_close,
                    'value' => '<a href="'. $this->category->getUrl() .'">'. $this->category->name .'</a>',
                    'unit' => '',
                ];
            }

            // Working width
            if ($this->floor_working_width > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_floor_working_width' . $tag_close,
                    'value' => $this->floor_working_width,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Working pressure
            if ('' !== $this->floor_working_pressure) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_floor_working_pressure' . $tag_close,
                    'value' => $this->floor_working_pressure,
                    'unit' => $tag_open . 'd2u_machinery_unit_kg' . $tag_close .'/'. $tag_open . 'd2u_machinery_unit_cm2' . $tag_close,
                ];
            }

            // Dust extraction connection
            if ($this->floor_dust_extraction_connection > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_floor_dust_extraction_connection' . $tag_close,
                    'value' => $this->floor_dust_extraction_connection,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Feedrate
            if ('' !== $this->floor_feedrate) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_floor_feedrate' . $tag_close,
                    'value' => $this->floor_feedrate,
                    'unit' => $tag_open . 'd2u_machinery_unit_m_min' . $tag_close,
                ];
            }

            // Beam power on concrete
            if ($this->floor_beam_power_on_concrete > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_floor_beam_power_on_concrete' . $tag_close,
                    'value' => $this->floor_beam_power_on_concrete,
                    'unit' => $tag_open . 'd2u_machinery_unit_m2_h' . $tag_close,
                ];
            }

            // Filter connection
            if ('' !== $this->floor_filter_connection) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_floor_filter_connection' . $tag_close,
                    'value' => $this->floor_filter_connection,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Rotations
            if ('' !== $this->floor_rotations) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_floor_rotations' . $tag_close,
                    'value' => $this->floor_rotations,
                    'unit' => $tag_open . 'd2u_machinery_unit_min1' . $tag_close,
                ];
            }

            // Technical description
            if ('' !== $this->description_technical) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_construction_equipment_description_technical' . $tag_close,
                    'value' => $this->description_technical,
                    'unit' => '',
                ];
            }
        }

        // Dimensions
        if ($this->length > 0 && $this->width > 0 && $this->height > 0) {
            $tech_data[] = [
                'description' => $tag_open . 'd2u_machinery_dimensions_length_width_height' . $tag_close,
                'value' => $this->length .' x '. $this->width .' x '. $this->height,
                'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
            ];
        } elseif ($this->depth > 0 && $this->width > 0 && $this->height > 0) {
            $tech_data[] = [
                'description' => $tag_open . 'd2u_machinery_dimensions_width_height_depth' . $tag_close,
                'value' => $this->width .' x '. $this->height	.' x '. $this->depth,
                'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
            ];
        } elseif ($this->length > 0) {
            $tech_data[] = [
                'description' => $tag_open . 'd2u_machinery_dimensions_length' . $tag_close,
                'value' => $this->length,
                'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
            ];
        }

        // Weight
        if ('' !== $this->weight) {
            $tech_data[] = [
                'description' => $tag_open . 'd2u_machinery_weight' . $tag_close,
                'value' => $tag_open . 'd2u_machinery_unit_ca' . $tag_close .' '. $this->weight,
                'unit' => $tag_open . 'd2u_machinery_unit_kg' . $tag_close,
            ];
        }

        if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
            // Procedures
            if (count($this->procedures) > 0) {
                $procedures = [];
                foreach ($this->procedures as $procedure) {
                    $procedures[] = $procedure->name;
                }
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_procedures' . $tag_close,
                    'value' => implode('<br>', $procedures),
                    'unit' => '',
                ];
            }

            // Saw blade
            if ('' !== $this->saw_blade) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_saw_blade' . $tag_close,
                    'value' => $this->saw_blade,
                    'unit' => $tag_open . 'd2u_machinery_unit_diameter_mm' . $tag_close,
                ];
            }

            // Saw band
            if ('' !== $this->saw_band) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_saw_band' . $tag_close,
                    'value' => $this->saw_band,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Saw band tilt
            if ('' !== $this->saw_band_tilt) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_saw_band_tilt' . $tag_close,
                    'value' => $this->saw_band_tilt,
                    'unit' => $tag_open . 'd2u_machinery_unit_degrees' . $tag_close,
                ];
            }

            // Saw cutting speed
            if ('' !== $this->saw_cutting_speed) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_saw_cutting_speed' . $tag_close,
                    'value' => $this->saw_cutting_speed,
                    'unit' => $tag_open . 'd2u_machinery_unit_m_min' . $tag_close,
                ];
            }

            // Feed rate
            if ('' !== $this->automation_feedrate) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_automation_feedrate' . $tag_close,
                    'value' => $this->automation_feedrate,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm_min' . $tag_close,
                ];
            }

            // Feed rate for saw blades
            if ('' !== $this->automation_feedrate_sawblade) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_automation_feedrate_sawblade' . $tag_close,
                    'value' => $this->automation_feedrate_sawblade,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm_min' . $tag_close,
                ];
            }

            // Rush leader flyback
            if (0 !== $this->automation_rush_leader_flyback) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_automation_rush_leader_flyback' . $tag_close,
                    'value' => $this->automation_rush_leader_flyback,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm_min' . $tag_close,
                ];
            }

            // Workspace max.
            if ('' !== $this->workspace) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace' . $tag_close,
                    'value' => $this->workspace,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Workspace square
            if ('' !== $this->workspace_square) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace_square' . $tag_close,
                    'value' => $this->workspace_square,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Workspace flat
            if ('' !== $this->workspace_flat) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace_flat' . $tag_close,
                    'value' => $this->workspace_flat,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Workspace round
            if ('' !== $this->workspace_round) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace_round' . $tag_close,
                    'value' => $this->workspace_round,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Workspace plates
            if ('' !== $this->workspace_plate) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace_plate' . $tag_close,
                    'value' => $this->workspace_plate,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Workspace profile
            if ('' !== $this->workspace_profile) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace_profile' . $tag_close,
                    'value' => $this->workspace_profile,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Workspace angle steel
            if ('' !== $this->workspace_angle_steel) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace_angle_steel' . $tag_close,
                    'value' => $this->workspace_angle_steel,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Workspace minimum
            if ('' !== $this->workspace_min) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_workspace_min' . $tag_close,
                    'value' => $this->workspace_min,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Bevel angle
            if ($this->bevel_angle > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_bevel_angle' . $tag_close,
                    'value' => $this->bevel_angle,
                    'unit' => $tag_open . 'd2u_machinery_unit_degrees' . $tag_close,
                ];
            }

            // Continuous opening
            if ('' !== $this->beam_continuous_opening) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_beam_continuous_opening' . $tag_close,
                    'value' => $this->beam_continuous_opening,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Color guns
            if ('' !== $this->beam_color_guns) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_beam_color_guns' . $tag_close,
                    'value' => $this->beam_color_guns,
                    'unit' => '',
                ];
            }

            // Number turbines
            if ($this->beam_turbines > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_beam_turbines' . $tag_close,
                    'value' => $this->beam_turbines,
                    'unit' => $tag_open . 'd2u_machinery_unit_pieces' . $tag_close,
                ];
            }

            // Turbine power
            if ('' !== $this->beam_turbine_power) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_beam_turbine_power' . $tag_close,
                    'value' => $this->beam_turbine_power,
                    'unit' => $tag_open . 'd2u_machinery_unit_kw' . $tag_close,
                ];
            }

            // saw miter
            if ('' !== $this->saw_miter) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_saw_miter' . $tag_close,
                    'value' => $this->saw_miter,
                    'unit' => '',
                ];
            }

            // Vertical drilling units
            if ('' !== $this->drilling_unit_vertical) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_drilling_unit_vertical' . $tag_close,
                    'value' => $this->drilling_unit_vertical,
                    'unit' => $tag_open . 'd2u_machinery_unit_pieces' . $tag_close,
                ];
            }

            // Horizontal drilling units
            if ('' !== $this->drilling_unit_horizontal) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_drilling_unit_horizontal' . $tag_close,
                    'value' => $this->drilling_unit_horizontal,
                    'unit' => $tag_open . 'd2u_machinery_unit_pieces' . $tag_close,
                ];
            }

            // Drilling units from below
            if ($this->drilling_unit_below > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_drilling_unit_below' . $tag_close,
                    'value' => $this->drilling_unit_below,
                    'unit' => $tag_open . 'd2u_machinery_unit_pieces' . $tag_close,
                ];
            }

            // Drilling diameter
            if ('' !== $this->drilling_diameter) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_drilling_diameter' . $tag_close,
                    'value' => $this->drilling_diameter,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Drilling tools per axis
            if ('' !== $this->drilling_tools_axis) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_drilling_tools_axis' . $tag_close,
                    'value' => $this->drilling_tools_axis,
                    'unit' => $tag_open . 'd2u_machinery_unit_pieces' . $tag_close,
                ];
            }

            // Drilling axis drive power
            if ('' !== $this->drilling_axis_drive_power) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_drilling_axis_drive_power' . $tag_close,
                    'value' => $this->drilling_axis_drive_power,
                    'unit' => $tag_open . 'd2u_machinery_unit_kw' . $tag_close,
                ];
            }

            // Max. drilling speed
            if ('' !== $this->drilling_rpm_speed) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_drilling_rpm_speed' . $tag_close,
                    'value' => $this->drilling_rpm_speed,
                    'unit' => $tag_open . 'd2u_machinery_unit_min1' . $tag_close,
                ];
            }

            // Sheet width
            if ('' !== $this->sheet_width) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_sheet_width' . $tag_close,
                    'value' => $this->sheet_width,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Sheet length
            if ('' !== $this->sheet_length) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_sheet_length' . $tag_close,
                    'value' => $this->sheet_length,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Sheet thickness
            if ('' !== $this->sheet_thickness) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_sheet_thickness' . $tag_close,
                    'value' => $this->sheet_thickness,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Tool changer locations
            if ('' !== $this->tool_changer_locations) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_tool_changer_locations' . $tag_close,
                    'value' => $this->tool_changer_locations,
                    'unit' => $tag_open . 'd2u_machinery_unit_pieces' . $tag_close,
                ];
            }

            // punching diameter
            if ('' !== $this->punching_diameter) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_punching_diameter' . $tag_close,
                    'value' => $this->punching_diameter,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Number of punching tools
            if ('' !== $this->punching_tools) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_punching_tools' . $tag_close,
                    'value' => $this->punching_tools,
                    'unit' => $tag_open . 'd2u_machinery_unit_pieces' . $tag_close,
                ];
            }

            // Punching power
            if ($this->punching_power > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_punching_power' . $tag_close,
                    'value' => $this->punching_power,
                    'unit' => $tag_open . 'd2u_machinery_unit_kn' . $tag_close,
                ];
            }

            // Shaving unit angle steel singel cut
            if ($this->shaving_unit_angle_steel_single_cut > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_shaving_unit_angle_steel_single_cut' . $tag_close,
                    'value' => $this->shaving_unit_angle_steel_single_cut,
                    'unit' => $tag_open . 'd2u_machinery_unit_kn' . $tag_close,
                ];
            }

            // Tools
            if (count($this->tools) > 0) {
                $tools = [];
                foreach ($this->tools as $tool) {
                    $tools[] = $tool->name;
                }
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_tools' . $tag_close,
                    'value' => implode('<br>', $tools),
                    'unit' => '',
                ];
            }

            // Automationgrades
            if (count($this->automation_automationgrades) > 0) {
                $automation_automationgrades = [];
                foreach ($this->automation_automationgrades as $automationgrade) {
                    $automation_automationgrades[] = $automationgrade->name;
                }
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_automation_automationgrades' . $tag_close,
                    'value' => implode('<br>', $automation_automationgrades),
                    'unit' => '',
                ];
            }

            // Material classes
            if (count($this->materials) > 0) {
                $materials = [];
                foreach ($this->materials as $material) {
                    $materials[] = $material->name;
                }
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_materials' . $tag_close,
                    'value' => implode('<br>', $materials),
                    'unit' => '',
                ];
            }

            // Processes
            if (count($this->processes) > 0) {
                $processes = [];
                foreach ($this->processes as $process) {
                    $processes[] = $process->name;
                }
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_processes' . $tag_close,
                    'value' => implode('<br>', $processes),
                    'unit' => '',
                ];
            }

            // Profiles
            if (count($this->profiles) > 0) {
                $profiles = [];
                foreach ($this->profiles as $profile) {
                    $profiles[] = $profile->name;
                }
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_profiles' . $tag_close,
                    'value' => implode('<br>', $profiles),
                    'unit' => '',
                ];
            }

            // Carrier width
            if ('' !== $this->carrier_width) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_carrier_width' . $tag_close,
                    'value' => $this->carrier_width,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Carrier height
            if ('' !== $this->carrier_height) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_carrier_height' . $tag_close,
                    'value' => $this->carrier_height,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Carrier max. weight
            if ($this->carrier_weight > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_carrier_weight' . $tag_close,
                    'value' => $this->carrier_weight,
                    'unit' => $tag_open . 'd2u_machinery_unit_kg' . $tag_close,
                ];
            }

            // Flange thickness
            if ('' !== $this->flange_thickness) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_flange_thickness' . $tag_close,
                    'value' => $this->flange_thickness,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Web thickness min. / max.
            if ('' !== $this->web_thickness) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_web_thickness' . $tag_close,
                    'value' => $this->web_thickness,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Component length
            if ('' !== $this->component_length) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_component_length' . $tag_close,
                    'value' => $this->component_length,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Component weight
            if ($this->component_weight > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_component_weight' . $tag_close,
                    'value' => $this->component_weight,
                    'unit' => $tag_open . 'd2u_machinery_unit_kg' . $tag_close,
                ];
            }

            // Welding processes
            if (count($this->weldings) > 0) {
                $welding_processes = [];
                foreach ($this->weldings as $welding) {
                    $welding_processes[] = $welding->name;
                }
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_weldings' . $tag_close,
                    'value' => implode('<br>', $welding_processes),
                    'unit' => '',
                ];
            }

            // Welding thickness
            if ($this->welding_thickness > 0) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_welding_thickness' . $tag_close,
                    'value' => $this->welding_thickness,
                    'unit' => $tag_open . 'd2u_machinery_unit_a_mm' . $tag_close,
                ];
            }

            // Welding wire thickness
            if ('' !== $this->welding_wire_thickness) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_welding_wire_thickness' . $tag_close,
                    'value' => $this->welding_wire_thickness,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Welding wire thickness
            if ('' !== $this->welding_wire_thickness) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_welding_wire_thickness' . $tag_close,
                    'value' => $this->welding_wire_thickness,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Automation supply single stroke
            if ('' !== $this->automation_supply_single_stroke) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_automation_supply_single_stroke' . $tag_close,
                    'value' => $this->automation_supply_single_stroke,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }

            // Automation supply multi stroke
            if ('' !== $this->automation_supply_multi_stroke) {
                $tech_data[] = [
                    'description' => $tag_open . 'd2u_machinery_steel_automation_supply_multi_stroke' . $tag_close,
                    'value' => $this->automation_supply_multi_stroke,
                    'unit' => $tag_open . 'd2u_machinery_unit_mm' . $tag_close,
                ];
            }
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
    public function getUrl($including_domain = false)
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
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = false;

        // Save the not language specific part
        $pre_save_object = new self($this->machine_id, $this->clang_id);

        $regenerate_urls = false;
        if (0 === $this->machine_id || $pre_save_object !== $this) {
            $query = \rex::getTablePrefix() .'d2u_machinery_machines SET '
                    ."name = '". addslashes($this->name) ."', "
                    ."pics = '". implode(',', $this->pics) ."', "
                    .'category_id = '. ($this->category instanceof Category ? $this->category->category_id : 0) .', '
                    ."alternative_machine_ids = '|". implode('|', $this->alternative_machine_ids) ."|', "
                    ."product_number = '". $this->product_number ."', "
                    ."article_id_software = '". $this->article_id_software ."', "
                    ."article_id_service = '". $this->article_id_service ."', "
                    ."article_ids_references = '". implode(',', $this->article_ids_references) ."', "
                    ."online_status = '". $this->online_status ."', "
                    ."engine_power = '". $this->engine_power ."', "
                    ."engine_power_frequency_controlled = '". ($this->engine_power_frequency_controlled ? 'true' : 'false') ."', "
                    ."length = '". $this->length ."', "
                    ."width = '". $this->width ."', "
                    ."height = '". $this->height ."', "
                    ."depth = '". $this->depth ."', "
                    ."weight = '". $this->weight ."', "
                    ."operating_voltage_v = '". $this->operating_voltage_v ."', "
                    ."operating_voltage_hz = '". $this->operating_voltage_hz ."', "
                    ."operating_voltage_a = '". $this->operating_voltage_a ."' ";
            if (rex_plugin::get('d2u_machinery', 'contacts')->isAvailable()) {
                $query .= ', contact_id = '. ($this->contact instanceof \D2U_Machinery\Contact ? $this->contact->contact_id : 0) .' ';
            }
            if (rex_plugin::get('d2u_machinery', 'equipment')->isAvailable()) {
                $query .= ", equipment_ids = '|". implode('|', $this->equipment_ids) ."|' ";
            }
            if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                $query .= ", industry_sector_ids = '|". implode('|', $this->industry_sector_ids) ."|' ";
            }
            if (rex_plugin::get('d2u_machinery', 'machine_agitator_extension')->isAvailable()) {
                $query .= ', agitator_type_id = '. $this->agitator_type_id .' '
                    .', viscosity = '. $this->viscosity .' ';
            }
            if (rex_plugin::get('d2u_machinery', 'machine_certificates_extension')->isAvailable()) {
                $query .= ", certificate_ids = '|". implode('|', $this->certificate_ids) ."|' ";
            }
            if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
                $query .= ", airless_hose_connection = '". $this->airless_hose_connection ."' "
                    .', airless_hose_diameter = '. $this->airless_hose_diameter .' '
                    .', airless_hose_length = '. $this->airless_hose_length .' '
                    .", airless_nozzle_size = '". $this->airless_nozzle_size ."' "
                    .", container_capacity = '". $this->container_capacity ."' "
                    .", container_mixing_performance = '". $this->container_mixing_performance ."' "
                    .', container_waterconnect_pressure = '. $this->container_waterconnect_pressure .' '
                    .", container_waterconnect_diameter = '". $this->container_waterconnect_diameter ."' "
                    .', container_weight_empty = '. ($this->container_weight_empty > 0 ? $this->container_weight_empty : 0) .' '
                    .", cutters_cutting_depth = '". $this->cutters_cutting_depth ."' "
                    .', cutters_cutting_length = '. ($this->cutters_cutting_length > 0 ? $this->cutters_cutting_length : 0) .' '
                    .", cutters_rod_length = '". $this->cutters_rod_length ."' "
                    .', floor_beam_power_on_concrete = '. ($this->floor_beam_power_on_concrete > 0 ? $this->floor_beam_power_on_concrete : 0) .' '
                    .', floor_dust_extraction_connection = '. ($this->floor_dust_extraction_connection > 0 ? $this->floor_dust_extraction_connection : 0) .' '
                    .", floor_feedrate = '". $this->floor_feedrate ."' "
                    .", floor_filter_connection = '". $this->floor_filter_connection ."' "
                    .", floor_rotations = '". $this->floor_rotations ."' "
                    .", floor_working_pressure = '". $this->floor_working_pressure ."' "
                    .', floor_working_width = '. $this->floor_working_width .' '
                    .', grinder_grinding_plate = '. $this->grinder_grinding_plate .' '
                    .', grinder_grinding_wheel = '. $this->grinder_grinding_wheel .' '
                    .", grinder_rotational_frequency = '". $this->grinder_rotational_frequency ."' "
                    .", grinder_sanding = '". $this->grinder_sanding ."' "
                    .', grinder_vacuum_connection = '. $this->grinder_vacuum_connection .' '
                    .", operating_pressure = '". $this->operating_pressure ."' "
                    .", pictures_delivery_set = '". implode(',', $this->pictures_delivery_set) ."' "
                    .', pump_conveying_distance = '. ($this->pump_conveying_distance > 0 ? $this->pump_conveying_distance : 0) .' '
                    .', pump_filling = '. ($this->pump_filling > 0 ? $this->pump_filling : 0) .' '
                    .", pump_flow_volume = '". $this->pump_flow_volume ."' "
                    .", pump_grain_size = '". $this->pump_grain_size ."' "
                    .", pump_material_container = '". $this->pump_material_container ."' "
                    .', pump_pressure_height = '. ($this->pump_pressure_height > 0 ? $this->pump_pressure_height : 0) .' '
                    .', waste_water_capacity = '. ($this->waste_water_capacity > 0 ? $this->waste_water_capacity : 0) .' ';
            }
            if (rex_plugin::get('d2u_machinery', 'service_options')->isAvailable()) {
                $query .= ", service_option_ids = '|". implode('|', $this->service_option_ids) ."|' ";
            }
            if (rex_plugin::get('d2u_machinery', 'machine_features_extension')->isAvailable()) {
                $query .= ", feature_ids = '|". implode('|', $this->feature_ids) ."|' ";
            }
            if (rex_plugin::get('d2u_machinery', 'machine_options_extension')->isAvailable()) {
                $query .= ", option_ids = '|". implode('|', $this->option_ids) ."|' ";
            }
            if (rex_plugin::get('d2u_machinery', 'machine_steel_processing_extension')->isAvailable()) {
                $query .= ", process_ids = '|". implode('|', array_keys($this->processes)) ."|' "
                    .", procedure_ids = '|". implode('|', array_keys($this->procedures)) ."|' "
                    .", material_ids = '|". implode('|', array_keys($this->materials)) ."|' "
                    .", tool_ids = '|". implode('|', array_keys($this->tools)) ."|' "
                    .", automation_supply_single_stroke = '". $this->automation_supply_single_stroke ."' "
                    .", automation_supply_multi_stroke = '". $this->automation_supply_multi_stroke ."' "
                    .", automation_feedrate = '". $this->automation_feedrate ."' "
                    .", automation_feedrate_sawblade = '". $this->automation_feedrate_sawblade ."' "
                    .", automation_rush_leader_flyback = '". $this->automation_rush_leader_flyback ."' "
                    .", automation_automationgrade_ids = '|". implode('|', array_keys($this->automation_automationgrades)) ."|' "
                    .", automation_supply_ids = '|". implode('|', $this->automation_supply_ids) ."|' "
                    .", workspace = '". $this->workspace ."' "
                    .", workspace_square = '". $this->workspace_square ."' "
                    .", workspace_flat = '". $this->workspace_flat ."' "
                    .", workspace_plate = '". $this->workspace_plate ."' "
                    .", workspace_profile = '". $this->workspace_profile ."' "
                    .", workspace_angle_steel = '". $this->workspace_angle_steel ."' "
                    .", workspace_round = '". $this->workspace_round ."' "
                    .", workspace_min = '". $this->workspace_min ."' "
                    .", sheet_width = '". $this->sheet_width ."' "
                    .", sheet_length = '". $this->sheet_length ."' "
                    .", sheet_thickness = '". $this->sheet_thickness ."' "
                    .", tool_changer_locations = '". $this->tool_changer_locations ."' "
                    .', drilling_unit_below = '. ($this->drilling_unit_below > 0 ? $this->drilling_unit_below : 0) .' '
                    .", drilling_unit_vertical = '". $this->drilling_unit_vertical ."' "
                    .", drilling_unit_horizontal = '". $this->drilling_unit_horizontal ."' "
                    .", drilling_diameter = '". $this->drilling_diameter ."' "
                    .", drilling_tools_axis = '". $this->drilling_tools_axis ."' "
                    .", drilling_axis_drive_power = '". $this->drilling_axis_drive_power ."' "
                    .", drilling_rpm_speed = '". $this->drilling_rpm_speed ."' "
                    .", saw_blade = '". $this->saw_blade ."' "
                    .", saw_band = '". $this->saw_band ."' "
                    .", saw_band_tilt = '". $this->saw_band_tilt ."' "
                    .", saw_cutting_speed = '". $this->saw_cutting_speed ."' "
                    .", saw_miter = '". $this->saw_miter ."' "
                    .', bevel_angle = '. $this->bevel_angle .' '
                    .", punching_diameter = '". $this->punching_diameter ."' "
                    .', punching_power = '. $this->punching_power .' '
                    .", punching_tools = '". $this->punching_tools ."' "
                    .', shaving_unit_angle_steel_single_cut = '. $this->shaving_unit_angle_steel_single_cut .' '
                    .", profile_ids = '|". implode('|', array_keys($this->profiles)) ."|' "
                    .", carrier_width = '". $this->carrier_width ."' "
                    .", carrier_height = '". $this->carrier_height ."' "
                    .', carrier_weight = '. $this->carrier_weight .' '
                    .", flange_thickness = '". $this->flange_thickness ."' "
                    .", web_thickness = '". $this->web_thickness ."' "
                    .", component_length = '". $this->component_length ."' "
                    .', component_weight = '. $this->component_weight .' '
                    .", welding_process_ids = '|". implode('|', array_keys($this->weldings)) ."|' "
                    .', welding_thickness = '. $this->welding_thickness .' '
                    .", welding_wire_thickness = '". $this->welding_wire_thickness ."' "
                    .", beam_continuous_opening = '". $this->beam_continuous_opening ."' "
                    .', beam_turbines = '. $this->beam_turbines .' '
                    .", beam_turbine_power = '". $this->beam_turbine_power ."' "
                    .", beam_color_guns = '". $this->beam_color_guns ."' "
                ;
            }
            if (rex_plugin::get('d2u_machinery', 'machine_usage_area_extension')->isAvailable()) {
                $query .= ", usage_area_ids = '|". implode('|', $this->usage_area_ids) ."|' ";
            }
            if (\rex_addon::get('d2u_videos') instanceof rex_addon && \rex_addon::get('d2u_videos')->isAvailable() && count($this->videos) > 0) {
                $query .= ", video_ids = '|". implode('|', array_keys($this->videos)) ."|' ";
            } else {
                $query .= ", video_ids = '' ";
            }

            if (0 === $this->machine_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE machine_id = '. $this->machine_id;
            }
            $result = \rex_sql::factory();
            $result->setQuery($query);
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
                        ."machine_id = '". $this->machine_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."lang_name = '". addslashes($this->lang_name) ."', "
                        ."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
                        ."description = '". addslashes(htmlspecialchars($this->description)) ."', "
                        ."benefits_long = '". addslashes(htmlspecialchars($this->benefits_long)) ."', "
                        ."benefits_short = '". addslashes(htmlspecialchars($this->benefits_short)) ."', "
                        ."leaflet = '". $this->leaflet ."', "
                        ."pdfs = '". implode(',', $this->pdfs) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."', "
                        .'updatedate = CURRENT_TIMESTAMP, '
                        ."updateuser = '". (\rex::getUser() instanceof rex_user ? \rex::getUser()->getLogin() : '') ."' ";
                if (rex_plugin::get('d2u_machinery', 'machine_construction_equipment_extension')->isAvailable()) {
                    $query .= ", container_connection_port = '". $this->container_connection_port ."' "
                        .", container_conveying_wave = '". $this->container_conveying_wave ."' "
                        .", description_technical = '". $this->description_technical ."' "
                        .", delivery_set_basic = '". addslashes(htmlspecialchars($this->delivery_set_basic)) ."' "
                        .", delivery_set_conversion = '". addslashes(htmlspecialchars($this->delivery_set_conversion)) ."' "
                        .", delivery_set_full = '". addslashes(htmlspecialchars($this->delivery_set_full)) ."' ";
                }
                $result = \rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();

                if (!$error && $pre_save_object->lang_name !== $this->lang_name) {
                    $regenerate_urls = true;
                }
            }
        }

        // Don't forget to regenerate URL cache / search_it index
        \d2u_addon_backend_helper::generateUrlCache();

        return !$error;
    }

    /**
     * Reassigns priorities in database.
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority($delete = false): void
    {
        // Pull prios from database
        $query = 'SELECT machine_id, priority FROM '. \rex::getTablePrefix() .'d2u_machinery_machines '
            .'WHERE machine_id <> '. $this->machine_id .' ORDER BY priority';
        $result = \rex_sql::factory();
        $result->setQuery($query);

        // When priority is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = (int) $result->getRows() + 1;
        }

        $machines = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $machines[$result->getValue('priority')] = $result->getValue('machine_id');
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
