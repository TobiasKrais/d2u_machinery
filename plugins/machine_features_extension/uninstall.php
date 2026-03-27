<?php

$sql = \rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_features');
$sql->setQuery('DROP TABLE IF EXISTS ' . \rex::getTablePrefix() . 'd2u_machinery_features_lang');

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_machines'))
    ->removeColumn('feature_ids')
    ->ensure();
