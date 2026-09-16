<?php

namespace TobiasKrais\D2UMachinery;

/**
 * Reusable FAQ field for the d2u_machinery backend and a decoder for the frontend.
 *
 * FAQ entries are stored as base64(JSON [{q,a,tags[]}]) in a single (language
 * specific) column. Base64 avoids REDAXO value escaping issues. The backend widget
 * (see assets/faq_input.js / faq_input.css) renders a repeater with question,
 * answer and Tagify-based tags and serialises everything into a hidden textarea.
 */
class FaqField
{
    /** @var bool Whether the shared assets have already been emitted on this page. */
    private static bool $assetsRendered = false;

    /**
     * Render the backend FAQ repeater field.
     * @param string $inputName Name attribute of the hidden textarea (e.g. form[lang][2][faq])
     * @param string $value Stored value (base64 JSON) to prefill
     * @param bool $readonly Render read-only (no editing)
     * @return string HTML
     */
    public static function render(string $inputName, string $value, bool $readonly = false): string
    {
        $out = '';
        if (!self::$assetsRendered) {
            self::$assetsRendered = true;
            $out .= '<link rel="stylesheet" href="'. \rex_escape(\rex_url::addonAssets('d2u_machinery', 'vendor/tagify/tagify.css')) .'">';
            $out .= '<link rel="stylesheet" href="'. \rex_escape(\rex_url::addonAssets('d2u_machinery', 'faq_input.css')) .'">';
            $out .= '<script src="'. \rex_escape(\rex_url::addonAssets('d2u_machinery', 'vendor/tagify/tagify.js')) .'"></script>';
            $out .= '<script src="'. \rex_escape(\rex_url::addonAssets('d2u_machinery', 'faq_input.js')) .'"></script>';
        }

        $disabled = $readonly ? ' disabled' : '';

        $out .= '<div class="d2u-faq-widget">';
            $out .= '<div class="d2u-faq-repeater"></div>';
            $out .= '<button type="button" class="btn btn-primary d2u-faq-add"'. $disabled .'>'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_add')) .'</button>';
            $out .= '<textarea name="'. \rex_escape($inputName, 'html_attr') .'" class="d2u-faq-data" hidden'. ($readonly ? ' readonly' : '') .'>'. \rex_escape($value) .'</textarea>';
            $out .= '<template class="d2u-faq-row-tpl">';
                $out .= '<div class="d2u-faq-row">';
                    $out .= '<div class="row d2u-faq-row-head">';
                        $out .= '<div class="col-xs-8"><b>'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_entry')) .'</b></div>';
                        $out .= '<div class="col-xs-4 text-right">';
                            $out .= '<button type="button" class="btn btn-xs btn-default d2u-faq-up" title="'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_move_up'), 'html_attr') .'">&#9650;</button> ';
                            $out .= '<button type="button" class="btn btn-xs btn-default d2u-faq-down" title="'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_move_down'), 'html_attr') .'">&#9660;</button> ';
                            $out .= '<button type="button" class="btn btn-xs btn-delete d2u-faq-remove" title="'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_remove'), 'html_attr') .'">&times;</button>';
                        $out .= '</div>';
                    $out .= '</div>';
                    $out .= '<div class="row"><div class="col-xs-4">'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_question')) .':</div><div class="col-xs-8"><input type="text" class="form-control d2u-faq-q-input"'. $disabled .'/></div></div>';
                    $out .= '<div class="row"><div class="col-xs-4">'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_answer')) .':</div><div class="col-xs-8"><textarea rows="3" class="form-control d2u-faq-a-input"'. $disabled .'></textarea></div></div>';
                    $out .= '<div class="row"><div class="col-xs-4">'. \rex_escape(\rex_i18n::msg('d2u_machinery_faq_tags')) .':</div><div class="col-xs-8"><input type="text" class="form-control d2u-faq-tags-input"'. $disabled .'/></div></div>';
                $out .= '</div>';
            $out .= '</template>';
        $out .= '</div>';

        return $out;
    }

