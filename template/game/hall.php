<br>
<table width="650">
	<tr>
	   <td class="c" width=100>TOP50</td>
        <td class="c"><a href="hall.php">Зал Славы</a></td>
        <td class="c" width=70><form method="POST" action="?set=hall"><select name="visible" onChange="javascript:document.forms[0].submit()"><option value=1 <?=((!isset($_POST['visible']) || $_POST['visible'] <= 1)?'selected':'') ?>>Бои<option value=2 <?=((isset($_POST['visible']) && $_POST['visible'] == 2)?'selected':'') ?>>САБ</select></form></td>
    </tr>
<table width="650">
	<tr>
	    <td class="c" width=35>Место</td>
  		<td class="c"><font color=#CDB5CD><?=((!isset($_POST['visible']) || $_POST['visible'] <= 1)?'Самые разрушительные бои':'Самые разрушительные групповые бои') ?></font></td>
  		<td class="c" width=45>Итог</td>
		<td class="c" width=85>Дата</td>
   	</tr>
<? if(count($parse['hall'])>0): $i=0; foreach($parse['hall'] AS $log): $i++; ?>
	<tr>
	    <th><?=$i ?></th>
  		<th><a href="?set=log&id=<?=$log['log'] ?>" target="_blank"><?=$log['title'] ?></a></th>
  		<th><? if($log['won']==0) echo'Н'; elseif($log['won']==1) echo'А'; else echo'О'; ?></th>
		<th><? if($parse['time'] == $log['time']): ?><font color="green"><? endif; ?><?=datezone("d.m H:i", $log['time']) ?><? if($parse['time'] == $log['time']): ?></font><? endif; ?></th>
   	</tr>
<? endforeach; ?>
		<? else: ?>
		<tr><th colspan="3">В этой вселенной еще не было крупных боев</th></tr>
<? endif; ?>
</table>