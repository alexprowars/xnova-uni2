<script language="JavaScript" src="/scripts/smiles_v2.js"></script>
<script src="scripts/ed.js" type="text/javascript"></script>
<table width="651">
	<tr>
	  <td class="c" colspan="2">Информация об альянсе</td>
	</tr>
<?=$parse['ally_image'] ?>
	<tr>
	  <th><?=$parse['Tag'] ?></th>
	  <th><?=$parse['ally_tag'] ?></th>
	</tr>
	<tr>
	  <th><?=$parse['Name'] ?></th>
	  <th><?=$parse['ally_name'] ?></th>
	</tr>
	<tr>
	  <th><?=$parse['Members'] ?></th>
	  <th><?=$parse['ally_member_scount'] ?></th>
	</tr>
<tr><td class="b" colspan="2" height="100" style="padding:3px;"><span id="m1"></span></td></tr>

<?=$parse['ally_web'] ?>

<? if(isset($user_id)): ?><?=$parse['bewerbung'] ?><? endif; ?>
</table>
<script>Text('<?=str_replace(array("\r\n", "\n", "\r"), '', stripslashes($parse['ally_description'])) ?>', 'm1');</script>