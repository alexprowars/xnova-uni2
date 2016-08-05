<br>
<table width=519><tr><td class=c colspan=12><?=$parse['Configure_laws'] ?></td></tr>

    <form action="?set=alliance&mode=admin&edit=rights" method=POST>
	<tr>
	  <th></th>
	  <th>Имя ранга</th>
	  <th><img src=images/r1.png></th>
	  <th><img src=images/r2.png></th>
	  <th><img src=images/r3.png></th>
	  <th><img src=images/r4.png></th>
	  <th><img src=images/r5.png></th>
	  <th><img src=images/r6.png></th>
	  <th><img src=images/r7.png></th>
	  <th><img src=images/r8.png></th>
	  <th><img src=images/r9.png></th>
	  <th><img src=images/r10.gif></th>
	</tr>
<? if(count($parse['list']) > 0): ?>
<? foreach($parse['list'] AS $r): ?>
	<tr>
	  <th><?=$r['delete'] ?></th>
	  <th>&nbsp;<?=$r['r0'] ?>&nbsp;</th>
	  <input type="hidden" name="id[]" value="<?=$r['a'] ?>">
	  <th><?=$r['r1'] ?></th>
	  <th><?=$r['r2'] ?></th>
	  <th><?=$r['r3'] ?></th>
	  <th><?=$r['r4'] ?></th>
	  <th><?=$r['r5'] ?></th>
	  <th><?=$r['r6'] ?></th>
	  <th><?=$r['r7'] ?></th>
	  <th><?=$r['r8'] ?></th>
	  <th><?=$r['r9'] ?></th>
	  <th><?=$r['r10'] ?></th>
	  </tr>
	<tr>
<? endforeach; ?>            
<tr>
	  <th colspan=12><input type=submit value="Сохранить"></th>
	</tr>
<? else: ?>
<tr><th colspan="12" align="center">нет рангов</th><tr>            
<? endif; ?>
</form>
</table>

<br>

<form action="?set=alliance&mode=admin&edit=rights&add=name" method=POST>
<table width=519>
	<tr>
	  <td class=c colspan=2><?=$parse['Range_make'] ?></td>
	</tr>
	<tr>
	  <th><?=$parse['Range_name'] ?></th>
	  <th><input type=text name="newrangname" size=20 maxlength=30></th>
	</tr>
	<tr>
	  <th colspan=2><input type=submit value="<?=$parse['Make'] ?>"></th>
	</tr>
</form>
</table>
<table width=519>
	<tr>
	  <td class=c colspan=2><?=$parse['Law_leyends'] ?></td>
	</tr>
	<tr>
	  <th><img src=images/r1.png></th>
	  <th><?=$parse['Alliance_dissolve'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r2.png></th>
	  <th><?=$parse['Expel_users'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r3.png></th>
	  <th><?=$parse['See_the_requests'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r4.png></th>
	  <th><?=$parse['See_the_list_members'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r5.png></th>
	  <th><?=$parse['Check_the_requests'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r6.png></th>
	  <th><?=$parse['Alliance_admin'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r7.png></th>
	  <th><?=$parse['See_the_online_list_member'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r8.png></th><th><?=$parse['Make_a_circular_message'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r9.png></th><th><?=$parse['Left_hand_text'] ?></th>
	</tr>
	<tr>
	  <th><img src=images/r10.gif></th><th>Дипломатия</th>
	</tr>
	<tr>
	  <td class="c" colspan="2"><a href="?set=alliance&mode=admin&edit=ally"><?=$parse['Return_to_overview'] ?></a></td>
	</tr>
</table>