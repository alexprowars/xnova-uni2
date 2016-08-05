<? if($vk==1): ?>
<script type="text/javascript">
window.onload = (function() {
	if (parent && parent != window) {
		VK.init(function() {
			//VK.loadParams(document.location.href);
			//VK.callMethod("setLocation", document.location.search);
		});
	}
});
</script>
<? else: ?>
<script type="text/javascript">
if (parent && parent != window && !getCookie('vkid')) {
	//top.location = 'http://vkontakte.ru/app1798249';
}
</script>
<? endif; ?>
<div class="contentBoxBody"><div id="boxBG"><div id="box"><div id="game_menu"></div><script>$('#game_menu').html(PrintMenu(<?=$mess ?>, <?=$vk ?>));</script>
<table width="100%" style="margin-top: 5px;">
<tr>
<? if($vk == 1): ?>
<td>
<table width="100%" class="table_menu"><tr><td width="157">
<ul id="menu_links">
	<li class="stepdown"><a href="?set=overview" id="link_overview" class="blm<?=(($set=='' || $set=='overview')?'_check':'')?>">Обзор</a></li>
	<li class="stepdown"><a href="?set=imperium" id="link_" class="blm<?=(($set=='imperium')?'_check':'')?>">Империя</a></li>
	<li class="stepdown"><a href="?set=galaxy&amp;mode=0" id="link_" class="blm<?=(($set=='galaxy')?'_check':'')?>">Галактика</a></li>
<li class="stepdown"><a href="?set=alliance" id="link_" class="blm<?=(($set=='alliance')?'_check':'')?>">Альянс</a></li>
</ul>
</td>
<td width="157">
<ul id="menu_links">
	<li class="stepdown"><a href="?set=fleet" id="link_" class="blm<?=(($set=='fleet')?'_check':'')?>">Флот</a></li>
	<li class="stepdown"><a href="?set=buildings" id="link_" class="blm<?=(($set=='buildings')?'_check':'')?>">Постройки</a></li>
	<li class="stepdown"><a href="?set=buildings&amp;mode=research" id="link_" class="blm<?=(($set=='buildingsresearch')?'_check':'')?>">Исследования</a></li>
<li class="stepdown"><a href="?set=buildings&amp;mode=research_fleet" id="link_" class="blm<?=(($set=='buildingsresearch_fleet')?'_check':'')?>">Фабрика</a></li>
</ul>
</td>
<td width="157">
<ul id="menu_links">
	<li class="stepdown"><a href="?set=buildings&amp;mode=fleet" id="link_" class="blm<?=(($set=='buildingsfleet')?'_check':'')?>">Верфь</a></li>
	<li class="stepdown"><a href="?set=buildings&amp;mode=defense" id="link_" class="blm<?=(($set=='buildingsdefense')?'_check':'')?>">Оборона</a></li>
	<li class="stepdown"><a href="?set=resources" id="link_" class="blm<?=(($set=='resources')?'_check':'')?>">Сырьё</a></li>
	<li class="stepdown"><a href="?set=marchand" id="link_" class="blm<?=(($set=='marchand')?'_check':'')?>">Рынок</a></li>
</ul>
</td>
	<td width="157">
<ul id="menu_links">
	<li class="stepdown"><a href="?set=officier" id="link_" class="blm<?=(($set=='officier')?'_check':'')?>">Офицеры</a></li>
	<li class="stepdown"><a href="?set=buddy" id="link_" class="blm<?=(($set=='buddy')?'_check':'')?>">Друзья</a></li>
<li class="stepdown"><a href="?set=notes" id="link_" class="blm<?=(($set=='notes')?'_check':'')?>">Заметки</a></li>
<li class="stepdown"><a href="?set=logs" id="link_" class="blm<?=(($set=='logs')?'_check':'')?>">Журнал</a></li></ul>
</td>
	<td width="157">
