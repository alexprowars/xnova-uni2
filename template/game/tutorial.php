<style>
td.k {
border-bottom-width:0;
border-right-width:0;
vertical-align:top;
}
</style>
<br><table width="650" style="border-spacing:0">
	<tr>
		<td class="c" colspan="10">Обучение</td>
	</tr><tr>
    <th width="56" valign="middle"><a href="?set=tutorial&p=1">Шаг 1</a><img src="images/<?=$parse['tut_1'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=2">Шаг 2</a><img src="images/<?=$parse['tut_2'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=3">Шаг 3</a><img src="images/<?=$parse['tut_3'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=4">Шаг 4</a><img src="images/<?=$parse['tut_4'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=5">Шаг 5</a><img src="images/<?=$parse['tut_5'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=6">Шаг 6</a><img src="images/<?=$parse['tut_6'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=7">Шаг 7</a><img src="images/<?=$parse['tut_7'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=8">Шаг 8</a><img src="images/<?=$parse['tut_8'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=9">Шаг 9</a><img src="images/<?=$parse['tut_9'] ?>.gif" height="11" width="14" align='absmiddle'></th>
    <th width="56"><a href="?set=tutorial&p=10">Шаг 10</a><img src="images/<?=$parse['tut_10'] ?>.gif" height="11" width="14" align='absmiddle'></th></tr><tr>
		<td class="k" colspan="10" border=0>


<? if($parse['p']==1): ?>
            
<h3><br>Задание 1 - Основное снабжение<br><br></h3></td>
    </tr><tr><td class="k" colspan="3"><img src="images/tutorial/1.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
	Для расширения вашей главной планеты вам прежде всего понадобится достаточное количество ресурсов. Вы можете производить их с помощью рудников. Основное снабжение ресурсами вы получите совершенствуя рудники по добыче металла и кристалла. Но имейте ввиду, что содержание рудников требует большого количества энергии. Вы можете производить энергию используя, например, солнечные электростанции.
</div>

        <h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Построить <b>Рудник по добыче металла</b> 4 уровня</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['met_4'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_1">
                <span id="text_aufgabe_1">Построить <b>Рудник по добыче кристалла</b> 2 уровня</span>
                <span id="status_aufgabe_1"><img src="images/<?=$parse['cris_2'] ?>.gif" height="11" width="13"></span>

            </li>
                <li id="aufgabe_2">
                <span id="text_aufgabe_2">Построить <b>Солнечная электростанция</b> 4 уровня</span>
                <span id="status_aufgabe_2"><img src="images/<?=$parse['sol_4'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>
        <div style="color:orange;">Награда: 1.000 ед. металла + 500 ед. кристалла</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

            <ul class="solution">
<li>Кликните на "<a href="?set=buildings">Постройки</a>" в левом меню</li>
<li>Выберите Солнечную электростанцию</li>
<li>Нажав на кнопку `Построить` вы запустите процесс модернизации строения</li>
<li>Ресурсы необходимые для постройки будут списаны из ваших хранилищ.</li>
<li>После постройки Солнечной электростанции вам необходимо перейти к постройке Шахты по добыче метала</li>
<li>Если вы запустили не ту постройку, то вы можете отменить это нажав на кпопку "Отменить". Ресурсы будут возвращены в полном объеме.</li>
<li>Для дополнительной информации о строении нажмите на картинку этого здания.</li>
<li>Обычно игроки начинаю застраивать планету с такой последовательности: Солнечная электростанция 1, Шахта по добыче метала 1, Шахт по добыче метала 2, Солнечная электростанция 2, Шахта по добыче метала 3, Шахта по добыче метала 4, Солнечная электростанция 3, Шахта по добыче кристалла 1, Шахта по добыче кристалла 2</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==2): ?>
        <h3><br>Задание 2 - Планетарная оборона<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/2.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
