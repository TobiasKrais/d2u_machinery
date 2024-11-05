<?php
    $text_1 = 'REX_VALUE[id=1 output="html"]';

    echo '<div class="col-sm-12 abstand">';
    echo '<h1>REX_VALUE[1]</h1>';
    echo '</div>';

    echo '<div class="col-sm-12 abstand">';
    echo '<div class="row">';
    foreach (IndustrySector::getAll(rex_clang::getCurrentId()) as $industry_sector) {
        if ('online' === $industry_sector->online_status && count($industry_sector->getMachines()) > 0) {
            echo '<div class="col-sm-6 col-md-4 col-lg-3 abstand">';
            echo '<a href="'. $industry_sector->getUrl() .'" class="bluebox">';
            echo '<div class="box same-height">';
            if ('' !== $industry_sector->pic) {
                echo '<img src="index.php?rex_media_type=d2u_machinery_list_tile&rex_media_file='.
                    $industry_sector->pic .'" alt="'. $industry_sector->name .'">';
            } else {
                echo '<img src="'.	rex_addon::get('d2u_machinery')->getAssetsUrl('white_tile.gif') .'" alt="Placeholder">';
            }
            echo '<div>'. $industry_sector->name .'</div>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
    }

    echo '</div>';
    echo '</div>';
?>
<div class="col-sm-12 abstand"><?php TobiasKrais\D2UHelper\FrontendHelper::prepareEditorField($text_1) ?></div>
<div class="col-sm-12 abstand"></div>