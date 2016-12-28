<?php
	print '<div class="col-xs-12 abstand">';
	print '<h1>REX_VALUE[1]</h1>';
	print '</div>';

	print '<div data-match-height>';
	$categories = array();
	foreach (Category::getAll(rex_clang::getCurrentId()) as $category) {
		// Only use used parent categories
		if(!$category->isChild() && count($category->getMachines()) > 0) {
			print '<div class="col-xs-6 col-sm-4 col-md-3 abstand" data-height-watch>';
			print '<a href="'. $category->getURL() .'" class="bluebox">';
			print '<div class="box" data-height-watch>';
			if($category->pic != "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
					$category->pic .'" alt="'. $category->name .'">';
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
?>
<div class="col-xs-10 abstand">REX_VALUE[id=2 output=html]</div>
<div class="col-xs-12 abstand"></div>