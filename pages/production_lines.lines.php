<?php

use TobiasKrais\D2UHelper\BackendHelper;

use TobiasKrais\D2UMachinery\IndustrySector;
use TobiasKrais\D2UMachinery\Machine;
use TobiasKrais\D2UMachinery\ProductionLine;
use TobiasKrais\D2UMachinery\Supply;
use TobiasKrais\D2UMachinery\USP;
use TobiasKrais\D2UReferences\Reference;

$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

$csrfToken = BackendHelper::getPageCsrfToken();
$invalidCsrf = false;
if ((
    1 === (int) filter_input(INPUT_POST, 'btn_save')
    || 1 === (int) filter_input(INPUT_POST, 'btn_apply')
    || 1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT)
    || 'save' === filter_input(INPUT_POST, 'btn_save')
    || 'Speichern' === rex_request::request('btn_save', 'string')
    || in_array($func, ['delete', 'changestatus', 'priority_up', 'priority_down'], true)
) && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    $invalidCsrf = true;
    if ('POST' !== rex_request::server('REQUEST_METHOD', 'string')) {
        $func = '';
    }
}
$productionLinesPageParams = isset($productionLinesPageParams) && is_array($productionLinesPageParams) ? $productionLinesPageParams : ['production_lines_subpage' => 'production_lines'];

// messages
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

if (1 === (int) filter_input(INPUT_POST, 'btn_abort', FILTER_VALIDATE_INT)) {
    header('Location: '. BackendHelper::getCurrentBackendPage([], ['entry_id', 'func', 'message', 'message_type']));
    exit;
}

