<?php
$headline = 'REX_VALUE[1]';
$text = "REX_VALUE[id=2 output=html]";
$type = 'REX_VALUE[3]' ?: 'machines';
$box_per_line = "REX_VALUE[4]" == 3 ? 3 : 4;

if($headline) {
	print '<div class="col-12 abstand">';
	print '<h1>REX_VALUE[1]</h1>';
	print '</div>';
}

print '<div class="col-12 abstand">';
print '<div class="row">';
foreach (Category::getAll(rex_clang::getCurrentId()) as $category) {
	// Only use used or parent categories
	if(!$category->isChild() && (($type == 'machines' && count($category->getMachines(true)) > 0) || ($type == 'used_machines' && rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable() && count($category->getUsedMachines(true)) > 0))) {
		print '<div class="col-6 col-md-4 col-md-4 col-lg-'. ($box_per_line == 4 ? '3' : '4') .' abstand">';
		print '<a href="'. $category->getURL() .'" class="bluebox">';
		print '<div class="box same-height">';
		if($category->pic != "" || $category->pic_lang != "") {
			print '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', ($category->pic_lang != "" ? $category->pic_lang : $category->pic)) .'" alt="'. $category->name .'">';
		}
		else {
			print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
		}
		print '<div>'. $category->name .'</div>';
		print '</div>';
		print '</a>';
		print '</div>';
	}
}
print '</div>';
print '</div>';

if($text) {
	print '<div class="col-12 abstand">'. $text .'</div>';
	print '<div class="col-12 abstand"></div>';
}