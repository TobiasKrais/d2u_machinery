<?php

if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_machinery[]', rex_i18n::msg('d2u_machinery_rights_all'));
	rex_perm::register('d2u_machinery[edit_lang]', rex_i18n::msg('d2u_machinery_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[edit_tech_data]', rex_i18n::msg('d2u_machinery_rights_edit_tech_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[settings]', rex_i18n::msg('d2u_machinery_rights_settings'));
}