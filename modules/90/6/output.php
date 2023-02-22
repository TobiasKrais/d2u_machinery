<?php

if (!function_exists('print_used_machines_top_offers')) {
    /**
     * Prints machines.
     * @param UsedMachine[] $used_machines used Machines
     */
    function print_used_machines_top_offers($used_machines): void
    {
        $d2u_machinery = rex_addon::get('d2u_machinery');
        $number_offers_row = 'REX_VALUE[1]' == '' ? 4 : (int) 'REX_VALUE[1]'; /** @phpstan-ignore-line */
        $counter = 0;
        foreach ($used_machines as $used_machine) {
            if ('online' !== $used_machine->online_status) {
                continue;
            }
            echo '<div class="col-sm-6 col-md-4'. (4 === $number_offers_row ? ' col-lg-3' : '') .' abstand">'; /** @phpstan-ignore-line */
            echo '<a href="'. $used_machine->getURL(false) .'">';
            echo '<div class="box" data-height-watch>';
            if (count($used_machine->pics) > 0 && '' !== $used_machine->pics[0]) {
                echo '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', $used_machine->pics[0])	 .'" alt="'. $used_machine->name .'">';
            }
            echo '<div><b>'. $used_machine->manufacturer .' '. $used_machine->name .'</b></div>';
            if ('show' === $d2u_machinery->getConfig('show_teaser', 'hide')) {
                echo '<div class="teaser">'. nl2br($used_machine->teaser) .'</div>';
            }
            echo '</div>';
            echo '</a>';
            echo '</div>';
            ++$counter;

            // show only one row
            if ($counter === $number_offers_row) { /** @phpstan-ignore-line */
                break;
            }
        }
    }
}

$d2u_machinery = rex_addon::get('d2u_machinery');
$picture_type = $d2u_machinery->getConfig('used_machines_pic_type', 'slider');
// Get placeholder wildcard tags
$sprog = rex_addon::get('sprog');
$tag_open = $sprog->getConfig('wildcard_open_tag');
$tag_close = $sprog->getConfig('wildcard_close_tag');

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

$offer_type = 'REX_VALUE[2]'; /** @phpstan-ignore-line */

$used_machines = UsedMachine::getAll(rex_clang::getCurrentId(), true, $offer_type);
shuffle($used_machines);

echo '<div class="col-12">';
echo '<div class="row" data-match-height>';
print_used_machines_top_offers($used_machines);
echo '</div>';
echo '</div>';
