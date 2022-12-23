<?php
if(!function_exists('print_used_machines')) {
	/**
	 * Prints machines.
	 * @param UsedMachine[] $used_machines Used Machines.
	 */
	function print_used_machines_top_offers($used_machines):void {
		$d2u_machinery = rex_addon::get("d2u_machinery");
		$number_offers_row = "REX_VALUE[1]" == "" ? 4 : intval("REX_VALUE[1]"); /** @phpstan-ignore-line */
		$counter = 0;
		foreach($used_machines as $used_machine) {
			if($used_machine->online_status !== 'online') {
				continue;
			}
			print '<div class="col-sm-6 col-md-4'. ($number_offers_row === 4 ? ' col-lg-3' : '') .' abstand">'; /** @phpstan-ignore-line */
			print '<a href="'. $used_machine->getURL(false) .'">';
			print '<div class="box" data-height-watch>';
			if(count($used_machine->pics) > 0 && $used_machine->pics[0] !== "") {
				print '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', $used_machine->pics[0])	 .'" alt="'. $used_machine->name .'">';
			}
			print '<div><b>'. $used_machine->manufacturer .' '. $used_machine->name .'</b></div>';
			if($d2u_machinery->getConfig('show_teaser', 'hide') === 'show') {
				print '<div class="teaser">'. nl2br($used_machine->teaser) .'</div>';
			}
			print '</div>';
			print '</a>';
			print '</div>';
			$counter++;
			
			// show only one row
			if($counter === $number_offers_row) { /** @phpstan-ignore-line */
				break;
			}
		}
	}
}

$d2u_machinery = rex_addon::get("d2u_machinery");
$picture_type = $d2u_machinery->getConfig('used_machines_pic_type', 'slider');
// Get placeholder wildcard tags
$sprog = rex_addon::get("sprog");
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();


$used_machines = UsedMachine::getAll(rex_clang::getCurrentId(), true);
shuffle($used_machines);

print '<div class="col-12">';
print '<div class="row" data-match-height>';
print_used_machines_top_offers($used_machines);
print '</div>';
print '</div>';