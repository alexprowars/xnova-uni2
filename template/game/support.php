<br>
<div id="content" class="content">
	<table style="width:600px">
		<tr>
			<td class="c" colspan="4">Служба техподдержки</td>
		</tr>
<? if(count($parse['TicketsList'])>0): ?>
		<tr>
			<th style="width:10%">ID</th>
			<th style="width:50%">Тема</th>
			<th style="width:15%">Статус</th>
			<th style="width:25%">Дата</th>
		</tr>
		<? foreach($parse['TicketsList'] AS $id => $list): ?>
		<tr>
		<td class="c"><?=$id ?></td>
		<td class="c"><a href="#" onclick="ShowHiddenBlock('ticket_<?=$id ?>');"><?=$list['subject'] ?></a></td>
		<td class="c"><? if($list['status'] == 0): ?><span style="color:red">закрыто</span>
                <? elseif($list['status'] == 1): ?><span style="color:green">открыто</span>
                <? elseif($list['status'] == 2): ?><span style="color:orange">ответ админа</span>
            <? elseif($list['status'] == 3): ?><span style="color:green">ответ игрока</span><? endif; ?></td>
		<td class="c"><?=$list['date'] ?></td>
		</tr>
		<? endforeach; ?>
	</table>
	<? foreach($parse['TicketsList'] AS $id => $list): ?>
	<div id="ticket_<?=$id ?>" style="display:none;" class="tickets">
		<form action="?set=support&amp;action=send&amp;id=<?=$id ?>" method="POST">
			<table style="width:600px">
				<tr>
					<th>Текст запроса</th>
				</tr>
				<tr>
					<td class="c" style="text-align:left;"><?=$list['text'] ?></td>
				</tr>
				<? if($list['status'] == 0): ?><tr><th>Закрыт</th></tr><? endif; ?>
				<tr>
					<td class="c">
					<? if($list['status'] != 0): ?>
					<textarea cols="50" rows="10" name="text"></textarea><br><input type="submit" value="Ответить">
					<? endif; ?>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<? endforeach; ?>
<? else: ?>
		<tr><th colspan="4">Нет запросов в техподдержку</th></tr>
</table>
<? endif; ?>
    <br><br>
	<div id="newbutton" style="display:block;">
		<table style="width:600px;">
			<tr>
				<th><a href="#" onclick="ShowHiddenBlock('new');">Создать запрос</a></th>
			</tr>
		</table>
	</div>
	<div id="new" style="display:none;">
		<form action="?set=support&amp;action=newticket" method="POST">
			<table style="width:600px;">
				<tr>
					<th colspan="2" width="50%">Новый запрос</th>
				</tr>
				<tr>
					<td class="c">Тема:</td>
					<td class="c"><input type="text" name="subject"></td>
				</tr>
				<tr>
					<td class="c" colspan="2">Текст сообщения:</td>
				</tr>
				<tr>
					<td class="c" colspan="2">
						<textarea name="text" rows="10"></textarea>
						<input type="submit" value="Отправить">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>