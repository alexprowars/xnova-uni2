var sm_find = new Array(/:adolf:/gi,/:am:/gi,/:angel:/gi,/:angl:/gi,/:aplause:/gi,/:baby:/gi,/:boxing:/gi,/:bye:/gi,/:censored:/gi,/:crazy:/gi,/:dollar:/gi,/:drink:/gi,/:duel:/gi,/:evil:/gi,/:face1:/gi,/:face2:/gi,/:face5:/gi,/:fingal:/gi,/:friday:/gi,/:fuck:/gi,/:fuu:/gi,/:girl:/gi,/:goodnigth:/gi,/:gun1:/gi,/:gun2:/gi,/:gun_1:/gi,/:ha:/gi,/:happy:/gi,/:heart:/gi,/:hello:/gi,/:helloween:/gi,/:help:/gi,/:hummer:/gi,/:hummer2:/gi,/:ill:/gi,/:inlove:/gi,/:invalid:/gi,/:jack:/gi,/:jedy:/gi,/:kill:/gi,/:killed:/gi,/:king:/gi,/:kiss2:/gi,/:knut:/gi,/:lick:/gi,/:lips:/gi,/:lol:/gi,/:loo:/gi,/:matrix:/gi,/:med:/gi,/:mediana:/gi,/:roze:/gi,/:mol:/gi,/:ninja:/gi,/:nunchak:/gi,/:ogo:/gi,/:pare:/gi,/:police:/gi,/:prise:/gi,/:punk:/gi,/:ravvin:/gi,/:rip:/gi,/:rupor:/gi,/:scare:/gi,/:shut:/gi,/:sleep:/gi,/:song:/gi,/:strong:/gi,/:terminator:/gi,/:training:/gi,/:user:/gi,/:wall:/gi,/:rofl:/gi,/:hunter:/gi,/:nosex:/gi,/:bratan:/gi,/:diskot:/gi,/:jedy1:/gi,/:vglaz:/gi,/:duet:/gi,/:ff:/gi,/:smoke:/gi,/:bita:/gi,/:eat:/gi,/:perec:/gi,/:noperec:/gi,/:popec:/gi,/:popope:/gi,/:morpeh:/gi,/:vistre:/gi,/:lethik:/gi,/:naem:/gi,/:pirat:/gi,/:baraban:/gi,/:klizma:/gi,/:yy:/gi,/:arbuz:/gi,/:gamer2:/gi,/:pulemet:/gi,/:good2:/gi,/:negative:/gi,/:quiet:/gi,/:ball:/gi,/:pooh:/gi,/:vv:/gi,/:tank:/gi,/:fig1:/gi,/:sisi:/gi,/:spam:/gi);
var sm_repl = new Array('adolf','am','angel','angl','aplause','baby','boxing','bye','censored','crazy','dollar','drink','duel','evil','face1','face2','face5','fingal','friday','fuck','fuu','girl','goodnigth','gun1','gun2','gun_1','ha','happy','heart','hello','helloween','help','hummer','hummer2','ill','inlove','invalid','jack','jedy','kill','killed','king','kiss2','knut','lick','lips','lol','loo','matrix','med','mediana','roze','mol','ninja','nunchak','ogo','pare','police','prise','punk','ravvin','rip','rupor','scare','shut','sleep','song','strong','terminator','training','user','wall','rofl','hunter','nosex','bratan','diskot','jedy1','vglaz','duet','ff','smoke','bita','eat','perec','noperec','popec','popope','morpeh','vistre','lethik','naem','pirat','baraban','klizma','yy','arbuz','gamer2','pulemet','good2','negative','quiet','ball','pooh','vv','tank','fig1','sisi','spam');


