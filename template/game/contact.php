<br>
<center>
<table width="550">
<tbody>
	<tr>
	<td colspan="3" class="c"><b>Администрация</b></td>
</tr><tr>
	<th colspan="3">
		<font color="orange">Здесь вы найдёте адреса всех администраторов и операторов игры для обратной связи</font>
	</th>
</tr><tr>
	<th width="166"><font color="lime">Имя</font></th>
	<th width="167"><font color="lime">Должность</font></th>
	<th width="166"><font color="lime">eMail</font></th>
</tr>
<? foreach($parse AS $list): ?>
    <tr>
	<th><?=$list['ctc_data_name'] ?></th>
	<th><?=$list['ctc_data_auth'] ?></th>
	<th><?=$list['ctc_data_mail'] ?></th>
</tr>
<? endforeach; ?>
</tbody>
</table>
</center>    