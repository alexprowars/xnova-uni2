<script type="text/javascript" src="/scripts/smiles_v2.js"></script>
<style>.image{max-width:556px !important}</style>
<br />
<table width="490">
<tbody>
<tr>
<td colspan="3" class="c"><b>Информация об игроке</b></td>
</tr>
<tr>
<th>
<table width="490">
<tr>
<td width="105" height="128" class="c" style="background-image:url(<?=$parse['avatar'] ?>); margin:0; padding:5px; padding-bottom:1px; background-repeat:no-repeat; background-position:top; vertical-align:bottom;">
<? if($parse['ingame']): ?>
<a href="?set=messages&amp;mode=write&amp;id=<?=$parse['id'] ?>"><img src="<?=$dpath ?>img/m.gif" alt="Отправить сообщение" title="Отправить сообщение" border="0"></a>&nbsp;
<a href="?set=buddy&amp;a=2&amp;u=<?=$parse['id'] ?>"><img src="<?=$dpath ?>img/b.gif" alt="Добавить в друзья" title="Добавить в друзья" border=0></a>
<? else: ?>&nbsp;<? endif; ?>
</td>
<td valign="top">
<table width="100%">
	<tr>
	<td width="30%">Логин:</td><td><?=$parse['username'] ?></td>
	</tr><tr>
	<td>Планета</td><td><a href="?set=galaxy&mode=3&galaxy=<?=$parse['galaxy'] ?>&system=<?=$parse['system'] ?>" style="font-weight:normal"><?=$parse['userplanet'] ?> [<?=$parse['galaxy'] ?>:<?=$parse['system'] ?>:<?=$parse['planet'] ?>]</a></td>
	</tr><tr>
	<td>Альянс:</td><td><?=$parse['ally_name'] ?></td>
	</tr><tr>
	<td>Пол:</td><td><?=$parse['sex'] ?></td>
	</tr><tr>
	<td>ICQ:</td><td><?=$parse['icq'] ?></td>
	</tr><tr>
	<td>vkontakte.ru:</td><td><?=$parse['vkontakte'] ?></td>
	</tr>
</table>

</td>
<td width="40">
<? if($parse['race'] != 0): ?><img src="<?=$dpath ?>images/race<?=$parse['race'] ?>.gif"><? else: ?>&nbsp;<? endif; ?>
</td>
<td width="70">
<img src="/images/ranks/m<?=$parse['m'] ?>.png" alt="Промышленная отрасль" title="Промышленная отрасль"><br>
<img src="/images/ranks/f<?=$parse['f'] ?>.png" alt="Военная отрасль" title="Военная отрасль">
</td>
</tr>

</table>
	<table width="100%">
	<tr><td colspan="3" class="c" width="100%">Статистика игры</td></tr>
	<tr>
	<td class="c" width="30%">&nbsp;</td>
	<td class="c" width="35%">Очки</td>
	<td class="c" width="35%">Место</td>
	</tr>
	<tr>
	<td class="c">Постройки</td>
	<th><?=$parse['build_points'] ?></th>
	<th><?=$parse['build_rank'] ?></th>
	</tr>
	<tr>
	<td class="c">Иследования</td>
	<th><?=$parse['tech_points'] ?></th>
	<th><?=$parse['tech_rank'] ?></th>
	</tr>
	<tr>
	<td class="c">Флот</td>
	<th><?=$parse['fleet_points'] ?></th>
	<th><?=$parse['fleet_rank'] ?></th>
	</tr>
	<tr>
	<td class="c">Оборона</td>
	<th><?=$parse['defs_points'] ?></th>
	<th><?=$parse['defs_rank'] ?></th>
	</tr>
	<tr>
	<td class="c">Всего</td>
	<th><?=$parse['total_points'] ?></th>
	<th><?=$parse['total_rank'] ?></th>
	</tr>
</table>
<table width="100%">
<tbody>
<tr><td colspan="3" class="c" width="100%">Статистика боёв</td></tr>
<tr>
<td class="c" width="30%">&nbsp;</td>
<td class="c" width="35%"><b>Сумма</b></td>
<td class="c" width="35%" align="right"><b>Процент</b></td>
</tr>
<tr>
<td class="c">Победы</td>
<th><b><?=$parse['wons'] ?></b></th>
<th align="right"><b><?=$parse['siegprozent'] ?> %</b></th>
</tr>
<tr>
<td class="c">Поражения</td>
<th><b><?=$parse['loos'] ?></b></th>
<th align="right"><b><?=$parse['loosprozent'] ?> %</b></th>
</tr>
<tr>
<td class="c">Всего вылетов</td>
<th><b><?=$parse['total'] ?></b></th>
<th align="right"><b><?=$parse['totalprozent'] ?> %</b></th>
</tr>
</tbody>
</table>
</tr>
<? if($parse['about'] != ''): ?>
<tr>
<th class="b">
	<span id="m100500"></span>
	<script>Text('<?=preg_replace("/(\r\n)/u", "<br>", stripslashes($parse['about'])) ?>', 'm100500');</script>
</th>
</tr>
<? endif; ?>
</tbody>
</table>
