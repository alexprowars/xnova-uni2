<script type="text/javascript" src="scripts/generate.js"></script>
<center>
<br/>
<h2><font size="+2">Регистрация в XNova Game</font><br>
</h2>
<form action="?set=reg" method="post">
<table width="500">
<tbody>
	  <tr>
	    <td colspan="2" class="c"><b>Форма регистрации</b></td>
</tr><tr>
	<th width="293">Логин</th>
    <th width="293"><input name="character" size="20" maxlength="20" type="text" onKeypress="
     if (event.keyCode==60 || event.keyCode==62) event.returnValue = false;
     if (event.which==60 || event.which==62) return false;"> <input type="button" value="Сгенерировать" onClick="character.value=profundity()" style="font-size: 10px; font-family: Verdana;"/></th>
</tr>
<tr>
  <th>Пароль</th>
  <th><input name="passwrd" size="20" maxlength="20" type="password" onKeypress="
     if (event.keyCode==60 || event.keyCode==62) event.returnValue = false;
     if (event.which==60 || event.which==62) return false;"></th>
</tr>
<tr>
  <th>E-Mail</th>
  <th><input name="email" size="20" maxlength="40" type="text" onKeypress="
     if (event.keyCode==60 || event.keyCode==62) event.returnValue = false;
     if (event.which==60 || event.which==62) return false;"></th>
</tr>
<tr>
  <th>Пол</th>
  <th><select name="sex">
		<option value="">неизвестный</option>
		<option value="M">мужской</option>
		<option value="F">женский</option>
		</select></th>
</tr>
<tr>
<th><img src="captcha.php"></th>
<th><input type="text" name="captcha" size="20" maxlength="20" /></th>
</tr>
<tr>
  <td height="20" colspan="2"></td>
  </tr>
<tr>
  <th colspan=2><input name="sogl" type="checkbox">
    Я принимаю <a href="?set=sogl" target="_blank">Пользовательское соглашение</a></th>
</tr>
	<tr>
  <th colspan=2><input name="rgt" type="checkbox">
    Я принимаю <a href="?set=agb" target="_blank">Законы игры</a></th>
</tr><tr>
  <th colspan=2><input name="submit" type="submit" value="Регистрация"></th>
</tr>
</table>
</form>
</center>