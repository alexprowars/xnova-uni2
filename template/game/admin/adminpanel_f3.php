<br >
<center>
<table width="300">
<form action="?set=admin&mode=paneladmina" method="post">
	<tr>
	  <td class="c" colspan="6"><?=$parse['adm_mod_level'] ?></td>
	</tr>
	<tr>
       <th><?=$parse['adm_player_nm'] ?></th>
	  <th><input type="text" name="player" style="width:150"></th>
	</tr>
	<tr>
	  <th colspan="2"><select name="authlvl"><?=$parse['adm_level_lst'] ?></select></th>
    </tr>
	<tr>
	  <th colspan="2"><input type="submit" value="<?=$parse['adm_bt_change'] ?>"></th>
	  </tr>
<input type="hidden" name="result" value="usr_level">
</form>
</table>
</center>
