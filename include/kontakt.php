<?php if(isset($mk_kontakt)) { 
	Logger::debug_array("include/kontakt.php HTML Kontakt",$mk_kontakt,true);
	$kontakt_nr = 0;
	foreach($mk_kontakt as $kontakt_element) {
		Logger::debug_print("include/kontakt.php HTML Kontakt Element ($kontakt_nr)", $kontakt_element, true);
?>
	<tr>
	<td class="Kontakt"><label for="kontakt_type">Kontakt:</label></td>
	<td class="Kontakt">
		<select id="kontakt_type" name="<?php echo "kontakt_type_" . $kontakt_nr ?>">
			<option value="" selected disabled hidden> -- Kontakt auswählen -- </option>
				<?php foreach($kontakt_types as $entry) { 
					$kSelected = (isset($kontakt_element['k_type']) and ($kontakt_element['k_type'] === $entry)); ?>
					<option value="<?php echo $entry ?>" <?php if($kSelected) { echo "selected"; } ?>> <?php echo $entry ?></option>
				<?php } ?>
		</select>
	</td>
	<td class="Kontakt">
		<input type="text" id="kontakt" name="<?php echo "kontakt_" . $kontakt_nr ?>" 
			value="<?php if(isset($kontakt_element['kontakt'])) echo $kontakt_element['kontakt'];  ?>" size="30" />
	</td>
   	    <td width=1% style="margin:0; padding:0;">
			<input id="kontakt_entfernen" class="expand" type="button"
			<?php Logger::debug("include/kontakt.php Set action_type 2: $action_type"); ?>
			onClick="javascript:send('<?php echo $action_type ?>','<?php echo $action_sub_type ?>','Del_Contact');"
			name="<?php echo "kontakt_delete_" . $kontakt_nr ?>" value="-"/></td>
	</tr>
	<tr>
	<td colspan=2>
		<input type="text" id="ktid" name="ktid_<?php echo $kontakt_nr ?>" 
			value="<?php if(isset($kontakt_element['id'])) echo $kontakt_element['id']; ?>" hidden/>
		<input type="text" id="person_id" name="person_id_kontakt_<?php echo $kontakt_nr ?>" 
			value="<?php if(isset($kontakt_element['person_id'])) echo $kontakt_element['person_id']; ?>" hidden/>
	</td>
	</tr>
<?php $kontakt_nr++; } } ?>

<!--
		<?php $action_type="'Del_Contact'"; ?>
   	    <td width=1% style="margin:0; padding:0;">
			<button 
			<?php Logger::debug("include/kontakt.php Set action_type 2: $action_type"); ?>
			onClick="javascript:send('<?php echo $action_type ?>','<?php echo $action_sub_type ?>','Del_Contact');"
			name="<?php echo "kontakt_delete_" . $kontakt_nr ?>" value="<?php echo $kontakt_nr ?>">-</button></td>
-->

