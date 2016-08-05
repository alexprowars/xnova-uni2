<div style="text-align:left;width:100%"><table border="0" cellpadding="0" cellspacing="1">
<tr valign="left"><td class="c" colspan="<?=$parse['mount'] ?>"><?=$parse['imperium_vision'] ?></td></tr>
<tr height="75"><th colspan="2">&nbsp;</th><?=$parse['file_images'] ?><th width=90>Сумма</th></tr>

<tr><th colspan="2"><?=$parse['name'] ?></th><?=$parse['file_names'] ?><th>&nbsp;</th></tr>
<tr><th colspan="2"><?=$parse['coordinates'] ?></th><?=$parse['file_coordinates'] ?><th>&nbsp;</th></tr>
<tr><th colspan="2"><?=$parse['fields'] ?></th><?=$parse['file_fields'] ?><th><?=$parse['file_fields_c'] ?> / <?=$parse['file_fields_t'] ?></th></tr>

<tr><td class="c" colspan="<?=$parse['mount'] ?>" align="left"><?=$parse['resources'] ?></td></tr>
<tr><th rowspan="5">на планете</th><th><?=$parse['metal'] ?></th><?=$parse['file_metal'] ?><th><?=$parse['file_metal_t'] ?></th></tr>
<tr><th><?=$parse['crystal'] ?></th><?=$parse['file_crystal'] ?><th><?=$parse['file_crystal_t'] ?></th></tr>
<tr><th><?=$parse['deuterium'] ?></th><?=$parse['file_deuterium'] ?><th><?=$parse['file_deuterium_t'] ?></th></tr>
<tr><th><?=$parse['energy'] ?></th><?=$parse['file_energy'] ?><th><?=$parse['file_energy_t'] ?></th></tr>
<tr><th>Заряд</th><?=$parse['file_zar'] ?><th>&nbsp;</th></tr>



<tr><th rowspan="3">в час</th><th><?=$parse['metal'] ?></th><?=$parse['file_metal_ph'] ?><th><?=$parse['file_metal_ph_t'] ?></th></tr>
<tr><th><?=$parse['crystal'] ?></th><?=$parse['file_crystal_ph'] ?><th><?=$parse['file_crystal_ph_t'] ?></th></tr>
<tr><th><?=$parse['deuterium'] ?></th><?=$parse['file_deuterium_ph'] ?><th><?=$parse['file_deuterium_ph_t'] ?></th></tr>


<tr><th rowspan="6">Производство</th><th>Металл</th><?=$parse['file_metal_p'] ?>
<th rowspan="6">&nbsp;</th>
</tr>
<tr><th>Кристаллы</th><?=$parse['file_crystal_p'] ?></th></tr>
<tr><th>Дейтерий</th><?=$parse['file_deuterium_p'] ?></tr>
<tr><th>Солн. ст.</th><?=$parse['file_solar_p'] ?></tr>
<tr><th>Терм. ст.</th><?=$parse['file_fusion_p'] ?></tr>
<tr><th>Спутники</th><?=$parse['file_solar2_p'] ?></tr>
<tr><th colspan="<?=$parse['mount1'] ?>">Кредиты</th><th><font color=#FFFF00><?=$parse['file_kredits'] ?></font></th></tr>
<tr><td class="c" colspan="<?=$parse['mount'] ?>" align="left"><?=$parse['buildings'] ?></td></tr>
	<?=$parse['building_row'] ?>
<tr><td class="c" colspan="<?=$parse['mount'] ?>" align="left"><?=$parse['ships'] ?></td></tr>
	<?=$parse['fleet_row'] ?>
<tr><td class="c" colspan="<?=$parse['mount'] ?>" align="left"><?=$parse['defense'] ?></td></tr>
	<?=$parse['defense_row'] ?>
<tr><td class="c" colspan="<?=$parse['mount'] ?>" align="left"><?=$parse['investigation'] ?></td></tr>
	<?=$parse['technology_row'] ?>
</table></div>