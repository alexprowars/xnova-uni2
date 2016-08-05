function edToolbar(obj, id)
{
	var str 	= '';
	var count 	= 0;
	var c 		= new Array('00', '33', '66', '99', 'cc', 'ff');
	var buttonColors = new Array(215);

	for (var r = 0; r < 6; r++) {
		for (var g = 0; g < 6; g++) {
			for (var b = 0; b < 6; b++){
					buttonColors[count] = c[r] + c[g] + c[b];
					count++;
			}
		}
	}

	str += ('<style>.button{cursor:pointer;} .colorpicker{background:#eee;width:100%;padding:0;margin-top:0;display:block;text-align:center;} .colorpicker span { cursor:pointer;float:left; border:1px #fff solid; height:10px; width:10px; margin-right:0;font-size:0.01em; display:block;} .gensmall {font-size: 1em;margin-top:0;color: #000000;font-weight: 400;padding-left:10px;} .gensmall select {margin:4px 0 0 0;padding:0 0 0 0;font-weight: 400;white-space: nowrap;vertical-align:top; background-color: #f2f4f8;color: #000000;} .toolbar{background-color:#FFFFFF;height:25px;text-align:center;} .button{border:1px solid #ccc;margin:1px;padding:2px;} .ed{width:400px;height:150px;}</style>');
    str += ("<div class=\"toolbar\">");
	str += ("<span class=\"gensmall\"><select name=\"btnSize\" onchange=\"doAddTags('[size=' + this.options[this.selectedIndex].value + ']','[/size]','" + obj + "',0); this.selectedIndex = 1;\"><option value=\"9\">Маленький</option><option value=\"11\" selected=\"selected\">Нормальный</option><option value=\"20\">Большой</option><option value=\"25\">Огромный</option></select></span>");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_bold.png\" name=\"btnBold\" title=\"Жирный\" onClick=\"doAddTags('[b]','[/b]','" + obj + "',0)\">");
    str += ("<img class=\"button\" src=\"images/bbcodes/text_italic.png\" name=\"btnItalic\" title=\"Курсив\" onClick=\"doAddTags('[i]','[/i]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_underline.png\" name=\"btnUnderline\" title=\"Подчёркнутый\" onClick=\"doAddTags('[u]','[/u]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_strikethrough.png\" name=\"btnUnderline\" title=\"Зачёркнутый\" onClick=\"doAddTags('[s]','[/s]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_align_center.png\" name=\"btnCenter\" title=\"По центру\" onClick=\"doAddTags('[center]','[/center]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_align_left.png\" name=\"btnLeft\" title=\"По левому краю\" onClick=\"doAddTags('[left]','[/left]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_align_right.png\" name=\"btnRight\" title=\"По правому краю\" onClick=\"doAddTags('[right]','[/right]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_align_justify.png\" name=\"btnJustify\" title=\"По ширине\" onClick=\"doAddTags('[justify]','[/justify]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/eye.png\" name=\"btnSpoiler\" title=\"Спойлер\" onClick=\"doAddTags('[spoiler=]','[/spoiler]','"+obj+"',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/film_add.png\" name=\"btnTelevision\" title=\"YOUTUBE\" onClick=\"doAddTags('[youtube]','[/youtube]','"+obj+"',2)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/world_link.png\" name=\"btnLink\" title=\"Вставить ссылку\" onClick=\"doAddTags('[url]','[/url]','" + obj + "',1)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/picture_add.png\" name=\"btnPicture\" title=\"Вставить картинку\" onClick=\"doAddTags('[img]','[/img]','" + obj + "',3)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/sound_add.png\" name=\"btnSound\" title=\"Вставить песню\" onClick=\"doAddTags('[mp3]','[/mp3]','" + obj + "',6)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/image_add.png\" name=\"btnPicture\" title=\"Вставить большую картинку\" onClick=\"doAddTags('[img_big]','[/img_big]','" + obj + "',4)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_list_numbers.png\" name=\"btnList\" title=\"Нумерованый список\" onClick=\"doAddTags('[NUMLIST]','[/NUMLIST]','" + obj + "',5)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_list_bullets.png\" name=\"btnList\" title=\"Список\" onClick=\"doAddTags('[LIST]','[/LIST]','" + obj + "',5)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/text_signature.png\" name=\"btnQuote\" title=\"Цитата\" onClick=\"doAddTags('[quote]','[/quote]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/user_comment.png\" name=\"btnQuoteUser\" title=\"Цитата\" onClick=\"doAddTags('[quote author=]','[/quote]','" + obj + "',0)\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/emoticon_grin.png\" name=\"btnSmile\" title=\"Смайлы\" onClick=\"showSmiles();\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/color_swatch.png\" name=\"btnColor\" title=\"Цвет текста\" onClick=\"ShowHiddenBlock('colorpicker');\">");
	str += ("<img class=\"button\" src=\"images/bbcodes/palette.png\" name=\"btnColor2\" title=\"Цвет фона\" onClick=\"ShowHiddenBlock('colorpicker2');\">");

	str += ("<img class=\"button\" src=\"images/bbcodes/tick.png\" name=\"btnTick\" title=\"Предварительный просмотр\" onClick=\"show();\">");
    str += ("</div>");

	str += ('<div id="colorpicker" class="colorpicker" style="display:none">');
	for (var clr = 1; clr <= buttonColors.length; clr++) {
		str += ('<span onclick="doAddTags(\'[color=#' + buttonColors[clr-1] + ']\',\'[/color]\',\'' + obj + '\',0)" style="background: #' + buttonColors[clr-1] + '">&nbsp;</span>');
		if(clr%54==0) {
			str += ('<br>');
		}
    }
    str += ('</div>');

	str += ('<div id="colorpicker2" class="colorpicker" style="display:none">');
	for (clr = 1; clr <= buttonColors.length; clr++) {
		str += ('<span onclick="doAddTags(\'[bgcolor=#' + buttonColors[clr-1] + ']\',\'[/bgcolor]\',\'' + obj + '\',0)" style="background: #' + buttonColors[clr-1] + '">&nbsp;</span>');
		if(clr%54==0) {
			str += ('<br>');
		}
    }

    str += ('</div>');

	str += ('<div id="smiles" class="colorpicker" style="display:none"></div>');

	if (id == undefined)
		$('#editor').html(str);
	else
		$('#'+id).html(str);
}

