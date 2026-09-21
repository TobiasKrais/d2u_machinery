<?php

namespace TobiasKrais\D2UMachinery\Api;

use rex_addon;
use rex_clang;
use TobiasKrais\D2UMachinery\Agitator;
use TobiasKrais\D2UMachinery\Category;
use TobiasKrais\D2UMachinery\Certificate;
use TobiasKrais\D2UMachinery\Contact;
use TobiasKrais\D2UMachinery\Equipment;
use TobiasKrais\D2UMachinery\EquipmentGroup;
use TobiasKrais\D2UMachinery\Extension;
use TobiasKrais\D2UMachinery\Feature;
use TobiasKrais\D2UMachinery\IndustrySector;
use TobiasKrais\D2UMachinery\Machine;
use TobiasKrais\D2UMachinery\Option;
use TobiasKrais\D2UMachinery\ProductionLine;
use TobiasKrais\D2UMachinery\ServiceOption;
use TobiasKrais\D2UMachinery\Supply;
use TobiasKrais\D2UMachinery\UsageArea;
use TobiasKrais\D2UMachinery\UsedMachine;

/**
 * Single source of truth for the d2u_machinery REST API.
 *
 * Describes every writable/readable resource field, which extension gates it and
 * whether it is language specific. The same definition drives the discovery
 * endpoint (so an external AI knows which fields it may fill for the current
 * addon configuration), write validation and the payload-to-model mapping.
 */
final class Schema
{
    /**
     * Resource meta: model class, id field and the extension that must be active
     * for the whole resource (null = always available).
     *
     * @var array<string,array{class:class-string,id:string,extension:?string}>
     */
    private const RESOURCES = [
        'machines' => ['class' => Machine::class, 'id' => 'machine_id', 'extension' => null],
        'categories' => ['class' => Category::class, 'id' => 'category_id', 'extension' => null],
        'features' => ['class' => Feature::class, 'id' => 'feature_id', 'extension' => 'machine_features_extension'],
        'options' => ['class' => Option::class, 'id' => 'option_id', 'extension' => 'machine_options_extension'],
        'certificates' => ['class' => Certificate::class, 'id' => 'certificate_id', 'extension' => 'machine_certificates_extension'],
        'equipment' => ['class' => Equipment::class, 'id' => 'equipment_id', 'extension' => 'equipment'],
        'contacts' => ['class' => Contact::class, 'id' => 'contact_id', 'extension' => 'contacts'],
        'used_machines' => ['class' => UsedMachine::class, 'id' => 'used_machine_id', 'extension' => 'used_machines'],
        'equipment_groups' => ['class' => EquipmentGroup::class, 'id' => 'group_id', 'extension' => 'equipment'],
        'agitators' => ['class' => Agitator::class, 'id' => 'agitator_id', 'extension' => 'machine_agitator_extension'],
        'service_options' => ['class' => ServiceOption::class, 'id' => 'service_option_id', 'extension' => 'service_options'],
        'industry_sectors' => ['class' => IndustrySector::class, 'id' => 'industry_sector_id', 'extension' => 'industry_sectors'],
        'production_lines' => ['class' => ProductionLine::class, 'id' => 'production_line_id', 'extension' => 'production_lines'],
        'supplies' => ['class' => Supply::class, 'id' => 'supply_id', 'extension' => 'machine_steel_automation_extension'],
        'usage_areas' => ['class' => UsageArea::class, 'id' => 'usage_area_id', 'extension' => 'machine_usage_area_extension'],
    ];

