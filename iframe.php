<?

date_default_timezone_set("Europe/Moscow");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<script src="http://vkontakte.ru/js/api/xd_connection.js?2" type="text/javascript"></script>

<script type="text/javascript">
function LoadGame ()
{
	document.getElementById('formiframe').submit();
}

window.onload = (function()
{
	VK.init(function()
    {
		LoadGame();

		//VK.api("isAppUser", function(data) {
		//	if (data.response == 0) {
		//		VK.callMethod('showInstallBox');
		//		VK.callMethod("showSettingsBox", 1024);
		//	} else {
		//		VK.api("getUserSettings", function(data) {
		//			if (data.response < 1024) {
		//				VK.callMethod("showSettingsBox", 1024);
		//			} else {
		//				LoadGame();
		//			}
		//		});
		//	}
		//});

		VK.addCallback("onSettingsChanged", onSettingsChanged);

		function onSettingsChanged(settings) {
			if (settings < 1026) {
				VK.callMethod("showSettingsBox", 1026);
			} else {
				LoadGame();
			}
		}

		VK.addCallback("onApplicationAdded", onApplicationAdded);

		function onApplicationAdded() {
			LoadGame();
		}

	});
});
</script>
</head>
<body>
<iframe src="" name="iframe" style="visibility:hidden" frameborder="0"></iframe>
<form method="POST" target="iframe" name="formiframe" id="formiframe" action="http://uni2.xnova.su/?set=login">
    <input type="hidden" name="viewer_id" value="<?=$_GET['viewer_id'] ?>">
    <input type="hidden" name="auth_key" value="<?=$_GET['auth_key'] ?>">
    <input type="hidden" name="user_id" value="<?=$_GET['user_id'] ?>">
    <input type="hidden" name="group_id" value="<?=$_GET['group_id'] ?>">
    <input type="hidden" name="viewer_type" value="<?=$_GET['viewer_type'] ?>">
</form>
<center>Загрузка...<br><img src="/images/loading.gif"></center>
</body>
</html>