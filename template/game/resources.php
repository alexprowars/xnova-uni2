<br>

<form action="?set=resources" method="post">

<table width="700">
	<tr>
	  <td class="c" align="center">Фактор продукции</td>
	  <th><?=$parse['production_level'] ?></th>
	  <th width="350">
		<div style="border: 1px solid rgb(153, 153, 255); width: 350px;">
		<div id="prodBar" style="background-color: <?=$parse['production_level_barcolor'] ?>; width: <?=$parse['production_level_bar'] ?>px;">
		&nbsp;
		</div>
		</div>
	  </th>
	</tr>
	<tr><td class="c" align="center"><a href="?set=infos&gid=113">Энергетическая технология</a></td><th><?=$parse['et'] ?> ур.</th>
	</tr>
</table>
<br>

<table width="700">
<tbody>
	<tr>
	  <td class="c" colspan="8"><?=$parse['Production_of_resources_in_the_planet'] ?></td>
	</tr>
	<tr>
	  <th width="200"></th>
	  <th>Ур.</th>
      <th>Бонус</th>
	  <th>Металл</th>
	  <th>Кристалл</th>
	  <th>Дейтерий</th>
	  <th>Энергия</th>
	  <th width="100">КПД</th>
	</tr>
	<tr>
	  <th align="left" style="text-align:left;">&nbsp;Естесственное производство</th>
	  <td class="k">-</td>
      <td class="k">-</td>
	  <td class="k"><?=$parse['metal_basic_income'] ?></td>
	  <td class="k"><?=$parse['crystal_basic_income'] ?></td>
	  <td class="k"><?=$parse['deuterium_basic_income'] ?></td>
	  <td class="k"><?=$parse['energy_basic_income'] ?></td>
	  <td class="k">100%</td>
	</tr>
<? foreach($parse['resource_row'] as $res): ?>
<tr>
	<th height="22" align="left" style="text-align:left;">&nbsp;<?=$res['type'] ?></th>
	<th><font color="#ffffff"><?=$res['level_type'] ?></font></th>
	<th><font color="#ffffff"><?=$res['bonus'] ?>%</font></th>
	<th><font color="#ffffff"><?=$res['metal_type'] ?></font></th>
	<th><font color="#ffffff"><?=$res['crystal_type'] ?></font></th>
	<th><font color="#ffffff"><?=$res['deuterium_type'] ?></font></th>
	<th><font color="#ffffff"><?=$res['energy_type'] ?></font></th>
	<th>
		<select name="<?=$res['name'] ?>" size="1">
		<?=$res['option'] ?>
		</select>
	</th>
</tr>
<? endforeach; ?>
	<tr>
	</tr>
	<tr>
	  <th colspan="2">Вместимость:</th>
      <th><?=$parse['bonus_h'] ?>%</th>
	  <td class="k"><?=$parse['metal_max'] ?></td>
	  <td class="k"><?=$parse['crystal_max'] ?></td>
	  <td class="k"><?=$parse['deuterium_max'] ?></td>
	  <td class="k"><font color="#00ff00"><?=$parse['energy_max'] ?></font></td>
	  <td class="k"><input name="action" value="Пересчитать" type="submit"></td>
	</tr>
	<tr>
	  <th colspan="3">Сумма:</th>
	  <td class="k"><?=$parse['metal_total'] ?></td>
	  <td class="k"><?=$parse['crystal_total'] ?></td>
	  <td class="k"><?=$parse['deuterium_total'] ?></td>
	  <td class="k"><?=$parse['energy_total'] ?></td>
	</tr>
</tbody>
</table>

<br>
<table width="700">
<tbody>
	<tr>
	  <td class="c" colspan="5">Информация о производстве</td>
	</tr>
	<tr>
	  <th width="16%">&nbsp;</th>
    <th width="21%">Час</th>
	  <th width="21%">День</th>
	  <th width="21%">Неделя</th>
	  <th width="21%">Месяц</th>
	</tr>
	<tr>
	  <th>Металл</th>
    <th><?=$parse['metal_total'] ?></th>
	  <th><?=$parse['daily_metal'] ?></th>
	  <th><?=$parse['weekly_metal'] ?></th>
	  <th><?=$parse['monthly_metal'] ?></th>
	</tr>
	<tr>
	  <th>Кристалл</th>
    <th><?=$parse['crystal_total'] ?></th>
	  <th><?=$parse['daily_crystal'] ?></th>
	  <th><?=$parse['weekly_crystal'] ?></th>
	  <th><?=$parse['monthly_crystal'] ?></th>
	</tr>
	<tr>
	  <th>Дейтерий</th>
    <th><?=$parse['deuterium_total'] ?></th>
	  <th><?=$parse['daily_deuterium'] ?></th>
	  <th><?=$parse['weekly_deuterium'] ?></th>
	  <th><?=$parse['monthly_deuterium'] ?></th>
	</tr>
</tbody>
</table>

<br>
<table width="700">
<tbody>
	<tr>
	  <td class="c" colspan="5">Управление производством</td>
	</tr>
	<tr><th width="50%"><a href="?set=resources&production_full=1"><input value="Включить на всех планетах" type="button"></a></th><th><a href="?set=resources&production_empty=1"><input value="Выключить на всех планетах" type="button"></a></th></tr>
</tbody>
</table>
<br>

<table width="700">
<tbody>
	<tr>
	  <td class="c" colspan="3">Статус хранилища</td>
	</tr>
	<tr>
	  <th width="200">Металл</th>
	  <th width="100"><?=$parse['metal_storage'] ?>%</th>
	  <th>
		<div style="border: 1px solid rgb(153, 153, 255); width: 425px;">
		<div id="AlmMBar" style="background-color: <?=$parse['metal_storage_barcolor'] ?>; width: <?=$parse['metal_storage_bar'] ?>px;">
		&nbsp;
		</div>
		</div>
	</th>
	</tr>
	<tr>
	  <th>Кристалл</th>
	  <th><?=$parse['crystal_storage'] ?>%</th>
	  <th width="250">
		<div style="border: 1px solid rgb(153, 153, 255); width: 425px;">
		<div id="AlmCBar" style="background-color: <?=$parse['crystal_storage_barcolor'] ?>; width: <?=$parse['crystal_storage_bar'] ?>px; opacity: 0.98;">
		&nbsp;
		</div>
		</div>
	  </th>
	</tr>
	<tr>
	  <th>Дейтерий</th>
	  <th><?=$parse['deuterium_storage'] ?>%</th>
	  <th width="250">
		<div style="border: 1px solid rgb(153, 153, 255); width: 425px;">
		<div id="AlmDBar" style="background-color: <?=$parse['deuterium_storage_barcolor'] ?>; width: <?=$parse['deuterium_storage_bar'] ?>px;">
		&nbsp;
		</div>
		</div>
	  </th>
	</tr>
</tbody>
</table>
</form>
<table width="700">
<tbody>
	<tr>
	  <td class="c" colspan="5">Покупка ресурсов (8 ч. выработка ресурсов)</td>
	</tr>
	<tr><th width="30%"><? if($parse['merchand'] < time()): ?><a href="?set=resources&buy=1">Купить за 10 кр.</a><? else: ?>Через <?=ceil(($parse['merchand']-time())/60)?> минут<? endif; ?></th><th>Вы можете купить: <?=$parse['buy_metal'] ?> металла, <?=$parse['buy_crystal'] ?> кристалла, <?=$parse['buy_deuterium'] ?> дейтерия</th></tr>
</tbody>
</table>
