<?php

$sql = \rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_service_options');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_service_options_lang');

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('service_option_ids')
    ->ensure();