function show()
{
	if (document.getElementById('showpanel').style.display == 'block') {
		document.getElementById('showbox').innerHTML = "";
		document.getElementById('showpanel').style.display = "none";
	} else {
		var txt = document.getElementById('text').value;
		if (txt != "") {
			Text (txt, 'showbox');
			document.getElementById('showpanel').style.display = "block";
		}
	}
}

function showSmiles()
{
	if ($('#smiles').css('display') == 'block') {
		$('#smiles').html('');
		$('#smiles').attr('style', 'display:none');
	} else {
		for (var i = 0; i < sm_repl.length; i++) {
        	$('#smiles').append('<img src="images/smile/'+sm_repl[i]+'.gif" alt="'+sm_repl[i]+'" onclick="AddSmile(\''+sm_repl[i]+'\')" style="cursor:pointer"> ');
		}

		$('#smiles').attr('style', 'display:block');
	}
}

function doAddTags(tag1,tag2,obj,type)
{
	var sel, scrollTop, scrollLeft, rep, url, textarea;

	if (doc('colorpicker').style.display == "block")
		doc('colorpicker').style.display = "none";

	if (doc('colorpicker2').style.display == "block")
		doc('colorpicker2').style.display = "none";

	textarea = doc(obj);

	scrollTop 	= textarea.scrollTop;
	scrollLeft 	= textarea.scrollLeft;

	if (type == 1) {
		url = prompt('Введите ссылку:','http://');
	} else if (type == 2) {
		url = prompt('Введите ссылку на видео:','http://www.youtube.com/watch?v=');
	} else if (type == 3 || type == 4) {
		url = prompt('Введите ссылку на картинку:','http://');
	} else if (type == 6) {
		url = prompt('Введите ссылку на песню:','http://');
	}

	if (type > 0 && type <= 6 && (url == '' || url == null))
    {
			return false;
	}

	if (document.selection)
    {
		textarea.focus();
		sel = document.selection.createRange();
	}
    else
    {
		var len 	= textarea.value.length;
	    var start 	= textarea.selectionStart;
		var end 	= textarea.selectionEnd;

        sel = textarea.value.substring(start, end);
	}

	if (type == 0) {
		rep = tag1 + sel + tag2;
	} else if (type == 1) {
		if (sel == ""){
			rep = '[url]' + url + '[/url]';
		} else {
			rep = '[url=' + url + ']' + sel + '[/url]';
		}
	} else if (type == 2) {
		rep = '[youtube]'  + url + '[/youtube]';
	} else if (type == 3) {
		rep = '[img]'  + url + '[/img]';
	} else if (type == 4) {
		rep = '[img_big]'  + url + '[/img_big]';
	} else if (type == 5) {
		var list = sel.split('\n');

		for(var i = 0;i < list.length; i++) {
			list[i] = '[*]' + list[i] + '[/*]';
		}

		rep = tag1 + '\n' + list.join("\n") + '\n' +tag2;
	} else if (type == 6) {
		rep = '[mp3]'  + url + '[/mp3]';
	}

	if (document.selection)
    {
		sel.text = rep;
	}
    else
    {
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);

		textarea.scrollTop 	= scrollTop;
		textarea.scrollLeft = scrollLeft;
	}

	return true;
}