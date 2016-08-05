function doc(id)
{
	return document.getElementById(id);
}

var mark = 1;

function SelectAll()
{
	for (var i = 0; i < document.forms['mes_form'].elements.length; i++)
	{
        var item = document.forms['mes_form'].elements[i];
		if (item.name.indexOf('delmes') >= 0)
        {
		    item.checked = mark;
		}
	}
	if (mark == 0)
		mark = 1;
	else
		mark = 0;
}

function ShowHiddenBlock (id)
{
    if (doc(id).style.display == 'none')
        doc(id).style.display = 'block';
    else
        doc(id).style.display = 'none';
}

var timeouts	= new Array();
var start_time 	= new Date();

function print_date(timestamp, view)
{
	timestamp = (timestamp + timezone * 1800) * 1000;

    var X = new Date(timestamp);

	if (view == 1) {
		return (X.getHours()+':'+((m=X.getMinutes())<10?'0':'')+m);
	} else {
		document.write(((d=X.getDate())<10?'0':'')+d+'-'+((mn=X.getMonth()+1)<10?'0':'')+mn+' '+X.getHours()+':'+((m=X.getMinutes())<10?'0':'')+m+':'+((s=X.getSeconds())<10?'0':'')+s);
		return '';
	}
}

function raport_to_bb(raport)
{
	var txt = document.getElementById(raport).innerHTML;

	txt = txt.replace(/<tbody>/gi, "");
	txt = txt.replace(/<\/tbody>/gi, "");
	txt = txt.replace(/<tr>/gi, "[tr]");
	txt = txt.replace(/<\/tr>/gi, "[\/tr]");
	txt = txt.replace(/<td>/gi, "[td]");
	txt = txt.replace(/<\/td>/gi, "[\/td]");
	txt = txt.replace(/<\/table>/gi, "[\/table]");
	txt = txt.replace(/<th>/gi, "[th]");
	txt = txt.replace(/<th width="40%">/gi, "[th(w=40)]");
	txt = txt.replace(/<th width="10%">/gi, "[th(w=10)]");
	txt = txt.replace(/<\/th>/gi, "[\/th]");
	txt = txt.replace(/<td class="c" colspan="4">/gi, "[td(cl=c)(cs=4)]");
	txt = txt.replace(/<td colspan="4" class="c">/gi, "[td(cl=c)(cs=4)]");
	txt = txt.replace(/<table width="100%">/gi, "[table(w=100)]");
	txt = txt.replace(/<table width="100%" cellspacing="1">/gi, "[table(w=100)]");
	txt = txt.replace(/<table cellspacing="1" width="100%">/gi, "[table(w=100)]");
	txt = txt.replace(/<th width="220" align="right">/gi, "[th(w=33)]");
	txt = txt.replace(/<th align="right" width="220">/gi, "[th(w=33)]");
	txt = txt.replace(/<th width="220">/gi, "[th]");
	txt = txt.replace(/<br>/gi, " ");
	txt = txt.replace(/<\/a>/gi, "[\/url]");
	txt = txt.replace(/<a href="(.*?)">/gi, "[url=http://uni2.xnova.su$1]");

	document.getElementById(raport).innerHTML = txt;
}

