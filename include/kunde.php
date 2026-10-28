	<?php Logger::debug("src/include/kunde.php: GUI Kunde: " . json_encode($mk_kunde)); $mk_kunde = $mk_kunde[0]; ?>
	<tr><td><label for="type">Type:</label></td>
	<td>
		<input type="checkbox" id="tafel" name="tafel" value="Tafel" onChange="javascript:send('<?php echo $action_type ?>','<?php echo $action_sub_type ?>','Select_Tafel');"
			<?php if(isset($mk_kunde['tafel']) and $mk_kunde['tafel']) { echo "checked"; } ?>
			<?php if($action_type === "Select_Customer") { ?>
				onChange="javascript:add('tafel');"
			<?php } ?>/>
    	<label for="tafel">Tafel</label>
		<input type="checkbox" id="kaufhaus" name="kaufhaus" value="Kaufhaus" 
			<?php if(isset($mk_kunde['kaufhaus']) and $mk_kunde['kaufhaus']) { echo "checked"; } ?> 
			<?php if($action_type === "Select_Customer") { ?>
				onChange="javascript:add('kaufhaus');"
			<?php } ?>/>
    	<label for="kaufhaus">Kaufhaus</label>
	</td></tr>
	<tr>
	<td><label for="knr">KNr:</label></td>
	<td><input type="text" id="knr" name="kundennummer" 
		value="<?php if(isset($mk_kunde['kundennummer'])) echo $mk_kunde['kundennummer']; ?>" required size="12" 
		<?php if($action_type === "Select_Customer") { echo "onChange=\"javascript:add('kundennummer');\""; } ?>/>
		</td>
	</tr>
	<tr>
	<td><label for="nachname">Nachname:</label></td>
	<td><input type="text" id="nachname" name="nachname" 
		value="<?php if(isset($mk_kunde['nachname'])) echo $mk_kunde['nachname'] ?>" required size="30" 
		<?php if($action_type === "Select_Customer") { echo "onChange=\"javascript:add('nachname');\""; } ?>/>
		</td>
	</tr>
	<tr>
	<td><label for="vorname">Vorname:</label></td>
	<td><input type="text" id="vorname" name="vorname" 
		value="<?php if(isset($mk_kunde['vorname'])) echo $mk_kunde['vorname'] ?>" required size="30" 
		<?php if($action_type === "Select_Customer") { echo "onChange=\"javascript:add('vorname');\""; } ?>/>
		</td>
	</tr>
	<tr>
	<td><label for="valid">Gültig bis:</label></td>
	<td><input type="text" id="gueltigkeit" name="gueltigkeit" 
		value="<?php if(isset($mk_kunde['gueltigkeit'])) echo $mk_kunde['gueltigkeit']; ?>" required size="12" 
		<?php if($action_type === "Select_Customer") { echo "onChange=\"javascript:add('gueltigkeit');\""; } ?>/>
		</td>
	</tr>
	<tr>
	<td><label for="nachweis">Nachweis:</label></td>
	<td><select id="nachweis" name="nachweis"
		<?php if($action_type === "Select_Customer") { echo "onChange=\"javascript:addSelect('nachweis');\""; } ?> required>
		<option value="" selected disabled hidden> -- Nachweis auswählen -- </option>
			<?php foreach($nachweise as $entry) { 
				$nSelected = (isset($mk_kunde['nachweis']) and ($mk_kunde['nachweis'] === $entry));
			?>
		<option value="<?php echo $entry ?>" <?php if($nSelected) { echo "selected"; } ?>><?php echo $entry ?></option>
		<?php } ?>
	</select></td>
	</tr>
	<tr>
	<td><label for="bemerkung">Bemerkung:</label></td>
	<td><input type="text" id="bemerkung" name="bemerkung" 
		value="<?php if(isset($mk_kunde['bemerkung'])) echo $mk_kunde['bemerkung']; ?>" size="50" 
		<?php if($action_type === "Select_Customer") { echo "onChange=\"javascript:add('bemerkung');\""; } ?>/>
		</td>
	</tr>
	<tr>
	<td><input type="text" id="kid" name="kid" 
		value="<?php if(isset($mk_kunde['id'])) echo $mk_kunde['id']; ?>" hidden/></td>
	</tr>