    /**
     * Field definitions per resource.
     *
     * Each field: type, required (on create), language (per clang), extension
     * (null = core), relation (target resource for id references).
     *
     * @var array<string,array<string,array{type:string,required?:bool,language?:bool,extension?:?string,relation?:string,seo?:string}>>
     */
    private const FIELDS = [
        'machines' => [
            'name' => ['type' => 'string', 'required' => true, 'seo' => 'title'],
            'product_number' => ['type' => 'string'],
            'priority' => ['type' => 'int'],
            'online_status' => ['type' => 'enum:online,offline'],
            'pics' => ['type' => 'media[]', 'seo' => 'image'],
            'category_id' => ['type' => 'int', 'relation' => 'categories'],
            'alternative_machine_ids' => ['type' => 'int[]', 'relation' => 'machines'],
            'additional_machine_ids' => ['type' => 'int[]', 'relation' => 'machines'],
            'reference_ids' => ['type' => 'int[]', 'relation' => 'references'],
            'video_ids' => ['type' => 'int[]', 'relation' => 'videos'],
            'lang_name' => ['type' => 'string', 'language' => true, 'required' => true, 'seo' => 'title'],
            'teaser' => ['type' => 'string', 'language' => true, 'seo' => 'description'],
            'description' => ['type' => 'html', 'language' => true],
            'benefits_short' => ['type' => 'html', 'language' => true],
            'benefits_long' => ['type' => 'html', 'language' => true],
            'faq' => ['type' => 'faq[]', 'language' => true],
            'leaflet' => ['type' => 'media', 'language' => true],
            'pdfs' => ['type' => 'media[]', 'language' => true],
            'engine_power' => ['type' => 'string', 'extension' => 'basic_tech_data'],
            'engine_power_frequency_controlled' => ['type' => 'bool', 'extension' => 'basic_tech_data'],
            'length' => ['type' => 'int', 'extension' => 'basic_tech_data'],
            'width' => ['type' => 'int', 'extension' => 'basic_tech_data'],
            'height' => ['type' => 'int', 'extension' => 'basic_tech_data'],
            'depth' => ['type' => 'int', 'extension' => 'basic_tech_data'],
            'weight' => ['type' => 'string', 'extension' => 'basic_tech_data'],
            'operating_voltage_v' => ['type' => 'string', 'extension' => 'basic_tech_data'],
            'operating_voltage_hz' => ['type' => 'string', 'extension' => 'basic_tech_data'],
            'operating_voltage_a' => ['type' => 'string', 'extension' => 'basic_tech_data'],
            'equipment_ids' => ['type' => 'int[]', 'extension' => 'equipment', 'relation' => 'equipment'],
            'industry_sector_ids' => ['type' => 'int[]', 'extension' => 'industry_sectors', 'relation' => 'industry_sectors'],
            'feature_ids' => ['type' => 'int[]', 'extension' => 'machine_features_extension', 'relation' => 'features'],
            'option_ids' => ['type' => 'int[]', 'extension' => 'machine_options_extension', 'relation' => 'options'],
            'certificate_ids' => ['type' => 'int[]', 'extension' => 'machine_certificates_extension', 'relation' => 'certificates'],
            'contact_id' => ['type' => 'int', 'extension' => 'contacts', 'relation' => 'contacts'],
            'automation_supply_ids' => ['type' => 'int[]', 'extension' => 'machine_steel_automation_extension', 'relation' => 'supplies'],
            'usage_area_ids' => ['type' => 'int[]', 'extension' => 'machine_usage_area_extension', 'relation' => 'usage_areas'],
            'service_option_ids' => ['type' => 'int[]', 'extension' => 'service_options', 'relation' => 'service_options'],
            'agitator_type_id' => ['type' => 'int', 'extension' => 'machine_agitator_extension', 'relation' => 'agitators'],
            'viscosity' => ['type' => 'int', 'extension' => 'machine_agitator_extension'],
            'airless_hose_connection' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'airless_hose_diameter' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'airless_hose_length' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'airless_nozzle_size' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'container_capacity' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'container_capacity_unit' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'container_mixing_performance' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'container_waterconnect_pressure' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'container_waterconnect_diameter' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'container_weight_empty' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'cutters_cutting_depth' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'cutters_cutting_length' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'cutters_rod_length' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'floor_beam_power_on_concrete' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'floor_dust_extraction_connection' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'floor_feedrate' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'floor_filter_connection' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'floor_rotations' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'floor_working_pressure' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'floor_working_width' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'grinder_grinding_plate' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'grinder_grinding_wheel' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'grinder_rotational_frequency' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'grinder_sanding' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'grinder_vacuum_connection' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'operating_pressure' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'pump_conveying_distance' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'pump_filling' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'pump_flow_volume' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'pump_grain_size' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'pump_material_container' => ['type' => 'string', 'extension' => 'machine_construction_equipment_extension'],
            'pump_pressure_height' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'waste_water_capacity' => ['type' => 'int', 'extension' => 'machine_construction_equipment_extension'],
            'pictures_delivery_set' => ['type' => 'media[]', 'extension' => 'machine_construction_equipment_extension'],
            'container_connection_port' => ['type' => 'string', 'language' => true, 'extension' => 'machine_construction_equipment_extension'],
            'container_conveying_wave' => ['type' => 'string', 'language' => true, 'extension' => 'machine_construction_equipment_extension'],
            'description_technical' => ['type' => 'html', 'language' => true, 'extension' => 'machine_construction_equipment_extension'],
            'delivery_set_basic' => ['type' => 'html', 'language' => true, 'extension' => 'machine_construction_equipment_extension'],
            'delivery_set_conversion' => ['type' => 'html', 'language' => true, 'extension' => 'machine_construction_equipment_extension'],
            'delivery_set_full' => ['type' => 'html', 'language' => true, 'extension' => 'machine_construction_equipment_extension'],
        ],
        'categories' => [
            'parent_category_id' => ['type' => 'int', 'relation' => 'categories'],
            'pic' => ['type' => 'media', 'seo' => 'image'],
            'pic_usage' => ['type' => 'media'],
            'priority' => ['type' => 'int'],
            'reference_ids' => ['type' => 'int[]'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true, 'seo' => 'title'],
            'teaser' => ['type' => 'string', 'language' => true, 'seo' => 'description'],
            'description' => ['type' => 'html', 'language' => true],
            'usage_area' => ['type' => 'string', 'language' => true],
            'pic_lang' => ['type' => 'media', 'language' => true, 'seo' => 'image'],
            'pdfs' => ['type' => 'media[]', 'language' => true],
            'faq' => ['type' => 'faq[]', 'language' => true],
        ],
        'features' => [
            'pic' => ['type' => 'media'],
            'priority' => ['type' => 'int'],
            'category_ids' => ['type' => 'int[]', 'relation' => 'categories'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'options' => [
            'pic' => ['type' => 'media'],
            'priority' => ['type' => 'int'],
            'category_ids' => ['type' => 'int[]', 'relation' => 'categories'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'certificates' => [
            'pic' => ['type' => 'media'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'equipment' => [
            'article_number' => ['type' => 'string'],
            'online_status' => ['type' => 'enum:online,offline'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
        ],
        'contacts' => [
            'name' => ['type' => 'string', 'required' => true],
            'picture' => ['type' => 'media'],
            'phone' => ['type' => 'string'],
            'email' => ['type' => 'string'],
        ],
        'used_machines' => [
            'manufacturer' => ['type' => 'string'],
            'name' => ['type' => 'string', 'required' => true],
            'offer_type' => ['type' => 'enum:rent,sale'],
            'availability' => ['type' => 'string'],
            'product_number' => ['type' => 'string'],
            'year_built' => ['type' => 'int'],
            'price' => ['type' => 'int'],
            'currency_code' => ['type' => 'string'],
            'vat' => ['type' => 'int'],
            'online_status' => ['type' => 'enum:online,offline'],
            'pics' => ['type' => 'media[]'],
            'location' => ['type' => 'string'],
            'external_url' => ['type' => 'string'],
            'teaser' => ['type' => 'string', 'language' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'equipment_groups' => [
            'picture' => ['type' => 'media'],
            'priority' => ['type' => 'int'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'agitators' => [
            'pic' => ['type' => 'media'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'service_options' => [
            'picture' => ['type' => 'media'],
            'online_status' => ['type' => 'enum:online,offline'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'industry_sectors' => [
            'icon' => ['type' => 'media'],
            'pic' => ['type' => 'media'],
            'online_status' => ['type' => 'enum:online,offline'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'teaser' => ['type' => 'string', 'language' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'production_lines' => [
            'line_code' => ['type' => 'string'],
            'industry_sector_ids' => ['type' => 'int[]', 'relation' => 'industry_sectors'],
            'pictures' => ['type' => 'media[]', 'seo' => 'image'],
            'link_picture' => ['type' => 'media'],
            'reference_ids' => ['type' => 'int[]'],
            'video_ids' => ['type' => 'int[]'],
            'online_status' => ['type' => 'enum:online,offline'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true, 'seo' => 'title'],
            'teaser' => ['type' => 'string', 'language' => true, 'seo' => 'description'],
            'description_short' => ['type' => 'html', 'language' => true],
            'description_long' => ['type' => 'html', 'language' => true],
            'faq' => ['type' => 'faq[]', 'language' => true],
        ],
        'supplies' => [
            'priority' => ['type' => 'int'],
            'pic' => ['type' => 'media'],
            'online_status' => ['type' => 'enum:online,offline'],
            'video_id' => ['type' => 'int', 'relation' => 'videos'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'description' => ['type' => 'html', 'language' => true],
        ],
        'usage_areas' => [
            'priority' => ['type' => 'int'],
            'category_ids' => ['type' => 'int[]', 'relation' => 'categories'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
        ],
    ];

    /**
     * @return array<int,string> Resource keys available with the current extension state
     */
    public static function getAvailableResources(): array
    {
        $resources = [];
        foreach (self::RESOURCES as $key => $meta) {
            if (null === $meta['extension'] || Extension::isActive($meta['extension'])) {
                $resources[] = $key;
            }
        }

        return $resources;
    }

    public static function hasResource(string $resource): bool
    {
        return in_array($resource, self::getAvailableResources(), true);
    }

    /**
     * @return array{class:class-string,id:string,extension:?string}
     */
    public static function getResourceMeta(string $resource): array
    {
        return self::RESOURCES[$resource];
    }

    /**
     * Fields available for a resource, honouring the current extension state.
     *
     * @return array<string,array{type:string,required?:bool,language?:bool,extension?:?string,relation?:string,seo?:string}>
     */
    public static function getFields(string $resource, bool $onlyActive = true): array
    {
        $fields = self::FIELDS[$resource] ?? [];
        if (!$onlyActive) {
            return $fields;
        }

        return array_filter($fields, static function (array $definition): bool {
            $extension = $definition['extension'] ?? null;
            return null === $extension || Extension::isActive($extension);
        });
    }

    public static function isLanguageField(string $resource, string $field): bool
    {
        return (bool) (self::FIELDS[$resource][$field]['language'] ?? false);
    }

    /**
     * Full machine-readable capability document for the discovery endpoint.
     *
     * @return array<string,mixed>
     */
    public static function describe(): array
    {
        $languages = [];
        foreach (rex_clang::getAll() as $clang) {
            $languages[] = [
                'id' => $clang->getId(),
                'code' => $clang->getCode(),
                'name' => $clang->getName(),
            ];
        }

        $resources = [];
        foreach (self::getAvailableResources() as $resource) {
            $fields = [];
            foreach (self::getFields($resource) as $name => $definition) {
                $fields[] = [
                    'name' => $name,
                    'type' => $definition['type'],
                    'required' => (bool) ($definition['required'] ?? false),
                    'language' => (bool) ($definition['language'] ?? false),
                    'relation' => $definition['relation'] ?? null,
                    'seo' => $definition['seo'] ?? null,
                ];
            }

            $resources[$resource] = [
                'id_field' => self::RESOURCES[$resource]['id'],
                'endpoints' => [
                    'list' => 'GET /api/d2u_machinery/' . $resource,
                    'get' => 'GET /api/d2u_machinery/' . $resource . '/{id}',
                    'create' => 'POST /api/d2u_machinery/' . $resource,
                    'update' => 'PATCH /api/d2u_machinery/' . $resource . '/{id}',
                    'delete' => 'DELETE /api/d2u_machinery/' . $resource . '/{id}',
                ],
                'fields' => $fields,
            ];
        }

        return [
            'addon' => 'd2u_machinery',
            'version' => (string) rex_addon::get('d2u_machinery')->getVersion(),
            'languages' => $languages,
            'active_extensions' => array_keys(array_filter(Extension::getStates())),
            'notes' => [
                'Language specific fields are provided per clang inside "translations".',
                'Upload images via the api addon endpoint POST /api/media first, then reference the returned file name.',
                'The "seo" attribute marks how a field is used for the frontend SEO meta data: "title" = meta/page title, "description" = meta description, "image" = source of the og:image (for media[] fields the first image is used).',
                'Only fields listed here may be written; unknown or inactive fields are rejected with HTTP 400.',
            ],
            'resources' => $resources,
        ];
    }
}