<ul id="menu_links">
<li class="stepdown"><a href="?set=records" id="link_" class="blm<?=(($set=='records')?'_check':'')?>">Рекорды</a></li>
<li class="stepdown"><a href="?set=hall" id="link_" class="blm<?=(($set=='hall')?'_check':'')?>">Зал славы</a></li>
<li class="stepdown"><a href="?set=chat" id="link_"  class="blm<?=(($set=='chat')?'_check':'')?>">Чат</a></li>
<li class="stepdown"><a href="?set=log" id="link_" class="blm<?=(($set=='log')?'_check':'')?>">Логовница</a></li>
</td>
</tr></table>
</td></tr><tr>
<? else: ?>
<td width="170" height="100%" valign="top">
<table width="157" cellpadding="0" cellspacing="0"><tr><td style="width:100%;height:22px;background:url(<?=$dpath ?>img/user_top.gif)"></td></tr></table>
<ul id="menu_links">
<? if (!$admin): ?>
<li class="stepdown"><a id="link_overview" href="?set=overview" class="blm<?=(($set=='' || $set=='overview')?'_check':'')?>">Обзор</a></li>
<li class="stepdown"><a id="link_imperium" href="?set=imperium" class="blm<?=(($set=='imperium')?'_check':'')?>">Империя</a></li>
<li class="stepdown"><a id="link_galaxy" href="?set=galaxy&amp;mode=0" class="blm<?=(($set=='galaxy')?'_check':'')?>">Галактика</a></li>
<li class="stepdown"><a id="link_fleet" href="?set=fleet" class="blm<?=(($set=='fleet')?'_check':'')?>">Флот</a></li>
<li class="stepdown"><a id="link_buildings" href="?set=buildings" class="blm<?=(($set=='buildings')?'_check':'')?>">Постройки</a></li>
<li class="stepdown"><a id="link_buildingsresearch" href="?set=buildings&amp;mode=research" class="blm<?=(($set=='buildingsresearch')?'_check':'')?>">Исследования</a></li>
<li class="stepdown"><a id="link_buildingsresearch_fleet" href="?set=buildings&amp;mode=research_fleet" class="blm<?=(($set=='buildingsresearch_fleet')?'_check':'')?>">Фабрика</a></li>
<li class="stepdown"><a id="link_buildingsfleet" href="?set=buildings&amp;mode=fleet" class="blm<?=(($set=='buildingsfleet')?'_check':'')?>">Верфь</a></li>
<li class="stepdown"><a id="link_buildingsdefense" href="?set=buildings&amp;mode=defense" class="blm<?=(($set=='buildingsdefense')?'_check':'')?>">Оборона</a></li>
<li class="stepdown"><a id="link_resources" href="?set=resources" class="blm<?=(($set=='resources')?'_check':'')?>">Сырьё</a></li>
<li class="stepdown"><a id="link_marchand" href="?set=marchand" class="blm<?=(($set=='marchand')?'_check':'')?>">Рынок</a></li>
<li class="stepdown"><a id="link_officier" href="?set=officier" class="blm<?=(($set=='officier')?'_check':'')?>">Офицеры</a></li>
<li class="stepdown"><a id="link_alliance" href="?set=alliance" class="blm<?=(($set=='alliance')?'_check':'')?>">Альянс</a></li>
<li class="stepdown"><a id="link_buddy" href="?set=buddy" class="blm<?=(($set=='buddy')?'_check':'')?>">Друзья</a></li>
<li class="stepdown"><a id="link_notes" href="?set=notes" class="blm<?=(($set=='notes')?'_check':'')?>">Заметки</a></li>
<li class="stepdown"><a id="link_logs" href="?set=logs" class="blm<?=(($set=='logs')?'_check':'')?>">Журнал</a></li>
<li class="stepdown"><a id="link_records" href="?set=records" class="blm<?=(($set=='records')?'_check':'')?>">Рекорды</a></li>
<li class="stepdown"><a id="link_hall" href="?set=hall" class="blm<?=(($set=='hall')?'_check':'')?>">Зал славы</a></li>
<li class="stepdown"><a id="link_chat" href="?set=chat" class="blm<?=(($set=='chat')?'_check':'')?>">Чат</a></li>
<li class="stepdown"><a id="link_banned" href="?set=banned" class="blm<?=(($set=='banned')?'_check':'')?>">Бан-лист</a></li>
<li class="stepdown"><a id="link_log" href="?set=log" class="blm<?=(($set=='log')?'_check':'')?>">Логовница</a></li>
<? if($adminlevel > 0): ?>
<li class="stepdown"><a onclick="this.href='?set=admin'" class="blm">Администрирование</a></li>
<? endif; ?>
<? elseif($adminlevel == 1): ?>
<li class="stepdown"><a href="?set=admin&mode=overview" class="blm">Обзор</a></li>
<li class="stepdown"><a href="?set=admin&mode=support" class="blm">Техподдержка</a></li>
<li class="stepdown"><a href="?set=admin&mode=paneladmina" class="blm">Поиск игрока</a></li>
<li class="stepdown"><a href="?set=admin&mode=banned" class="blm">Забанить</a></li>
<li class="stepdown"><a href="?set=overview" class="blm">В игру</a></li>
<? elseif($adminlevel == 2): ?>
<li class="stepdown"><a href="?set=admin&mode=overview" class="blm">Обзор</a></li>
<li class="stepdown"><a href="?set=admin&mode=support" class="blm">Техподдержка</a></li>
<li class="stepdown"><a href="?set=admin&mode=paneladmina" class="blm">Поиск игрока</a></li>
<li class="stepdown"><a href="?set=admin&mode=activeplanet"class="blm">Активные планеты</a></li>
<li class="stepdown"><a href="?set=admin&mode=moonlist" class="blm">Список лун</a></li>
<li class="stepdown"><a href="?set=admin&mode=alliancelist" class="blm">Список альянсов</a></li>
<li class="stepdown"><a href="?set=admin&mode=banned" class="blm">Забанить</a></li>
<li class="stepdown"><a href="?set=admin&mode=unbanned" class="blm">Разбанить</a></li>
<li class="stepdown"><a href="?set=admin&mode=messall" class="blm">Общее сообщение</a></li>
<li class="stepdown"><a href="?set=admin&mode=errors" class="blm">Ошибки SQL</a></li>
<li class="stepdown"><a href="?set=overview" class="blm">В игру</a></li>
<? else: ?>
<li class="stepdown"><a href="?set=admin&mode=overview" class="blm">Обзор</a></li>
<li class="stepdown"><a href="?set=admin&mode=support" class="blm">Техподдержка</a></li>
<li class="stepdown"><a href="?set=admin&mode=server" class="blm">Информация</a></li>
<li class="stepdown"><a href="?set=admin&mode=settings" class="blm">Настройки</a></li>
<li class="stepdown"><a href="?set=admin&mode=userlist" class="blm">Список игроков</a></li>
<li class="stepdown"><a href="?set=admin&mode=paneladmina" class="blm">Поиск игрока</a></li>
<li class="stepdown"><a href="?set=admin&mode=planetlist" class="blm">Список планет</a></li>
<li class="stepdown"><a href="?set=admin&mode=activeplanet"class="blm">Активные планеты</a></li>
<li class="stepdown"><a href="?set=admin&mode=moonlist" class="blm">Список лун</a></li>
<li class="stepdown"><a href="?set=admin&mode=flyfleettable" class="blm">Флоты в полёте</a></li>
<li class="stepdown"><a href="?set=admin&mode=alliancelist" class="blm">Список альянсов</a></li>
<li class="stepdown"><a href="?set=admin&mode=banned" class="blm">Забанить</a></li>
<li class="stepdown"><a href="?set=admin&mode=unbanned" class="blm">Разбанить</a></li>
<li class="stepdown"><a href="?set=admin&mode=md5changepass" class="blm">Сменить пароль</a></li>
<li class="stepdown"><a href="?set=admin&mode=email" class="blm">Сменить email</a></li>
<li class="stepdown"><a href="?set=admin&mode=messagelist" class="blm">Список сообщений</a></li>
<li class="stepdown"><a href="?set=admin&mode=messall" class="blm">Общее сообщение</a></li>
<li class="stepdown"><a href="?set=admin&mode=errors" class="blm">Ошибки SQL</a></li>
<li class="stepdown"><a onclick="this.href='?set=overview'" class="blm">В игру</a></li>
<? endif; ?>
</td>
<? endif; ?>
<td valign='top' align='center'>
<? if($vk==1): ?>
<div class="vk_content">
<? endif; ?>
<div id="gamediv">
<center>
