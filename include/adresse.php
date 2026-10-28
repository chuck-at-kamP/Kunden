	<?php Logger::debug("src/include/adresse.php: GUI Adresse: " . json_encode($mk_adresse)); $mk_adresse = $mk_adresse[0]; ?>
	<tr>
	<td><label for="strasse">Strasse</label><label for="nr">/Nr:</label>
	</td>
	<td><input type="text" id="strasse" name="strasse" 
			value="<?php if(isset($mk_adresse['strasse'])) echo $mk_adresse['strasse']; ?>" required size="30" />
	    <input type="text" id="hausnummer" name="hausnummer" 
			value="<?php if(isset($mk_adresse['hausnummer'])) echo $mk_adresse['hausnummer']; ?>" required size="7" /></td>
	</tr>
	<tr>
	<td><label for="plz">PLZ</label><label for="ort">/Ort:</label></td>
	<td><input type="text" id="plz" name="plz" 
			value="<?php if(isset($mk_adresse['plz'])) echo $mk_adresse['plz']; ?>" required size="7" />
	    <input type="text" id="ort" name="ort" 
			value="<?php if(isset($mk_adresse['ort'])) echo $mk_adresse['ort']; ?>" required size="30" /></td>
	</tr>
	<tr>
	<td colspan=2>
		<input type="text" id="aid" name="aid" 
			value="<?php if(isset($mk_adresse['id'])) echo $mk_adresse['id']; ?>" hidden/>
		<!-- <input type="text" id="person_id" name="person_id" value="<?php //echo $mk_adresse['person_id'] ?>" hidden/> -->
	</td>
	</tr>
