<?php

if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_machinery[used_machines]', rex_i18n::msg('d2u_machinery_rights_all'));
}