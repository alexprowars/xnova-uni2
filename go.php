<?
session_set_cookie_params(0, '/', 'uni2.xnova.su');
eaccelerator_set_session_handlers();
session_start();
date_default_timezone_set("Europe/Moscow");

define('INSIDE'  , true);
define('INSTALL' , false);

include("includes/class/class.db.php");

db::init();

$id = db::escape_string($_SERVER['QUERY_STRING']);

if (!is_numeric($id) || strlen($id) == 0) echo"Error!";
else {
        $login = db::query("SELECT `id` FROM {{table}} WHERE `id` = '".$id."'", 'users', true);

        if (!empty($login['id'])) {

              $ip = GetEnv("HTTP_X_REAL_IP");
              $now=time();
		$timeb = $now-86400;

              $res = db::query("SELECT `id` FROM {{table}} where `ip` = '".$ip."' AND `time` > '$timeb'", 'moneys', true);

		if (empty($res['id'])) {

 			db::query("INSERT INTO {{table}} values ('".$login['id']."','$ip','$now','".addslashes($_SERVER['HTTP_REFERER'])."', '".addslashes($_SERVER['HTTP_USER_AGENT'])."')", 'moneys');
			db::query("UPDATE {{table}} set links=links+1 where id='".$login['id']."'", 'users');

		}
		$_SESSION['ref'] = $login['id'];
        }

        $host=GetEnv("HTTP_HOST");

?>

<!--Rating@Mail.ru COUNTER--><script language="JavaScript" type="text/javascript"><!--
d=document;var a='';a+=';r='+escape(d.referrer)
js=10//--></script><script language="JavaScript1.1" type="text/javascript"><!--
a+=';j='+navigator.javaEnabled()
js=11//--></script><script language="JavaScript1.2" type="text/javascript"><!--
s=screen;a+=';s='+s.width+'*'+s.height
a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth)
js=12//--></script><script language="JavaScript1.3" type="text/javascript"><!--
js=13//--></script><script language="JavaScript" type="text/javascript"><!--
d.write('<a href="http://top.mail.ru/jump?from=1436203"'+
' target="_top"><img src="http://da.ce.b5.a1.top.list.ru/counter'+
'?id=1436203;t=50;js='+js+a+';rand='+Math.random()+
'" alt="Рейтинг@Mail.ru"'+' border="0" height="31" width="88"/><\/a>')
if(11<js)d.write('<'+'!-- ')//--></script><noscript><a
target="_top" href="http://top.mail.ru/jump?from=1436203"><img
src="http://da.ce.b5.a1.top.list.ru/counter?js=na;id=1436203;t=50"
border="0" height="31" width="88"
alt="Рейтинг@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--
if(11<js)d.write('--'+'>')//--></script><!--/COUNTER-->

<?

        print "<script>top.location='index.php'</script>";

}
?>