var find = [
	/script/g,
	/\[mp3\](https?:\/\/.*?\.(?:mp3|m3u))\[\/mp3\]/gi,
	/\[quote\](.*?)\[\/quote\]/gi,
	/\[quote author=(.*?)\](.*?)\[\/quote\]/gi,
	/\[b\](.*?)\[\/b\]/gi,
	/\[i\](.*?)\[\/i\]/gi,
	/\[u\](.*?)\[\/u\]/gi,
	/\[s\](.*?)\[\/s\]/gi,
	/\[left\](.*?)\[\/left\]/gi,
	/\[center\](.*?)\[\/center\]/gi,
	/\[right\](.*?)\[\/right\]/gi,
	/\[justify\](.*?)\[\/justify\]/gi,
	/\[size=([1-9]|1[0-9]|2[0-5])\](.*?)\[\/size\]/gi,
	/\[color=#?([A-F0-9]{3}|[A-F0-9]{6})\](.*?)\[\/color\]/gi,
	/\[img\](https?:\/\/.*?\.(?:jpg|jpeg|gif|png|bmp))\[\/img\]/gi,
	/\[img_big\](https?:\/\/.*?\.(?:jpg|jpeg|gif|png|bmp))\[\/img_big\]/gi,
	/\[url=((?:ftp|https?):\/\/.*?)\](.*?)\[\/url\]/g,
	/\[url\]((?:ftp|https?):\/\/.*?)\[\/url\]/g,
	/\[numlist\](.*?)\[\/numlist\]/gi,
    /\[list\]([\s\S]*?)\[\/list\]/gi,
    /\[\*\](.*?)\[\/\*\]/gi,
	/\[youtube\]http:\/\/www.youtube.com\/watch\?v=(.*?)\[\/youtube\]/gi,
	/\[spoiler=(.*?)\](.*?)\[\/spoiler\]/gi,
	/\[bgcolor=#?([A-F0-9]{3}|[A-F0-9]{6})\](.*?)\[\/bgcolor\]/gi,
	/\[background=(https?:\/\/.*?\.(?:jpg|jpeg|gif|png|bmp)) w=([0-9]*) h=([0-9]*)\](.*?)\[\/background\]/gi,
	/\[p\](.*?)\[\/p\]/gi,
	/\[([1-9]{1}):([0-9]{1,3}):([0-9]{1,2})\]/gi,
	/\[table(.*?)\](.*?)\[\/table\]/gi,
	/\[tr\](.*?)\[\/tr\]/gi,
	/\[td(.*?)\](.*?)\[\/td\]/gi,
	/\[th(.*?)\](.*?)\[\/th\]/gi,
	/\(w=([0-9]{1,3})\)/gi,
	/\(cs=([0-9]{1,2})\)/gi,
	/\(cl=(.*?)\)/gi,
	/\[bashtube\]http:\/\/bashtube.ru\/video\/(.*?)\/\[\/bashtube\]/gi
];

var replace = [
	'',
	'<object type="application/x-shockwave-flash" data="/scripts/player.swf" id="audioplayer" height="24" width="288"><param name="movie" value="/scripts/player.swf"><param name="FlashVars" value="playerID=1&autostart=no&initialvolume=100&animation=no&soundFile=$1"><param name="quality" value="high"><param name="menu" value="false"><param name="wmode" value="transparent"></object>',
	'<div class="quotewrapper"><div class="quotecontent">$1</div></div>',
	'<div class="quotewrapper"><div class="quotetitle">$1 писал(а):</div><div class="quotecontent">$2</div></div>',
	'<strong>$1</strong>',
    '<em>$1</em>',
    '<span style="text-decoration: underline;">$1</span>',
    '<span style="text-decoration: line-through;">$1</span>',
	'<div align="left">$1<\/div>',
	'<div align="center">$1<\/div>',
	'<div align="right">$1<\/div>',
	'<div style="text-align:justify;word-spacing:-0.3ex;">$1<\/div>',
	'<span style="font-size: $1px;">$2</span>',
	'<span style="color: #$1;">$2</span>',
	'<a href="$1" target="_blank"><img src="$1" style="max-width:300px;" alt="XNova" /></a>',
	'<img src="$1" style="max-width:650px;" class="image" alt="" />',
	'<a href="$1" target="_blank">$2</a>',
	'<a href="$1" target="_blank">$1</a>',
	"<ol>$1</ol>",
    "<ul>$1</ul>",
    "<li>$1</li>",
	'<object><param name="movie" value="http://www.youtube.com/v/$1"><param name="wmode" value="transparent"><embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>',
	'<div><div class="quotetitle"><b>$1</b> <input type="button" value="Показать" style="width:65px;font-size:10px;margin:0px;padding:0px;background-image:none;color:#000000;" onclick="if (this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'\'; this.innerText = \'\'; this.value = \'Скрыть\'; } else { this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'none\'; this.innerText = \'\'; this.value = \'Показать\'; }" /></div><div class="quotecontent"><div style="display: none;">$2</div></div></div>',
	'<span style="background-color:#$1;">$2</span>',
	'<span style="background-image:url($1);background-repeat:no-repeat;display:block;width:$2;height:$3;max-width:650px;">$4</span>',
	'<p>$1</p>',
	'<a href="?set=galaxy&amp;mode=3&amp;galaxy=$1&amp;system=$2">[$1:$2:$3]</a>',
	'<table$1>$2</table>',
	'<tr>$1</tr>',
	'<td$1>$2</td>',
	'<th$1>$2</th>',
	' style="width:$1%"',
	' colspan="$1"',
	' class="$1"',
	'<object width="556" height="360" data="http://bashtube.ru/swf/flowplayer.swf" type="application/x-shockwave-flash"><param name="movie" value="http://bashtube.ru/swf/flowplayer.swf" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="flashvars" value=\'config={"canvas":{"backgroundGradient":"none","backgroundColor":"#000000"},"clip":{"urlResolvers":"secure","baseUrl":"http://video.bashtube.ru","autoPlay":false,"scaling":"fit","url":"$1"},"plugins":{"controls":{"url":"http://bashtube.ru/swf/flowplayer.controls.swf","tooltips":{"buttons":true,"fullscreen":"На весь экран","fullscreenExit":"Выйти из полного экрана","play":"Старт","pause":"Пауза","mute":"Приглушить","unmute":"Со звуком"}}},"playlist":[{"urlResolvers":"secure","baseUrl":"http://video.bashtube.ru","autoPlay":false,"scaling":"fit","url":"http://video.bashtube.ru/$1"}]}\' /></object>'
];

function AddSmile(id) {
	document.getElementById('text').value += ' :'+id+': ';
}

function AddQuote (user, id)
{
	var text = messages[id];
	text = text.replace(/<br>/gi, "\n");
    text = text.replace(/<br \/>/gi, "\n");
	document.getElementById('text').value += '[quote author='+user+']'+text+'[/quote]';
}

function Text (txt, id) 
{
	//txt = txt.replace(/\b(((https?|ftp|irc|telnet|nntp|gopher|file):\/\/|(mailto|news|data):)[^\s\"<>\{\}\'\(\)]*)/g, '[url]$1[/url]');

	for (i = 0; i < sm_repl.length; i++) 
	{
		txt = txt.replace(sm_find[i], '<img border="0" src="/images/smile/' + sm_repl[i] + '.gif" onclick="AddSmile(\''+sm_repl[i]+'\')" style="cursor:pointer">');
	}

	for(var i in find) 
	{
    	txt = txt.replace(find[i],replace[i]);
		if(i == 2 || i == 3 || i == 21) while(txt.match(find[i])) txt = txt.replace(find[i],replace[i]);
	}

	document.getElementById(id).innerHTML = txt;
}

function ShowText() {
	for(var i in messages) {
		Text(messages[i], i);
	}
}