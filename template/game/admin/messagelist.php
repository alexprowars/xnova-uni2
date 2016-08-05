<script language="JavaScript">
function f(target_url, win_name) {
var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
new_win.focus();
}
</script>
<br>
<form action="?set=admin&mode=messagelist" method="post">
<input type="hidden" name="curr" value="<?=$parse['mlst_data_page'] ?>">
<input type="hidden" name="pmax" value="<?=$parse['mlst_data_pagemax'] ?>">
<input type="hidden" name="sele" value="<?=$parse['mlst_data_sele'] ?>">
<table width="750" border="0" cellspacing="1" cellpadding="1">
<tr>
	<td class="c"><div align="center"><input type="submit" name="prev" value="<?=$parse['mlst_hdr_prev'] ?>"></div></td>
	<td class="c"><div align="center"><?=$parse['mlst_hdr_page'] ?></div></td>
	<td class="c"><div align="center">
		<select name="page" onchange="submit();">
		<?=$parse['mlst_data_pages'] ?>
		</select></div>
	</td>
	<td class="c"><div align="center"><input type="submit" name="next" value="<?=$parse['mlst_hdr_next'] ?>" /></div></td>
</tr><tr>
	<td class="c">owner: <input type="text" name="userid" size="7" value="<?=$parse['userid'] ?>" /> sender: <input type="text" name="userid_s" size="7" value="<?=$parse['userid_s'] ?>" /><input type="submit" name="usersearch" value="По id" /></td>
	<td class="c"><div align="center"><?=$parse['mlst_hdr_type'] ?></div></td>
	<td class="c"><div align="center">
		<select name="type" onchange="submit();">
		<?=$parse['mlst_data_types'] ?>
		</select></div>
	</td>
	<td class="c">&nbsp;</td>
</tr><tr>
	<td class="c"><div align="center"><input type="submit" name="delsel" value="<?=$parse['mlst_bt_delsel'] ?>" /></div></td>
	<td class="c"><div align="center"><?=$parse['mlst_hdr_delfrom'] ?></div></td>
	<td class="c"><div align="center"><input type="text"   name="selday" size="3" /> <input type="text"   name="selmonth" size="3" /> <input type="text"   name="selyear" size="6" /></div></td>
	<td class="c"><div align="center"><input type="submit" name="deldat" value="<?=$parse['mlst_bt_deldate'] ?>" /></div></td>
</tr><tr>
	<th colspan="4">
		<table width="750" border="0" cellspacing="1" cellpadding="1">
		<tr align="center" valign="middle">
			<th class="c">&nbsp;</th>
			<th class="c"><?=$parse['mlst_hdr_time'] ?></th>
			<th class="c"><?=$parse['mlst_hdr_from'] ?></th>
			<th class="c"><?=$parse['mlst_hdr_to'] ?></th>
			<th class="c" width="300"><?=$parse['mlst_hdr_text'] ?></th>
		</tr>
<? foreach($parse['mlst_data_rows'] AS $list): ?>
            <tr>
	<th><input type="checkbox" name="sele_mes[<?=$list['mlst_id'] ?>]" /></th>
	<th><?=$list['mlst_time'] ?></th>
	<th><?=$list['mlst_from'] ?></th>
	<th><?=$list['mlst_to'] ?></th>
	<th><?=$list['mlst_text'] ?></th>
</tr>
<? endforeach; ?>
		</table>
	</th>
</tr>
</table>
</form>
