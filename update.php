<?php
// Update language replacements
d2u_machinery_lang_helper::factory()->install();

// Update modules
$d2u_module_manager = new D2UModuleManager(D2UMachineryModules::getD2UMachineryModules(), "", "d2u_machinery");
$d2u_module_manager->autoupdate();