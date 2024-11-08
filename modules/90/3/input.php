<div class="row">
	<div class="col-xs-12">
		Ausgabe der Kategorien. Diese können im <a href="index.php?page=d2u_machinery/machine">D2U Maschinen Addon</a> bearbeitet werden.
	</div>
</div>
<div class="row"><div class="col-xs-12">&nbsp;</div></div>
<div class="row">
	<div class="col-xs-4">Überschrift</div>
	<div class="col-xs-8">
		<input class="form-control" id="headline" type="text" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" />
	</div>
</div>
<?php
if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
?>
<div class="row"><div class="col-xs-12">&nbsp;</div></div>
<div class="row">
	<div class="col-xs-4">
		Kategorien für...
	</div>
	<div class="col-xs-8">
		<select name="REX_INPUT_VALUE[3]" class="form-control">
		<?php
        $values = ['machines' => 'Maschinen', 'used_machines_sale' => 'Gebrauchtmaschinen Verkaufsangebote', 'used_machines_rent' => 'Gebrauchtmaschinen Mietangebote'];
        foreach ($values as $key => $value) {
            echo '<option value="'. $key .'" ';

            if ('REX_VALUE[3]' === $key) { /** @phpstan-ignore-line */
                echo 'selected="selected" ';
            }
            echo '>'. $value .'</option>';
        }
        ?>
		</select>
	</div>
</div>
<?php
}
?>
<div class="row"><div class="col-xs-12">&nbsp;</div></div>
<div class="row">
	<div class="col-xs-4">Anzahl Kategorien / Zeile</div>
	<div class="col-xs-8">
		<?php
            echo '<select name="REX_INPUT_VALUE[4]" class="form-control">';
            echo '<option value="3" '. (3 === (int) 'REX_VALUE[4]' ? 'selected="selected" ' : '') .'>3</option>'; /** @phpstan-ignore-line */
            echo '<option value="4" '. (4 === (int) 'REX_VALUE[4]' ? 'selected="selected" ' : '') .'>4</option>'; /** @phpstan-ignore-line */
            echo '</select>';
        ?>
	</div>
</div>
<div class="row"><div class="col-xs-12">&nbsp;</div></div>
<div class="row">
	<div class="col-xs-12">Text unter Kategorienliste:</div>
	<div class="col-xs-12">
		<textarea name="REX_INPUT_VALUE[2]" class="form-control <?= \TobiasKrais\D2UHelper\BackendHelper::getWYSIWYGEditorClass() ?>">REX_VALUE[2]</textarea>
	</div>
</div>