С самого начала игры вам нужно думать о том как уберечь собственные ресурсы от разграбления. Например, вы можете строить простейшую оборону - Ракетные установки.
    </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Построить <b>Синтезатор дейтерия</b> 2 уровня</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['deu_4'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_1">

                <span id="text_aufgabe_1">Построить <b>Фабрику роботов</b> 2 уровня</span>
                <span id="status_aufgabe_1"><img src="images/<?=$parse['robot_2'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_2">
                <span id="text_aufgabe_2">Построить <b>Верфь</b> 1 уровня</span>
                <span id="status_aufgabe_2"><img src="images/<?=$parse['han_1'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_3">

                <span id="text_aufgabe_3">Построить <b>Ракетную установку</b></span>
                <span id="status_aufgabe_3"><img src="images/<?=$parse['lanz_1'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>
        <div style="color:orange;">Награда: +3 ракетные установки</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

<ul class="solution"><li>Для того, чтобы получить возможность строить оборону, вам необходим дейтерий.</li>
<li>Перейдите в меню Постройки и постройте синтезатор дейтерия уровня 2.</li>
<li>Не забывайте об энергоснабжении, так как производство дейтерия требует много энергии.</li>
<li>Выберите ракетную установку в меню обороны.</li>
<li>
В дереве технологий ракетной установки вы найдете все необходимые условия, которые должны быть выполнены для ее постройки.</li>
<li>Перейдите в меню построек и выберите фабрику роботов.</li>
<li>Как только вы накопите достаточно дейтерия, постройте фабрику роботов уровня 2.</li>
<li>Фабрика роботов уровня 2 позволяет вам построить верфь.</li>
<li>После окончания строительства верфи вы сможете построить ракетную установку.</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==3): ?>
        <h3><br>Задание 3 - Планетарное обеспечение<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/3.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
Чтобы ваши шахты работали на полную мощность не забывайте обеспечивать их достаточным количеством энергии.
        </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Построить <b>Рудник по добыче металла</b> 10 уровня</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['met_10'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_1">
                <span id="text_aufgabe_1">Построить <b>Рудник по добыче кристалла</b> 7 уровня</span>
                <span id="status_aufgabe_1"><img src="images/<?=$parse['cris_7'] ?>.gif" height="11" width="13"></span>

            </li>
                <li id="aufgabe_2">
                <span id="text_aufgabe_2">Построить <b>Синтезатор дейтерия</b> 5 уровня</span>
                <span id="status_aufgabe_2"><img src="images/<?=$parse['deut_5'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>
        <div style="color:orange;">Награда: 5.000 ед. металла + 2.500 ед. кристалла</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

<ul class="solution"><li>Совершенствуйте шахты в меню Постройки.</li>
<li>При возникновении затруднений обратитесь в дополнительное меню пункт 1.</li>
<li>Солнечные электростанции - это наиболее дешевый источник энергии. Следите за тем, чтобы у вас всегда было достаточно энергии для ваших шахт.</li>
<li>Если энергии не хватает, вы можете понизить производство менее необходимого ресурса в меню сырья, чтобы вывести производство более необходимого ресурса на максимум.</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==4): ?>
       <h3><br>Задание 4 - Первый корабль<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/4.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
Корабли, как и ракетные установки, помогут вам защититься от противников. Преимуществом кораблей является то, что корабли так же можно использовать и в наступательных целях. Чтобы разработать новые виды кораблей и обороны, вам необходима Исследовательская лаборатория.
        </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Построить <b>Исследовательскую лабораторию</b> 1 уровня</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['inv_1'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_1">
                <span id="text_aufgabe_1">Исследовать <b>Ракетный двигатель</b> 2 уровня</span>
                <span id="status_aufgabe_1"><img src="images/<?=$parse['comb_2'] ?>.gif" height="11" width="13"></span>

            </li>
                <li id="aufgabe_2">
                <span id="text_aufgabe_2">Построить <b>Малый транспорт</b></span>
                <span id="status_aufgabe_2"><img src="images/<?=$parse['navp_1'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>
        <div style="color:orange;">Награда: 2.000 ед. дейтерия, 10 кредитов</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

            <ul class="solution"><li>Вы можете найти исследовательскую лабораторию в меню объектов обороны.</li>
<li>Найдите дерево технологий постройки малого транспорта в верфи.</li>
<li>В дереве технологий вы увидите необходимые требования к реактивному двигателю для постройки малого транспорта.</li>
<li>Как только постройка исследовательской лаборатории завершена, исследуйте энергетическую технологию до уровня 1.</li>
<li>Далее вам необходимо исследовать реактивный двигатель до уровня 2.</li>
<li>Как только эти исследования закончены, постройте малый транспорт в вашей верфи.</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==5): ?>
        <h3><br>Задание 5 - Информационные сети<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/5.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
