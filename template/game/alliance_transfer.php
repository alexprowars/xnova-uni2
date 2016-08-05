<table width="520">
	<tr>
	  <td class="c" colspan="8">Передача альянса</td>
	</tr>
    
<form action="?set=alliance&mode=admin&edit=give&id=<?=$parse['id'] ?>" method="POST">
    <tr>
        <th colspan="3">Передать альянс игроку:</th>
        <th><select name="newleader"><?=$parse['righthand'] ?></select></th>
        <th colspan="3"><input type="submit" value="Передача"></th>
    </tr>
</form>

	<tr>
	  <td class="c" colspan="8"><a href="?set=alliance&mode=admin&edit=ally">назад</a></td>
	</tr>
</table>