	<tr>
	<td><label for="nachname">Name:</label></td>
	<td><input type="text" id="nachname" name="nachname" value="<?php echo $mk_person['nachname'] ?>" required size="30" /></td>
	</tr>
	<tr>
	<td><label for="vorname">Vorname:</label></td>
	<td><input type="text" id="vorname" name="vorname" value="<?php echo $mk_person['vorname'] ?>" required size="30" /></td>
	</tr>
	<tr>
	<td colspan=2>
		<input type="text" id="pid" name="pid" value="<?php echo $mk_person['id'] ?>" hidden/>
		<input type="text" id="p_type" name="p_type" value="<?php echo $mk_person['p_type'] ?>" hidden/>
		<input type="text" id="kunde_id" name="kunde_id" value="<?php echo $mk_person['kunde_id'] ?>" hidden/>
	</td>
	</tr>