Вы не одиноки во вселенной! Информация и контакты с другими игроками очень важны. Если вы смогли влиться в общество, то можете положиться на помощь других игроков и легко найти торговых партнеров. Многие игроки объединяются в альянсы, чтобы добиваться поставленных целей вместе.    
        </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Переименовать планету</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['planet'] ?>.gif" height="11" width="13"></span>
            </li>
            <li id="aufgabe_1"> Посетить форум и скопировать ссылку на сайт форума в это окно:
			<form action="?set=tutorial&p=5" method="post">
				<input name="forum_content" class="input" size="25" type="text" style="text-align: left;"> <input value="&gt;" type="submit">
                <span id="status_aufgabe_1"><img src="images/<?=$parse['forum'] ?>.gif" height="11" width="13"></span>
            </form></li>

                <li id="aufgabe_2">
                <span id="text_aufgabe_2">Отправить запрос на дружбу</span>
                <span id="status_aufgabe_2"><img src="images/<?=$parse['buddy'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_3">

                <span id="text_aufgabe_3">Вступить в альянс с более чем тремя игроками</span>
                <span id="status_aufgabe_3"><img src="images/<?=$parse['ally'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>

        <div style="color:orange;">Награда: 10 кредитов</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

            <ul class="solution"><li>На странице обзора вы можете переименовать вашу планету.</li>
<li>Внизу страницы вы найдете ссылку на форум игры. Нажмите ее для того чтобы посетить форум.</li>
<li>Адрес интернет-страницы форума вы увидите в адресной строке вашего интернет-браузера.</li>
<li>Если вы начали играть в XNova вместе с другом, используйте функцию поиска в заголовке и введите его/ее игровой ник. Далее нажмите на значок дружбы и пошлите запрос дружбы. Если вы никого не знаете в вашей вселенной, то просто пошлите запрос вашему соседу. Просмотр остальных солнечных систем будет стоить вам 10 единиц дейтерия за каждую систему.</li>
<li>Вы можете основать свой собственный альянс вместе с вашими друзьями или вступить в уже существующий. Вы можете основать альянс в пункте меню `Альянс`. Если же вы хотите вступить в другой альянс, то выберите желаемый альянс и нажмите кнопку вступления.</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==6): ?>
        <h3><br>Задание 6 - Торговец<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/6.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
Скупщик, как одна из премиальных возможностей, способен помогать вам с обменом одних ресурсов на другие по заданному курсу. Максимальное количество ресурсов для обмена лимитированы лишь возможностями ваших хранилищ.   
        </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Постройте любое хранилище ресурсов 1 уровня</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['alm'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_1">
                <span id="text_aufgabe_1">Воспользоваться торговцем для обмена ресурсов</span>
                <span id="status_aufgabe_1"><img src="images/<?=$parse['mer'] ?>.gif" height="11" width="13"></span>

            </li>
            </ul>
        <div style="color:orange;">Награда: +1 уровень одного из хранилищ</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

            <ul class="solution"><li>Вы можете построить хранилища ресурсов в меню Постройки.</li>
<li>Вызывать Торговца можно в левом меню.</li>
<li>Чтобы вызвать нового Торговца выберите ресурс, который вы хотите ему предложить на продажу.</li>
<li>Для вызова Торговца требуются кредиты. Вы можете получить достаточное количество кредитов после выполнения 5го задания курса обучения.</li>
<li>Достать больше кредитов можно в меню Офицеры.</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==7): ?>
        <h3><br>Задание 7 - Действия флотом<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/7.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
Ресурсы можно добывать и грабежом с чужих планет. Но аккуратнее, некоторые планеты могут быть хорошо защищены обороной. Вы можете прошпионить чужую планету, чтобы получить больше информации.
        </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Построить шпионский зонд</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['sond'] ?>.gif" height="11" width="13"></span>
            </li>
                <li id="aufgabe_1">
                <span id="text_aufgabe_1">Просканировать планеты других игроков</span>
                <span id="status_aufgabe_1"><img src="images/<?=$parse['esp'] ?>.gif" height="11" width="13"></span>

            </li>
            </ul>
        <div style="color:orange;">Награда: +5 шпионских зондов</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

<ul class="solution"><li>В дереве технологий шпионского зонда в верфи вы найдете все необходимые условия для его постройки. Выполните все эти условия и вы сможете построить шпионский зонд.</li>
<li>Выберите цель в обзоре галактики и запомните ее координаты.</li>
<li>Перейдите в меню `флоты`, выберите шпионский зонд и нажмите `дальше`.</li>
<li>Введите координаты цели и нажмите `дальше`.</li>
<li>Выберите действие `шпионаж` и запустите зонд.</li>
<li>Далее вы будете перенаправлены в обзор флотов, где вы можете видеть все ваши активные флоты.</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==8): ?>
        <h3><br>Задание 8 - Бесконечные дали<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/8.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
