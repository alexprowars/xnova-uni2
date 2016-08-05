<tr>
	<td class="c" width="30">Место</td>
	<td class="c" width="30">+/-</td>
	<td class="c" width="25%">Игрок</td>
	<td class="c" width="18">&nbsp;</td>
	<? if(isset($user_id)): ?><td class="c" width="18">&nbsp;</td><? endif; ?>
	<td class="c" width="35%">Альянс</td>
	<td class="c" width="80">Очки</td>
</tr>
<? foreach($stat AS $s): ?>
<tr>
	<th><?=$s['player_rank'] ?></th>
	<th><?=$s['player_rankplus'] ?></th>
	<th><?=$s['player_name'] ?></th>
	<th><? if($s['player_race'] != 0): ?><img src="<?=$dpath ?>images/race<?=$s['player_race'] ?>.gif" width="16" height="16"><? else: ?>&nbsp;<? endif; ?></th>
	<? if(isset($user_id)): ?><th><?=$s['player_mes'] ?></th><? endif; ?>
	<th><?=$s['player_alliance'] ?></th>
	<th><?=$s['player_points'] ?></th>
</tr>
<? endforeach; ?>
</table>