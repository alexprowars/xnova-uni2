function galaxy_submit(value)
{
	document.getElementById('auto').name = value;

    if (ajax_nav == 0)
        $("#galaxy_form").submit();
    else
        $("#galaxy_form").ajaxSubmit({target: '#gamediv'});
}

function Spy (planet, type)
{
	var spy_num = $('#spy'+planet+'').val();

	QuickFleet(6, galaxy, system, planet, type, spy_num);
}

var race_str = new Array('', 'Конфедерация', 'Бионики', 'Сайлоны', 'Древние');

function PrintRow ()
{
	var result = '';
	
	result += "<table width=710><tr>";
	result += "<td class=c colspan=7>Солнечная система "+galaxy+":"+system+"</td><td class=c colspan=2>Дейтерий: "+Deuterium+"</td>";
	result += "</tr><tr>";
	result += "<td class=c>№</td>";
	result += "<td class=c>&nbsp;</td>";
	result += "<td class=c>Планета</td>";
	result += "<td class=c>Луна</td>";
	result += "<td class=c>ПО</td>";
	result += "<td class=c>Игрок</td>";
	result += "<td class=c>&nbsp;</td>";
	result += "<td class=c>Альянс</td>";
	result += "<td class=c>Действия</td>";
	result += "</tr>";
	
	var planetcount = 0;
	
	for (planet = 1; planet <= 15; planet++)
    {
		result += '<tr>';
		result += '<th width=30>'+planet+'</th>';

		result += '<th width=30>';

		if (row[planet] && row[planet]["destruyed"] == 0)
        {
			planetcount++;

			result += "<a style=\"cursor: pointer;\" onmouseover='return overlib(\"";
			result += "<table width=240>";
			result += "<tr><td class=c colspan=2>Планета "+row[planet]["name"]+" ["+galaxy+":"+system+":"+planet+"]</td></tr>";
			result += "<tr>";
			result += "<th width=80><img src="+dpath+"planeten/small/s_"+row[planet]["image"]+".jpg height=75 width=75></th>";
			result += "<th align=left>";

			if (user['phalanx'] == 1)
				result += "<a href=# onclick=fenster(&#039;?set=phalanx&amp;galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&#039;) >Фаланга</a><br />";

			if (row[planet]['user_id'] != user['id'])
            {
				result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&amp;planettype="+row[planet]['planet_type']+"&amp;target_mission=1>Атаковать</a><br />";
				result += "<a href=?set=fleet&galaxy="+galaxy+"&system="+system+"&planet="+planet+"&planettype="+row[planet]['planet_type']+"&target_mission=5>Удерживать</a><br />";
			}
            else
            {
				result += "<a href=?set=fleet&galaxy="+galaxy+"&system="+system+"&planet="+planet+"&planettype="+row[planet]['planet_type']+"&target_mission=4>Оставить</a><br />";
			}

			result += "<a href=?set=fleet&galaxy="+galaxy+"&system="+system+"&planet="+planet+"&planettype="+row[planet]['planet_type']+"&target_mission=3>Транспорт</a>";


			result += "</th></tr>";
			result += "</table>\", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );' onmouseout='return nd();'>";
			result += "<img src="+dpath+"planeten/small/s_"+row[planet]["image"]+".jpg height=30 width=30></a>";
		}
        else
            result += "&nbsp;";

		result += "</th>";

		result += "<th style=\"white-space: nowrap;\" width=130>";

		if (row[planet] && row[planet]["destruyed"] == 0)
        {
			if (row[planet]['ally_id'] == user['ally_id'] && row[planet]['user_id'] != user['id'] && row[planet]['ally_id'] != 0)
            {
				TextColor = "<font color=\"green\">";
				EndColor  = "</font>";
			}
            else if (row[planet]['user_id'] == user['id'])
            {
				TextColor = "<font color=\"red\">";
				EndColor  = "</font>";
			}
            else
            {
				TextColor = '';
				EndColor  = "";
			}

			result += TextColor;

			result += row[planet]['name'];

			result += EndColor;

			if (row[planet]['last_update']  < 60 && row[planet]['user_id'] != user['id'])
            {
				if (row[planet]['last_update']  <= 10 && row[planet]['user_id'] != user['id'])
					result += "(*)";
				else
					result += " ("+Math.floor(row[planet]['last_update'])+")";
			}
		}
        else if (row[planet] && row[planet]["destruyed"] != 0)
        {
			result += 'Планета уничтожена';
		}
        else
        {
            result += "&nbsp;";
        }

		result += "</th>";

		result += "<th style=\"white-space: nowrap;\" width=30>";

		if (row[planet] && row[planet]["luna_destruyed"] == 0 && row[planet]["luna_id"])
        {
			result += "<a style=\"cursor: pointer;\" onmouseover='return overlib(\"";
			result += "<table width=240>";
			result += "<tr>";
			result += "<td class=c colspan=2>";
			result += "Луна: "+row[planet]["luna_name"]+" ["+galaxy+":"+system+":"+planet+"]";
			result += "</td>";
			result += "</tr><tr>";
			result += "<th width=80>";
			result += "<img src="+dpath+"planeten/mond.jpg height=75 width=75 />";
			result += "</th>";
			result += "<th>";
			result += "<table>";
			result += "<tr>";
			result += "<td class=c colspan=2>Характеристики</td>";
			result += "</tr><tr>";
			result += "<th>Диаметр</th>";
			result += "<th>"+format(row[planet]['luna_diameter'])+"</th>";
			result += "</tr><tr>";
			result += "<th>Температура</th><th>"+row[planet]['luna_temp']+"</th>";
			result += "</tr><tr>";
			result += "<td class=c colspan=2>Действия</td>";
			result += "</tr><tr>";
			result += "<th colspan=2 align=center>";

			if (row[planet]['user_id'] != user['id'])
            {
				result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&amp;planettype=3&amp;target_mission=1>Атаковать</a><br />";
				result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&planettype=3&target_mission=5>Удерживать</a><br />";

				if (user['destroy'] > 0)
                {
					result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&planettype=3&target_mission=9>Уничтожить</a><br>";
				}
			}
            else
            {
				result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&planettype=3&target_mission=4>Оставить</a><br />";
			}

			result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&planettype=3&target_mission=3>Транспорт</a><br />";

			result += "</tr>";
			result += "</table>";
			result += "</th>";
			result += "</tr>";
			result += "</table>\"";
			result += ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );' onmouseout='return nd();'>";
			result += "<img src=\""+dpath+"planeten/small/s_mond.jpg\" height=\"30\" width=\"30\"></a>";
		}
        else if (row[planet] && row[planet]["luna_destruyed"] > 0 && row[planet]["luna_id"])
			result += "~";
		else
            result += "&nbsp;";

		result += "</th>";

		if (row[planet] && (row[planet]["metal"] != 0 || row[planet]["crystal"] != 0))
        {
			result += "<th style=\"";

			if ((row[planet]["metal"] + row[planet]["crystal"]) >= 10000000) {
				result += "background-color: rgb(100, 0, 0);";
			} else if ((row[planet]["metal"] + row[planet]["crystal"]) >= 1000000) {
				result += "background-color: rgb(100, 100, 0);";
			} else if ((row[planet]["metal"] + row[planet]["crystal"]) >= 100000) {
				result += "background-color: rgb(0, 100, 0);";
			}

			result += "background-image: none;\" width=30>";
			result += "<a style=\"cursor: pointer;\" onmouseover='return overlib(\"";
			result += "<table width=240>";
			result += "<tr>";
			result += "<td class=c colspan=2>";
			result += "Обломки: ["+galaxy+":"+system+":"+planet+"]";
			result += "</td>";
			result += "</tr><tr>";
			result += "<th width=80>";
			result += "<img src="+dpath+"planeten/debris.jpg height=75 width=75 />";
			result += "</th>";
			result += "<th>";
			result += "<table width=95%>";
			result += "<tr>";
			result += "<td class=c colspan=2>Ресурсы</td>";
			result += "</tr><tr>";
			result += "<th>Металл</th><th>"+row[planet]['metal']+"</th>";
			result += "</tr><tr>";
			result += "<th>Кристалл</th><th>"+row[planet]['crystal']+"</th>";
			result += "</tr><tr>";
			result += "<th colspan=2 align=left><a href=# onclick=QuickFleet(8,"+galaxy+","+system+","+planet+",2)>Собрать</a></th>";
			result += "</tr><tr><th colspan=2 align=left><a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&planettype=2&target_mission=8>Отправить флот</a></th>";
			result += "</tr></table>";
			result += "</th>";
			result += "</tr>";
			result += "</table>\"";
            result += ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );' onmouseout='return nd();'>";
			result += "<img src="+dpath+"planeten/debris.jpg height=22 width=22></a>";
		}
        else
			result += "<th style=\"white-space: nowrap;\" width=30>&nbsp;";

		result += "</th>";
		result += "<th width=150>";

		if (row[planet] && row[planet]['user_id'] && row[planet]["destruyed"] == 0)
        {
			CurrentPoints 	= user['total_points'];
			RowUserPoints 	= row[planet]['total_points'];
			if (!RowUserPoints) RowUserPoints = 0;
			if (!row[planet]['total_rank']) row[planet]['total_rank'] = 0;
			CurrentLevel  	= CurrentPoints * 5;
			RowUserLevel  	= RowUserPoints * 5;

			if (row[planet]['banaday'] > time && row[planet]['urlaubs_modus_time'] > 0) {
				Systemtatus2 = "U <a href=\"?set=banned\"><span class=\"banned\">G</span></a>";
				Systemtatus  = "<span class=\"vacation\">";
			} else if (row[planet]['banaday'] > time) {
				Systemtatus2 = "<a href=\"?set=banned\"><span class=\"banned\">G</span></a>";
				Systemtatus  = "";
			} else if (row[planet]['urlaubs_modus_time'] > 0) {
				Systemtatus2 = "<span class=\"vacation\">U</span>";
				Systemtatus  = "<span class=\"vacation\">";
			} else if (row[planet]['onlinetime'] == 1) {
				Systemtatus2 = "<span class=\"inactive\">i</span>";
				Systemtatus  = "<span class=\"inactive\">";
			} else if (row[planet]['onlinetime'] == 2) {
				Systemtatus2 = "<span class=\"longinactive\">iI</span>";
				Systemtatus  = "<span class=\"longinactive\">";
			} else if (RowUserLevel < CurrentPoints && RowUserPoints < 50000) {
				Systemtatus2 = "<span class=\"noob\">N</span>";
				Systemtatus  = "<span class=\"noob\">";
			} else if (RowUserPoints > CurrentLevel && CurrentPoints < 50000) {
				Systemtatus2 = "S";
				Systemtatus  = "<span class=\"strong\">";
			} else {
				Systemtatus2 = "";
				Systemtatus  = "";
			}
			Systemtatus4 = row[planet]['total_rank'];
			if (Systemtatus2 != '') {
				Systemtatus6 = "<font color=\"white\">(</font>";
				Systemtatus66 = "<font color=\"white\">)</font>";
			}
			if (Systemtatus2 == '') {
				Systemtatus6 = "";
				Systemtatus66 = "";
			}
			admin = "";
			if (row[planet]['authlevel'] == 3) {
				admin = " <font color=\"red\">A</font>";
			}
			sgo = "";
			if (row[planet]['authlevel'] == 2) {
				sgo = " <font color=\"orange\">SGo</font>";
			}
			go = "";
			if (row[planet]['authlevel'] == 1) {
				go = " <font color=\"green\">Go</font>";
			}

			Systemtatus3 = row[planet]['username'];

			Systemtart = row[planet]['total_rank'];
			if (Systemtart < 100) {
				Systemtart = 1;
			} else {
				Systemtart = (Math.floor( row[planet]['total_rank'] / 100 ) * 100) + 1;
			}
			result += "<a style=\"cursor: pointer;\" onmouseover=\"return overlib('";
			result += "<table width=260>";
			result += "<tr>";
			result += "<td class=c colspan=2>Игрок "+row[planet]['username']+", место "+Systemtatus4+"</td>";
			result += "</tr><tr>";
			result += "<td width=95 height=95 rowspan=3 class=c";

			if (row[planet]['avatar'] != 0)
            {
				if (row[planet]['avatar'] != 99)
                {
					result += " style=\\\'background-image:url(/images/avatars/"+row[planet]['avatar']+".jpg);background-repeat:no-repeat;\\\'></td>";
				}
                else
                {
					result += " style=\\\'background-image:url(/images/avatars/upload/upload_"+row[planet]['user_id']+".jpg);background-repeat:no-repeat\\\'></td>";
				}
			}
            else
				result += ">нет<br>аватара</td>";

			if (row[planet]['user_id'] != user['id'])
            {
				result += "<th><a href=?set=messages&mode=write&id="+row[planet]['user_id']+">Послать сообщение</a></th>";
				result += "</tr><tr>";
				result += "<th><a href=?set=buddy&a=2&u="+row[planet]['user_id']+">Добавить в друзья</a></th>";
				result += "</tr><tr>";
			}
			result += "<th valign=top><a href=?set=stat&who=1&range="+Systemtart+"&pid="+row[planet]['user_id']+">Статистика</a></th>";
			result += "</tr>";
			result += "</table>', STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );\" onmouseout='return nd();'>";
			result += Systemtatus+Systemtatus3+Systemtatus6+Systemtatus+Systemtatus2+Systemtatus66+admin+sgo+go;
			result += "</span></a></th><th width='18'>";

			if (row[planet]['race'] == 0) {
				result += "&nbsp;";
			} else {
				result += "<a href='?set=infos&gid=70"+row[planet]['race']+"'><img src='"+dpath+"images/race"+row[planet]['race']+".gif' width='16' height='16' alt='"+race_str[row[planet]['race']]+"' title='"+race_str[row[planet]['race']]+"'></a>";
			}
		}
        else
            result += "&nbsp;</th><th width='18'>&nbsp;";

		result += "</th>";

		result += "<th width=80>";

		if (row[planet] && row[planet]['ally_id'] != 0)
        {
			if (row[planet]['ally_name'])
            {
				result += "<a style=\"cursor: pointer;\" onmouseover='return overlib(\"";
				result += "<table width=240>";
				result += "<tr>";
				result += "<td class=c>Альянс "+row[planet]['ally_name']+" с "+row[planet]['ally_members']+" членами</td>";
				result += "</tr>";
				result += "<th><table><tr>";
				result += "<td><a href=?set=alliance&mode=ainfo&a="+row[planet]['ally_id']+">Информация</a></td>";
				result += "</tr><tr>";
				result += "<td><a href=?set=stat&start=0&who=2>Статистика</a></td>";

				if (row[planet]["ally_web"] != "") {
					result += "</tr><tr><td><a href="+row[planet]["ally_web"]+" target=_new>Сайт альянса</td>";
				}

				result += "</tr></table></th>";
				result += "</table>\", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );' onmouseout='return nd();'>";

				if (user['ally_id'] == row[planet]['ally_id']) {
					result += "<span class=\"allymember\">"+row[planet]['ally_tag']+"</span></a>";
				} else {
					result += row[planet]['ally_tag']+"</a>";
				}

				if (row[planet]['ally_id'] != user['ally_id'])
                {
					if (row[planet]['type'] == 0)
						result += "<br><small>[нейтральное]</small>";
					else if (row[planet]['type'] == 1)
						result += "<br><small><font color=\"orange\">[перемирие]</font></small>";
					else if (row[planet]['type'] == 2)
						result += "<br><small><font color=\"green\">[мир]</font></small>";
					else if (row[planet]['type'] == 3)
						result += "<br><small><font color=\"red\">[война]</font></small>";
				}
			}
		}
        else
            result += "&nbsp;";

		result += "</th>";

		result += "<th style=\"white-space: nowrap;\" width=125>";

		if (row[planet] && row[planet]['user_id'] != user['id'])
        {
			if (row[planet]['user_id'] && row[planet]["destruyed"] == 0)
            {

				result += "<a href=?set=messages&mode=write&id="+row[planet]["user_id"]+">";
				result += "<img src="+dpath+"img/m.gif alt=\"Отправить сообщение\" title=\"Отправить сообщение\" border=0></a>&nbsp;";

				result += "<a href=?set=buddy&a=2&amp;u="+row[planet]["user_id"]+" >";
				result += "<img src="+dpath+"img/b.gif alt=\"Добавить в друзья\" title=\"Добавить в друзья\" border=0></a>&nbsp;";

				if (user['missile'] == 1)
                {
					result += "<a href=?set=galaxy&mode=2&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&current="+user['current_planet']+" >";
					result += "<img src="+dpath+"img/r.gif alt=\"Ракетная атака\" title=\"Ракетная атака\" border=0></a>&nbsp;";
				}

				if (user['spy_sonde'] > 0)
                {
					result += "<a href=\"#\" onclick=\"return overlib('<center><input type=\\\'text\\\' name=\\\'spy"+planet+"\\\' id=\\\'spy"+planet+"\\\' value=\\\'1\\\'><br><input type=button value=Отправить onclick=\\\'Spy("+planet+","+row[planet]['planet_type']+")\\\'></center>',STICKY,CAPTION,'Количество шпионов:',CLOSETEXT,'[X]',CENTER,WIDTH,170);\"><img src="+dpath+"img/e.gif alt=\"Шпионаж\" title=\"Шпионаж\" border=0></a>&nbsp;";
				}

				result += "<a href=?set=players&id="+row[planet]["user_id"]+">";
				result += "<img src="+dpath+"img/s.gif alt=\"Информация об игроке\" title=\"Информация об игроке\" border=0></a>&nbsp;";

				result += "<a href=?set=fleet&page=shortcut&mode=add&g="+galaxy+"&s="+system+"&i="+planet+"&t="+row[planet]['planet_type']+">";
				result += "<img src="+dpath+"img/z.gif alt=\"Добавить в закладки\" title=\"Добавить в закладки\" border=0></a>";
			}
		}
        else if (!row[planet] && user['colonizer'] > 0)
                result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet="+planet+"&amp;target_mission=7><img src="+dpath+"img/e.gif alt=\"Колонизация\" title=\"Колонизация\" border=0></a>&nbsp;";
		else
            result += "&nbsp;";

		result += "</th>";
		result += "</tr>";
	}
	
	result += "<tr><th width=\"30\">16</th>";
	result += "<th colspan=8>";
	result += "<a href=?set=fleet&galaxy="+galaxy+"&amp;system="+system+"&amp;planet=16&amp;target_mission=15>Неизведанные дали</a>";
	result += "</th>";
	result += "</tr><tr>";
	
	if (planetcount == 1) {
		PlanetCountMessage = planetcount+" заселённая планета";
	} else if (planetcount == 0) {
		PlanetCountMessage = "нет заселённых планет";
	} else {
		PlanetCountMessage = planetcount+" заселённые планеты";
	}
	
	result += "<tr>";
	result += "<td class=c colspan=6>( "+PlanetCountMessage+" )</td>";
	result += "<td class=c colspan=3>";
	
	result += "<a href=\"#\" style=\"cursor: pointer;\"";
	result += " onmouseover='return overlib(\"";

	result += "<table width=240>";
	result += "<tr>";
	result += "<td class=c colspan=3>Легенда</td>";
	result += "</tr><tr>";
	result += "<td width=220>Сильный игрок</td><td><span class=strong>S</span></td>";
	result += "</tr><tr>";
	result += "<td>Слабый игрок</td><td><span class=noob>N</span></td>";
	result += "</tr><tr>";
	result += "<td>Режим отпуска</td><td><span class=vacation>U</span></td>";
	result += "</tr><tr>";
	result += "<td>Заблокирован</td><td><span class=banned>G</span></td>";
	result += "</tr><tr>";
	result += "<td>Неактивен 7 дней</td><td><span class=inactive>i</span></td>";
	result += "</tr><tr>";
	result += "<td>Неактивен 28 дней</td><td><span class=longinactive>iI</span></td>";
	result += "</tr><tr>";
	result += "<td><font color=red>Администратор</font></td><td><font color=red>A</font></td>";
	result += "</tr><tr>";
	result += "<td><font color=green>Оператор</font></td><td><font color=green>GO</font></td>";
	result += "</tr><tr>";
	result += "<td><font color=orange>Супер оператор</font></td><td><font color=orange>SGO</font></td>";
	result += "</tr>";
	result += "</table>";
	result += "\", ABOVE, WIDTH, 150, STICKY, MOUSEOFF, DELAY, 50, CENTER);' onmouseout='return nd();'>";
	result += "Легенда</a>";
	
	result += "</td>";
	result += "</tr>\n<tr>";
	result += "<td class=c colspan=3><span id=\"missiles\">"+user['interplanetary_misil']+"</span> межпланетных ракет</td>";
	result += "<td class=c colspan=3><span id=\"slots\">"+user['fleets']+"</span>/"+user['max_fleets']+" флотов</td>";
	result += "<td class=c colspan=3>";
	result += "<span id=\"recyclers\">"+format(user['recycler'])+"</span> переработчиков<br>";
	result += "<span id=\"probes\">"+format(user['spy_sonde'])+"</span> шпионских зондов</td>";
	result += "</tr></table>";

	return result;
}

