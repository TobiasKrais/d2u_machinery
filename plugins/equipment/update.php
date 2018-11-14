<?php
// Update language replacements
if(!class_exists('d2u_machinery_equipment_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_machinery_equipment_lang_helper.php';
}
d2u_machinery_equipment_lang_helper::factory()->install();