Вселенная бесконечна. Время от времени храбрые исследователи предпринимают попытки открыть что-то новое в глубинах космоса, найти залежи ресурсов или даже встретиться в бою с космическими пиратами.
        </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Слетать в экспедицию</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['exp'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>
        <div style="color:orange;">Награда: 5 малых транспортов, 3 тяжелых истребителей</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

<ul class="solution"><li>Исследуйте первый уровень экспедиционной</li>
<li>Пошлите флот на 16-ю позицию в солнечной системе или в меню Галактика нажмите на кнопку `Экспедиция`.</li>
<li>Результат экспедиции всегда непредсказуем. Не стоит посылать туда весь свой флот.</li></ul>
    </div></center><br>
</td>
<? elseif($parse['p']==9): ?>
        <h3><br>Задание 9 - Расширение вашей империи<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/9.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
Император всегда стремится расширить свою империю. Вы уже заложили фундамент для этого на вашей главной планете, но на определенном этапе и она будет полностью застроена. Развивайте новые планеты как можно раньше, чтобы получить больше ресурсов и пространства для строительства. А с помощью свободных полетов между планетами вы получите отличную возможность, чтобы защитить ваши корабли и ресурсы от вражеских атак.    
        </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Основать новую колонию</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['colonia'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>
        <div style="color:orange;">Архитектор на 3 дня</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

<ul class="solution">
<li>Постройте колонизатор</li>
<li>Выберите подходящую позицию для планеты и пошлите туда колонизатор.</li>
<li>Желательно вместе с ним послать немного ресурсов для развития.</li>
</ul>
    </div></center><br>
</td>
<? elseif($parse['p']==10): ?>
        <h3><br>Задание 10 - Поле обломков<br><br></h3></td>
            </tr><tr><td class="k" colspan="3"><img src="images/tutorial/10.jpg" class="pic"></td><td class="k" colspan="7"><div style="text-align:left">
После орбитальных боев из останков металла и кристалла от сбитых кораблей образуются поля обломков. Переработка этих полей обломков представляет собой важный альтернативный метод добычи ресурсов.       </div><h3 style="text-align:left;">Задачи:</h3>

        <ul id="aufgabe_liste" style="text-align: left;">
            <li>
                <span>Соберите обломки около любой из планет</span>
                <span id="status_aufgabe_0"><img src="images/<?=$parse['rec'] ?>.gif" height="11" width="13"></span>
            </li>
            </ul>
        <div style="color:orange;">Награда: 3 переработчика</div>
		</tr><tr><td class="k" colspan="10"><br>
        <?=$parse['button'] ?>
       <div>&nbsp;</div><center><div style="text-align: left;background:#13181D;border:1px solid #030303;color:#848484;font-size:10px;margin:0 10px;padding:20px;width:490px;" id="tutorial_solution">

<li>Постройте переработчик в верфи.</li>
<li>В галактике выберите поле обломков.</li>
<li>Пошли переработчик на эту позицию. Обязательно укажите "поле обломков" в цели задания, а не "планету".
</li></div></center><br>
</td>
<? else: ?>
		<h2><br>Обучение в игре XNova!</h2>
Вы здесь впервые? Этот курс обучения позволит вам овладеть базовыми навыками.</p>
            <ul style="text-align: left;">
<li>В течение игры вам будут даны несколько заданий, за успешное выполнение которых вы получите награду.</li>
<li>Порядок следования задач строго определен, а значит вам необходимо выполнить задание, чтобы приступить к следующему.</li>
<li>Если у вас возникли проблемы с каким-либо из заданий, то под каждым из заданий находится краткое описание прохождения. Там вы получите детальное объяснение.</li>
<li>Как только вы закончите выполнение задания, вы получите дальнейшую информацию и награду за проделанную работу.</li></ul> <div
<br><br></td></tr><tr><th colspan="7">
<br><input type="button" onclick="window.location = '?set=tutorial&p=<?=($parse['t']+1) ?>'" value="Начать/Продолжить" style="cursor:pointer;width:180px;height:27px;"/><br>&nbsp;</th>
 <th colspan="3"><a href="?set=tutorial&p=exit">Закончить обучение</a></th>        
<? endif; ?>

</tr>
</table>