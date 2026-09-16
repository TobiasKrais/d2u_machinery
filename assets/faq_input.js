/**
 * d2u_machinery FAQ backend widget.
 *
 * A repeater (question / answer / tags) whose entries are serialised as
 * base64(JSON [{q,a,tags[]}]) into a single hidden textarea. Base64 avoids any
 * REDAXO value escaping issues. Supports MULTIPLE instances per page (one FAQ
 * field per language tab). Markup + labels are rendered by PHP; this script only
 * wires the behaviour and (if available) Tagify for the tag inputs.
 */
(function () {
    'use strict';

    function b64encode(str) {
        var bytes = new TextEncoder().encode(str);
        var bin = '';
        for (var i = 0; i < bytes.length; i++) {
            bin += String.fromCharCode(bytes[i]);
        }
        return btoa(bin);
    }

    function b64decode(b64) {
        var bin = atob(b64);
        var bytes = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) {
            bytes[i] = bin.charCodeAt(i);
        }
        return new TextDecoder().decode(bytes);
    }

    function parseData(raw) {
        raw = (raw || '').trim();
        if (raw === '') {
            return [];
        }
        var text = raw;
        if (/^[A-Za-z0-9+/=\s]+$/.test(raw)) {
            try {
                text = b64decode(raw.replace(/\s/g, ''));
            } catch (e) {
                text = raw;
            }
        }
        try {
            var parsed = JSON.parse(text);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function initWidget(widget) {
        if (widget.dataset.d2uFaqReady === '1') {
            return;
        }
        widget.dataset.d2uFaqReady = '1';

        var dataField = widget.querySelector('.d2u-faq-data');
        var repeater = widget.querySelector('.d2u-faq-repeater');
        var addBtn = widget.querySelector('.d2u-faq-add');
        var tpl = widget.querySelector('.d2u-faq-row-tpl');
        if (!dataField || !repeater || !addBtn || !tpl) {
            return;
        }

        var tagifyInstances = [];

        function getRowTags(row) {
            // Read from Tagify's own always-current array (its hidden input is synced async).
            if (row._tagify) {
                return row._tagify.value.map(function (item) { return item.value; });
            }
            return (row.querySelector('.d2u-faq-tags-input').value || '')
                .split(',')
                .map(function (t) { return t.trim(); })
                .filter(function (t) { return t !== ''; });
        }

        function serialize() {
            var out = [];
            repeater.querySelectorAll('.d2u-faq-row').forEach(function (row) {
                out.push({
                    q: row.querySelector('.d2u-faq-q-input').value || '',
                    a: row.querySelector('.d2u-faq-a-input').value || '',
                    tags: getRowTags(row)
                });
            });
            dataField.value = b64encode(JSON.stringify(out));
        }

        function refreshWhitelists() {
            var all = [];
            tagifyInstances.forEach(function (t) {
                t.value.forEach(function (item) {
                    if (all.indexOf(item.value) === -1) {
                        all.push(item.value);
                    }
                });
            });
            tagifyInstances.forEach(function (t) {
                t.whitelist = all;
            });
        }

        function initTagify(input) {
            // Enter must never submit the slice form from within a tag input.
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
            if (typeof Tagify === 'undefined') {
                input.addEventListener('input', serialize);
                return null;
            }
            var tagify = new Tagify(input, {
                delimiters: ',',
                trim: true,
                dropdown: { enabled: 0, maxItems: 20, closeOnSelect: false, classname: 'd2u-faq-tags-dropdown' },
                originalInputValueFormat: function (valuesArr) {
                    return valuesArr.map(function (item) { return item.value; }).join(',');
                }
            });
            tagify.on('change', function () {
                refreshWhitelists();
                serialize();
            });
            // REDAXO's accesskeys treat single letters as shortcuts; disable while a tag input is focused.
            var accesskeysBackup;
            tagify.DOM.scope.addEventListener('focusin', function () {
                if (typeof rex !== 'undefined') {
                    accesskeysBackup = rex.accesskeys;
                    rex.accesskeys = false;
                }
            });
            tagify.DOM.scope.addEventListener('focusout', function () {
                if (typeof rex !== 'undefined' && typeof accesskeysBackup !== 'undefined') {
                    rex.accesskeys = accesskeysBackup;
                }
            });
            tagifyInstances.push(tagify);
            return tagify;
        }

        function addRow(item) {
            item = item || { q: '', a: '', tags: [] };
            var frag = tpl.content.cloneNode(true);
            var row = frag.querySelector('.d2u-faq-row');

            row.querySelector('.d2u-faq-q-input').value = item.q || '';
            row.querySelector('.d2u-faq-a-input').value = item.a || '';
            var tagsInput = row.querySelector('.d2u-faq-tags-input');
            tagsInput.value = (item.tags || []).join(', ');

            row.querySelectorAll('input, textarea').forEach(function (el) {
                if (el !== tagsInput) {
                    el.addEventListener('input', serialize);
                }
            });
            row.querySelector('.d2u-faq-remove').addEventListener('click', function () {
                var tagify = row._tagify;
                if (tagify) {
                    var idx = tagifyInstances.indexOf(tagify);
                    if (idx !== -1) {
                        tagifyInstances.splice(idx, 1);
                    }
                    tagify.destroy();
                }
                row.remove();
                refreshWhitelists();
                serialize();
            });
            row.querySelector('.d2u-faq-up').addEventListener('click', function () {
                var prev = row.previousElementSibling;
                if (prev) {
                    repeater.insertBefore(row, prev);
                    serialize();
                }
            });
            row.querySelector('.d2u-faq-down').addEventListener('click', function () {
                var next = row.nextElementSibling;
                if (next) {
                    repeater.insertBefore(next, row);
                    serialize();
                }
            });

            repeater.appendChild(frag);
            row._tagify = initTagify(tagsInput);
            refreshWhitelists();
        }

        var data = parseData(dataField.value);
        data.forEach(addRow);
        if (data.length === 0) {
            addRow();
        }

        addBtn.addEventListener('click', function () {
            addRow();
            serialize();
        });

        var form = dataField.closest('form');
        if (form) {
            form.addEventListener('submit', serialize);
        }

        serialize();
    }

    function initAll() {
        document.querySelectorAll('.d2u-faq-widget').forEach(initWidget);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
