# D2U Machinery - Redaxo Addon

A comprehensive Redaxo 5 CMS addon for managing industrial machinery catalogs. Includes machines, categories, technical data, videos, references, export, industry sectors, production lines, used machines, and various machine-specific extensions directly in the main addon.

## Tech Stack

- **Language:** PHP >= 8.0
- **CMS:** Redaxo >= 5.10.0
- **Frontend Framework:** Bootstrap 4/5 (via d2u_helper templates)
- **Namespaces:** `TobiasKrais\D2UMachinery` (FrontendHelper, Module), `D2U_Machinery` (Contact), global (Machine, Category)

## Project Structure

```text
d2u_machinery/
├── boot.php               # Addon bootstrap (extension points, permissions, URL shortening)
├── install.php             # Installation (database tables, views, media manager types, URL profiles)
├── update.php              # Update (calls install.php)
├── uninstall.php           # Cleanup (tables, views, media manager types, URL profiles)
├── package.yml             # Addon configuration, version, dependencies
├── README.md
├── assets/                 # Fonts, placeholder images
├── lang/                   # Backend translations (de_de, en_gb)
├── lib/                    # PHP classes
│   ├── machine.php         # Machine model (main entity)
│   ├── category.php        # Category model (hierarchical)
│   ├── FrontendHelper.php  # Frontend utilities (alternate URLs, breadcrumbs)
│   ├── d2u_machinery_lang_helper.php  # Sprog wildcard provider (7 languages)
│   ├── d2u_machinery_module_manager.php  # Module definitions and revisions
│   └── deprecated_helper_classes.php
├── modules/                # 6 module variants in group 90
│   └── 90/
│       ├── 1/              # Hauptausgabe (main output)
│       ├── 2/              # Branchen (industry sectors)
│       ├── 3/              # Kategorien (categories)
│       ├── 4/              # Gebrauchtmaschinen (used machines)
│       ├── 5/              # Box Beratungshinweis (consultation box)
│       └── 6/              # Gebrauchtmaschinen Topangebote (top offers)
├── pages/                  # Backend pages
│   ├── index.php           # Page router
│   ├── machine.php         # Machine management
│   ├── category.php        # Category management
│   ├── settings.settings.php  # Addon settings
│   ├── settings.setup.php     # Module manager
│   ├── help.readme.php        # Help/README page
│   └── help.changelog.php     # Changelog
└── plugins/                # Legacy compatibility stubs
    └── */package.yml       # package.yml only, kept so rex_plugin::get(...) still works for older integrations
```

## Coding Conventions

- **Namespaces:** `TobiasKrais\D2UMachinery` (FrontendHelper, Module), `D2U_Machinery` (Contact), global (Machine, Category)
- **Deprecated:** `d2u_machinery_frontend_helper` alias since 1.5.0
- **Naming:** camelCase for variables, PascalCase for classes
- **Indentation:** 4 spaces in PHP classes, tabs in module files
- **Comments:** English comments only
- **Frontend labels:** Use `Sprog\Wildcard::get()` backed by lang helper, not `rex_i18n::msg()`
- **Backend labels:** Use `rex_i18n::msg()` with keys from `lang/` files

## AGENTS.md Maintenance

- When new project insights are gained during work and they are relevant to agent guidance, workflows, conventions, architecture, or known pitfalls, update this AGENTS.md accordingly.

## Key Classes

| Class | Description |
| ----- | ----------- |
| `Machine` | Machine model: name, pictures, category, technical data, videos, references, online status. Implements `ITranslationHelper` |
| `Category` | Category model: hierarchical with parent, picture, usage pictures, videos, references. Implements `ITranslationHelper` |
| `FrontendHelper` | Frontend utilities: alternate URLs, breadcrumbs for machines/categories/used machines/industry sectors/production lines |
| `d2u_machinery_lang_helper` | Sprog wildcard provider for 7 languages (DE, EN, FR, NL, ES, RU, ZH) |
| `Module` | Module definitions and revision numbers for 6 modules |

## Database Tables

| Table | Description |
| ----- | ----------- |
| `rex_d2u_machinery_machines` | Machines (language-independent): pictures, category, product number, technical data, video IDs, online status |
| `rex_d2u_machinery_machines_lang` | Machines (language-specific): name, teaser, description, benefits, leaflets, PDFs |
| `rex_d2u_machinery_categories` | Categories (language-independent): parent category, picture, usage picture, video/reference IDs |
| `rex_d2u_machinery_categories_lang` | Categories (language-specific): name, teaser, description, usage area, PDFs |

### Database Views (for URL addon)

- `rex_d2u_machinery_url_machines` — Machine URLs for URL addon
- `rex_d2u_machinery_url_machine_categories` — Category URLs for URL addon

## Architecture

### Extension Points