function format(zahl)
{
	var zahl_tmp1;
	var zahl_tmp2;
	var zahl_tmp3;
	var html = "";

	if(zahl >= 1000000)
	{
		zahl_tmp1 = Math.floor(zahl / 1000000);
		html += "" + zahl_tmp1 + ".";
		zahl_tmp2 = Math.floor((zahl - (zahl_tmp1 * 1000000)) / 1000) + "";
		if(zahl_tmp2.length == 1)
		{
			html += "00" + zahl_tmp2 + ".";
		}
		else if(zahl_tmp2.length == 2)
		{
			html += "0" + zahl_tmp2 + ".";
		}
		else
		{
			html += "" + zahl_tmp2 + ".";
		}
		zahl_tmp3 = Math.floor(zahl - (zahl_tmp1 * 1000000) - (zahl_tmp2 * 1000)) + "";
		if(zahl_tmp3.length == 1)
		{
			html += "00" + zahl_tmp3 + "";
		}
		else if(zahl_tmp3.length == 2)
		{
			html += "0" + zahl_tmp3 + "";
		}
		else
		{
			html += "" + zahl_tmp3 + "";
		}
	}
	else if(zahl >= 1000)
	{
		zahl_tmp1 = Math.floor(zahl / 1000);
		html += "" + zahl_tmp1 + ".";
		zahl_tmp2 = Math.floor(zahl - (zahl_tmp1 * 1000)) + "";
		if(zahl_tmp2.length == 1)
		{
			html += "00" + zahl_tmp2 + "";
		}
		else if(zahl_tmp2.length == 2)
		{
			html += "0" + zahl_tmp2 + "";
		}
		else
		{
			html += "" + zahl_tmp2 + "";
		}
	}
	else
	{
		html = zahl;
	}
	return html;
}

function Res_count()
{
	var metall = 0;
	var crystall = 0;
	var deuterium = 0;
	var bold1_met = '<font color=#3abc55>';
	var bold2_met = '</font>';
	var bold1_cry = '<font color=#3abc55>';
	var bold2_cry = '</font>';
	var bold1_deu = '<font color=#3abc55>';
	var bold2_deu = '</font>';
	var faktor_met = 1;
	var faktor_cry = 1;
	var faktor_deu = 1;
	var ges_met = production[0];
	var ges_cry = production[1];
	var ges_deu = production[2];

	var rohstoffe = doc('ress');

	if(rohstoffe.metall.value >= max[0] - ress[0] || rohstoffe.bmetall.value == 1 || ress[0] >= max[0]) {
		bold1_met = '<font color=red>';
		bold2_met = '</font>';
		rohstoffe.bmetall.value = 1;
		faktor_met = 0;

	}
	metall = Math.floor(rohstoffe.metall.value) + Math.floor(ress[0]);
	rohstoffe.metall.value = (Math.floor(rohstoffe.metall.value * 10000)/10000) + (ges_met * faktor_met);

	if(rohstoffe.crystall.value >= max[1] - ress[1] || rohstoffe.bcrystall.value == 1 || ress[1] >= max[1]) {
		bold1_cry = '<font color=red>';
		bold2_cry = '</font>';
		rohstoffe.bcrystall.value = 1;
		faktor_cry = 0;
	}
	crystall = Math.floor(rohstoffe.crystall.value) + Math.floor(ress[1]);
	rohstoffe.crystall.value = (Math.floor(rohstoffe.crystall.value * 10000)/10000) + (ges_cry * faktor_cry);

	if(rohstoffe.deuterium.value >= max[2] - ress[2] || rohstoffe.bdeuterium.value == 1 || ress[2] >= max[2]) {
		bold1_deu = '<font color=red>';
		bold2_deu = '</font>';
		rohstoffe.bdeuterium.value = 1;
		faktor_deu = 0;
	}
	deuterium = Math.floor(rohstoffe.deuterium.value) + Math.floor(ress[2]);
	rohstoffe.deuterium.value = (Math.floor(rohstoffe.deuterium.value * 10000)/10000) + (ges_deu * faktor_deu);

	if (metall < 0) metall = 0;
	if (crystall < 0) crystall = 0;
	if (deuterium < 0) deuterium = 0;

    if(doc('met') && doc('cry') && doc('deu'))
    {
    	doc('met').innerHTML = bold1_met+format(metall)+bold2_met;
    	doc('cry').innerHTML = bold1_cry+format(crystall)+bold2_cry;
    	doc('deu').innerHTML = bold1_deu+format(deuterium)+bold2_deu;
    }
}

