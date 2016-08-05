<br>
<form action="?set=infos&gid=34" method="post">
<table border="1">
<tr>
	<th width=400><?=$parse[2] ?></th>
</tr>
</table>
<table border="1">
<tr>
	<th width=400>Флоты возле планеты</th>
</tr><tr>
	<th>
		<select name="jmpto">
		<?=$parse[0] ?>
		</select>
	</th>
</tr>
</table>
<br>
<table width="519">
<tr>
	<th colspan="2"><input value="Отправить <?=$parse[1] ?> дейтерия" name="send" type="submit"></th>
</tr>
</table>