| Extension Point | Location | Purpose |
| --------------- | -------- | ------- |
| `D2U_HELPER_TRANSLATION_LIST` | boot.php (backend) | Registers addon in D2U Helper translation manager |
| `ART_PRE_DELETED` | boot.php (backend) | Prevents deletion of articles used by the addon |
| `CLANG_DELETED` | boot.php (backend) | Cleans up language-specific data |
| `MEDIA_IS_IN_USE` | boot.php (backend) | Prevents deletion of media files in use |
| `D2U_HELPER_ALTERNATE_URLS` | boot.php (frontend) | Provides alternate URLs for machines, categories, etc. |
| `D2U_HELPER_BREADCRUMBS` | boot.php (frontend) | Provides breadcrumb segments |
| `YREWRITE_SITEMAP` | boot.php (frontend) | Video sitemap entries (when d2u_videos >= 1.1 available) |
| `URL_PRE_SAVE` | boot.php | URL shortening (removes article name from URL) |

### Modules

6 module variants in group 90:

| Module | Name | Requires Plugin |
| ------ | ---- | --------------- |
| 90-1 | Hauptausgabe | — |
| 90-2 | Branchen | `industry_sectors` |
| 90-3 | Kategorien | — |
| 90-4 | Gebrauchtmaschinen | `used_machines` |
| 90-5 | Box Beratungshinweis | — |
| 90-6 | Gebrauchtmaschinen Topangebote | `used_machines` |

#### Module Versioning

Each module has a revision number defined in `lib/d2u_machinery_module_manager.php` inside the `getModules()` method. When a module is changed:

1. Add a changelog entry in `pages/help.changelog.php` describing the change.
2. Increment the module's revision number by one.

**Important:** The revision only needs to be incremented **once per release**, not per commit. Check the changelog: if the version number is followed by `-DEV`, the release is still in development and no additional revision bump is needed.

### Integrated Features

| Feature | Description | Key Classes |
| ------ | ----------- | ----------- |
| `contacts` | Contact persons for machines | `Contact` |
| `equipment` | Equipment and equipment groups | `Equipment`, `EquipmentGroup` |
| `export` | Export to marketplaces (EuropeMachinery, MachineryPark, Mascus, LinkedIn). CronJob-based FTP/ZIP export | `AExport`, `AFTPExport`, `EuropeMachinery`, `MachineryPark`, `Mascus`, `SocialExportLinkedIn`, `Provider`, `ExportedUsedMachine` |
| `industry_sectors` | Industry sector assignments | `IndustrySector` |
| `machine_agitator_extension` | Agitator types and agitators | `Agitator`, `AgitatorType` |
| `machine_certificates_extension` | Machine certificates | `Certificate` |
| `machine_construction_equipment_extension` | Construction equipment extension fields | LangHelper only |
| `machine_features_extension` | Machine features/characteristics | `Feature` |
| `machine_options_extension` | Machine options | `Option` |
| `machine_steel_processing_extension` | Steel processing (8 sub-models) | `Supply`, `Automation`, `Material`, `Procedure`, `Process`, `Profile`, `Tool`, `Welding` |
| `machine_usage_area_extension` | Machine usage areas | `UsageArea` |
| `production_lines` | Production lines and USPs | `ProductionLine`, `USP` |
| `service_options` | Service options | `ServiceOption` |
| `used_machines` | Used machine management | `UsedMachine` |

## Legacy Plugin Stubs

- Plugin directories under `plugins/` must contain only `package.yml` files.
- These stubs exist solely so `rex_plugin::get('d2u_machinery', ...)` remains available for backward compatibility.
- No PHP logic, pages, libs, install, update or uninstall files should remain inside plugin directories.

### Media Manager Types

| Type | Purpose |
| ---- | ------- |
| `d2u_machinery_list_tile` | List tile images (768×768, resize + workspace) |
| `d2u_machinery_features` | Feature/equipment images (768×768, resize) |

### YForm Email Templates

- `d2u_machinery_machine_request` — Machine inquiry form email template

## Settings

Managed via `pages/settings.settings.php` and stored in `rex_config`:

- `default_category_sort` / `default_machine_sort` — Sort by name or priority
- `show_teaser` — Show/hide teaser text
- `show_techdata` — Show/hide technical data
- `lang_wildcard_overwrite` — Preserve custom Sprog translations
- `lang_replacement_{clang_id}` — Language mapping per REDAXO language

## Dependencies

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `d2u_helper` | >= 1.14.0 | Backend/frontend helpers, module manager, translation interface |
| `media_manager` | >= 2.3 | Image processing |
| `sprog` | >= 1.0.0 | Frontend translation wildcards |
| `url` | >= 2.0 | SEO-friendly URLs |
| `yform` | >= 4.0 | Form handling (inquiry forms) |
| `yrewrite` | >= 2.0.1 | URL rewriting |

### Optional Dependencies

| Package | Purpose |
| ------- | ------- |
| `d2u_videos` >= 1.1 | Video integration, video sitemap (conflicts with < 1.2.1) |

## Multi-language Support

- **Backend:** de_de, en_gb
- **Frontend (Sprog Wildcards):** DE, EN, FR, NL, ES, RU, ZH (7 languages)

## Versioning

This addon follows [Semantic Versioning](https://semver.org/):

- **Major** (1st digit): Breaking changes (e.g. removed classes, renamed methods, incompatible DB changes)
- **Minor** (2nd digit): New features, new modules, new database fields (backward compatible)
- **Patch** (3rd digit): Bug fixes, small improvements (backward compatible)

The version number is maintained in `package.yml`. During development, the changelog uses a `-DEV` suffix.

## Changelog

The changelog is located in `pages/help.changelog.php`.
