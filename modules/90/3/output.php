<?php

$headline = 'REX_VALUE[1]';
$text = 'REX_VALUE[id=2 output=html]';
$type = 'REX_VALUE[3]' !== '' ? 'REX_VALUE[3]' : 'machines'; /** @phpstan-ignore-line */
$box_per_line = 3 === (int) 'REX_VALUE[4]' ? 3 : 4; /** @phpstan-ignore-line */

if ('' !== $headline) { /** @phpstan-ignore-line */
    echo '<div class="col-12 abstand">';
    echo '<h1>REX_VALUE[1]</h1>';
    echo '</div>';
}

echo '<div class="col-12 abstand">';
echo '<div class="row">';
foreach (Category::getAll(rex_clang::getCurrentId()) as $category) {
    $article_id_link = 0;

    // Only use used or parent categories
    if ($category->isChild()) {
        // Skip parent categories
        continue;
    }
    if ('machines' === $type) { /** @phpstan-ignore-line */
        if (!$category->hasMachines(true)) {
            if ($category->hasChildren()) {
                // Show categories with child categories having machines
                foreach ($category->getChildren() as $child_category) {
                    if ($child_category->hasMachines(true)) {
                        // Skip categories without machines
                        break;
                    }
                }
            } else {
                // Skip categories without machines and without child categories
                continue;
            }
        }
        $article_id_link = (int) rex_config::get('d2u_machinery', 'article_id');
    } elseif (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
        $category->setOfferType('used_machines_rent' === $type ? 'rent' : 'sale'); /** @phpstan-ignore-line */
        if (!$category->hasUsedMachines(true)) {
            // Skip categories without used machines
            continue;
        }
        $article_id_link = (int) rex_config::get('d2u_machinery', 'used_machines_rent' === $type ? 'used_machine_article_id_rent' : 'used_machine_article_id_sale'); /** @phpstan-ignore-line */
    }

    echo '<div class="col-6 col-md-4'. (3 === $box_per_line ? '' : ' col-lg-3') .' mr-auto ml-auto abstand">'; /** @phpstan-ignore-line */
    echo '<a href="'. $category->getURL(false, $article_id_link) .'" class="bluebox">';
    echo '<div class="box same-height">';
    if ('' !== $category->pic || '' !== $category->pic_lang) {
        echo '<img src="'. rex_media_manager::getUrl('d2u_machinery_list_tile', '' !== $category->pic_lang ? $category->pic_lang : $category->pic) .'" alt="'. $category->name .'">';
    } else {
        echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
    }
    echo '<div>'. $category->name .'</div>';
    echo '</div>';
    echo '</a>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

if ('' !== $text) { /** @phpstan-ignore-line */
    echo '<div class="col-12 abstand">'. $text .'</div>';
    echo '<div class="col-12 abstand"></div>';
}
