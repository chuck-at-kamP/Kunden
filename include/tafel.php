	<?php if(isset($mk_kunde['tafel']) and $mk_kunde['tafel']) { ?>
	<?php Logger::debug("src/include/tafel.php: GUI Tafel: " . json_encode($mk_tafel)); $mk_tafel = $mk_tafel[0]; ?>
	<tr><td colspan=2><hr></td></tr>
	<tr>
	<td><label for="personen">Personen:</label></td>
	<td><input type="text" id="personen" name="personen" 
		value="<?php if(isset($mk_tafel['personen'])) echo $mk_tafel['personen']; ?>" required size="12" /></td>
	</tr>
	<tr>
	<td><label for="preis">Preis:</label></td>
	<td><input type="text" id="preis" name="preis" 
		value="<?php if(isset($mk_tafel['preis'])) echo $mk_tafel['preis']; ?>" required size="12" /></td>
	</tr>
	<tr>
	<td><label for="gruppe">Gruppe:</label></td>
	<td><input type="text" id="gruppe" name="gruppe" 
		value="<?php if(isset($mk_tafel['gruppe'])) echo $mk_tafel['gruppe']; ?>" required size="12" /></td>
	</tr>
	<tr>
	<td colspan=2>
		<input type="text" id="tid" name="tid" 
			value="<?php if(isset($mk_tafel['id'])) echo $mk_tafel['id']; ?>" hidden/>
		<input type="text" id="kunde_id_tafel" name="kunde_id_tafel" 
			value="<?php if(isset($mk_tafel['kunde_id'])) echo $mk_tafel['kunde_id']; ?>" hidden/>
	</td>
	</tr>
	<?php } ?>
