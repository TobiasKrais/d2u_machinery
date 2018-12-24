<?php
	print '<div class="col-sm-12 abstand">';
	print '<h1>REX_VALUE[1]</h1>';
	print '</div>';

	print '<div class="col-sm-12 abstand">';
	print '<div class="row">';
	foreach (Category::getAll(rex_clang::getCurrentId()) as $category) {
		// Only use used parent categories
		if(!$category->isChild() && count($category->getMachines()) > 0) {
			print '<div class="col-sm-6 col-md-4 col-lg-3 abstand">';
			print '<a href="'. $category->getURL() .'" class="bluebox">';
			print '<div class="box same-height">';
			if($category->pic != "" || $category->pic_lang != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
					($category->pic_lang != "" ? $category->pic_lang : $category->pic) .'" alt="'. $category->name .'">';
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

$text = "REX_VALUE[id=2 output=html]";
if($text != '') {
	print '<div class="col-sm-12 abstand">'. $text .'</div>';
	print '<div class="col-sm-12 abstand"></div>';
}
?>