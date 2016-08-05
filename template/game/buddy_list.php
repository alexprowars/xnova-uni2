<table width=519>
<tr><td class=c colspan=6><?=$parse['title'] ?></td></tr>

<? if(!$parse['a']): ?>
<tr>
	<th colspan=6><a href=?set=buddy&a=1>Запросы</a></th>
</tr><tr>
	<th colspan=6><a href=?set=buddy&a=1&e=1>Мои запросы</a></th>
</tr><tr>
	<td class=c>&nbsp;</td>
	<td class=c>Имя</td>
	<td class=c>Альянс</td>
	<td class=c>Координаты</td>
	<td class=c>Позиция</td>
	<td class=c>&nbsp;</td>
</tr>
<? else: ?>
<tr>
	<td class=c>&nbsp;</td>
	<td class=c>Пользователь</td>
	<td class=c>Альянс</td>
	<td class=c>Координаты</td>
	<td class=c>Текст</td>
	<td class=c>&nbsp;</td>
</tr>
<? endif; ?>

<? if(count($parse['list'])): ?>
	<? foreach($parse['list'] AS $id => $list): ?>
		<tr>
		<th width="20"><?=($id+1) ?></th>
		<th><a href=?set=messages&mode=write&id=<?=$list['id'] ?>><?=$list['username'] ?></a></th>
		<th><?=$list['ally'] ?></th>
		<th><a href="?set=galaxy&mode=3&galaxy=<?=$list['g'] ?>&system=<?=$list['s'] ?>"><?=$list['g'] ?>:<?=$list['s'] ?>:<?=$list['p'] ?></a></th>
		<th><?=$list['online'] ?></th>
		<th><?=$list['c'] ?></th>
	</tr>
	<? endforeach; ?>
<? else: ?>
	<tr><th colspan=6>Нет друзей</th></tr>
<? endif; ?>

<? if($parse['a']): ?>
	<tr><td colspan=6 class=c><a href="?set=buddy">назад</a></td></tr>
<? endif; ?>

</table>

<? if (isset($_COOKIE['vkid'])): ?>

<br><center><div id="friends" style="width:519px;"></div></center><script>
window.onload = (function() {
	if (parent && parent != window) {
	var friends  =  document.getElementById("friends");
	VK.init(function() {
		VK.api("friends.getAppUsers", function(data) {
			VK.api("getProfiles", {uids: data.response.toString(), fields: "photo_rec"}, function(friends_data) {
				var prompt  =   "<table width=100%><tr><td class=c colspan=5>Друзья Вконтакте:</td></tr>";
				for (i=0;i<friends_data.response.length;i++) {
					if (i%5 == 0)
						prompt += "<tr>";

					prompt += "<th width=20% style='align:center'>";
					prompt += "<table width=100%><tr><td style='text-align:center;'><img src='"+friends_data.response[i].photo_rec+"'></td></tr><tr><td style='text-align:center;'>"+friends_data.response[i].first_name+"<br>"+friends_data.response[i].last_name+"</td></tr></table>";
					prompt += "</th>";

					if (i%5 == 4)
						prompt += "</tr>";
				}
				prompt += "</table>";
				friends.innerHTML = prompt;
			})

		})
	})
}
})</script>

<? endif; ?>