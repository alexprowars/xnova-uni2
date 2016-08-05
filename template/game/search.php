<br>
 <form action="?set=search" method="post">

 <table width="700">
  <tr>
   <td class="c">Поиск по игре</td>
  </tr>
  <tr>
   <th>
    <select name="type">
     <option value="playername"<?=(($parse['type'] == "playername") ? " SELECTED" : "") ?>>Логин игрока</option>
     <option value="planetname"<?=(($parse['type'] == "planetname") ? " SELECTED" : "") ?>>Название планеты</option>
     <option value="allytag"<?=(($parse['type'] == "allytag") ? " SELECTED" : "") ?>>Аббревиатура альянса</option>
     <option value="allyname"<?=(($parse['type'] == "allyname") ? " SELECTED" : "") ?>>Название альянса</option>
    </select>
    &nbsp;&nbsp;
    <input type="text" name="searchtext" value="<?=$parse['searchtext'] ?>">
    &nbsp;&nbsp;

    <input type="submit" value="Поиск">
   </th>
  </tr>
</table>
 </form>
<? if ($parse['searchtext'] != ''): ?>
<? if(isset($parse['type']) && ($parse['type'] == 'playername' || $parse['type'] == 'planetname')): ?>
<table width="700">
	<tr>
	  <td class="c" width="120">Имя</td>
	  <td class="c" width="40">&nbsp;</td>
		<td class="c" width="20">&nbsp;</td>
	  <td class="c">Альянс</td>
	  <td class="c">Планета</td>
	  <td class="c" width="80">Координаты</td>
	  <td class="c" width="40">Место</td></tr>
	</tr>
<? if (count($parse['result']) > 0): ?>
<? foreach($parse['result'] AS $result): ?>
	<tr>
	  <th><?=$result['username'] ?></th>
	  <th><a href="?set=messages&mode=write&id=<?=$result['id'] ?>" alt="Написать сообщение" title="Написать сообщение"><img src="<?=$dpath ?>img/m.gif" alt="Написать сообщение" /></a>
		  &nbsp;<a href="?set=buddy&a=2&amp;u=<?=$result['id'] ?>" alt="Предложение подружиться"><img src="<?=$dpath ?>img/b.gif" alt="Предложение подружиться" title="Предложение подружиться"></a>
	</th><th><? if($result['race'] != 0): ?><img src="<?=$dpath ?>images/race<?=$result['race'] ?>.gif" width="16" height="16"><? else: ?>&nbsp;<? endif; ?>
	  </th>
	  <th><?=$result['ally_name'] ?></th>
	  <th><?=$result['planet_name'] ?></th>
	  <th><a href="?set=galaxy&mode=3&galaxy=<?=$result['g'] ?>&system=<?=$result['s'] ?>"><?=$result['g'] ?>:<?=$result['s'] ?>:<?=$result['p'] ?></a></th>
	  <th><a href="?set=stat&range=<?=$result['total_rank'] ?>"><?=$result['total_rank'] ?></a></th>
	</tr>
<? endforeach; ?>
<? else: ?>
    <tr><th colspan="6">Поиск не дал результатов</th></tr>
<? endif; ?>
            </table>
<? else: ?>

    <table width="700">
	<tr>
	  <td class="c">Аббревиатура</td>
	  <td class="c">Имя</td>
	  <td class="c">Члены</td>
	  <td class="c">Очки</td>
	</tr>
<? if (count($parse['result']) > 0): ?>
<? foreach($parse['result'] AS $result): ?>
        	<tr>
	  <th><a href="?set=alliance&mode=ainfo&a=<?=$result['id'] ?>"><?=$result['ally_tag'] ?></a></th>
	  <th><?=$result['ally_name'] ?></th>
	  <th><?=$result['ally_members'] ?></th>
	  <th><?=$result['total_points'] ?></th>
	</tr>
<? endforeach; ?>
<? else: ?>
    <tr><th colspan="6">Поиск не дал результатов</th></tr>
<? endif; ?>
</table>

<? endif; ?>

<? endif; ?>