<br><table width="600">
<tbody>
<tr>
	<td class="c"><?=$parse['name'] ?></td>
</tr><tr>
	<th>
	<table>
	<tbody>
	<tr>
		<td><img src="<?=$dpath ?>gebaeude/<?=$parse['image'] ?>.gif" align="top" height="120" width="120"></td>
		<td><?=$parse['description'] ?></td>
	</tr>
	</tbody>
	</table>
	</th>
</tr><tr>
	<th>
		<center>
		<table border="1" width="100%">
		<tbody>
        <? if ($parse['image'] == 42): ?>
			<tr><td class="c">Уровень</td><td class="c">Дальность</td></tr>
			<? foreach($parse['table_data'] AS $data): ?>
				<tr><th><?=$data['build_lvl'] ?></th><th><?=$data['build_range'] ?></th></tr>
			<? endforeach; ?>
		<? elseif ($parse['image'] == 22 || $parse['image'] == 23 || $parse['image'] == 24): ?>
			<tr><td class="c">Уровень</td><td class="c">Вместимость</td></tr>
			<? foreach($parse['table_data'] AS $data): ?>
				<tr><th><?=$data['build_lvl'] ?></th><th><?=$data['build_range'] ?>k</th></tr>
			<? endforeach; ?>
        <? elseif ($parse['image'] != 4): ?>
			<tr><td class="c">Уровень</td><td class="c">Выработка</td><td class="c">Разница</td><td class="c">Потребление энергии</td><td class="c">Разница</td></tr>
			<? foreach($parse['table_data'] AS $data): ?>
				<tr><th><?=$data['build_lvl'] ?></th><th><?=$data['build_prod'] ?></th><th><?=$data['build_prod_diff'] ?></th><th><?=$data['build_need'] ?></th><th><?=$data['build_need_diff'] ?></th></tr>
			<? endforeach; ?>
        <? else: ?>
			<tr><td class="c">Уровень</td><td class="c">Выработка</td><td class="c">Разница</td></tr>
			<? foreach($parse['table_data'] AS $data): ?>
				<tr><th><?=$data['build_lvl'] ?></th><th><?=$data['build_prod'] ?></th><th><?=$data['build_prod_diff'] ?></th></tr>
			<? endforeach; ?>
        <? endif; ?>

		</tbody>
		</table>
		</center>
	</th>
</tr>
</tbody>
</table>