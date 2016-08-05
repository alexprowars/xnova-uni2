<br><br><center>
<table width="600">
	<? if($race==0): ?><tr><td class="c" colspan="2">Выбор фракции</td></tr><? endif; ?>
	<tr><td class="k"><a href='?set=infos&gid=701'>Конфедерация</a></td><td class="k"><a href='?set=infos&gid=702'>Бионики</a></td></tr>
	<tr>
		<th width="50%" style="text-align:left">
			<div style="text-align:center"><a href='?set=infos&gid=701'><img src="<?=$dpath ?>images/race1.gif"></a></div><br>
			<font color="#adff2f">Особенности расы:</font>
			<br>&nbsp;&nbsp;&nbsp;<font color="#84CFEF">+15% к добыче металла
			<br>&nbsp;&nbsp;&nbsp;+10% к скорости постройки кораблей
			<br>&nbsp;&nbsp;&nbsp;+15% к энергии спутников
			<br>&nbsp;&nbsp;&nbsp;-10% к стоимости улучшения кораблей
			<br>&nbsp;&nbsp;&nbsp;Уникальный корабль: <font color="#adff2f"><a href="?set=infos&gid=220">Утилизатор</a></font> (вместительный и скоростной переработчик)</font>
			<br><br><? if($race==0): ?><div style="text-align:center"><a href="?set=race&sel=1">Выбрать</a></div><? endif; ?><br>
		</th>
		<th style="text-align:left">
			<div style="text-align:center"><a href='?set=infos&gid=702'><img src="<?=$dpath ?>images/race2.gif"></a></div><br>
			<font color="#adff2f">Особенности расы:</font>
			<br>&nbsp;&nbsp;&nbsp;<font color="#84CFEF">+15% к добыче дейтерия
			<br>&nbsp;&nbsp;&nbsp;-10% к стоимости постройки кораблей
			<br>&nbsp;&nbsp;&nbsp;+20% к вместимости хранилищ
			<br>&nbsp;&nbsp;&nbsp;+5% к энергии от солнечных батарей
			<br>&nbsp;&nbsp;&nbsp;Уникальный корабль: <font color="#adff2f"><a href="?set=infos&gid=221">Перехватчик</a></font> (скоростной легкий корабль)</font>
			<br><br><? if($race==0): ?><div style="text-align:center"><a href="?set=race&sel=2">Выбрать</a></div><? endif; ?><br>
		</th>
	</tr>
	<tr><td class="k"><a href='?set=infos&gid=703'>Сайлоны</a></td><td class="k"><a href='?set=infos&gid=704'>Древние</a></td></tr>
	<tr>
		<th style="text-align:left">
			<div style="text-align:center"><a href='?set=infos&gid=703'><img src="<?=$dpath ?>images/race3.gif"></a></div><br>
			<font color="#adff2f">Особенности расы:</font>
			<br>&nbsp;&nbsp;&nbsp;<font color="#84CFEF">+5% к добыче всех ресурсов
			<br>&nbsp;&nbsp;&nbsp;-10% к стоимости обороны
			<br>&nbsp;&nbsp;&nbsp;+10% к скорости постройки зданий
			<br>&nbsp;&nbsp;&nbsp;-10% к стоимости постройки зданий
			<br>&nbsp;&nbsp;&nbsp;Уникальный корабль: <font color="#adff2f"><a href="?set=infos&gid=222">Дредноут</a></font> (тяжелый боевой корабль)</font>
			<br><br><? if($race==0): ?><div style="text-align:center"><a href="?set=race&sel=3">Выбрать</a></div><? endif; ?><br>
		</th>
		<th style="text-align:left">
			<div style="text-align:center"><a href='?set=infos&gid=704'><img src="<?=$dpath ?>images/race4.gif"></a></div><br>
			<font color="#adff2f">Особенности расы:</font>
			<br>&nbsp;&nbsp;&nbsp;<font color="#84CFEF">+15% к добыче кристаллов
			<br>&nbsp;&nbsp;&nbsp;+10% к скорости полёта кораблей
			<br>&nbsp;&nbsp;&nbsp;+5% энергии от электростанций
			<br>&nbsp;&nbsp;&nbsp;-10% к стоимости исследований
			<br>&nbsp;&nbsp;&nbsp;Уникальный корабль: <font color="#adff2f"><a href="?set=infos&gid=223">Корсар</a></font> (быстрый пиратский корабль)</font>
			<br><br><? if($race==0): ?><div style="text-align:center"><a href="?set=race&sel=4">Выбрать</a></div><? endif; ?><br>
		</th>
	</tr>
	<? if ($race != 0 && $free_race_change > 0): ?>
	<tr><td class="k" colspan="2">Бесплатная смена фракции (1 раз за игру):</td></tr>
	<tr>
		<th colspan="2">
			На планетах не должно идти строительство, исследования, летать флот и весь флот фракции подлежит демонтировке (без возврата ресурсов).<br><br>
			<form action="?set=race&mode=change" method="POST">
				<select name="race">
					<option value="0">выбрать...</option>
					<option value="1">Конфедерация</option>
					<option value="2">Бионики</option>
					<option value="3">Сайлоны</option>
					<option value="4">Древние</option>
				</select>
				<br><br>
				<input type="submit" value="Сменить">
			</form>
		</th>
	</tr>
	<? endif; ?>
</table>
</center>