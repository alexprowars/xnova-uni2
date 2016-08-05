<? if($parse['type'] == 'metal'): ?>
<script type="text/javascript" >
function calcul() {
	var Cristal = document.forms['marchand'].elements['cristal'].value;
	var Deuterium = document.forms['marchand'].elements['deut'].value;

	Cristal   = Cristal * <?=$parse['mod_ma_res_a'] ?>;
	Deuterium = Deuterium * <?=$parse['mod_ma_res_b'] ?>;

	var Metal = Cristal + Deuterium;
	document.getElementById("metal").innerHTML = Metal;

	if (isNaN(document.forms['marchand'].elements['cristal'].value)) {
		document.getElementById("metal").innerHTML = "<?=$parse['mod_ma_nbre'] ?>";
	}
	if (isNaN(document.forms['marchand'].elements['deut'].value)) {
		document.getElementById("metal").innerHTML = "<?=$parse['mod_ma_nbre'] ?>";
	}
}
</script>
<br>
<center>
<form id="marchand" action="?set=marchand" method="post">
<input type="hidden" name="ress" value="metal">
<table width="569">
<tr>
	<td class="c" colspan="5"><b><?=$parse['mod_ma_buton'] ?></b></td>
</tr><tr>
	<th></th>
	<th></th>
	<th><?=$parse['mod_ma_cours'] ?></th>
</tr><tr>
	<th><?=$parse['Metal'] ?></th>
	<th><span id='metal'></span></th>
	<th><?=$parse['mod_ma_res'] ?></th>
</tr><tr>
	<th><?=$parse['Crystal'] ?></th>
	<th><input name="cristal" type="text" value="0" onkeyup="calcul()"/></th>
	<th><?=$parse['mod_ma_res_a'] ?></th>
</tr><tr>
	<th><?=$parse['Deuterium'] ?></th>
	<th><input name="deut" type="text" value="0" onkeyup="calcul()"/></th>
	<th><?=$parse['mod_ma_res_b'] ?></th>
</tr><tr>
	<th colspan="6"><input type="submit" value="<?=$parse['mod_ma_excha'] ?>"></th>
</tr><tr>
	<th colspan="6"><font color="red">Внимание! Стоимость обмена 1 кр.</font></th>
</tr>
</form>
</table>
</center>
<? elseif($parse['type'] == 'cristal'): ?>
<script type="text/javascript" >
function calcul() {
	var Metal = document.forms['marchand'].elements['metal'].value;
	var Deuterium = document.forms['marchand'].elements['deut'].value;

	Metal = Metal * <?=$parse['mod_ma_res_a'] ?>;
	Deuterium = Deuterium * <?=$parse['mod_ma_res_b'] ?>;

	var Cristal = Metal + Deuterium;
	document.getElementById("cristal").innerHTML=Cristal;

	if (isNaN(document.forms['marchand'].elements['metal'].value)) {
		document.getElementById("cristal").innerHTML="<?=$parse['mod_ma_nbre'] ?>";
	}
	if (isNaN(document.forms['marchand'].elements['deut'].value)) {
		document.getElementById("cristal").innerHTML="<?=$parse['mod_ma_nbre'] ?>";
	}
}
</script>
<br>
<center>
<form id="marchand" action="?set=marchand" method="post">
<input type="hidden" name="ress" value="cristal">
<table width="569">
<tr>
	<td class="c" colspan="5"><b><?=$parse['mod_ma_buton'] ?></b></td>
</tr><tr>
	<th></th>
	<th></th>
	<th><?=$parse['mod_ma_cours'] ?></th>
</tr><tr>
	<th><?=$parse['Crystal'] ?></th>
	<th><span id='cristal'></span></th>
	<th><?=$parse['mod_ma_res'] ?></th>
</tr><tr>
	<th><?=$parse['Metal'] ?></th>
	<th><input name="metal" type="text" value="0" onkeyup="calcul()"/></th>
	<th><?=$parse['mod_ma_res_a'] ?></th>
</tr><tr>
	<th><?=$parse['Deuterium'] ?></th>
	<th><input name="deut" type="text" value="0" onkeyup="calcul()"/></th>
	<th><?=$parse['mod_ma_res_b'] ?></th>
</tr><tr>
	<th colspan="6"><input type="submit" value="<?=$parse['mod_ma_excha'] ?>"></th>
</tr><tr>
	<th colspan="6"><font color="red">Внимание! Стоимость обмена 1 кр.</font></th>
</tr>
</table>
</form>
<? elseif($parse['type'] == 'deut'): ?>
<script type="text/javascript" >
function calcul() {
	var Metal   = document.forms['marchand'].elements['metal'].value;
	var Cristal = document.forms['marchand'].elements['cristal'].value;

	Metal   = Metal * <?=$parse['mod_ma_res_a'] ?>;
	Cristal = Cristal * <?=$parse['mod_ma_res_b'] ?>;

	var Deuterium = Metal + Cristal;
	document.getElementById("deut").innerHTML=Deuterium;

	if (isNaN(document.forms['marchand'].elements['metal'].value)) {
		document.getElementById("deut").innerHTML="<?=$parse['mod_ma_nbre'] ?>";
	}
	if (isNaN(document.forms['marchand'].elements['cristal'].value)) {
		document.getElementById("deut").innerHTML="<?=$parse['mod_ma_nbre'] ?>";
	}
}
</script>
<br>
<center>
<form id="marchand" action="?set=marchand" method="post">
<input type="hidden" name="ress" value="deuterium">
<table width="569">
<tr>
	<td class="c" colspan="5"><b><?=$parse['mod_ma_buton'] ?></b></td>
</tr><tr>
	<th></th>
	<th></th>
	<th><?=$parse['mod_ma_cours'] ?></th>
</tr><tr>
	<th><?=$parse['Deuterium'] ?></th>
	<th><span id='deut'></span></th>
	<th><?=$parse['mod_ma_res'] ?></th>
</tr><tr>
	<th><?=$parse['Metal'] ?></th>
	<th><input name="metal" type="text" value="0" onkeyup="calcul()"/></th>
	<th><?=$parse['mod_ma_res_a'] ?></th>
</tr><tr>
	<th><?=$parse['Crystal'] ?></th>
	<th><input name="cristal" type="text" value="0" onkeyup="calcul()"/></th>
	<th><?=$parse['mod_ma_res_b'] ?></th>
</tr><tr>
	<th colspan="6"><input type="submit" value="<?=$parse['mod_ma_excha'] ?>"></th>
</tr><tr>
	<th colspan="6"><font color="red">Внимание! Стоимость обмена 1 кр.</font></th>
</tr>
</table>
</form>
<? else: ?>

<br>
<form action="?set=marchand" method="post">
<input type="hidden" name="action" value="2">
<table width="700">
<tr>
	<td class="c" align="center"><b>Обмен сырья</b><td>
</tr><tr><th>
Вы можете вызвать межгалактического торговца для обмена ресурсов.<br>
<font color="red">Каждая операция обмена будет стоить вам 1 кредит.</font><br><br>
	Обменять сырьё &nbsp;<select name="choix"><option value="metal">Металл</option><option value="cristal">Кристалл</option><option value="deut">Дейтерий</option></select>
	&nbsp;&nbsp;(курс 2/1/0.5)<br><br>
	</th>
</tr>
<tr>
	<td class="c" align="center"><input type="submit" value="Обмен" /></td>
</tr>
</table>
</form>
<? endif; ?>    