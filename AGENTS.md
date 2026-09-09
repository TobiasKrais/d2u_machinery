# D2U Machinery - Agent Notes

Rules only. Short. Actionable.

## Core Rules

- Namespace: `TobiasKrais\D2UMachinery`
- PHP classes: 4 spaces. Module files: tabs
- Comments only in English
- Frontend labels via `Sprog\Wildcard::get()`, backend labels via `rex_i18n::msg()` with keys from `lang/`

## When Changing

- Keep backend translation keys in sync across all files under `lang/`
- Extensions are managed via `rex_config` state (`extension_*` keys) through `lib/Extension.php`, not as REDAXO plugins. There is no `plugins/` directory anymore (removed in 2.0.0). Extension install/uninstall runs the main `install.php`/`uninstall.php` with `$d2uMachineryAction` set to the extension key.
- For changes under `modules/90/*`: check or update changelog in `pages/help.changelog.php`
- Raise revision in `lib/Module.php` only once per release
- If target version in changelog already has `-DEV`: do not raise again in same phase
- Use real umlauts in changelog files, AGENTS.md, and README.md

## Maintenance

- Keep only recurring pitfalls, fixed conventions, and agent-relevant workflows here
