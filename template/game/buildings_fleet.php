<table align="center">
<tr>
	<td>
		<form action="?set=buildings&mode=fleet" method="post">
		<table width="700" align="center" style="border-spacing:0;">
<? if(count($parse['buildlist']) > 0): ?>
 <? $i = 0; foreach($parse['buildlist'] AS $build): $i++ ?>

<?=(($i%2 == 1) ? '<tr>' : '') ?>


<td class="j"><table width="350" style="border-spacing:0;"><tr><td class="l" width="120">
<a href=?set=infos&gid=<?=$build['i'] ?>>
<img border=0 src="<?=$dpath ?>gebaeude/<?=$build['i'] ?>.gif" alt='<?=$build['n'] ?>' align=top width=120 height=120 onmouseover="return overlib('<center><?=$build['desc'] ?></center>',LEFT,WIDTH,200,FGCOLOR,'#465673');" onmouseout="nd()"></a>
</td><th style="text-align:left;vertical-align:top;">
<a href=?set=infos&gid=<?=$build['i'] ?>><?=$build['n'] ?></a><br><b>Количество:</b> <u><?=$build['count'] ?></u><br>
<?=$build['time'] ?>
<? if($build['can_build']): ?>
	<br><br><center><input type=text name=fmenge[<?=$build['i'] ?>] alt='<?=$build['n'] ?>' size=5 maxlength=5 value=0></center>
	<br><a href=javascript:setMaximum(<?=$build['i'] ?>,<?=$build['max']?>);><b>Максимум:</b> <u><font color=green><?=$build['max']?></font></u></a>
<? endif; ?>
</th></tr><tr><td colspan='2' class='c'><?=$build['price']?></td>
</tr>
</table></td>

<?=(($i%2 == 0) ? '</tr>' : '') ?>

<? endforeach; ?>
<? if($i%2 == 1): ?>
	<th style="border-spacing:0;height:100%;width:100%">&nbsp;</th></tr>
<? endif; ?>
<? endif; ?>
		<tr>
			<td class="c" colspan=8 align="center"><input type="submit" value="Построить"></td>
		</tr>
		</table>
		</form>
	</td>
	</tr>
</table>