function FlotenTime (obj, time)
{
	var v       = new Date();
	var divs    = doc(obj);
	var ttime   = time;
	var mfs1    = 0;
	var hfs1     = 0;

	if (ttime < 1)
		divs.innerHTML = "-";
	else
    {
		if (ttime > 59) {
			mfs1 = Math.floor(ttime / 60);
			ttime = ttime - mfs1 * 60;
		}
		if (mfs1 > 59) {
			hfs1 = Math.floor(mfs1 / 60);
			mfs1 = mfs1 - hfs1 * 60;
		}
		if (ttime < 10) {
			ttime = "0" + ttime;
		}
		if (mfs1 < 10) {
			mfs1 = "0" + mfs1;
		}
		divs.innerHTML = hfs1 + ":" + mfs1 + ":" + ttime;
	}

	time--;

	timeouts['fleet'+obj] = window.setTimeout(function(){FlotenTime(obj,time)}, 999);
}

var Djs = start_time.getTime() - start_time.getTimezoneOffset()*60000;

function hms(layr, X)
{
      var d,mn,m,s;

      $("#" + layr).html(((d=X.getDate())<10?'0':'')+d+'-'+((mn=X.getMonth()+1)<10?'0':'')+mn+'-'+X.getFullYear()+' '+X.getHours()+':'+((m=X.getMinutes())<10?'0':'')+m+':'+((s=X.getSeconds())<10?'0':'')+s);
}

function UpdateClock()
{
   	var D0 = new Date;
    hms('clock', new Date(D0.getTime() + serverTime));

	timeouts['clock'] = setTimeout(UpdateClock, 999);
}

function setMaximum(type, number)
{
    if(document.getElementsByName('fmenge['+type+']')[0].value == 0)
   		document.getElementsByName('fmenge['+type+']')[0].value = number;
	else
		document.getElementsByName('fmenge['+type+']')[0].value = 0;
}

function PrintMenu (mess, vk)
{
	var result = '';

	result += '<div class="bar">';
	result += '<table align="center" style="width:800px;border-spacing:0;">';
	result += '<tr>';
	if (vk == 0)
		result += '<td class="m1"><a href="?set=links">Ссылки</a></td>';
	result += '<td class="m1"><a href="?set=stat">Статистика</a></td>';
	result += '<td class="m1"><a href="?set=techtree">Технологии</a>';
	result += '<td class="m1"><a href="?set=sim">Симулятор</a>';
	result += '<td class="m1"><a href="?set=search">Поиск</a></td>';
	result += '<td class="m1"><a href="?set=support">Техподдержка</a></td>';
	result += '<td class="m1"><a href="?set=messages">Сообщения <div id="new_messages" style="display: inline;">'+((mess > 0) ? ' ('+mess+')' : '')+'</div></a></td>';
	if (vk == 0)
		result += '<td class="m1"><a onclick=\'this.target="_blank";this.href="http://forum.xnova.su/"\'>Форум</a></td>';
	result += '<td class="m1"><a href="?set=options">Настройки</a></td>';
	if (vk == 0)
		result += '<td class="m1"><a href="?set=logout" style="color:red">Выход</a></td>';
	result += '</tr>';
	result += '</table>';
	result += '</div>';

	return result;
}

function UpdateGameInfo (mes)
{
    if (mes == 0)
        $('#new_messages').html('');
    else
        $('#new_messages').html(' ('+mes+')');
}

function setCookie (name, value, expires, path, domain, secure)
{
      document.cookie = name + "=" + escape(value) + ((expires) ? "; expires=" + expires : "") + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");
}

