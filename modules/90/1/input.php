<div class="row">
	<div class="col-xs-4">Anzahl Kategorien / Zeile</div>
	<div class="col-xs-8">
		<?php
			print '<select name="REX_INPUT_VALUE[1]" class="form-control">';
			print '<option value="3" '. (intval("REX_VALUE[1]") === 3 ? 'selected="selected" ' : '') .'>3</option>'; /** @phpstan-ignore-line */
			print '<option value="4" '. (intval("REX_VALUE[1]") === 4 || "REX_VALUE[1]" === '' ? 'selected="selected" ' : '') .'>4</option>'; /** @phpstan-ignore-line */
			print '</select>';
		?>
	</div>
</div>
<div class="row"><div class="col-xs-12">&nbsp;</div></div>
<div class="row">
	<div class="col-xs-12">
		<br>
		Alle Einstellungen können im <a href="index.php?page=d2u_machinery/machine">
				D2U Maschinen Addon</a> vorgenommen werden.
	</div>
</div>