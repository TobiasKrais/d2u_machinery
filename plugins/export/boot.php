<?php

if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_machinery[export]', rex_i18n::msg('d2u_machinery_export_rights_export'), rex_perm::OPTIONS);
	rex_perm::register('d2u_machinery[export_provider]', rex_i18n::msg('d2u_machinery_export_rights_export_provider'));	
}