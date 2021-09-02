<?php
$this->includeFile(__DIR__.'/install.php');

\rex_sql_table::get(
    \rex::getTable('d2u_machinery_features_lang'))
    ->removeColumn('title')
    ->ensure();