// save settings
if (!$invalidCsrf && (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply'))) {
    $form = rex_post('form', 'array', []);

    // Media fields and links need special treatment
    $input_media_list = rex_post('REX_INPUT_MEDIALIST', 'array', []);
    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

    $success = true;
    $production_line = false;
    $production_line_id = $form['production_line_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (!$production_line instanceof ProductionLine) {
            $production_line = new ProductionLine($production_line_id, $rex_clang->getId());
            $production_line->production_line_id = $production_line_id; // Ensure correct ID in case first language has no object
            $production_line->complementary_machine_ids = $form['complementary_machine_ids'] ?? [];
            $production_line->industry_sector_ids = $form['industry_sector_ids'] ?? [];
            $production_line->line_code = $form['line_code'];
            $production_line->machine_ids = $form['machine_ids'] ?? [];
            $production_line->pictures = '' !== ($input_media[2] ?? '') ? [$input_media[2]] : [];
            $production_line->link_picture = $input_media[1] ?? '';
            $production_line->markers = $form['markers'] ?? '';
            $production_line->usp_ids = $form['usp_ids'] ?? [];
            $production_line->video_ids = $form['video_ids'] ?? [];
            if (rex_addon::get('d2u_references')->isAvailable()) {
                $production_line->reference_ids = $form['reference_ids'] ?? [];
            }
            $production_line->online_status = null === $form['online_status'] || '' === $form['online_status'] ? 'offline' : $form['online_status'];
            if (\TobiasKrais\D2UMachinery\Extension::isActive('machine_steel_automation_extension')) {
                $automation_supply_ids = $form['automation_supply_ids'] ?? [];
                $production_line->automation_supply_ids = [];
                foreach ($automation_supply_ids as $automation_supply_id) {
                    if ($automation_supply_id > 0) {
                        $production_line->automation_supply_ids[] = $automation_supply_id;
                    }
                }
            }
        } else {
            $production_line->clang_id = $rex_clang->getId();
        }
        $production_line->description_long = $form['lang'][$rex_clang->getId()]['description_long'];
        $production_line->description_short = $form['lang'][$rex_clang->getId()]['description_short'];
        $production_line->name = $form['lang'][$rex_clang->getId()]['name'];
        $production_line->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
        $production_line->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

        if ('delete' === $production_line->translation_needs_update) {
            $production_line->delete(false);
        } elseif ($production_line->save()) {
            // remember id, for each database lang object needs same id
            $production_line_id = $production_line->production_line_id;
        } else {
            $success = false;
        }
    }

    // message output
    $message = 'form_save_error';
    if ($success) {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $production_line) {
        header('Location: '. rex_url::currentBackendPage(array_merge($productionLinesPageParams, ['entry_id' => $production_line->production_line_id, 'func' => 'edit', 'message' => $message]), false));
    } else {
        header('Location: '. rex_url::currentBackendPage(array_merge($productionLinesPageParams, ['message' => $message]), false));
    }
    exit;
}
// Delete
if ((!$invalidCsrf && 1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT)) || 'delete' === $func) {
    $production_line_id = $entry_id;
    if (0 === $production_line_id) {
        $form = rex_post('form', 'array', []);
        $production_line_id = $form['production_line_id'];
    }
    $production_line = new ProductionLine($production_line_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $production_line->production_line_id = $production_line_id; // Ensure correct ID in case language has no object
    $production_line->delete(true);

    $func = '';
}
// Change online status of machine
elseif ('changestatus' === $func) {
    $production_line = new ProductionLine($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $production_line->production_line_id = $entry_id;
    $production_line->changeStatus();

    header('Location: '. rex_url::currentBackendPage($productionLinesPageParams));
    exit;
}

// Eingabeformular
if ('edit' === $func || 'add' === $func) {
?>
    <form action="<?= rex_url::currentBackendPage($productionLinesPageParams) ?>" method="post">
		<?= $csrfToken->getHiddenField() ?>
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_machinery_production_lines') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[production_line_id]" value="<?= $entry_id ?>">
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_data_all_lang') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            // Do not use last object from translations, because you don't know if it exists in DB
                            $production_line = new ProductionLine($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            $readonly = true;
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                $readonly = false;
                            }

                            BackendHelper::form_input('d2u_machinery_production_lines_line_code', 'form[line_code]', $production_line->line_code, false, $readonly, 'text');
                            BackendHelper::form_mediafield('d2u_helper_pictures', '2', $production_line->pictures[0] ?? '', $readonly);

                            // Marker editor for the main picture
                            $marker_machines = [];
                            foreach (Machine::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $marker_machine) {
                                $marker_machines[] = ['id' => $marker_machine->machine_id, 'name' => $marker_machine->name];
                            }
                            $marker_supplies = [];
                            $marker_supply_active = \TobiasKrais\D2UMachinery\Extension::isActive('machine_steel_automation_extension');
                            if ($marker_supply_active) {
                                foreach (Supply::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $marker_supply) {
                                    $marker_supplies[] = ['id' => $marker_supply->supply_id, 'name' => $marker_supply->name];
                                }
                            }
                            $marker_image_url = count($production_line->pictures) > 0 ? rex_url::media($production_line->pictures[0]) : '';
                            usort($marker_machines, static fn(array $a, array $b): int => strcasecmp((string) $a['name'], (string) $b['name']));
                            usort($marker_supplies, static fn(array $a, array $b): int => strcasecmp((string) $a['name'], (string) $b['name']));
                            echo '<div class="form-group"><label class="control-label">'. rex_i18n::msg('d2u_machinery_production_lines_markers') .'</label>';
                            if ('' !== $marker_image_url) {
                                echo '<div id="d2u-marker-editor"'
                                    .' data-readonly="'. ($readonly ? '1' : '0') .'"'
                                    ." data-machines='". rex_escape((string) json_encode($marker_machines)) ."'"
                                    ." data-supplies='". rex_escape((string) json_encode($marker_supplies)) ."'"
                                    .' data-supply-active="'. ($marker_supply_active ? '1' : '0') .'"'
                                    .' data-label-type-machine="'. rex_escape(rex_i18n::msg('d2u_machinery_production_lines_marker_machine')) .'"'
                                    .' data-label-type-supply="'. rex_escape(rex_i18n::msg('d2u_machinery_production_lines_marker_supply')) .'"'
                                    .' data-label-delete="'. rex_escape(rex_i18n::msg('form_delete')) .'">'
                                    .'<div class="d2u-marker-stage"><img src="'. rex_escape($marker_image_url) .'" alt="" class="d2u-marker-image"></div>'
                                    .'<p class="rex-note">'. rex_i18n::msg('d2u_machinery_production_lines_marker_hint') .'</p>'
                                    .'<div class="d2u-marker-list"></div>'
                                    .'</div>';
                            } else {
                                echo '<p class="rex-note">'. rex_i18n::msg('d2u_machinery_production_lines_marker_no_image') .'</p>';
                            }
                            echo '<input type="hidden" name="form[markers]" id="d2u-marker-data" value="'. rex_escape($production_line->markers) .'">';
                            echo '</div>';

                            BackendHelper::form_mediafield('d2u_machinery_production_lines_link_picture', '1', $production_line->link_picture, $readonly);
                            if (\rex_addon::get('d2u_videos')->isAvailable()) {
                                $options = [];
                                foreach (TobiasKrais\D2UVideos\Video::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $video) {
                                    $options[$video->video_id] = $video->name;
                                }
                                BackendHelper::form_select('d2u_machinery_category_videos', 'form[video_ids][]', $options, $production_line->video_ids, 10, true, $readonly);
                            }
                            if (\rex_addon::get('d2u_references')->isAvailable()) {
                                $options_references = [];
                                foreach (Reference::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $reference) {
                                    $options_references[$reference->reference_id] = $reference->name;
                                }
                                BackendHelper::form_select('d2u_references', 'form[reference_ids][]', $options_references, $production_line->reference_ids, 10, true, $readonly);
                            }
                            $option_machines = [];
                            foreach (Machine::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $machine) {
                                $option_machines[$machine->machine_id] = $machine->name;
                            }
                            BackendHelper::form_select('d2u_machinery_meta_machines', 'form[machine_ids][]', $option_machines, $production_line->machine_ids, 10, true, $readonly);
                            BackendHelper::form_select('d2u_machinery_production_lines_complementary_machines', 'form[complementary_machine_ids][]', $option_machines, $production_line->complementary_machine_ids, 10, true, $readonly);
                            if (\TobiasKrais\D2UMachinery\Extension::isActive('machine_steel_automation_extension')) {
                                $options_supply = [];
                                foreach (Supply::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $supply) {
                                    $options_supply[$supply->supply_id] = $supply->priority .' - '. $supply->name .' (ID: '. $supply->supply_id .')';
                                }
                                BackendHelper::form_select('d2u_machinery_steel_automation_supplys', 'form[automation_supply_ids][]', $options_supply, $production_line->automation_supply_ids, 4, true, $readonly);
                            }
                            if (\TobiasKrais\D2UMachinery\Extension::isActive('industry_sectors')) {
                                $options_industry_sectors = [];
                                foreach (IndustrySector::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $industry_sector) {
                                    $options_industry_sectors[$industry_sector->industry_sector_id] = $industry_sector->name;
                                }
                                BackendHelper::form_select('d2u_machinery_industry_sectors', 'form[industry_sector_ids][]', $options_industry_sectors, $production_line->industry_sector_ids, 10, true, $readonly);
                            }
                            $option_usps = [];
                            foreach (USP::getAll((int) rex_config::get('d2u_helper', 'default_lang')) as $usp) {
                                $option_usps[$usp->usp_id] = $usp->name;
                            }
                            BackendHelper::form_select('d2u_machinery_production_lines_usp', 'form[usp_ids][]', $option_usps, $production_line->usp_ids, 10, true, $readonly);
                            BackendHelper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', 'online' === $production_line->online_status, $readonly);
                        ?>
					</div>
				</fieldset>
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $production_line = new ProductionLine($entry_id, $rex_clang->getId());
                        $required = $rex_clang->getId() === (int) (rex_config::get('d2u_helper', 'default_lang')) ? true : false;

                        $readonly_lang = true;
                        if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || (\rex::getUser()->hasPerm('d2u_machinery[edit_lang]') && \rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && \rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
                            $readonly_lang = false;
                        }
                ?>
					<fieldset>
						<legend><?= rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"' ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
                                if ($rex_clang->getId() !== (int) rex_config::get('d2u_helper', 'default_lang')) {
                                    $options_translations = [];
                                    $options_translations['yes'] = rex_i18n::msg('d2u_helper_translation_needs_update');
                                    $options_translations['no'] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
                                    $options_translations['delete'] = rex_i18n::msg('d2u_helper_translation_delete');
                                    BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$production_line->translation_needs_update], 1, false, $readonly_lang);
                                } else {
                                    echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
                                }
                            ?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?= $rex_clang->getId() ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});
							</script>
							<div id="details_clang_<?= $rex_clang->getId() ?>">
								<?php
                                    BackendHelper::form_input('d2u_helper_name', 'form[lang]['. $rex_clang->getId() .'][name]', $production_line->name, $required, $readonly_lang, 'text');
                                    BackendHelper::form_input('d2u_machinery_machine_teaser', 'form[lang]['. $rex_clang->getId() .'][teaser]', $production_line->teaser, false, $readonly_lang, 'text');
                                    BackendHelper::form_textarea('d2u_helper_description', 'form[lang]['. $rex_clang->getId() .'][description_short]', $production_line->description_short, 5, false, $readonly_lang, true);
                                    BackendHelper::form_textarea('d2u_helper_description_long', 'form[lang]['. $rex_clang->getId() .'][description_long]', $production_line->description_long, 5, false, $readonly_lang, true);
                                ?>
							</div>
						</div>
					</fieldset>
				<?php
                    }
                ?>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?= rex_i18n::msg('form_save') ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?= rex_i18n::msg('form_apply') ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?= rex_i18n::msg('form_abort') ?></button>
						<?php
                            if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
                                echo '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
                            }
                        ?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<style>
		#d2u-marker-editor .d2u-marker-stage { position: relative; display: inline-block; max-width: 100%; line-height: 0; }
		#d2u-marker-editor .d2u-marker-image { max-width: 100%; height: auto; display: block; }
		#d2u-marker-editor .d2u-marker-dot { position: absolute; width: 26px; height: 26px; margin: -13px 0 0 -13px; border-radius: 50%; background: #f9b000; color: #00121a; border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,.4); font-size: 12px; font-weight: 700; line-height: 22px; text-align: center; cursor: move; z-index: 2; }
		#d2u-marker-editor .d2u-marker-list { margin-top: 12px; }
		#d2u-marker-editor .d2u-marker-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap; }
		#d2u-marker-editor .d2u-marker-index { width: 26px; height: 26px; border-radius: 50%; background: #f9b000; color: #00121a; text-align: center; line-height: 26px; font-weight: 700; flex: 0 0 auto; }
		#d2u-marker-editor .d2u-marker-row select { flex: 1 1 180px; }
	</style>
	<script>
		(function () {
			var editor = document.getElementById('d2u-marker-editor');
			if (!editor) { return; }
			var dataField = document.getElementById('d2u-marker-data');
			var stage = editor.querySelector('.d2u-marker-stage');
			var listEl = editor.querySelector('.d2u-marker-list');
			var readonly = editor.getAttribute('data-readonly') === '1';
			var supplyActive = editor.getAttribute('data-supply-active') === '1';
			var machines = JSON.parse(editor.getAttribute('data-machines') || '[]');
			var supplies = JSON.parse(editor.getAttribute('data-supplies') || '[]');
			var labelMachine = editor.getAttribute('data-label-type-machine') || 'Machine';
			var labelSupply = editor.getAttribute('data-label-type-supply') || 'Supply';
			var labelDelete = editor.getAttribute('data-label-delete') || 'Delete';
			var markers = [];
			try { markers = JSON.parse(dataField.value || '[]'); } catch (e) { markers = []; }
			if (!Array.isArray(markers)) { markers = []; }

			function optionsFor(type) { return type === 'supply' ? supplies : machines; }

			function serialize() {
				dataField.value = JSON.stringify(markers.map(function (m) {
					return { x: Math.round(m.x * 100) / 100, y: Math.round(m.y * 100) / 100, type: m.type, id: parseInt(m.id, 10) };
				}));
			}

			function fillObjSel(sel, type, id) {
				sel.innerHTML = '';
				optionsFor(type).forEach(function (o) {
					var opt = document.createElement('option');
					opt.value = o.id;
					opt.textContent = o.name + ' (ID: ' + o.id + ')';
					if (parseInt(o.id, 10) === parseInt(id, 10)) { opt.selected = true; }
					sel.appendChild(opt);
				});
			}

			function attachDrag(dot, index) {
				dot.addEventListener('mousedown', function (e) {
					e.preventDefault();
					function move(ev) {
						var rect = stage.getBoundingClientRect();
						var x = Math.max(0, Math.min(100, ((ev.clientX - rect.left) / rect.width) * 100));
						var y = Math.max(0, Math.min(100, ((ev.clientY - rect.top) / rect.height) * 100));
						markers[index].x = x;
						markers[index].y = y;
						dot.style.left = x + '%';
						dot.style.top = y + '%';
					}
					function up() {
						document.removeEventListener('mousemove', move);
						document.removeEventListener('mouseup', up);
						serialize();
					}
					document.addEventListener('mousemove', move);
					document.addEventListener('mouseup', up);
				});
			}

			function render() {
				Array.prototype.slice.call(stage.querySelectorAll('.d2u-marker-dot')).forEach(function (d) { d.parentNode.removeChild(d); });
				listEl.innerHTML = '';
				markers.forEach(function (m, i) {
					var dot = document.createElement('div');
					dot.className = 'd2u-marker-dot';
					dot.style.left = m.x + '%';
					dot.style.top = m.y + '%';
					dot.textContent = (i + 1);
					stage.appendChild(dot);
					if (!readonly) { attachDrag(dot, i); }

					var row = document.createElement('div');
					row.className = 'd2u-marker-row';
					var idx = document.createElement('span');
					idx.className = 'd2u-marker-index';
					idx.textContent = (i + 1);
					row.appendChild(idx);

					var typeSel = document.createElement('select');
					typeSel.className = 'form-control';
					var oM = document.createElement('option'); oM.value = 'machine'; oM.textContent = labelMachine; typeSel.appendChild(oM);
					if (supplyActive) { var oS = document.createElement('option'); oS.value = 'supply'; oS.textContent = labelSupply; typeSel.appendChild(oS); }
					typeSel.value = m.type;
					row.appendChild(typeSel);

					var objSel = document.createElement('select');
					objSel.className = 'form-control';
					fillObjSel(objSel, m.type, m.id);
					row.appendChild(objSel);

					var del = document.createElement('button');
					del.type = 'button';
					del.className = 'btn btn-delete';
					del.innerHTML = '<i class="rex-icon fa-trash"></i>';
					del.title = labelDelete;
					del.setAttribute('aria-label', labelDelete);
					row.appendChild(del);

					if (readonly) {
						typeSel.disabled = true; objSel.disabled = true; del.disabled = true;
					} else {
						typeSel.addEventListener('change', function () {
							m.type = typeSel.value;
							var opts = optionsFor(m.type);
							m.id = opts.length ? opts[0].id : 0;
							fillObjSel(objSel, m.type, m.id);
							serialize();
						});
						objSel.addEventListener('change', function () { m.id = parseInt(objSel.value, 10); serialize(); });
						del.addEventListener('click', function () { markers.splice(i, 1); render(); serialize(); });
					}
					listEl.appendChild(row);
				});
			}

			if (!readonly) {
				stage.addEventListener('click', function (e) {
					if (e.target.classList.contains('d2u-marker-dot')) { return; }
					var rect = stage.getBoundingClientRect();
					var x = ((e.clientX - rect.left) / rect.width) * 100;
					var y = ((e.clientY - rect.top) / rect.height) * 100;
					var opts = optionsFor('machine');
					markers.push({ x: x, y: y, type: 'machine', id: opts.length ? opts[0].id : 0 });
					render();
					serialize();
				});
			}

			render();
			serialize();
		})();
	</script>
	<?php
        echo BackendHelper::getCSS();
        echo BackendHelper::getJS();
}

