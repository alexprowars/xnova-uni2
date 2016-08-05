<table width="710">
<? foreach($parse AS $list): ?>
    <? if (!isset($list['required_list'])): ?>
    <tr>
	<td class="c"><?=$list['tt_name'] ?></td>
	<td class="c">Требования</td>
    </tr>        
    <? else: ?>
	<tr>
	<th class="l" width="40%" style="vertical-align:bottom;">
	<table width="100%">
	<tr>
		<td style="text-align:left;"><a href="?set=infos&gid=<?=$list['tt_info'] ?>"><?=$list['tt_name'] ?></a></td>
		<? if($list['required_list'] != ''): ?>
		<td style="text-align:right;"><a href="?set=techtree&id=<?=$list['tt_info'] ?>">[i]</a></td>
		<? endif; ?>
	</tr>
	</table>
	</th>
	<th class="l" width="60%">
	<table width="100%">
	<tr>
		<td style="background-color: transparent;" align="left"><?=$list['required_list'] ?></td>
	</tr>
	</table>
	</th>
    </tr>
    <? endif; ?>
<? endforeach; ?>
</table>