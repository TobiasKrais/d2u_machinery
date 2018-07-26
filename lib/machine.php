<?php
/**
 * Redaxo D2U Machines Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Machine
 */
class Machine implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Machine id
	 */
	var $machine_id = 0;
	
	/**
	 * @var int Redaxo language id
	 */
	var $clang_id = 0;

	/**
	 * @var string Machine name
	 */
	var $name = "";

	/**
	 * @var string[] Machine pictures
	 */
	var $pics = [];

	/**
	 * @var Category Machine category
	 */
	var $category = FALSE;

	/**
	 * @var int[] Usage area IDs
	 */
	var $usage_area_ids = [];

	/**
	 * @var int[] IDs of alternative machines
	 */
	var $alternative_machine_ids = [];

	/**
	 * @var int[] Machine feature ids
	 */
	var $feature_ids = [];

	/**
	 * @var string Machine accessory ids.
	 */
	var $product_number = "";

	/**
	 * @var int[] Machine business ids.
	 */
	var $industry_sector_ids = [];

	/**
	 * @var int Redaxo article id for additional software information
	 */
	var $article_id_software = 0;

	/**
	 * @var int Redaxo article id for additional service information
	 */
	var $article_id_service = 0;

	/**
	 * @var int[] Array with Redaxo article ids with customer success stories
	 */
	var $article_ids_references = [];

	/**
	 * @var String Status. Either "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var int[] Certificate ids
	 */
	var $certificate_ids = [];

	/**
	 * @var int Agitator type id
	 */
	var $agitator_type_id = 0;

	/**
	 * @var int Max. viscosity in mPas
	 */
	var $viscosity = 0;

	/**
	 * @var string Engine power
	 */
	var $engine_power = "";

	/**
	 * @var boolean Is engine power frequency controlled?
	 */
	var $engine_power_frequency_controlled = false;

	/**
	 * @var int Machine length
	 */
	var $length = 0;

	/**
	 * @var int Machine width
	 */
	var $width = 0;

	/**
	 * @var int Machine height
	 */
	var $height = 0;

	/**
	 * @var int Machine depth
	 */
	var $depth = 0;

	/**
	 * @var string Machine weight
	 */
	var $weight = "";	
	
	/**
	 * @var string Machine operating voltage (v)
	 */
	var $operating_voltage_v = "";	
	
	/**
	 * @var string Machine operating voltage (hz)
	 */
	var $operating_voltage_hz = "";	
	
	/**
	 * @var string Machine operating voltage (a)
	 */
	var $operating_voltage_a = "";	

	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var Process[] machine_steel_processing_extension: Process objects
	 */
	var $processes = [];
	
	/**
	 * @var Procedure[] machine_steel_processing_extension: Procedure objects
	 */
	var $procedures = [];
	
	/**
	 * @var Material[] machine_steel_processing_extension: Material objects
	 */
	var $materials = [];
	
	/**
	 * @var Tool[] machine_steel_processing_extension: Tool objects
	 */
	var $tools = [];
	
	/**
	 * @var string machine_steel_processing_extension: Automation - Supply single stroke (mm)
	 */
	var $automation_supply_single_stroke = "";	

	/**
	 * @var string machine_steel_processing_extension: Automation - Multiple single stroke (mm)
	 */
	var $automation_supply_multi_stroke = "";

	/**
	 * @var string machine_steel_processing_extension: Automation - Feed rate range (mm/min)
	 */
	var $automation_feedrate = "";

	/**
	 * @var int machine_steel_processing_extension: Automation - Rush leader flyback (mm/min)
	 */
	var $automation_rush_leader_flyback = 0;

	/**
	 * @var Automation[] machine_steel_processing_extension: Automation grade objects
	 */
	var $automation_automationgrades = [];
	
	/**
	 * @var Supply[] machine_steel_processing_extension: Automation supply objects
	 */
	var $automation_supplys = [];

	/**
	 * @var string machine_steel_processing_extension: Workspace (mm x mm or only mm).
	 */
	var $workspace = "";	

	/**
	 * @var string machine_steel_processing_extension: Workspace square material (mm x mm or only mm).
	 */
	var $workspace_square = "";	

	/**
	 * @var string machine_steel_processing_extension: Workspace flat material (mm x mm or only mm).
	 */
	var $workspace_flat = "";	

	/**
	 * @var string machine_steel_processing_extension: Workspace plates (mm x mm or only mm).
	 */
	var $workspace_plate = "";	

	/**
	 * @var string machine_steel_processing_extension: Workspace for profiles (mm x mm or only mm).
	 */
	var $workspace_profile = "";	

	/**
	 * @var string machine_steel_processing_extension: Workspace for angle steels (mm x mm x mm).
	 */
	var $workspace_angle_steel = "";	

	/**
	 * @var string machine_steel_processing_extension: Workspace for round materials (mm x mm or only mm).
	 */
	var $workspace_round = "";	

	/**
	 * @var string machine_steel_processing_extension: Minimum Workspace (mm x mm x mm or mm x mm or only mm).
	 */
	var $workspace_min = "";	

	/**
	 * @var string machine_steel_processing_extension: sheet width range (mm).
	 */
	var $sheet_width = "";	

	/**
	 * @var string machine_steel_processing_extension: sheet length range (mm).
	 */
	var $sheet_length = "";	

	/**
	 * @var string machine_steel_processing_extension: sheet thickness ragne (mm).
	 */
	var $sheet_thickness = "";	

	/**
	 * @var string machine_steel_processing_extension: number of tool changer locations.
	 */
	var $tool_changer_locations = "";	

	/**
	 * @var string machine_steel_processing_extension: number of vertical drilling units.
	 */
	var $drilling_unit_vertical = "";	

	/**
	 * @var string machine_steel_processing_extension: number of horizontal drilling units.
	 */
	var $drilling_unit_horizontal = "";	

	/**
	 * @var string machine_steel_processing_extension: drilling whole diameter (range in mm).
	 */
	var $drilling_diameter = "";	

	/**
	 * @var string machine_steel_processing_extension: number of drilling tools per axis.
	 */
	var $drilling_tools_axis = "";	

	/**
	 * @var string machine_steel_processing_extension: axis driver power.
	 */
	var $drilling_axis_drive_power = "";	

	/**
	 * @var string machine_steel_processing_extension: drilling speed (rpm).
	 */
	var $drilling_rpm_speed = "";	

	/**
	 * @var string machine_steel_processing_extension: saw blade diameter (mm, sometimes mm x mm).
	 */
	var $saw_blade = "";	

	/**
	 * @var string machine_steel_processing_extension: saw band dimensions (mm x mm x mm).
	 */
	var $saw_band = "";	

	/**
	 * @var string machine_steel_processing_extension: saw band tilt range (°).
	 */
	var $saw_band_tilt = "";	

	/**
	 * @var string machine_steel_processing_extension: saw cutting speed range (mm).
	 */
	var $saw_cutting_speed = "";	

	/**
	 * @var string machine_steel_processing_extension: saw miter (°).
	 */
	var $saw_miter = "";

	/**
	 * @var int machine_steel_processing_extension: Max. bevel angle (°).
	 */
	var $bevel_angle = 0;

	/**
	 * @var string machine_steel_processing_extension: punching diameter range (mm).
	 */
	var $punching_diameter = "";

	/**
	 * @var string machine_steel_processing_extension: punching power.
	 */
	var $punching_power = 0;

	/**
	 * @var string machine_steel_processing_extension: number of punching tools.
	 */
	var $punching_tools = "";

	/**
	 * @var string machine_steel_processing_extension: angle steel single cut.
	 */
	var $shaving_unit_angle_steel_single_cut = 0;	

	/**
	 * @var Profile[] machine_steel_processing_extension: Profile area objects
	 */
	var $profiles = [];

	/**
	 * @var string machine_steel_processing_extension: carrier width (mm).
	 */
	var $carrier_width = "";	

	/**
	 * @var string machine_steel_processing_extension: carrier height (mm).
	 */
	var $carrier_height = "";	

	/**
	 * @var int machine_steel_processing_extension: carrier weight (kg).
	 */
	var $carrier_weight = 0;	

	/**
	 * @var string machine_steel_processing_extension: flange thickness min. / max. (mm).
	 */
	var $flange_thickness = "";	

	/**
	 * @var string machine_steel_processing_extension: web thickness min. / max. (mm).
	 */
	var $web_thickness = "";	

	/**
	 * @var string machine_steel_processing_extension: component length min. / max. (mm).
	 */
	var $component_length = "";	

	/**
	 * @var int machine_steel_processing_extension: component weight (kg).
	 */
	var $component_weight = 0;	

	/**
	 * @var Welding[] machine_steel_processing_extension: welding process objects
	 */
	var $weldings = [];

	/**
	 * @var int machine_steel_processing_extension: welding thickness ((a) mm).
	 */
	var $welding_thickness = 0;	

	/**
	 * @var string machine_steel_processing_extension: welding_wire_thickness (mm).
	 */
	var $welding_wire_thickness = "";	

	/**
	 * @var string machine_steel_processing_extension: beam machine continuous opening (mm).
	 */
	var $beam_continuous_opening = "";	

	/**
	 * @var int machine_steel_processing_extension: number of turbines.
	 */
	var $beam_turbines = 0;	

	/**
	 * @var string machine_steel_processing_extension: beam machine power per turbine (kW).
	 */
	var $beam_turbine_power = "";	

	/**
	 * @var string machine_steel_processing_extension: beam machine number of color guns.
	 */
	var $beam_color_guns = "";	

	/**
	 * @var string Language specific name
	 */
	var $lang_name = "";

	/**
	 * @var string Teaser
	 */
	var $teaser = "";

	/**
	 * @var string Machine description
	 */
	var $description = "";

	/**
	 * @var string[] File names of PDF files for the machine
	 */
	var $pdfs = [];

	/**
	 * @var Video[] Videomanager videos
	 */
	var $videos = [];
	
	/**
	 * @var string Needs translation update? "no", "yes" or "delete"
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var String URL der Maschine
	 */
	private $url = "";
	
	/* Variables from machine_construction_equipment_extension following */

	/**
	 * @var string Airless devices: hose connection (")
	 */
	var $airless_hose_connection = "";

	/**
	 * @var int Airless devices: hose diameter (mm)
	 */
	var $airless_hose_diameter = 0;

	/**
	 * @var int Airless devices: maximum hose length (m)
	 */
	var $airless_hose_length = 0;

	/**
	 * @var string Airless devices: maximum nozzle size
	 */
	var $airless_nozzle_size = "";

	/**
	 * @var string Containers: maximum capacity (kg)
	 */
	var $container_capacity = "";

	/**
	 * @var string Containers: connection port
	 */
	var $container_connection_port = "";

	/**
	 * @var string Containers: conveying wave
	 */
	var $container_conveying_wave = "";

	/**
	 * @var string Containers: mixing performance (l/min)
	 */
	var $container_mixing_performance = "";

	/**
	 * @var int Containers: water connection pressure (bar)
	 */
	var $container_waterconnect_pressure = 0;

	/**
	 * @var string Containers: water connection diameter (")
	 */
	var $container_waterconnect_diameter = "";

	/**
	 * @var string Containers: empty container weight (kg)
	 */
	var $container_weight_empty = "";

	/**
	 * @var string Cutting devices: maximum cutting depth (cm)
	 */
	var $cutters_cutting_depth = "";

	/**
	 * @var string Cutting devices: maximum cutting length (cm)
	 */
	var $cutters_cutting_length = "";

	/**
	 * @var string Cutting devices: rod length (mm)
	 */
	var $cutters_rod_length = "";

	/**
	 * @var string Tillage machines: beam power on concrete (m²/h)
	 */
	var $floor_beam_power_on_concrete = "";

	/**
	 * @var string Tillage machines: dust extraction connection size (mm)
	 */
	var $floor_dust_extraction_connection = "";

	/**
	 * @var string Tillage machines: feedrate (m/min)
	 */
	var $floor_feedrate = "";

	/**
	 * @var string Tillage machines: filter connection (mm)
	 */
	var $floor_filter_connection = "";

	/**
	 * @var string Tillage machines: rotations (min-1)
	 */
	var $floor_rotations = "";

	/**
	 * @var string Tillage machines: working pressure (kg)
	 */
	var $floor_working_pressure = "";

	/**
	 * @var int Tillage machines: working width (mm)
	 */
	var $floor_working_width = 0;

	/**
	 * @var int Grinding machines: grinding plate (cm²)
	 */
	var $grinder_grinding_plate = 0;

	/**
	 * @var int Grinding machines: grinding wheel (mm)
	 */
	var $grinder_grinding_wheel = 0;

	/**
	 * @var string Grinding machines: working pressure (u/min)
	 */
	var $grinder_rotational_frequency = "";

	/**
	 * @var string Grinding machines: sanding (u/min)
	 */
	var $grinder_sanding = "";

	/**
	 * @var int Grinding machines: vacuum connection (mm)
	 */
	var $grinder_vacuum_connection = 0;

	/**
	 * @var string Pumps and other machines: operating pressure (bar)
	 */
	var $operating_pressure = "";

	/**
	 * @var int Pumps: ca. conveying distance for fluid materials (m)
	 */
	var $pump_conveying_distance_fluid = 0;

	/**
	 * @var int Pumps: ca. conveying distance for mineral materials (m)
	 */
	var $pump_conveying_distance_mineral = 0;

	/**
	 * @var int Pumps: ca. conveying distance for pasty materials (m)
	 */
	var $pump_conveying_distance_pasty = 0;

	/**
	 * @var string Pumps: filling (mm)
	 */
	var $pump_filling = "";

	/**
	 * @var string Pumps: flow volume for fluid materials (l/min)
	 */
	var $pump_flow_volume_fluid = "";

	/**
	 * @var string Pumps: flow volume for mineral materials (l/min)
	 */
	var $pump_flow_volume_mineral = "";

	/**
	 * @var string Pumps: flow volume for pasty materials (l/min)
	 */
	var $pump_flow_volume_pasty = "";

	/**
	 * @var string Pumps: maximum grain size (mm)
	 */
	var $pump_grain_size = "";

	/**
	 * @var string Pumps: material container (l)
	 */
	var $pump_material_container = "";

	/**
	 * @var int Pumps: pressure height for fluid materials (m)
	 */
	var $pump_pressure_height_fluid = 0;

	/**
	 * @var int Pumps: pressure height for mineral materials (m)
	 */
	var $pump_pressure_height_mineral = 0;

	/**
	 * @var int Pumps: pressure height for pasty materials (m)
	 */
	var $pump_pressure_height_pasty = 0;

	/**
	 * @var string Waste water containers: capacity (l)
	 */
	var $waste_water_capacity = "";

	/**
	 * @var string Description shown in technical data overview
	 */
	var $description_technical = "";

	/**
	 * @var string[] Delivery set picture name
	 */
	var $pictures_delivery_set = [];

	/**
	 * @var string Basic delivery set description
	 */
	var $delivery_set_basic = "";
	
	/**
	 * @var string Conversion delivery set description
	 */
	var $delivery_set_conversion = "";
	
	/**
	 * @var string Full delivery set description
	 */
	var $delivery_set_full = "";

	/* Variables from service_options plugin following */

	/**
	 * @var int[] Machine service option ids
	 */
	var $service_option_ids = [];

	/* Variables from equipment plugin following */

	/**
	 * @var int[] Machine equipment ids
	 */
	var $equipment_ids = [];

	/**
	 * Fetches a machine object from database or creates an empty machine object.
	 * @param int $machine_id Database machine id
	 * @param int $clang_id Redaxo language id
	 */
	 public function __construct($machine_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_machines AS machines "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_machinery_machines_lang AS lang "
					."ON machines.machine_id = lang.machine_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE machines.machine_id = ". $machine_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if($num_rows > 0) {
			$this->machine_id = $result->getValue("machine_id");
			$this->name = stripslashes($result->getValue("name"));
			$this->pics = preg_grep('/^\s*$/s', explode(",", $result->getValue("pics")), PREG_GREP_INVERT);
			$this->category = new Category($result->getValue("category_id"), $clang_id);
			$this->alternative_machine_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("alternative_machine_ids")), PREG_GREP_INVERT);
			$this->product_number = $result->getValue("product_number");
			$this->article_id_software = $result->getValue("article_id_software");
			$this->article_id_service = $result->getValue("article_id_service");
			$this->article_ids_references = preg_grep('/^\s*$/s', explode(",", $result->getValue("article_ids_references")), PREG_GREP_INVERT);
			$this->online_status = $result->getValue("online_status");
			$this->engine_power = $result->getValue("engine_power");
			$this->engine_power_frequency_controlled = $result->getValue("engine_power_frequency_controlled") == "true" ? TRUE : FALSE;
			$this->length = $result->getValue("length");
			$this->width = $result->getValue("width");
			$this->height = $result->getValue("height");
			$this->depth = $result->getValue("depth");
			$this->weight = $result->getValue("weight");
			$this->operating_voltage_v = $result->getValue("operating_voltage_v");
			$this->operating_voltage_hz = $result->getValue("operating_voltage_hz");
			$this->operating_voltage_a = $result->getValue("operating_voltage_a");
			$this->lang_name = $result->getValue("lang_name");
			$this->teaser = stripslashes(htmlspecialchars_decode($result->getValue("teaser")));
			$this->description = stripslashes(htmlspecialchars_decode($result->getValue("description")));
			$this->pdfs = preg_grep('/^\s*$/s', explode(",", $result->getValue("pdfs")), PREG_GREP_INVERT);
			$this->priority = $result->getValue("priority");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}

			if(rex_plugin::get("d2u_machinery", "equipment")->isAvailable()) {
				$this->equipment_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("equipment_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
				$this->industry_sector_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("industry_sector_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$this->agitator_type_id = $result->getValue("agitator_type_id");
				$this->viscosity = $result->getValue("viscosity");
			}

			if(rex_plugin::get("d2u_machinery", "machine_certificates_extension")->isAvailable()) {
				$this->certificate_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("certificate_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "machine_construction_equipment_extension")->isAvailable()) {
				$this->airless_hose_connection = $result->getValue("airless_hose_connection");
				$this->airless_hose_diameter = $result->getValue("airless_hose_diameter");
				$this->airless_hose_length = $result->getValue("airless_hose_length");
				$this->airless_nozzle_size = $result->getValue("airless_nozzle_size");
				$this->container_capacity = $result->getValue("container_capacity");
				$this->container_mixing_performance = $result->getValue("container_mixing_performance");
				$this->container_waterconnect_pressure = $result->getValue("container_waterconnect_pressure");
				$this->container_waterconnect_diameter = $result->getValue("container_waterconnect_diameter");
				$this->container_weight_empty = $result->getValue("container_weight_empty");
				$this->cutters_cutting_depth = $result->getValue("cutters_cutting_depth");
				$this->cutters_cutting_length = $result->getValue("cutters_cutting_length");
				$this->cutters_rod_length = $result->getValue("cutters_rod_length");
				$this->floor_beam_power_on_concrete = $result->getValue("floor_beam_power_on_concrete");
				$this->floor_dust_extraction_connection = $result->getValue("floor_dust_extraction_connection");
				$this->floor_feedrate = $result->getValue("floor_feedrate");
				$this->floor_filter_connection = $result->getValue("floor_filter_connection");
				$this->floor_rotations = $result->getValue("floor_rotations");
				$this->floor_working_pressure = $result->getValue("floor_working_pressure");
				$this->floor_working_width = $result->getValue("floor_working_width");
				$this->grinder_grinding_plate = $result->getValue("grinder_grinding_plate");
				$this->grinder_grinding_wheel = $result->getValue("grinder_grinding_wheel");
				$this->grinder_rotational_frequency = $result->getValue("grinder_rotational_frequency");
				$this->grinder_sanding = $result->getValue("grinder_sanding");
				$this->grinder_vacuum_connection = $result->getValue("grinder_vacuum_connection");
				$this->operating_pressure = $result->getValue("operating_pressure");
				$this->pump_conveying_distance_fluid = $result->getValue("pump_conveying_distance_fluid");
				$this->pump_conveying_distance_mineral = $result->getValue("pump_conveying_distance_mineral");
				$this->pump_conveying_distance_pasty = $result->getValue("pump_conveying_distance_pasty");
				$this->pump_filling = $result->getValue("pump_filling");
				$this->pump_flow_volume_fluid = $result->getValue("pump_flow_volume_fluid");
				$this->pump_flow_volume_mineral = $result->getValue("pump_flow_volume_mineral");
				$this->pump_flow_volume_pasty = $result->getValue("pump_flow_volume_pasty");
				$this->pump_grain_size = $result->getValue("pump_grain_size");
				$this->pump_material_container = $result->getValue("pump_material_container");
				$this->pump_pressure_height_fluid = $result->getValue("pump_pressure_height_fluid");
				$this->pump_pressure_height_mineral = $result->getValue("pump_pressure_height_mineral");
				$this->pump_pressure_height_pasty = $result->getValue("pump_pressure_height_pasty");
				$this->waste_water_capacity = $result->getValue("waste_water_capacity");
				$this->container_connection_port = $result->getValue("container_connection_port");
				$this->container_conveying_wave = $result->getValue("container_conveying_wave");
				$this->description_technical = $result->getValue("description_technical");
				$this->pictures_delivery_set = preg_grep('/^\s*$/s', explode(",", $result->getValue("pictures_delivery_set")), PREG_GREP_INVERT);
				$this->delivery_set_basic = stripslashes(htmlspecialchars_decode($result->getValue("delivery_set_basic")));
				$this->delivery_set_conversion = stripslashes(htmlspecialchars_decode($result->getValue("delivery_set_conversion")));
				$this->delivery_set_full = stripslashes(htmlspecialchars_decode($result->getValue("delivery_set_full")));
			}

			if(rex_plugin::get("d2u_machinery", "service_options")->isAvailable()) {
				$this->service_option_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("service_option_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable()) {
				$this->feature_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("feature_ids")), PREG_GREP_INVERT);
			}

			if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
				$process_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("process_ids")), PREG_GREP_INVERT);
				foreach($process_ids as $process_id) {
					$this->processes[$process_id] = new Process($process_id, $this->clang_id);
				}
				$procedure_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("procedure_ids")), PREG_GREP_INVERT);
				foreach($procedure_ids as $procedure_id) {
					$this->procedures[$procedure_id] = new Procedure($procedure_id, $this->clang_id);
				}
				$material_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("material_ids")), PREG_GREP_INVERT);
				foreach($material_ids as $material_id) {
					$this->materials[$material_id] = new Procedure($material_id, $this->clang_id);
				}
				$tool_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("tool_ids")), PREG_GREP_INVERT);
				foreach($tool_ids as $tool_id) {
					$this->tools[$tool_id] = new Tool($tool_id, $this->clang_id);
				}
				$this->automation_supply_single_stroke = $result->getValue("automation_supply_single_stroke");
				$this->automation_supply_multi_stroke = $result->getValue("automation_supply_multi_stroke");
				$this->automation_feedrate = $result->getValue("automation_feedrate");
				$this->automation_rush_leader_flyback = $result->getValue("automation_rush_leader_flyback");
				$automation_automationgrade_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("automation_automationgrade_ids")), PREG_GREP_INVERT);
				foreach($automation_automationgrade_ids as $automation_automationgrade_id) {
					$this->automation_automationgrades[$automation_automationgrade_id] = new Automation($automation_automationgrade_id, $this->clang_id);
				}
				$automation_supply_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("automation_supply_ids")), PREG_GREP_INVERT);
				foreach($automation_supply_ids as $automation_supply_id) {
					$this->automation_supplys[$automation_supply_id] = new Supply($automation_supply_id, $this->clang_id);
				}
				$this->workspace = $result->getValue("workspace");
				$this->workspace_square = $result->getValue("workspace_square");
				$this->workspace_flat = $result->getValue("workspace_flat");
				$this->workspace_plate = $result->getValue("workspace_plate");
				$this->workspace_profile = $result->getValue("workspace_profile");
				$this->workspace_angle_steel = $result->getValue("workspace_angle_steel");
				$this->workspace_round = $result->getValue("workspace_round");
				$this->workspace_min = $result->getValue("workspace_min");
				$this->sheet_width = $result->getValue("sheet_width");
				$this->sheet_length = $result->getValue("sheet_length");
				$this->sheet_thickness = $result->getValue("sheet_thickness");
				$this->tool_changer_locations = $result->getValue("tool_changer_locations");
				$this->drilling_unit_vertical = $result->getValue("drilling_unit_vertical");
				$this->drilling_unit_horizontal = $result->getValue("drilling_unit_horizontal");
				$this->drilling_diameter = $result->getValue("drilling_diameter");
				$this->drilling_tools_axis = $result->getValue("drilling_tools_axis");
				$this->drilling_axis_drive_power = $result->getValue("drilling_axis_drive_power");
				$this->drilling_rpm_speed = $result->getValue("drilling_rpm_speed");
				$this->saw_blade = $result->getValue("saw_blade");
				$this->saw_band = $result->getValue("saw_band");
				$this->saw_band_tilt = $result->getValue("saw_band_tilt");
				$this->saw_cutting_speed = $result->getValue("saw_cutting_speed");
				$this->saw_miter = $result->getValue("saw_miter");
				$this->bevel_angle = $result->getValue("bevel_angle");
				$this->punching_diameter = $result->getValue("punching_diameter");
				$this->punching_power = $result->getValue("punching_power");
				$this->punching_tools = $result->getValue("punching_tools");
				$this->shaving_unit_angle_steel_single_cut = $result->getValue("shaving_unit_angle_steel_single_cut");
				$profile_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("profile_ids")), PREG_GREP_INVERT);
				foreach($profile_ids as $profile_id) {
					$this->profiles[$profile_id] = new Profile($profile_id, $this->clang_id);
				}
				$this->carrier_width = $result->getValue("carrier_width");
				$this->carrier_height = $result->getValue("carrier_height");
				$this->carrier_weight = $result->getValue("carrier_weight");
				$this->flange_thickness = $result->getValue("flange_thickness");
				$this->web_thickness = $result->getValue("web_thickness");
				$this->component_length = $result->getValue("component_length");
				$this->component_weight = $result->getValue("component_weight");
				$welding_process_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("welding_process_ids")), PREG_GREP_INVERT);
				foreach($welding_process_ids as $welding_process_id) {
					$this->weldings[$welding_process_id] = new Welding($welding_process_id, $this->clang_id);
				}
				$this->welding_thickness = $result->getValue("welding_thickness");
				$this->welding_wire_thickness = $result->getValue("welding_wire_thickness");
				$this->beam_continuous_opening = $result->getValue("beam_continuous_opening");
				$this->beam_turbines = $result->getValue("beam_turbines");
				$this->beam_turbine_power = $result->getValue("beam_turbine_power");
				$this->beam_color_guns = $result->getValue("beam_color_guns");
			}

			if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable()) {
				$this->usage_area_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("usage_area_ids")), PREG_GREP_INVERT);
			}
			
			// Videos
			if(\rex_addon::get('d2u_videos')->isAvailable() && $result->getValue("video_ids") != "") {
				$video_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("video_ids")), PREG_GREP_INVERT);
				foreach ($video_ids as $video_id) {
					$this->videos[$video_id] = new Video($video_id, $clang_id);
				}
			}
		}
	}

	/**
	 * Changes the status of a machine
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->machine_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_machines "
					."SET online_status = 'offline' "
					."WHERE machine_id = ". $this->machine_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->machine_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_machines "
					."SET online_status = 'online' "
					."WHERE machine_id = ". $this->machine_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}
	
	/**
	 * Deletes the object.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang "
			."WHERE machine_id = ". $this->machine_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_machinery_machines_lang "
			."WHERE machine_id = ". $this->machine_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
				."WHERE machine_id = ". $this->machine_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
	
	/**
	 * Get all machines.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $only_online Show only online machines
	 * @return Machines[] Array with Machine objects.
	 */
	public static function getAll($clang_id, $only_online = FALSE) {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines ";
		if($only_online) {
			$query .= "WHERE online_status = 'online' ";
		}
		if(\rex_addon::get('d2u_machinery')->getConfig('default_machine_sort') == 'priority') {
			$query .= 'ORDER BY priority ASC';
		}
		else {
			$query .= 'ORDER BY name ASC';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[] = new Machine($result->getValue("machine_id"), $clang_id);
			$result->next();
		}
		return $machines;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
	}
	
	/**
	 * Get Feature objects related to this machine.
	 * @return Feature[] Array with Feature objects.
	 */
	public function getFeatures() {
		$features = [];
		foreach ($this->feature_ids as $feature_id) {
			$features[] = new Feature($feature_id, $this->clang_id);
		}
		return $features;
	}

	/**
	 * Get the <meta rel="alternate" hreflang=""> tags for page header.
	 * @return Complete tags.
	 */
	public function getMetaAlternateHreflangTags() {
		$hreflang_tags = "";
		foreach(rex_clang::getAll(TRUE) as $rex_clang) {
			if($rex_clang->getId() == $this->clang_id && $this->translation_needs_update != "delete") {
				$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $this->getURL() .'" title="'. str_replace('"', '', $this->category->name .': '. $this->name) .'">';
			}
			else {
				$machine = new Machine($this->machine_id, $rex_clang->getId());
				if($machine->translation_needs_update != "delete") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $machine->getURL() .'" title="'. str_replace('"', '', $machine->category->name .': '. $machine->name) .'">';
				}
			}
		}
		return $hreflang_tags;
	}
	
	/**
	 * Get the <meta name="description"> tag for page header.
	 * @return Complete tag.
	 */
	public function getMetaDescriptionTag() {
		return '<meta name="description" content="'. $this->teaser .'">';
	}
	
	/**
	 * Gets the machines referring to this machine as alternate machine.
	 * @return Machine[] Machines referring to this machine as alternate machine.
	 */
	public function getReferringMachines() {
		$query = "SELECT machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE alternative_machine_ids LIKE '%|". $this->machine_id ."|%'";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[] = new Machine($result->getValue("machine_id"), $this->clang_id);
			$result->next();
		}
		return $machines;
	}
	
	/**
	 * Gets the used machines referring to this machine.
	 * @return UsedMachine[] Used machines referring to this machine.
	 */
	public function getReferringUsedMachines() {
		if(rex_plugin::get("d2u_machinery", "used_machines")->isAvailable()) {
			$query = "SELECT used_machine_id FROM ". \rex::getTablePrefix() ."d2u_machinery_used_machines "
				."WHERE machine_id =  ". $this->machine_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			$used_machines = [];
			for($i = 0; $i < $result->getRows(); $i++) {
				$used_machines[] = new UsedMachine($result->getValue("used_machine_id"), $this->clang_id);
				$result->next();
			}
			return $used_machines;
		}
		else {
			return [];
		}
	}
	
	/**
	 * Get Service Option objects related to this machine.
	 * @param boolean $online_only TRUE if only online objects are returned
	 * @return ServiceOption[] Array with ServiceOption objects.
	 */
	public function getServiceOptions($online_only = TRUE) {
		$service_options = [];
		foreach ($this->service_option_ids as $service_option_id) {
			$service_option = new ServiceOption($service_option_id, $this->clang_id);
			if(($online_only && $service_option->online_status == "online") || !$online_only) {
				$service_options[] = $service_option;
			}
		}
		return $service_options;
	}

	/**
	 * Get Technical Data as array.
	 * @return string[] Array with technical data. Each element is an array itself.
	 * First element ist the translation wildcard, second is the value and third
	 * the unit.
	 */
	public function getTechnicalData() {
		$tech_data = [];
		
		// Get placeholder wildcard tags
		$sprog = rex_addon::get("sprog");
		$tag_open = $sprog->getConfig('wildcard_open_tag');
		$tag_close = $sprog->getConfig('wildcard_close_tag');
		
		// Max. viscosity
		if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable() && $this->viscosity > 0) {
			$tech_data[] = [
				"description" => $tag_open . "d2u_machinery_machines_viscosity" . $tag_close,
				"value" => $this->viscosity,
				"unit" => $tag_open . "d2u_machinery_machines_mpas" . $tag_close
			];
		}

		if(rex_plugin::get("d2u_machinery", "machine_construction_equipment_extension")->isAvailable()) {
			// Operating pressure
			if($this->operating_pressure != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_operating_pressure" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_up_to" . $tag_close .' '. $this->operating_pressure,
					"unit" => $tag_open . "d2u_machinery_unit_bar" . $tag_close
				];
			}
			
			// More following later ...
		}

		// Operating voltage
		if($this->operating_voltage_v != "") {
			$v = $this->operating_voltage_v == "" ? "-" : $this->operating_voltage_v;
			$h = $this->operating_voltage_hz == "" ? " / -" : " / ". $this->operating_voltage_hz;
			$a = $this->operating_voltage_a == "" ? " / -" : " / ". $this->operating_voltage_a;
			$tech_data[] = [
				"description" => $tag_open . "d2u_machinery_operating_voltage" . $tag_close,
				"value" => $v . $h . $a,
				"unit" => $tag_open . "d2u_machinery_unit_v" . $tag_close ."/". $tag_open . "d2u_machinery_unit_hz" . $tag_close ."/". $tag_open . "d2u_machinery_unit_a" . $tag_close
			];
		}

		// Engine power
		if($this->engine_power != "") {
			$tech_data[] = [
				"description" => $tag_open . "d2u_machinery_engine_power" . $tag_close,
				"value" => $this->engine_power . ($this->engine_power_frequency_controlled ?  ' ('. $tag_open . "d2u_machinery_engine_power_frequency_controlled" . $tag_close .')' : ''),
				"unit" => $tag_open . "d2u_machinery_unit_kw" . $tag_close
			];
		}
		
		if(rex_plugin::get("d2u_machinery", "machine_construction_equipment_extension")->isAvailable()) {
			// Water capacity
			if($this->waste_water_capacity != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_waste_water_capacity" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->waste_water_capacity,
					"unit" => $tag_open . "d2u_machinery_unit_l" . $tag_close
				];
			}
			
			// Capacity
			if($this->container_capacity != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_container_capacity" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->container_capacity,
					"unit" => $tag_open . "d2u_machinery_unit_kg" . $tag_close
				];
			}
			
			// Empty weight
			if($this->container_weight_empty != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_container_weight_empty" . $tag_close,
					"value" => $this->container_weight_empty,
					"unit" => $tag_open . "d2u_machinery_unit_kg" . $tag_close
				];
			}
			
			// Mixing performance
			if($this->container_mixing_performance != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_container_mixing_performance" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->container_mixing_performance,
					"unit" => $tag_open . "d2u_machinery_unit_l_min" . $tag_close
				];
			}
			
			// Flow volume
			if($this->pump_flow_volume_fluid != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_flow_volume_fluid" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_flow_volume_fluid,
					"unit" => $tag_open . "d2u_machinery_unit_l_min" . $tag_close
				];
			}
			
			// Flow volume
			if($this->pump_flow_volume_mineral != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_flow_volume_mineral" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_flow_volume_mineral,
					"unit" => $tag_open . "d2u_machinery_unit_l_min" . $tag_close
				];
			}
			
			// Flow volume
			if($this->pump_flow_volume_pasty != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_flow_volume_pasty" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_flow_volume_pasty,
					"unit" => $tag_open . "d2u_machinery_unit_l_min" . $tag_close
				];
			}
			
			// Conveying distance
			if($this->pump_conveying_distance_fluid > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_conveying_distance_fluid" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_conveying_distance_fluid,
					"unit" => $tag_open . "d2u_machinery_unit_m" . $tag_close
				];
			}
			
			// Conveying distance
			if($this->pump_conveying_distance_mineral > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_conveying_distance_mineral" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_conveying_distance_mineral,
					"unit" => $tag_open . "d2u_machinery_unit_m" . $tag_close
				];
			}
			
			// Conveying distance
			if($this->pump_conveying_distance_pasty > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_conveying_distance_pasty" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_conveying_distance_pasty,
					"unit" => $tag_open . "d2u_machinery_unit_m" . $tag_close
				];
			}
			
			// Pressure height
			if($this->pump_pressure_height_fluid > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_pressure_height_fluid" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_pressure_height_fluid,
					"unit" => $tag_open . "d2u_machinery_unit_m" . $tag_close
				];
			}
			
			// Pressure height
			if($this->pump_pressure_height_mineral > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_pressure_height_mineral" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_pressure_height_mineral,
					"unit" => $tag_open . "d2u_machinery_unit_m" . $tag_close
				];
			}
			
			// Grain size
			if($this->pump_grain_size != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_grain_size" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->pump_grain_size,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Nozzle size
			if($this->airless_nozzle_size != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_airless_nozzle_size" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->airless_nozzle_size,
					"unit" => '"'
				];
			}
			
			// Nozzle size
			if($this->pump_material_container != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_material_container" . $tag_close,
					"value" => $this->pump_material_container,
					"unit" => $tag_open . "d2u_machinery_unit_l" . $tag_close
				];
			}
			
			// Filling
			if($this->pump_filling != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_pump_filling" . $tag_close,
					"value" => $tag_open . "d2u_machinery_unit_ca" . $tag_close .' '. $this->pump_filling,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Hose connection
			if($this->airless_hose_connection != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_airless_hose_connection" . $tag_close,
					"value" => $this->airless_hose_connection,
					"unit" => '"'
				];
			}
			
			// Hose diameter
			if($this->airless_hose_diameter > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_airless_hose_diameter" . $tag_close,
					"value" => $this->airless_hose_diameter,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Hose length
			if($this->airless_hose_length > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_airless_hose_length" . $tag_close,
					"value" => $this->airless_hose_length,
					"unit" => $tag_open . "d2u_machinery_unit_m" . $tag_close
				];
			}
			
			// Grinding plate
			if($this->grinder_grinding_plate > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_grinder_grinding_plate" . $tag_close,
					"value" => $this->grinder_grinding_plate,
					"unit" => $tag_open . "d2u_machinery_unit_cm2" . $tag_close
				];
			}
			
			// Grinding wheel
			if($this->grinder_grinding_wheel > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_grinder_grinding_wheel" . $tag_close,
					"value" => $this->grinder_grinding_wheel,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Grinding wheel
			if($this->grinder_rotational_frequency != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_grinder_rotational_frequency" . $tag_close,
					"value" => $this->grinder_rotational_frequency,
					"unit" => $tag_open . "d2u_machinery_unit_rotations_min" . $tag_close
				];
			}
			
			// Vacuum connection
			if($this->grinder_vacuum_connection > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_grinder_vacuum_connection" . $tag_close,
					"value" => $this->grinder_vacuum_connection,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Sanding
			if($this->grinder_sanding != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_grinder_sanding" . $tag_close,
					"value" => $this->grinder_sanding,
					"unit" => $tag_open . "d2u_machinery_unit_rotations_min" . $tag_close
				];
			}
			
			// Cutting length
			if($this->cutters_cutting_length != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_cutters_cutting_length" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->cutters_cutting_length,
					"unit" => $tag_open . "d2u_machinery_unit_cm" . $tag_close
				];
			}
			
			// Cutting depth
			if($this->cutters_cutting_depth != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_cutters_cutting_depth" . $tag_close,
					"value" => $tag_open . "d2u_machinery_construction_equipment_max" . $tag_close .' '. $this->cutters_cutting_depth,
					"unit" => $tag_open . "d2u_machinery_unit_cm" . $tag_close
				];
			}
			
			// Rod length
			if($this->cutters_rod_length != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_cutters_rod_length" . $tag_close,
					"value" => $this->cutters_rod_length,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Conveying wave
			if($this->container_conveying_wave != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_container_conveying_wave" . $tag_close,
					"value" => $this->container_conveying_wave,
					"unit" => ""
				];
			}
			
			// Water connection
			if($this->container_waterconnect_diameter != "" && $this->container_waterconnect_pressure > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_container_waterconnect" . $tag_close,
					"value" => $this->container_waterconnect_pressure .' ('. $this->container_waterconnect_diameter .'")',
					"unit" => $tag_open . "d2u_machinery_unit_bar" . $tag_close
				];
			}
			
			// Connection port
			if($this->container_connection_port != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_container_connection_port" . $tag_close,
					"value" => $this->container_connection_port,
					"unit" => ""
				];
			}
			
			// Machine technique
			if($this->category !== FALSE) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_machine_technique" . $tag_close,
					"value" => '<a href="'. $this->category->getUrl() .'">'. $this->category->name .'</a>',
					"unit" => ""
				];
			}
			
			// Working width
			if($this->floor_working_width > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_floor_working_width" . $tag_close,
					"value" => $this->floor_working_width,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Working pressure
			if($this->floor_working_pressure != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_floor_working_pressure" . $tag_close,
					"value" => $this->floor_working_pressure,
					"unit" => $tag_open . "d2u_machinery_unit_kg" . $tag_close
				];
			}
			
			// Dust extraction connection
			if($this->floor_dust_extraction_connection != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_floor_dust_extraction_connection" . $tag_close,
					"value" => $this->floor_dust_extraction_connection,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Feedrate
			if($this->floor_feedrate != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_floor_feedrate" . $tag_close,
					"value" => $this->floor_feedrate,
					"unit" => $tag_open . "d2u_machinery_unit_m_min" . $tag_close
				];
			}
			
			// Beam power on concrete
			if($this->floor_beam_power_on_concrete != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_floor_beam_power_on_concrete" . $tag_close,
					"value" => $this->floor_beam_power_on_concrete,
					"unit" => $tag_open . "d2u_machinery_unit_m2_h" . $tag_close
				];
			}
			
			// Filter connection
			if($this->floor_filter_connection != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_floor_filter_connection" . $tag_close,
					"value" => $this->floor_filter_connection,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Rotations
			if($this->floor_rotations != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_floor_rotations" . $tag_close,
					"value" => $this->floor_rotations,
					"unit" => $tag_open . "d2u_machinery_unit_min1" . $tag_close
				];
			}

			// Technical description
			if($this->description_technical != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_construction_equipment_description_technical" . $tag_close,
					"value" => $this->description_technical,
					"unit" => ""
				];
			}
		}

		
		// Dimensions
		if($this->length > 0 && $this->width > 0 && $this->height > 0) {
			$tech_data[] = [
				"description" => $tag_open . "d2u_machinery_dimensions_length_width_height" . $tag_close,
				"value" => $this->length .' x '. $this->width .' x '. $this->height,
				"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
			];
		}
		else if($this->depth > 0 && $this->width > 0 && $this->height > 0) {
			$tech_data[] = [
				"description" => $tag_open . "d2u_machinery_dimensions_width_height_depth" . $tag_close,
				"value" => $this->width .' x '. $this->height	.' x '. $this->depth,
				"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
			];
		}
		else if($this->length > 0) {
			$tech_data[] = [
				"description" => $tag_open . "d2u_machinery_dimensions_length" . $tag_close,
				"value" => $this->length,
				"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
			];
		}

		// Weight
		if($this->weight != "") {
			$tech_data[] = [
				"description" => $tag_open . "d2u_machinery_weight" . $tag_close,
				"value" => $tag_open . "d2u_machinery_unit_ca" . $tag_close .' '. $this->weight,
				"unit" => $tag_open . "d2u_machinery_unit_kg" . $tag_close
			];
		}
		
		if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
			// Procedures
			if(count($this->procedures) > 0) {
				$procedures = [];
				foreach($this->procedures as $procedure) {
					$procedures[] = $procedure->name;
				}
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_procedures" . $tag_close,
					"value" => implode('<br>', $procedures),
					"unit" => ""
				];
			}

			// Saw blade
			if($this->saw_blade != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_saw_blade" . $tag_close,
					"value" => $this->saw_blade,
					"unit" => $tag_open . "d2u_machinery_unit_diameter_mm" . $tag_close
				];
			}

			// Saw band
			if($this->saw_band != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_saw_band" . $tag_close,
					"value" => $this->saw_band,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Saw band tilt
			if($this->saw_band_tilt != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_saw_band_tilt" . $tag_close,
					"value" => $this->saw_band_tilt,
					"unit" => $tag_open . "d2u_machinery_unit_degrees" . $tag_close
				];
			}

			// Saw cutting speed
			if($this->saw_cutting_speed != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_saw_cutting_speed" . $tag_close,
					"value" => $this->saw_cutting_speed,
					"unit" => $tag_open . "d2u_machinery_unit_m_min" . $tag_close
				];
			}

			// Feed rate
			if($this->automation_feedrate != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_automation_feedrate" . $tag_close,
					"value" => $this->automation_feedrate,
					"unit" => $tag_open . "d2u_machinery_unit_mm_min" . $tag_close
				];
			}

			// Rush leader flyback
			if($this->automation_rush_leader_flyback != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_automation_rush_leader_flyback" . $tag_close,
					"value" => $this->automation_rush_leader_flyback,
					"unit" => $tag_open . "d2u_machinery_unit_mm_min" . $tag_close
				];
			}

			// Workspace max.
			if($this->workspace != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace" . $tag_close,
					"value" => $this->workspace,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Workspace square
			if($this->workspace_square != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace_square" . $tag_close,
					"value" => $this->workspace_square,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Workspace flat
			if($this->workspace_flat != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace_flat" . $tag_close,
					"value" => $this->workspace_flat,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Workspace round
			if($this->workspace_round != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace_round" . $tag_close,
					"value" => $this->workspace_round,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Workspace plates
			if($this->workspace_plate != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace_plate" . $tag_close,
					"value" => $this->workspace_plate,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Workspace profile
			if($this->workspace_profile != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace_profile" . $tag_close,
					"value" => $this->workspace_profile,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Workspace angle steel
			if($this->workspace_angle_steel != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace_angle_steel" . $tag_close,
					"value" => $this->workspace_angle_steel,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Workspace minimum
			if($this->workspace_min != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_workspace_min" . $tag_close,
					"value" => $this->workspace_min,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Bevel angle
			if($this->bevel_angle > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_bevel_angle" . $tag_close,
					"value" => $this->bevel_angle,
					"unit" => $tag_open . "d2u_machinery_unit_degrees" . $tag_close
				];
			}

			// Continuous opening
			if($this->beam_continuous_opening != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_beam_continuous_opening" . $tag_close,
					"value" => $this->beam_continuous_opening,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Color guns
			if($this->beam_color_guns != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_beam_color_guns" . $tag_close,
					"value" => $this->beam_color_guns,
					"unit" => ""
				];
			}

			// Number turbines
			if($this->beam_turbines > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_beam_turbines" . $tag_close,
					"value" => $this->beam_turbines,
					"unit" => $tag_open . "d2u_machinery_unit_pieces" . $tag_close
				];
			}

			// Turbine power
			if($this->beam_turbine_power != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_beam_turbine_power" . $tag_close,
					"value" => $this->beam_turbine_power,
					"unit" => $tag_open . "d2u_machinery_unit_kw" . $tag_close
				];
			}

			// saw miter
			if($this->saw_miter != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_saw_miter" . $tag_close,
					"value" => $this->saw_miter,
					"unit" => ""
				];
			}

			// Vertical drilling units 
			if($this->drilling_unit_vertical != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_drilling_unit_vertical" . $tag_close,
					"value" => $this->drilling_unit_vertical,
					"unit" => $tag_open . "d2u_machinery_unit_pieces" . $tag_close
				];
			}

			// Horizontal drilling units 
			if($this->drilling_unit_horizontal != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_drilling_unit_horizontal" . $tag_close,
					"value" => $this->drilling_unit_horizontal,
					"unit" => $tag_open . "d2u_machinery_unit_pieces" . $tag_close
				];
			}

			// Horizontal drilling units 
			if($this->drilling_diameter != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_drilling_diameter" . $tag_close,
					"value" => $this->drilling_diameter,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Drilling tools per axis 
			if($this->drilling_tools_axis != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_drilling_tools_axis" . $tag_close,
					"value" => $this->drilling_tools_axis,
					"unit" => $tag_open . "d2u_machinery_unit_pieces" . $tag_close
				];
			}

			// Drilling axis drive power
			if($this->drilling_axis_drive_power != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_drilling_axis_drive_power" . $tag_close,
					"value" => $this->drilling_axis_drive_power,
					"unit" => $tag_open . "d2u_machinery_unit_kw" . $tag_close
				];
			}

			// Max. drilling speed
			if($this->drilling_rpm_speed != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_drilling_rpm_speed" . $tag_close,
					"value" => $this->drilling_rpm_speed,
					"unit" => $tag_open . "d2u_machinery_unit_min1" . $tag_close
				];
			}

			// Sheet width
			if($this->sheet_width != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_sheet_width" . $tag_close,
					"value" => $this->sheet_width,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Sheet length
			if($this->sheet_length != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_sheet_length" . $tag_close,
					"value" => $this->sheet_length,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Sheet thickness
			if($this->sheet_thickness != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_sheet_thickness" . $tag_close,
					"value" => $this->sheet_thickness,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Tool changer locations
			if($this->tool_changer_locations != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_tool_changer_locations" . $tag_close,
					"value" => $this->tool_changer_locations,
					"unit" => $tag_open . "d2u_machinery_unit_pieces" . $tag_close
				];
			}

			// punching diameter
			if($this->punching_diameter != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_punching_diameter" . $tag_close,
					"value" => $this->punching_diameter,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Number of punching tools
			if($this->punching_tools != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_punching_tools" . $tag_close,
					"value" => $this->punching_tools,
					"unit" => $tag_open . "d2u_machinery_unit_pieces" . $tag_close
				];
			}

			// Punching power
			if($this->punching_power > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_punching_power" . $tag_close,
					"value" => $this->punching_power,
					"unit" => $tag_open . "d2u_machinery_unit_kn" . $tag_close
				];
			}

			// Shaving unit angle steel singel cut
			if($this->shaving_unit_angle_steel_single_cut > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_shaving_unit_angle_steel_single_cut" . $tag_close,
					"value" => $this->shaving_unit_angle_steel_single_cut,
					"unit" => $tag_open . "d2u_machinery_unit_kn" . $tag_close
				];
			}
			
			// Tools
			if(count($this->tools) > 0) {
				$tools = [];
				foreach($this->tools as $tool) {
					$tools[] = $tool->name;
				}
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_tools" . $tag_close,
					"value" => implode('<br>', $tools),
					"unit" => ""
				];
			}
			
			// Automationgrades
			if(count($this->automation_automationgrades) > 0) {
				$automation_automationgrades = [];
				foreach($this->automation_automationgrades as $automationgrade) {
					$automation_automationgrades[] = $automationgrade->name;
				}
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_automation_automationgrades" . $tag_close,
					"value" => implode('<br>', $automation_automationgrades),
					"unit" => ""
				];
			}
			
			// Material classes
			if(count($this->materials) > 0) {
				$materials = [];
				foreach($this->materials as $material) {
					$materials[] = $material->name;
				}
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_materials" . $tag_close,
					"value" => implode('<br>', $materials),
					"unit" => ""
				];
			}
			
			// Processes
			if(count($this->processes) > 0) {
				$processes = [];
				foreach($this->processes as $process) {
					$processes[] = $process->name;
				}
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_processes" . $tag_close,
					"value" => implode('<br>', $processes),
					"unit" => ""
				];
			}
			
			// Profiles
			if(count($this->profiles) > 0) {
				$profiles = [];
				foreach($this->profiles as $profile) {
					$profiles[] = $profile->name;
				}
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_profiles" . $tag_close,
					"value" => implode('<br>', $profiles),
					"unit" => ""
				];
			}

			// Carrier width
			if($this->carrier_width != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_carrier_width" . $tag_close,
					"value" => $this->carrier_width,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Carrier height
			if($this->carrier_height != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_carrier_height" . $tag_close,
					"value" => $this->carrier_height,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}

			// Carrier max. weight
			if($this->carrier_weight > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_carrier_weight" . $tag_close,
					"value" => $this->carrier_weight,
					"unit" => $tag_open . "d2u_machinery_unit_kg" . $tag_close
				];
			}
			
			// Flange thickness
			if($this->flange_thickness != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_flange_thickness" . $tag_close,
					"value" => $this->flange_thickness,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Web thickness min. / max.
			if($this->web_thickness != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_web_thickness" . $tag_close,
					"value" => $this->web_thickness,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Component length
			if($this->component_length != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_component_length" . $tag_close,
					"value" => $this->component_length,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Component weight
			if($this->component_weight > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_component_weight" . $tag_close,
					"value" => $this->component_weight,
					"unit" => $tag_open . "d2u_machinery_unit_kg" . $tag_close
				];
			}
			
			// Welding processes
			if(count($this->weldings) > 0) {
				$welding_processes = [];
				foreach($this->weldings as $welding) {
					$welding_processes[] = $welding->name;
				}
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_weldings" . $tag_close,
					"value" => implode('<br>', $welding_processes),
					"unit" => ""
				];
			}
			
			// Welding thickness
			if($this->welding_thickness > 0) {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_welding_thickness" . $tag_close,
					"value" => $this->welding_thickness,
					"unit" => $tag_open . "d2u_machinery_unit_a_mm" . $tag_close
				];
			}
			
			// Welding wire thickness
			if($this->welding_wire_thickness != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_welding_wire_thickness" . $tag_close,
					"value" => $this->welding_wire_thickness,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Welding wire thickness
			if($this->welding_wire_thickness != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_welding_wire_thickness" . $tag_close,
					"value" => $this->welding_wire_thickness,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Automation supply single stroke
			if($this->automation_supply_single_stroke != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_automation_supply_single_stroke" . $tag_close,
					"value" => $this->automation_supply_single_stroke,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
			
			// Automation supply multi stroke
			if($this->automation_supply_multi_stroke != "") {
				$tech_data[] = [
					"description" => $tag_open . "d2u_machinery_steel_automation_supply_multi_stroke" . $tag_close,
					"value" => $this->automation_supply_multi_stroke,
					"unit" => $tag_open . "d2u_machinery_unit_mm" . $tag_close
				];
			}
		}
		
		return $tech_data;
	}
	
	/**
	 * Get the <title> tag for page header.
	 * @return Complete title tag.
	 */
	public function getTitleTag() {
		return '<title>'. $this->name .' / '. $this->category->name .' / '. \rex::getServerName() .'</title>';
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Machine[] Array with Machine objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT lang.machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS lang '
				.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines AS main '
					.'ON lang.machine_id = main.machine_id '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.machine_id FROM '. \rex::getTablePrefix() .'d2u_machinery_machines AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS target_lang '
						.'ON main.machine_id = target_lang.machine_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_machines_lang AS default_lang '
						.'ON main.machine_id = default_lang.machine_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.machine_id IS NULL "
					.'ORDER BY main.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Machine($result->getValue("machine_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/**
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$parameterArray = [];
			$parameterArray['machine_id'] = $this->machine_id;
			$this->url = rex_getUrl(rex_config::get('d2u_machinery', 'article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			if(\rex_addon::get('yrewrite')->isAvailable())  {
				return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
			}
			else {
				return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);
			}
		}
		else {
			return $this->url;
		}
	}

	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = FALSE;

		// Save the not language specific part
		$pre_save_machine = new Machine($this->machine_id, $this->clang_id);

		if($this->machine_id == 0 || $pre_save_machine != $this) {
			$query = \rex::getTablePrefix() ."d2u_machinery_machines SET "
					."name = '". addslashes($this->name) ."', "
					."pics = '". implode(",", $this->pics) ."', "
					."category_id = ". ($this->category ? $this->category->category_id : 0) .", "
					."alternative_machine_ids = '|". implode("|", $this->alternative_machine_ids) ."|', "
					."product_number = '". $this->product_number ."', "
					."article_id_software = '". $this->article_id_software ."', "
					."article_id_service = '". $this->article_id_service ."', "
					."article_ids_references = '". implode(",", $this->article_ids_references) ."', "
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
			if(rex_plugin::get("d2u_machinery", "equipment")->isAvailable()) {
				$query .= ", equipment_ids = '|". implode("|", $this->equipment_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "industry_sectors")->isAvailable()) {
				$query .= ", industry_sector_ids = '|". implode("|", $this->industry_sector_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_agitator_extension")->isAvailable()) {
				$query .= ", agitator_type_id = ". $this->agitator_type_id ." "
					.", viscosity = ". $this->viscosity ." ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_certificates_extension")->isAvailable()) {
				$query .= ", certificate_ids = '|". implode("|", $this->certificate_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_construction_equipment_extension")->isAvailable()) {
				$query .= ", airless_hose_connection = '". $this->airless_hose_connection ."' "
					.", airless_hose_diameter = ". $this->airless_hose_diameter ." "
					.", airless_hose_length = ". $this->airless_hose_length ." "
					.", airless_nozzle_size = '". $this->airless_nozzle_size ."' "
					.", container_capacity = '". $this->container_capacity ."' "
					.", container_mixing_performance = '". $this->container_mixing_performance ."' "
					.", container_waterconnect_pressure = ". $this->container_waterconnect_pressure ." "
					.", container_waterconnect_diameter = '". $this->container_waterconnect_diameter ."' "
					.", container_weight_empty = '". $this->container_weight_empty ."' "
					.", cutters_cutting_depth = '". $this->cutters_cutting_depth ."' "
					.", cutters_cutting_length = '". $this->cutters_cutting_length ."' "
					.", cutters_rod_length = '". $this->cutters_rod_length ."' "
					.", floor_beam_power_on_concrete = '". $this->floor_beam_power_on_concrete ."' "
					.", floor_dust_extraction_connection = '". $this->floor_dust_extraction_connection ."' "
					.", floor_feedrate = '". $this->floor_feedrate ."' "
					.", floor_filter_connection = '". $this->floor_filter_connection ."' "
					.", floor_rotations = '". $this->floor_rotations ."' "
					.", floor_working_pressure = '". $this->floor_working_pressure ."' "
					.", floor_working_width = ". $this->floor_working_width ." "
					.", grinder_grinding_plate = ". $this->grinder_grinding_plate ." "
					.", grinder_grinding_wheel = ". $this->grinder_grinding_wheel ." "
					.", grinder_rotational_frequency = '". $this->grinder_rotational_frequency ."' "
					.", grinder_sanding = '". $this->grinder_sanding ."' "
					.", grinder_vacuum_connection = ". $this->grinder_vacuum_connection ." "
					.", operating_pressure = '". $this->operating_pressure ."' "
					.", pictures_delivery_set = '". implode(",", $this->pictures_delivery_set) ."' "
					.", pump_conveying_distance_fluid = ". ($this->pump_conveying_distance_fluid > 0 ? $this->pump_conveying_distance_fluid : 0) ." "
					.", pump_conveying_distance_mineral = ". ($this->pump_conveying_distance_mineral > 0 ? $this->pump_conveying_distance_mineral : 0) ." "
					.", pump_conveying_distance_pasty = ". ($this->pump_conveying_distance_pasty > 0 ? $this->pump_conveying_distance_pasty : 0) ." "
					.", pump_filling = '". $this->pump_filling ."' "
					.", pump_flow_volume_fluid = '". $this->pump_flow_volume_fluid ."' "
					.", pump_flow_volume_mineral = '". $this->pump_flow_volume_mineral ."' "
					.", pump_flow_volume_pasty = '". $this->pump_flow_volume_pasty ."' "
					.", pump_grain_size = '". $this->pump_grain_size ."' "
					.", pump_material_container = '". $this->pump_material_container ."' "
					.", pump_pressure_height_fluid = ". ($this->pump_pressure_height_fluid > 0 ? $this->pump_pressure_height_fluid : 0) ." "
					.", pump_pressure_height_mineral = ". ($this->pump_pressure_height_mineral > 0 ? $this->pump_pressure_height_mineral : 0) ." "
					.", pump_pressure_height_pasty = ". ($this->pump_pressure_height_pasty > 0 ? $this->pump_pressure_height_pasty : 0) ." "
					.", waste_water_capacity = '". $this->waste_water_capacity ."' ";
			}
			if(rex_plugin::get("d2u_machinery", "service_options")->isAvailable()) {
				$query .= ", service_option_ids = '|". implode("|", $this->service_option_ids) ."|' ";
			}			
			if(rex_plugin::get("d2u_machinery", "machine_features_extension")->isAvailable()) {
				$query .= ", feature_ids = '|". implode("|", $this->feature_ids) ."|' ";
			}
			if(rex_plugin::get("d2u_machinery", "machine_steel_processing_extension")->isAvailable()) {
				$query .= ", process_ids = '|". implode("|", array_keys($this->processes)) ."|' "
					.", procedure_ids = '|". implode("|", array_keys($this->procedures)) ."|' "
					.", material_ids = '|". implode("|", array_keys($this->materials)) ."|' "
					.", tool_ids = '|". implode("|", array_keys($this->tools)) ."|' "
					.", automation_supply_single_stroke = '". $this->automation_supply_single_stroke ."' "
					.", automation_supply_multi_stroke = '". $this->automation_supply_multi_stroke ."' "
					.", automation_feedrate = '". $this->automation_feedrate ."' "
					.", automation_rush_leader_flyback = '". $this->automation_rush_leader_flyback ."' "
					.", automation_automationgrade_ids = '|". implode("|", array_keys($this->automation_automationgrades)) ."|' "
					.", automation_supply_ids = '|". implode("|", array_keys($this->automation_supplys)) ."|' "
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
					.", bevel_angle = ". $this->bevel_angle ." "
					.", punching_diameter = '". $this->punching_diameter ."' "
					.", punching_power = ". $this->punching_power ." "
					.", punching_tools = '". $this->punching_tools ."' "
					.", shaving_unit_angle_steel_single_cut = ". $this->shaving_unit_angle_steel_single_cut ." "
					.", profile_ids = '|". implode("|", array_keys($this->profiles)) ."|' "
					.", carrier_width = '". $this->carrier_width ."' "
					.", carrier_height = '". $this->carrier_height ."' "
					.", carrier_weight = ". $this->carrier_weight ." "
					.", flange_thickness = '". $this->flange_thickness ."' "
					.", web_thickness = '". $this->web_thickness ."' "
					.", component_length = '". $this->component_length ."' "
					.", component_weight = ". $this->component_weight ." "
					.", welding_process_ids = '|". implode("|", array_keys($this->weldings)) ."|' "
					.", welding_thickness = ". $this->welding_thickness ." "
					.", welding_wire_thickness = '". $this->welding_wire_thickness ."' "
					.", beam_continuous_opening = '". $this->beam_continuous_opening ."' "
					.", beam_turbines = ". $this->beam_turbines ." "
					.", beam_turbine_power = '". $this->beam_turbine_power ."' "
					.", beam_color_guns = '". $this->beam_color_guns ."' "
				;
			}
			if(rex_plugin::get("d2u_machinery", "machine_usage_area_extension")->isAvailable()) {
				$query .= ", usage_area_ids = '|". implode("|", $this->usage_area_ids) ."|' ";
			}
			if(\rex_addon::get('d2u_videos')->isAvailable() && count($this->videos) > 0) {
				$query .= ", video_ids = '|". implode("|", array_keys($this->videos)) ."|' ";
			}
			else {
				$query .= ", video_ids = '' ";
			}

			if($this->machine_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE machine_id = ". $this->machine_id;
			}
			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->machine_id == 0) {
				$this->machine_id = $result->getLastId();
				$error = $result->hasError();
			}
		}
		// save priority, but only if new or changed
		if($this->priority != $pre_save_machine->priority || $this->machine_id == 0) {
			$this->setPriority();
		}

		if($error === FALSE) {
			// Save the language specific part
			$pre_save_machine = new Machine($this->machine_id, $this->clang_id);
			if($pre_save_machine != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_machinery_machines_lang SET "
						."machine_id = '". $this->machine_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."lang_name = '". $this->lang_name ."', "
						."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."pdfs = '". implode(",", $this->pdfs) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". \rex::getUser()->getLogin() ."' ";
				if(rex_plugin::get("d2u_machinery", "machine_construction_equipment_extension")->isAvailable()) {
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
			}
		}

		// Update URLs
		if(\rex_addon::get("url")->isAvailable()) {
			UrlGenerator::generatePathFile([]);
		}
		
		return !$error;
	}
	
	/**
	 * Reassigns priority to all Machines in database.
	 */
	private function setPriority() {
		// Pull prios from database
		$query = "SELECT machine_id, priority FROM ". \rex::getTablePrefix() ."d2u_machinery_machines "
			."WHERE machine_id <> ". $this->machine_id ." ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high, simply add at end 
		if($this->priority > $result->getRows()) {
			$this->priority = $result->getRows() + 1;
		}

		$machines = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$machines[$result->getValue("priority")] = $result->getValue("machine_id");
			$result->next();
		}
		array_splice($machines, ($this->priority - 1), 0, array($this->machine_id));

		// Save all prios
		foreach($machines as $prio => $machine_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_machinery_machines "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE machine_id = ". $machine_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}