if ('' === $func) {
    $query = 'SELECT production_lines.production_line_id, name, line_code, online_status '
        . 'FROM '. \rex::getTablePrefix() .'d2u_machinery_production_lines AS production_lines '
        . 'LEFT JOIN '. \rex::getTablePrefix() .'d2u_machinery_production_lines_lang AS lang '
			. 'ON production_lines.production_line_id = lang.production_line_id AND lang.clang_id = '. (int) rex_config::get('d2u_helper', 'default_lang') .' ';
        $list = rex_list::factory(query: $query, rowsPerPage: 1000, defaultSort: ['name' => 'ASC']);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-arrows-h"></i>';
    $thIcon = '';
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->setColumnLabel('production_line_id', rex_i18n::msg('id'));
    $list->setColumnLayout('production_line_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);
    $list->setColumnSortable('production_line_id');

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###production_line_id###']);
    $list->setColumnSortable('name');

    $list->setColumnLabel('line_code', rex_i18n::msg('d2u_machinery_production_lines_line_code'));
    $list->setColumnParams('line_code', ['func' => 'edit', 'entry_id' => '###production_line_id###']);
    $list->setColumnSortable('line_code');

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###production_line_id###']);

    $list->removeColumn('online_status');
    if (\rex::getUser() instanceof rex_user && (\rex::getUser()->isAdmin() || \rex::getUser()->hasPerm('d2u_machinery[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(array_merge($productionLinesPageParams, ['func' => 'changestatus'])) . '&entry_id=###production_line_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
        $list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###production_line_id###'] + $csrfToken->getUrlParams());
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->addColumn(rex_i18n::msg('d2u_helper_open_frontend'), '');
    $list->setColumnLayout(rex_i18n::msg('d2u_helper_open_frontend'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnFormat(rex_i18n::msg('d2u_helper_open_frontend'), 'custom', static function ($params) {
        $listParams = $params['list'];

        return BackendHelper::getFrontendLinkButton((new \TobiasKrais\D2UMachinery\ProductionLine((int) $listParams->getValue('production_line_id'), (int) rex_config::get('d2u_helper', 'default_lang')))->getUrl());
    });

    $list->setNoRowsMessage(rex_i18n::msg('d2u_machinery_production_lines_no_production_lines_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_machinery_production_lines'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
