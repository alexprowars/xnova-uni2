<script src="scripts/ed.js" type="text/javascript"></script>
<script src="scripts/smiles_v2.js" type="text/javascript"></script>
<form action="?set=notes" method=post>
  <?=$parse['inputs'] ?>
  <table width=651>
	<tr>
	  <td class=c colspan=2><?=$parse['TITLE'] ?></td>
	</tr>
	<tr>
	  <th>Приоритет:
		<select name=u>
		  <?=$parse['c_Options'] ?>
		</select>
	  </th>
	  <th>Тема:
		<input type="text" name="title" size="30" maxlength="30" value="<?=$parse['title'] ?>">
	  </th>
	</tr>
	<tr>
	  <th colspan="2" style="padding:0 0 0 0;">	<div id="editor"></div>
	<script type="text/javascript">edToolbar('text');</script>
	    <textarea name="text" id="text" style="width:646px;" rows="10"><?=$parse['text'] ?></textarea>
	  </th>
	</tr>
	<tr>
	  <td class="c" colspan="2">
		<span style="float:left;font-size:14px;"><a href="?set=notes">Назад</a></span>
		<input type="reset" value="<?=$parse['Reset'] ?>">
		<input type="submit" value="<?=$parse['Save'] ?>">
	  </td>
	</tr>
  </table>
<div id="showpanel" style="display:none">
<table align="center" width='651'>
<tr><td class="c" ><b>Предварительный просмотр</b></td></tr>
<tr><td class="b"><span id="showbox"></span></td></tr>
</table>
</div>
</form>
