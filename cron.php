<?php

 	define('INSIDE'  , true);

	include("includes/class/class.db.php");
	include("includes/class/class.system.php");
	db::init();

	$query = db::query("SELECT COUNT(*) as `online` FROM {{table}} WHERE `onlinetime` > '" . (time()-3600) ."';", "users", true);

	system::UpdateConfig('online', $query['online']);
	system::ClearConfigCache();
 ?>
