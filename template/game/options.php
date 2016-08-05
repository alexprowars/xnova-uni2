<script src="scripts/ed.js" type="text/javascript"></script>
<script src="scripts/smiles_v2.js" type="text/javascript"></script>
<br>
<form action="?set=options&mode=change" method="post">
<table width="651">
<tr>
	<td class="c" colspan="2">Информация</td>
</tr><tr>
	<th width="50%">Логин</th>
	<th><? if($parse['opt_usern_datatime'] < (time()-86400)): ?><input name="db_character" size="20" value="<? endif; ?><?=$parse['opt_usern_data'] ?><? if($parse['opt_usern_datatime'] < (time()-86400)): ?>" type="text"><? endif; ?></th>
</tr>
<? if ($parse['password_vk'] != ''): ?>
<tr>
	<th>Текущий пароль</th>
	<th><?=$parse['password_vk'] ?></th>
</tr>
<? endif; ?>
<tr>
	<th>Старый пароль</th>
	<th><input name="db_password" size="20" value="" type="password"></th>
</tr><tr>
	<th>Новый пароль (мин. 8 Знаков)</th>
	<th><input name="newpass1" size="20" maxlength="40" type="password"></th>
</tr><tr>
	<th>Новый пароль (повтор)</th>
	<th><input name="newpass2" size="20" maxlength="40" type="password"></th>
</tr><tr>
	<th>Адрес e-mail</th>
	<th><?=$parse['opt_mail_data'] ?> <a href="?set=options&mode=changeemail">(сменить)</a></th>
</tr><tr>
	<th><a title="Номер ICQ">ICQ</a></th>
	<th><input name="icq" maxlength="10" size="20" value="<?=$parse['opt_icq_data'] ?>" type="text"></th>
</tr><tr>
	<th><a title="Пример: http://vkontakte.ru/id{??????}">vkontakte.ru ID</a></th>
	<th><input name="vkontakte" maxlength="15" size="20" value="<?=$parse['opt_vkontakte_data'] ?>" type="text"></th>
</tr><tr>
  <th>Пол</th>
  <th><select name="sex"><option value="M">мужской</option><option value="F" <?=(($parse['sex']==2)?' selected':'')?>>женский</option></select></th>
</tr><tr>
	<td class="c" colspan="2">Общие настройки</td>
</tr><tr>
	<th>Упорядочить планеты по:</th>
	<th>
		<select name="settings_sort" style='width:170px'>
		<?=$parse['opt_lst_ord_data'] ?>
		</select>
	</th>
</tr><tr>
	<th>Упорядочить по:</th>
	<th>
		<select name="settings_order" style='width:170px'>
		<?=$parse['opt_lst_cla_data'] ?>
		</select>
	</th>
</tr><tr>
	<th>Стандартное оформление</th>
	<th><input name="design"<?=$parse['opt_sskin_data'] ?> type="checkbox"></th>
</tr><tr>
	<th>Привязка сессии к IP</th>
	<th><input name="design"<?=$parse['opt_sec_data'] ?> type="checkbox"></th>
</tr><tr>
	<th>Участвовать в рекордах</th>
	<th><input name="records"<?=$parse['opt_record_data'] ?> type="checkbox"></th>
</tr><tr>
	<th>Использовать BB коды в сообщениях</th>
	<th><input name="bbcode"<?=$parse['opt_bbcode_data'] ?> type="checkbox"></th>
</tr><tr>
	<th>Включить AJAX навигацию</th>
	<th><input name="ajax"<?=$parse['opt_ajax_data'] ?> type="checkbox"></th>
</tr><tr>
	<th>Цвет чата</th>
	<th><select name='color' style='width:170px'><?=$parse['opt_lst_color_data'] ?></select></th>
