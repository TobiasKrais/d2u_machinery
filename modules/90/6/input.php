
<div class="row">
	<div class="col-xs-4">
		Anzahl Topangebote (eine Reihe):
	</div>
	<div class="col-xs-8">
		<select name="REX_INPUT_VALUE[1]" class="form-control">
		<?php
		$values = [4 => "4 Angebote pro Reihe (auf großen Bildschirmen)", 3 => "3 Angebote pro Reihe (auf großen Bildschirmen)"];
		foreach($values as $key => $value) {
			echo '<option value="'. $key .'" ';
	
			if ("REX_VALUE[1]" == $key) {
				echo 'selected="selected" ';
			}
			echo '>'. $value .'</option>';
		}
		?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<p>Alle weiteren Einstellungen können im <a href="index.php?page=d2u_machinery/used_machines">
				D2U Maschinen Addon</a> vorgenommen werden.</p>
	</div>
</div>