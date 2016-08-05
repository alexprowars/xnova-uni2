<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<? if(isset($title) && $title == 'login'): ?>
<title>XNova - браузерная онлайн стратегия</title>
<meta name="Description" content="Вы являетесь межгалактическим императором, который распространяет своё влияние посредством различных стратегий на множество галактик. Вы начинаете на своей собственой планете и строите там экономическую и военную инфраструктуру. Исследования дают Вам доступ к новым технологиям и более совершенным системам вооружения. На всём протяжении игры Вы будете колонизировать множество планет, заключать альянсы с другими владыками и вести с ними торговлю или войну.">
<meta name="Keywords" content="XNova, Space Wars, Space, Wars, Ogame, Online, Game, Игра, Огама, Огейм, Икснова, Хнова, браузер, браузерная, прокачка, кредиты">
<? else: ?>
<title>XNova<? if(isset($title)): ?> :: <?=$title ?><? endif; ?></title>
<? endif; ?>
<link rel="shortcut icon" href="favicon.ico">
<? if(!isset($title) || $title != 'login'): ?>
<link rel="stylesheet" type="text/css" href="<?=((isset($dpath)) ? $dpath : DEFAULT_SKINPATH) ?>formate.css">
<? endif; ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META http-equiv=Cache-Control content=no-cache>
<META HTTP-EQUIV="Expires" CONTENT="-1">
<?=$meta ?>
<script type="text/javascript" src="scripts/jquery-1.7.min.js"></script>
<script type="text/javascript" src="scripts/jquery.form.min.js"></script>
<script type="text/javascript" src="scripts/overlib.js"></script>
<script type="text/javascript" src="scripts/game.js?v2"></script>
<? if($vk == 1): ?>
<script src="http://vkontakte.ru/js/api/xd_connection.js?2" type="text/javascript"></script>
<? endif; ?>
</head>
<? if($vk == 1): ?>
<style>
#box {
    width:800px;
	padding-bottom:5px;
}
</style>
<? endif; ?>
<? if($design == 0): ?>
<style>
body {
	background-image: none;
}
</style>
<? endif; ?>
<body>
<script type="text/javascript">
	var timezone = <?=$timezone ?>;
    var ajax_nav = <?=$ajax_nav ?>;
</script>
