<?php

if (!function_exists('print_consulation_hint')) {
    /**
     * Prints consulation hint.
     */
    function print_consulation_hint(): void
    {
        $d2u_machinery = rex_addon::get('d2u_machinery');
        // Get placeholder wildcard tags
        $sprog = rex_addon::get('sprog');

        echo '<div class="col-12">';
        echo '<div class="consultation">';
        echo '<a href="'. rex_getUrl((int) $d2u_machinery->getConfig('consultation_article_id')) .'">';
        echo '<div class="row abstand">';

        echo '<div class="col-12 col-md-4 col-lg-3">';
        if ('' !== $d2u_machinery->getConfig('consultation_pic')) {
            echo '<img src="'. rex_url::media((string) $d2u_machinery->getConfig('consultation_pic')) .'" alt="">';
        }
        echo '</div>';

        echo '<div class="col-12 col-md-8 col-lg-9">';
        echo '<p>'. $sprog->getConfig('wildcard_open_tag') .'d2u_machinery_consultation_hint'. $sprog->getConfig('wildcard_close_tag') .'</p>';
        if ($d2u_machinery->hasConfig('contact_phone') && '' !== $d2u_machinery->getConfig('contact_phone')) {
            echo '<h3>'. $sprog->getConfig('wildcard_open_tag') .'d2u_helper_module_form_phone'. $sprog->getConfig('wildcard_close_tag') .' '. $d2u_machinery->getConfig('contact_phone') .'</h3>';
        }
        echo '</div>';

        echo '</div>';
        echo '</a>';
        echo '</div>';
        echo '</div>';
    }
}

print_consulation_hint();
