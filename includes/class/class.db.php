<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class db
{
	private static $server 		= 'localhost';
	private static $database 	= 'uni2';
	private static $login 		= 'root';
	private static $password 	= '';
	public static $log			= '';
	public static $numqueries	= 0;
	public static $link			= null;

    private function __construct()
	{}

    private function __clone()
	{}

	public static function init()
	{
		self::$link = @mysqli_connect(self::$server, self::$login, self::$password, self::$database) or die('Error connecting to database. Please try later.');

		@mysqli_query(self::$link, "SET NAMES utf8");
	}

	public static function query ($query, $table, $fetch = false)
	{
		global $game_config, $starttime;

		if (is_null(self::$link))
		{
			self::init();
		}

		if ($game_config['debug'] == 1)
		{
			$mtime        = microtime();
			$mtime        = explode(" ", $mtime);
			$mtime        = $mtime[1] + $mtime[0];
			$starttime    = $mtime;
		}

		$sql = str_replace("{{table}}", "game_".$table, $query);

		$sqlquery = mysqli_query(self::$link, $sql) or self::sql_error(mysqli_error(self::$link)."<br />$sql<br />","SQL Error");

		if ($game_config['debug'] == 1)
		{
			$mtime        = microtime();
			$mtime        = explode(" ", $mtime);
			$mtime        = $mtime[1] + $mtime[0];
			$endtime      = $mtime;
			$totaltime    = round((($endtime - $starttime)*1000), 2);

			self::$numqueries++;

			self::add_log("<tr><th>".self::$numqueries."</th><th>".htmlspecialchars($sql)."</th><th>".$fetch."</th><th>".$totaltime."</th></tr>");
		}

		if ($fetch) {
			return self::fetch_assoc($sqlquery);
		} else {
			return $sqlquery;
		}
	}

	public static function fetch_assoc ($result)
	{
		//$result = mysqli_fetch_assoc($result);

		//foreach ($result AS $key => $value) {
		//	self::add_log('<tr><th colspan="2">'.$key.'</th><th colspan="2">'.$value.'</th></tr>');
		//}

		return mysqli_fetch_assoc($result);
	}

	public static function fetch_array ($result)
	{
		return mysqli_fetch_array($result);
	}

	public static function num_rows ($result)
	{
		if ($result)
		{
			return mysqli_num_rows($result);
		}
		return 0;
	}

	public static function insert_id ()
	{
		return mysqli_insert_id(self::$link);
	}

	public static function escape_string ($string)
	{
		if (is_null(self::$link))
		{
			self::init();
		}

		return mysqli_real_escape_string(self::$link, $string);
	}

	private static function add_log ($mes)
	{
		self::$log .= $mes;
		self::$numqueries++;
	}

	public static function echo_log ()
	{
		echo "<br><table width=950 align=center><tr><td class=k colspan=4>Debug Log:</td></tr>".self::$log."</table>";
	}

	private static function sql_error ($message, $title)
	{
		if(isset($_SESSION['uid']) && $_SESSION['uid'] == 1){
			message('<font color=red>'.$message.'</font>', $title);
		}

		if(!self::$link)
			die('Сбой работы.<br><a href="http://forum.xnova.su/">Forum</a>');

		self::query("INSERT INTO {{table}} SET `error_sender` = '".((isset($_SESSION['uid'])) ? $_SESSION['uid'] : 0)."' , `error_time` = '".time()."' , `error_type` = '{$title}' , `error_text` = '".self::escape_string($message)."';", "errors");

        if (function_exists('message'))
		    message("Ошибка SQL обработчика. Добавлена запись в журнал событий.", "Ошибка");
        else
            echo 'sql error!';
	}
}
 
 ?>