function getCookie(name)
{
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;

	if (cookie.length > 0)
    {
		offset = cookie.indexOf(search);
		if (offset != -1)
        {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1)
            {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

function QuickFleet (mission, galaxy, system, planet, type, count)
{
	$.ajax({
		type: "GET",
		url: "?set=fleet&page=quick",
		data: "ajax=1&mode="+mission+"&g="+galaxy+"&s="+system+"&p="+planet+"&t="+type+"&count="+count+"",
		success: function(msg)
		{
			alert(msg);
		}
	});
}

function fenster(target_url, win_name, w, h)
{
	if (!w)
		w=640;
	if (!h)
		h=480;

	var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width='+w+',height='+h+',top=0,left=0');
	new_win.focus();
}

function BuildTimeout(pp, pk, pl)
{
	var blc     	= doc('blc');
	var s           = pp;
	var m           = 0;
	var h           = 0;

	if ( s < 0 )
    {
		blc.innerHTML = "Завершено<br>" + "<a href=?set=buildings&planet=" + pl + ">Продолжить</a>";
		window.setTimeout('document.location.href="?set=buildings&planet=' + pl + '";', 5000);
	}
    else
    {
		if ( s > 59) {
			m = Math.floor( s / 60);
			s = s - m * 60;
		}
		if ( m > 59) {
			h = Math.floor( m / 60);
			m = m - h * 60;
		}
		if ( s < 10 ) {
			s = "0" + s;
		}
		if ( m < 10 ) {
			m = "0" + m;
		}

		blc.innerHTML = h + ":" + m + ":" + s + "<br><a href=?set=buildings&listid=" + pk + "&cmd=cancel&planet=" + pl + ">Отменить</a>";
	}

	pp--;

	timeouts['build'+pk+'-'+pl] = window.setTimeout("BuildTimeout("+pp+", "+pk+", "+pl+");", 999);
}

function ClearTimers ()
{
	for(var i in timeouts)
	{
		clearTimeout(timeouts[i]);
        clearInterval(timeouts[i]);
	}

	timeouts.length = 0;
}

function load (url)
{
    ClearTimers();

    $('.stepdown a').attr('class', 'blm');

    var loc = url.substring(1).split("&");

    var set = loc[0].split("=");

    if (set[1] != 'buildings')
        currentState = set[1];
    else
        currentState = set[1] + ((loc[1] != undefined && loc[1] != 'ajax=2' && loc[1] != 'ajax=1') ? '&'+loc[1] : '');

    window.location.hash = currentState;

	url = url+'&ajax=1&random=' + Math.random()*99999;

	$('#gamediv').html('<div style="height:'+parseInt($("#gamediv").height())+'px;vertical-align:top;text-align:center;margin-top:100px;"><font color="white">загрузка...</font><br><img src="images/loading.gif" alt=""></div>');
	$('#gamediv').load(url);

	start_time      = new Date();
    Djs             = start_time.getTime() - start_time.getTimezoneOffset()*60000;
}

function RebuildHref (loc)
{
    if (loc != undefined && loc != '')
        $('#link_'+loc).attr('class', 'blm_check');

	$('a').attr('onclick', setOnClick);

	function setOnClick(index, attributeValue)
	{
		if (this.href != '')
		{
			str = this.href;

            if (this.target == '_blank')
            {
                return ('this.href="'+str+'";this.target="_blank"');
            }
            else
            {
                if (str.substring(21, 30) == 'index.php')
                    return ('load("'+str.substring(30, str.length)+'")');
                else
                    return ('load("'+str.substring(21, str.length)+'")');
            }
		}
		else
		{
			return attributeValue;
		}
	}

	$('a').removeAttr('href');

	$("form").attr("action", function() {
          return this.action+'&ajax';
    });

	$('form').ajaxForm({
		target: '#gamediv',
		beforeSubmit: function(){
			$('#gamediv').html('<div style="height:'+parseInt($("#gamediv").height())+'px;vertical-align:top;text-align:center;margin-top:100px;"><font color="white">загрузка...</font><br><img src="images/loading.gif" alt=""></div>');

            ClearTimers();
            start_time = new Date();
            Djs = start_time.getTime() - start_time.getTimezoneOffset()*60000;
		}
	});
}

var currentState = window.location.hash.slice(1);

$(document).ready(function()
{
    if (ajax_nav == 1)
    {
        $(window).on('hashchange', function()
        {
            if (window.location.hash && window.location.hash.slice(1) != currentState)
            {
                currentState = window.location.hash.slice(1);
                load('?set='+currentState+'&ajax=2');
            }
        });
    }
});