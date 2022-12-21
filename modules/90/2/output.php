<?php
	$text_1 = 'REX_VALUE[id=1 output="html"]';

	print '<div class="col-sm-12 abstand">';
	print '<h1>REX_VALUE[1]</h1>';
	print '</div>';

	print '<div class="col-sm-12 abstand">';
	print '<div class="row">';
	foreach (IndustrySector::getAll(rex_clang::getCurrentId()) as $industry_sector) {
		if($industry_sector->online_status === "online" && count($industry_sector->getMachines()) > 0) {
			print '<div class="col-sm-6 col-md-4 col-lg-3 abstand">';
			print '<a href="'. $industry_sector->getURL() .'" class="bluebox">';
			print '<div class="box same-height">';
			if($industry_sector->pic !== "") {
				print '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
					$industry_sector->pic .'" alt="'. $industry_sector->name .'">';
			}
			else {
				print '<img src="'.	rex_addon::get("d2u_machinery")->getAssetsUrl("white_tile.gif") .'" alt="Placeholder">';
			}
			print '<div>'. $industry_sector->name .'</div>';
			print '</div>';
			print '</a>';
			print '</div>';
		}
	}

	print '</div>';
	print '</div>';
?>
<div class="col-sm-12 abstand"><?php d2u_addon_frontend_helper::prepareEditorField($text_1); ?></div>
<div class="col-sm-12 abstand"></div>