    /**
     * Decode a stored FAQ value into a normalized list of items (question required).
     * @param string $value Stored value (base64 JSON, or raw JSON as fallback)
     * @return array<int,array{q:string,a:string,tags:array<int,string>}>
     */
    public static function decode(string $value): array
    {
        $value = trim($value);
        if ('' === $value) {
            return [];
        }
        $json = base64_decode($value, true);
        if (false === $json) {
            $json = $value;
        }
        $items = json_decode((string) $json, true);
        if (!is_array($items)) {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $question = trim((string) ($item['q'] ?? ''));
            if ('' === $question) {
                continue;
            }
            $tags = [];
            foreach ((array) ($item['tags'] ?? []) as $tag) {
                $tag = trim((string) $tag);
                if ('' !== $tag && !in_array($tag, $tags, true)) {
                    $tags[] = $tag;
                }
            }
            $result[] = [
                'q' => $question,
                'a' => trim((string) ($item['a'] ?? '')),
                'tags' => $tags,
            ];
        }

        return $result;
    }

    /**
     * Render the frontend FAQ block (accordion + tag filter + schema.org FAQPage).
     * Uses the same markup/classes as the Kaltenbach FAQ module so the existing
     * theme CSS and tag-filter JS apply automatically.
     * @param array<int,array{q:string,a:string,tags:array<int,string>}> $items Decoded FAQ items
     * @param string $idPrefix Unique id prefix for the item anchors (e.g. faq-machine-12)
     * @param string $heading Optional heading shown above the FAQ list
     * @param string $extraInnerHtml Optional extra HTML rendered inside .faq-inner after the list (e.g. a CTA block)
     * @return string HTML (empty string when there are no items)
     */
    public static function renderFrontend(array $items, string $idPrefix, string $heading = '', string $extraInnerHtml = ''): string
    {
        $items = array_values(array_filter($items, static function ($item): bool {
            return is_array($item) && '' !== trim((string) ($item['q'] ?? ''));
        }));
        if (0 === count($items)) {
            return '';
        }

        // Collect tags in first-seen order for the filter bar.
        $tags = [];
        foreach ($items as $item) {
            foreach ((array) ($item['tags'] ?? []) as $tag) {
                $tag = trim((string) $tag);
                if ('' !== $tag && !in_array($tag, $tags, true)) {
                    $tags[] = $tag;
                }
            }
        }

        $out = '<div class="container-fluid faq-module py-5" data-faq>';
        $out .= '<div class="container faq-inner">';
        if ('' !== $heading) {
            $out .= '<h2 class="faq-heading mb-4">'. \rex_escape($heading) .'</h2>';
        }
        if (count($tags) > 0) {
            $out .= '<div class="faq-tags mb-4" data-faq-tags>';
            foreach ($tags as $tag) {
                $out .= '<button type="button" class="faq-tag" aria-pressed="false" data-faq-tag="'. \rex_escape($tag, 'html_attr') .'">'. \rex_escape($tag) .'</button>';
            }
            $out .= '</div>';
        }
        $out .= '<div class="faq-list" data-faq-list>';
        foreach ($items as $index => $item) {
            $question = trim((string) $item['q']);
            $answer = trim((string) ($item['a'] ?? ''));
            $itemTags = array_values(array_filter(
                array_map(static function ($t): string { return trim((string) $t); }, (array) ($item['tags'] ?? [])),
                static function ($t): bool { return '' !== $t; }
            ));
            $anchorId = \rex_escape($idPrefix, 'html_attr') .'-'. $index;

            $out .= '<details class="faq-item" data-faq-item data-faq-item-tags="'. \rex_escape((string) json_encode($itemTags), 'html_attr') .'">';
            $out .= '<summary class="faq-q" id="'. $anchorId .'">';
            $out .= '<span class="faq-chevron" aria-hidden="true"></span>';
            $out .= '<span class="faq-q-text">'. \rex_escape($question) .'</span>';
            $out .= '</summary>';
            $out .= '<div class="faq-a"><div class="faq-a-inner">'. nl2br(\rex_escape($answer)) .'</div></div>';
            $out .= '</details>';
        }
        $out .= '</div>'; // .faq-list
        $out .= $extraInnerHtml;
        $out .= '</div>'; // .faq-inner
        $out .= '</div>'; // .faq-module

        // SEO: schema.org FAQPage (always all entries, independent of the filter).
        $ld = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => []];
        foreach ($items as $item) {
            $ld['mainEntity'][] = [
                '@type' => 'Question',
                'name' => trim((string) $item['q']),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags((string) ($item['a'] ?? ''))),
                ],
            ];
        }
        $out .= '<script type="application/ld+json" nonce="'. \rex_response::getNonce() .'">'. json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .'</script>';

        return $out;
    }
}
