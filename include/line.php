<tr><td colspan=2>
<table style="width:100%; margin:0; padding:0;">
   	<tr style="margin:0; padding:0;">
   	    <td width=30%><hr width=100% style="border:none; border-top:4px dashed grey;"></td>
		<td width=20%><label><?php echo $text ?></label></td>
   	    <td width=1% style="margin:0; padding:0;">
			<input id="kontakt_hinzu" class="expand" type="button"
			<?php Logger::debug("Set action_type 2: $action_type"); ?>
			onChange="javascript:send('<?php echo $action_type ?>','<?php echo $action_sub_type ?>','<?php echo $text . '_hinzu'; ?>');"
			value="+"/></td>
   	    <td width=50%><hr width=100% style="border:none; border-top:4px dashed grey;"></td>
	</tr>
</table>
</td></tr>

<!--
   	    <td width=1% style="margin:0; padding:0;">
			<button style="margin:0; font-size:24px; width:100%; background-color:blue; color:white;" 
			<?php Logger::debug("Set action_type 2: $action_type"); ?>
			onClick="javascript:send('<?php echo $action_type ?>','<?php echo $action_sub_type ?>','<?php echo $text . '_hinzu'; ?>');"
			value="<?php echo $text . '_hinzu' ?>">+</button></td>
-->
