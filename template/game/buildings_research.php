<? if ($parse['noresearch']): ?><font color="#ff0000"><?=$parse['noresearch'] ?></font><? endif; ?>
<table align=center>
<tr>
	<td>
	<table width="700" align=center style="border-spacing:0;">

<? if (count($parse['technolist']) > 0): ?>
<? $i = 0; foreach($parse['technolist'] AS $research): $i++; ?>
<?=(($i%2 == 1) ? '<tr>' : '') ?>
<td class="j">
	<table width="350" style="border-spacing:0;"><tr>
	<td class="l" width="120">
		<a href="?set=infos&gid=<?=(($research['tech_id']>300) ? ($research['tech_id']-100) : $research['tech_id']) ?>"><img src="<?=$dpath ?>gebaeude/<?=(($research['tech_id']>300) ? ($research['tech_id']-100) : $research['tech_id']) ?>.gif" align="top" width="120" height="120"  onmouseover="return overlib('<center><?=$research['tech_descr'] ?></center>',LEFT,WIDTH,150,FGCOLOR,'#465673');" onmouseout="nd()"></a>
	</td>
	<th style="text-align:left;vertical-align:top;">
		<a href="?set=infos&gid=<?=(($research['tech_id']>300) ? ($research['tech_id']-100) : $research['tech_id']) ?>"><?=$research['tech_name'] ?></a><br>
		<b>Уровень:</b> <?=$research['tech_level'] ?><br>
		<?=$research['search_time'] ?>

        <? if(isset($research['add'])): ?>
        <br><br>Бонусы:<br><?=$research['add'] ?>
                    <? endif; ?>
	</th>
	</tr><tr>
	<td colspan="2" class="c" align="center"><?=$research['tech_price'] ?></td>
	</tr><tr>
	<td colspan="2" class="k" align="center" height="30">
        <? if (is_array($research['tech_link'])): ?>
        <div id="brp" class="z"></div>
<script   type="text/javascript">
v = new Date();
var brp = document.getElementById('brp');
function t(){
	n  = new Date();
	ss = <?=$research['tech_link']['tech_time'] ?>;
	s  = ss - Math.round( (n.getTime() - v.getTime()) / 1000);
	m  = 0;
	h  = 0;
	if ( s < 0 ) {
		brp.innerHTML = '<?=$research['tech_link']['ready'] ?><br><a href=?set=buildings&mode=<?=$parse['mode'] ?>&cp=<?=$research['tech_link']['tech_home'] ?>>продолжить...</a>';
	} else {
		if ( s > 59 ) { m = Math.floor( s / 60 ); s = s - m * 60; }
		if ( m > 59 ) { h = Math.floor( m / 60 ); m = m - h * 60; }
		if ( s < 10 ) { s = "0" + s }
		if ( m < 10 ) { m = "0" + m }
		brp.innerHTML = h + ':' + m + ':' + s + '<br><a href=?set=buildings&mode=<?=$parse['mode'] ?>&cmd=cancel&tech=<?=$research['tech_link']['tech_id'] ?>>Отменить<?=$research['tech_link']['tech_name'] ?></a>';
	}
	window.setTimeout("t();",999);
}
window.onload=t;
</script>
        <? else: ?>
        <?=$research['tech_link'] ?>
        <? endif; ?>
    </td>
	</tr></table>
</td>
<?=(($i%2 == 0) ? '</tr>' : '') ?>
<? endforeach; ?>
<? if($i%2 == 1): ?>
	<th style="border-spacing:0;height:100%;width:100%">&nbsp;</th></tr>
<? endif; ?>
<? endif; ?>
	</table>
	</td>
</tr>
</table>