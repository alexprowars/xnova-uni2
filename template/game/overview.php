<table><tr><td valign="top">
<table width="700">
<? if($parse['bonus']): ?>


<script>
<? if($parse['vk']): ?>
var title = new Array();
title[0] = "XNova – космическая стратегия в реальном времени http://vkontakte.ru/app1798249";
title[1] = "XNova - отличная космическая войнушка http://vkontakte.ru/app1798249";
title[2] = "XNova - интересная игра, приглашаю стать моим союзником. http://vkontakte.ru/app1798249";
title[3] = "Отстраивай планету и повышай свой рейтинг - http://vkontakte.ru/app1798249";
title[4] = "Ищу союзников, для серьезных дел. http://vkontakte.ru/app1798249";
title[5] = "Игра затягивает! Советую! http://vkontakte.ru/app1798249";
title[6] = "Друзья, помогите мне с победой в http://vkontakte.ru/app1798249 Спасибо!";
title[7] = "Пришло время воевать! http://vkontakte.ru/app1798249";

var img = new Array();
img[0] = "photo7611407_223935301";
img[1] = "photo7611407_223935302";
img[2] = "photo7611407_223935303";
img[3] = "photo7611407_223935304";
img[4] = "photo7611407_223935305";
img[5] = "photo7611407_223935307";

var imgout  = img[Math.round(Math.random()*5)];
var textout = title[Math.round(Math.random()*7)];

function bonus() {
	VK.api('wall.post',{ message: textout, attachment: imgout },function(data) {
		setTimeout('location.replace("?set=overview&mode=bonus")', 200);
	});
}
<? else: ?>
function bonus() {
	setTimeout('location.replace("?set=overview&mode=bonus")', 200);
}
<? endif; ?>
	
</script>

<tr><td class="c" colspan="3">Ежедневный бонус</td></tr>
<tr><th colspan="3">
Сейчас вы можете получить по <b><?=($parse['bonus_multi']*1000) ?></b> Металла, Кристаллов и Дейтерия.<br>
Каждый день размер бонуса будет увеличиваться.<br>
<? if($parse['vk']): ?>
Взамен мы разместим в вашем статусе информацию о приложении. Вы согласны?<br>
<? endif; ?>
<br>
<a href="javascript:bonus()">ПОЛУЧИТЬ БОНУС</a><br>
</th></tr>
<? endif; ?>
<tr>
	<td class="c" colspan="3">
		<?=$parse['planet_type'] ?> "<?=$parse['planet_name'] ?>" <a href="?set=galaxy&mode=0&galaxy=<?=$parse['galaxy_galaxy'] ?>&system=<?=$parse['galaxy_system'] ?>">[<?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?>]</a> <a href="?set=overview&mode=renameplanet" title="Редактирование планеты">(ред.)</a>
	</td>
</tr>
<?=$parse['Have_new_level'] ?>
<tr>
	<th>Время</th>
	<th colspan=2><div id="clock"><?=$parse['time'] ?></div><script>UpdateClock();</script></th>
</tr>

<tr>
	<th>Новости</th>
	<th colspan=2>Данный скрипт скачен с сайта http://xnova.su и является собственностью его владельца</th>

<? if(count($parse['fleet_list']) > 0): ?>
 <? foreach($parse['fleet_list'] as $id => $list): ?>
    <tr class="<?=$list['fleet_status'] ?>">
	<th>
		<div id="bxx<?=$list['fleet_order'] ?>" class="z"><?=$list['fleet_count_time'] ?></div>
		<font color="lime"><?=$list['fleet_time'] ?></font>
	</th><th colspan="3">
		<span class="<?=$list['fleet_status'] ?> <?=$list['fleet_prefix'] ?><?=$list['fleet_style'] ?>"><?=$list['fleet_descr'] ?></span>
	</th>
	<?=$list['fleet_javas'] ?>
</tr>
   <? endforeach; ?>