function ChangePlanet(id)
{
    if (ajax_nav == 0)
	    eval("location='?set=galaxy&mode=3&"+document.getElementById('planet_select').options[id].value+"'");
    else
        load("?set=galaxy&mode=3&"+document.getElementById('planet_select').options[id].value+"");
}

function PrintSelector(fleet_shortcut, r, l, c)
{
	var result = '';

	result += "<form action=\"?set=galaxy&mode=1\" method=\"post\" id=\"galaxy_form\" style='margin-bottom:0;'>";
	result += "<input type=\"hidden\" id=\"auto\" value=\"dr\" >";
	result += "<input type=\"hidden\" name=\"center\" value=\""+c+"\" >";
	result += "<input type=\"hidden\" name=\"left\" value=\""+l+"\" >";
	result += "<input type=\"hidden\" name=\"right\" value=\""+r+"\" >";
	result += "<table border=\"0\" style=\"border-spacing:0\">";
	result += "<tr><td>";

	result += "<table style=\"border-spacing:0\"><tr>";
	result += "<td class=\"c\" colspan=\"3\">Галактика</td></tr><tr>";
	result += "<th><input name=\"galaxyLeft\" value=\"&lt;-\" onclick=\"galaxy_submit('galaxyLeft')\" type=\"button\"></th>";
	result += "<th><input name=\"galaxy\" value=\""+galaxy+"\" size=\"5\" maxlength=\"3\" tabindex=\"1\" type=\"text\"></th>";
	result += "<th><input name=\"galaxyRight\" value=\"-&gt;\" onclick=\"galaxy_submit('galaxyRight')\" type=\"button\"></th>";
	result += "</tr></table>";

	result += "</td><th align=\"center\" style=\"vertical-align:middle\">";

	result += '<select id=\'planet_select\' onChange=\'ChangePlanet(this.selectedIndex);\' style=\"width:100%\">';

	for (i = 0; i < fleet_shortcut.length; i++)
    {
		result += '<option';
        
		if (fleet_shortcut[i][4] == 1 && ((fleet_shortcut[i-1] && (fleet_shortcut[i-1][1] != fleet_shortcut[i][1] || fleet_shortcut[i-1][2] != fleet_shortcut[i][2])) || !fleet_shortcut[i-1]))
			result += ' selected="selected" ';

		result += ' value="galaxy='+fleet_shortcut[i][1]+'&system='+fleet_shortcut[i][2]+'">'+fleet_shortcut[i][0]+'&nbsp;['+fleet_shortcut[i][1]+':'+fleet_shortcut[i][2]+':'+fleet_shortcut[i][3]+']&nbsp;&nbsp;</option>';
	}

	result += "</select><br><input value=\"Просмотр\" type=\"submit\" style=\"width:250px\"></th><td>";

	result += "<table style=\"border-spacing:0\"><tr>";
	result += "<td class=\"c\" colspan=\"3\">Солнечная система</td></tr><tr>";
	result += "<th><input name=\"systemLeft\" value=\"&lt;-\" onclick=\"galaxy_submit('systemLeft')\" type=\"button\"></th>";
	result += "<th><input name=\"system\" value=\""+system+"\" size=\"5\" maxlength=\"3\" tabindex=\"2\" type=\"text\"></th>";
	result += "<th><input name=\"systemRight\" value=\"-&gt;\" onclick=\"galaxy_submit('systemRight')\" type=\"button\"></th>";
	result += "</tr></table>";

	result += "</td></tr></table></form>";
	
	return result;
}