</tr><tr>
	<th>Часовой пояс</th>
	<th><select name='timezone' style='width:170px'>
		<option value="-32"<?=(($parse['timezone']==(-32)) ? 'selected' : '')?>>-12</option>
		<option value="-30"<?=(($parse['timezone']==(-30)) ? 'selected' : '')?>>-11</option>
		<option value="-28"<?=(($parse['timezone']==(-28)) ? 'selected' : '')?>>-10</option>
		<option value="-26"<?=(($parse['timezone']==(-26)) ? 'selected' : '')?>>-9</option>
		<option value="-24"<?=(($parse['timezone']==(-24)) ? 'selected' : '')?>>-8</option>
		<option value="-22"<?=(($parse['timezone']==(-22)) ? 'selected' : '')?>>-7</option>
		<option value="-20"<?=(($parse['timezone']==(-20)) ? 'selected' : '')?>>-6</option>
		<option value="-18"<?=(($parse['timezone']==(-18)) ? 'selected' : '')?>>-5</option>
		<option value="-16"<?=(($parse['timezone']==(-16)) ? 'selected' : '')?>>-4</option>
		<option value="-14"<?=(($parse['timezone']==(-14)) ? 'selected' : '')?>>-3</option>
		<option value="-12"<?=(($parse['timezone']==(-12)) ? 'selected' : '')?>>-2</option>
		<option value="-10"<?=(($parse['timezone']==(-10)) ? 'selected' : '')?>>-1</option>
		<option value="-8"<?=(($parse['timezone']==(-8)) ? 'selected' : '')?>>0</option>
		<option value="-6"<?=(($parse['timezone']==(-6)) ? 'selected' : '')?>>+1</option>
		<option value="-4"<?=(($parse['timezone']==(-4)) ? 'selected' : '')?>>+2 (Украинское время)</option>
		<option value="-2"<?=(($parse['timezone']==(-2)) ? 'selected' : '')?>>+3</option>
		<option value="0"<?=(($parse['timezone']==0) ? 'selected' : '')?>>+4 (Московское время)</option>
		<option value="2"<?=(($parse['timezone']==2) ? 'selected' : '')?>>+5</option>
		<option value="4"<?=(($parse['timezone']==4) ? 'selected' : '')?>>+6 (Уфимское время)</option>
		<option value="6"<?=(($parse['timezone']==6) ? 'selected' : '')?>>+7</option>
		<option value="8"<?=(($parse['timezone']==8) ? 'selected' : '')?>>+8</option>
		<option value="10"<?=(($parse['timezone']==10) ? 'selected' : '')?>>+9</option>
		<option value="12"<?=(($parse['timezone']==12) ? 'selected' : '')?>>+10</option>
		<option value="14"<?=(($parse['timezone']==14) ? 'selected' : '')?>>+11</option>
		<option value="16"<?=(($parse['timezone']==16) ? 'selected' : '')?>>+12</option>
	</select></th>
</tr><tr>
	<th>Аватар</th>
	<th><?=$parse['avatar'] ?> <a href="?set=avatar">Выбрать аватар</a></th>
</tr><tr>
	<td class="c" colspan="2">Описание аккаунта</td>
</tr><tr>
	<th colspan="2" style="padding:0 0 0 0;">	<div id="editor"></div>
	<script type="text/javascript">edToolbar('text');</script>
	    <textarea name="text" id="text" style="width:646px;" rows="10"><?=preg_replace ('!<br.*>!iU' , "\n" ,  $parse['about']) ?></textarea>
		<div id="showpanel" style="display:none">
			<table align="center" width='650'>
			<tr><td class="c" ><b>Предварительный просмотр</b></td></tr>
			<tr><td class="b"><span id="showbox"></span></td></tr>
			</table>
		</div>
	</th>
</tr><tr>
	<td class="c" colspan="2">Режим отпуска / Удалить профиль</td>
</tr><tr>
	<th><a title="Режим отпуска нужен для защиты планет во время вашего отсутствия">Включить режим отпуска</a></th>
	<th><input name="urlaubs_modus"<?=$parse['opt_modev_data'] ?> type="checkbox" /></th>
</tr><tr>
	<th><a title="Профиль будет удалён через 7 дней">Удалить профиль</a></th>
	<th><input name="db_deaktjava"<?=$parse['opt_delac_data'] ?> type="checkbox" /></th>
</tr><tr>
	<th colspan="2"><input value="Сохранить изменения" type="submit"></th>
</tr>
</table>
</form>
	<form action="?set=options&mode=ld" method="post">
		<table width="519">
<tr>
	<td class="c" colspan="2">Добавление записи в личное дело</td>
</tr><tr>
	<th width="30%">Текст сообщения</th>
	<th><input name="text" size="30" value="" type="text" maxlength="250"></th>
</tr><tr>
	<th colspan="2"><input value="Записать" type="submit"></th>
</tr>
			</table>
		</form>
<? if (isset($_COOKIE['vkid'])): ?>
		<form action="?set=options&mode=vk" method="post">
		<table width="519">
<tr>
	<td class="c" colspan="2">Привязка игрового аккаунта к приложению</td>
</tr><tr>
<th colspan="2"><font color="red">Данное действие необходимо если вы регистрировались в игре не через сайт vkontakte.ru<br>Данное действие удаляет текущий аккаун безвозвратно!</font></th>
</tr><tr>
	<th width="30%">E-mail:</th>
	<th><input name="mail" size="30" value="" type="text"></th>
</tr><tr>
	<th width="30%">Пароль:</th>
	<th><input name="password" size="30" value="" type="password"></th>
</tr><tr>
	<th colspan="2"><input value="Применить" type="submit"></th>
</tr>
			</table>
		</form>
<? endif; ?>