<? endif; ?>
<tr>
	<th style="width:70px !important"><?=$parse['moon_img'] ?><br><?=$parse['moon'] ?></th>
	<th>
   		<table align="center" border="0" width="100%">
		<tr>
			<th rowspan="14" width="210"><img src="<?=$dpath ?>planeten/<?=$parse['planet_image'] ?>.jpg" width="200" height="200"></th>
			<td class="c" colspan="2">Диаметр</td>
        </tr><tr>
            <th colspan="2"><?=$parse['planet_diameter'] ?> км</th>
		</tr><tr>
			<td class="c" colspan="2">Занятость</td>
        </tr><tr>
            <th colspan="2"><a title="Занятость полей"><?=$parse['planet_field_current'] ?></a> / <a title="Максимальное колличество полей"><?=$parse['planet_field_max'] ?></a> поля</th>
		</tr><tr>
			<td class="c" colspan="2">Температура</td>
        </tr><tr>
            <th colspan="2">от. <?=$parse['planet_temp_min'] ?>&deg;C до <?=$parse['planet_temp_max'] ?>&deg;C</th>
		</tr><tr>
			<td class="c" colspan="2">Обломки <?=$parse['get_link'] ?></td>
        </tr><tr>
            <th colspan="2"><a title="Металл"><?=$parse['metal_debris'] ?></a></th>
		</tr><tr>
			<th colspan="2"><a title="Кристалл"><?=$parse['crystal_debris'] ?></a></th>
		</tr><tr>
			<td class="c" colspan="2">Бои</td>
        </tr><tr>
            <td class="c" width="25%">Удачные</td><th><?=$parse['raids_win'] ?></th>
		</tr><tr>
			<td class="c">Провальные</td><th><?=$parse['raids_lose'] ?></th>
		</tr><tr>
			 <td class="c">Всего</td><th><?=$parse['raids'] ?></th>
		</tr><tr>
			<th colspan="2">Фракция: <a href="?set=race"><?=$parse['race'] ?></a></th>
		</tr><tr>
			<th>
				<div  style="border: 1px solid rgb(153, 153, 255); width: 200px; margin: 0 auto;">
				<div  id="CaseBarre" style="background-color: <?=$parse['case_barre_barcolor'] ?>; width: <?=$parse['case_pourcentage'] ?>%;  margin: 0 auto; text-align:center;">
				<font color="#000000"><b><?=$parse['case_pourcentage'] ?>%</b></font></div></div>
			</th>
            <th colspan="2"><? if (!isset($_COOKIE['vkid'])): ?><a href="?set=refers">http://uni2.xnova.su/?<?=$parse['user_id'] ?></a><br>[<?=$parse['links'] ?>]<? else: ?>&nbsp;<? endif; ?></th>
		</tr>
		</table>
	</th>
	<th class="s" valign="top" style="width:195px !important">
		<table align="center" border="0">
		<tr>
			<td class="c" width="40%">Игрок:</td><td class="c"><a href="?set=players&id=<?=$parse['user_id'] ?>"><?=$parse['user_username'] ?></a></td>
		</tr><tr>
			<th width="90">Постройки:</th><th><font color="green"><?=$parse['user_points'] ?></font></th>
		</tr><tr>
			<th width="90">Флот:</th><th><font color="green"><?=$parse['user_fleet'] ?></font></th>
		</tr><tr>
			<th width="90">Оборона:</th><th><font color="green"><?=$parse['user_defs'] ?></font></th>
		</tr><tr>
			<th width="90">Исследования:</th><th><font color="green"><?=$parse['player_points_tech'] ?></font></th>
		</tr><tr>
			<th width="90">Всего:</th><th><font color="green"><?=$parse['total_points'] ?></font></th>
		</tr><tr>
			<th width="90">Место:</th><th><a href="?set=stat&range=<?=$parse['user_rank'] ?>"><?=$parse['user_rank'] ?></a> из <?=$parse['max_users'] ?> (<?=$parse['ile'] ?>)</th>
		</tr><tr>
			<td class="c" colspan="2">Промышленная отрасль:</td>
		</tr><tr>
			<th width="90">Уровень:</th><th><?=$parse['lvl_minier'] ?>/100</th>
		</tr><tr style="height:29px;">
			<th width="90" style="height:26px;">Опыт:</th><th><?=$parse['xpminier'] ?><br>из <?=$parse['lvl_up_minier'] ?></th>
		</tr><tr>
			<td class="c" colspan="2">Военная отрасль:</td>
		</tr><tr>
			<th width="90">Уровень:</th><th><?=$parse['lvl_raid'] ?>/100</th>
		</tr><tr style="height:29px;">
			<th width="90" style="height:26px;">Опыт:</th><th><?=$parse['xpraid'] ?><br>из <?=$parse['lvl_up_raid'] ?></th>
		</tr>
		</table>
	</th>
</tr>
<? if(isset($parse['build_list']) && count($parse['build_list']) > 0): ?>
	<? foreach($parse['build_list'] as $id => $list): ?>
		<tr class="flight">
		<th>
			<div id="build<?=$id ?>" class="z"><?=($list[0]-time()) ?></div>
			<script>FlotenTime('build<?=$id ?>', <?=($list[0]-time()) ?>);</script>
		</th><th colspan="2" style="text-align:left;">
			<span style="float:left;"><span class="flight owndeploy"><?=$list[1] ?></span></span>
			<font color="lime" style="float:right;"><?=datezone("d.m H:i:s", $list[0]) ?></font>
		</th>
		</tr>
	<? endforeach; ?>
<? endif; ?>
</table>
</td></tr><tr align="center"><td valign="top""><table class="s" valign="top" border="0"><tr><?=$parse['anothers_planets'] ?></tr></table></td></tr></table>
<br>
<?=$parse['banner'] ?>