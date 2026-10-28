
<!-- ############################################################################# -->
<table class="title" width=100%>
<tr><td>
	<img class=title src="html/MK-Logo3.png">
</td><td>
	<?php if($app === "/kunden/kunden.php") { ?>
	<span class="title"> Kunden eingeben </span>
	<?php } elseif($app === "/kunden/index.php") { ?>
	<span class="title"> Kundenkarte mit DB </span>
	<?php } elseif($app === "/kunden/liste.php") { ?>
	<span class="title"> Kundenliste </span>
	<?php } ?>
</td><td>
	<a href="/mkadmin/logout.php?application=<?php echo $app ?>"> <div class="logout"> Logout </div> </a>
</td></tr>
</table>
<!-- ############################################################################# -->
