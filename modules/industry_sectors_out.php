<?php
	print '<div class="col-xs-12 abstand">';
	print '<h1>REX_VALUE[1]</h1>';
	print '</div>';

	print '<div data-match-height>';
	foreach (IndustrySector::getAll(rex_clang::getCurrentId()) as $industry_sector) {
		if($industry_sector->online_status == "online" && count($industry_sector->getMachines()) > 0) {
			print '<div class="col-xs-6 col-sm-4 col-md-3 abstand">';
			print '<a href="'. $industry_sector->getURL() .'" class="bluebox">';
			print '<div class="box" data-height-watch>';
			if($industry_sector->pic != "") {
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
?>
<div class="col-xs-10 abstand">REX_VALUE[id=2 output=html]</div>
<div class="col-xs-12 abstand"></div>