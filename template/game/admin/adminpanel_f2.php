<br >
<center>
<table width="300">
<form action="?set=admin&mode=paneladmina" method="post">
	<tr>
	  <td class="c" colspan="6"><?=$parse['adm_search_ip'] ?></td>
	</tr>
	<tr>
       <th><?=$parse['adm_ip'] ?></th>
	  <th><input type="text" name="ip" style="width:150" ></th>
	</tr>
	<tr>
	  <th colspan="2"><input type="submit" value="<?=$parse['adm_bt_search'] ?>"></th>
    </tr>
<input type="hidden" name="result" value="ip_search">
</form>
</table>
</center>
