<?php
	print '<div class="col-xs-12 abstand">';
	print '<h1>REX_VALUE[1]</h1>';
	print '</div>';

	$categories = array();
	foreach (Category::getAll(rex_clang::getCurrentId()) as $category) {
		// Only use used parent categories
		if(!$category->isChild() && count($category->getMachines()) > 0) {
			print '<div class="col-xs-6 col-sm-4 col-md-3 abstand">';
			print '<a href="'. $category->getURL() .'">';
			print '<div class="box">';
			print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
				$category->pic .'" alt='. $category->name .' style="max-width:100%;">';
			print '<div>'. $category->name .'</div>';
			print '</div>';
			print '</a>';
			print '</div>';
		}
	}
?>
<div class="col-xs-10 abstand">REX_VALUE[id=2 output=html]</div>
<div class="col-xs-12 abstand"></div>