<br>
<form action="?set=notes" method=post>
  <table width=519>
	<tr>
	  <td class=c colspan=4><?=$parse['Notes'] ?></td>
	</tr>
	<tr>
	  <th colspan=4><a href="?set=notes&a=1"><?=$parse['MakeNewNote'] ?></a></th>
	</tr>
	<tr>
	  <td class=c></td>
	  <td class=c><?=$parse['Date'] ?></td>
	  <td class=c><?=$parse['Subject'] ?></td>
	  <td class=c><?=$parse['Size'] ?></td>
	</tr>
<? if(count($parse['list']) > 0): ?>
 <? foreach($parse['list'] AS $list): ?>
	<tr>
	  <th width=20><input name="delmes<?=$list['NOTE_ID'] ?>" value="y" type="checkbox"></th>
	  <th width=150><?=$list['NOTE_TIME'] ?></th>
	  <th>
		<a href="?set=notes&a=2&amp;n=<?=$list['NOTE_ID'] ?>">
			<font color="<?=$list['NOTE_COLOR'] ?>"><?=$list['NOTE_TITLE'] ?></font>
		</a>
	  </th>
	  <th align="right" width="40"><?=$list['NOTE_TEXT'] ?></th>
	</tr>
  <? endforeach; ?>
<? else: ?>
<tr><th colspan="4">Заметки отсутствуют</th></tr>
<? endif; ?>
<tr>
	  <td colspan=4><input value="<?=$parse['Delete'] ?>" type="submit"></td>
	</tr>
  </table>
</form>