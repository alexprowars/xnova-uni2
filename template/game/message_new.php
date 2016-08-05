<script language="JavaScript" src="/scripts/smiles_v2.js"></script>
<script src="scripts/ed.js" type="text/javascript"></script>
<? if($msg): ?><?=$msg ?><? endif; ?>
<br>
<form action="?set=messages&mode=write&id=<?=$id ?>" method="post">
<table width="651">
<tr>
	<td class="c" colspan="2">Отправка сообщения</td>
</tr><tr>
	<th>Получатель: <input type="text" name="to" id="to" size="55" value="<?=$to ?>" /></th>
</tr><tr>
	<th style="padding:0 0 0 0;">	<div id="editor"></div>
	<script type="text/javascript">edToolbar('text');</script>
		<textarea name="text" id="text" style="width:646px;" rows="15" size="100" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) submit()"><?=$text ?></textarea></th>
</tr><tr>
	<th colspan="2"><input type="submit" value="Отправить"></th>
</tr>
</table>
<div id="showpanel" style="display:none">
<table align="center" width='651'>
<tr><td class="c" ><b>Предварительный просмотр</b></td></tr>
<tr><td class="b"><span id="showbox"></span></td></tr>
</table>
</div>
</form>