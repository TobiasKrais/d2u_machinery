<?php
$german = array();
$english = array();

$sql = rex_sql::factory();

$sql->setQuery("INSERT INTO ". rex::getTablePrefix() ."sprog_wildcard (`id`, `clang_id`, `wildcard`, `replace`, `createuser`, `createdate`) VALUES
(1, 1, 'd2u_machinery_our_products', 'Unsere Produkte', 'd2u_machinery', '". date("Y-m-d H:i:s") ."'),
(1, 2, 'd2u_machinery_our_products', 'Our Products', 'd2u_machinery', '". date("Y-m-d H:i:s") ."',);");