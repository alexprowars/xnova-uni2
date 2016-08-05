<?php

class cache
{
	/**
	 * @var $cache Memcache
	 */
	static public $cache;

	static function init ()
	{
		if (class_exists('Memcache', false))
		{
			self::$cache = new Memcache;
			self::$cache->connect('localhost', 11211);
			
			//self::$cache->flush();
		}

        if (function_exists('eaccelerator_set_session_handlers'))
        {
            eaccelerator_set_session_handlers();
        }
	}

	static function get ($name)
	{
		if (class_exists('Memcache', false))
			return self::$cache->get($name);
		elseif (function_exists('eaccelerator_get'))
			return eaccelerator_get($name);
		else
			return false;
	}

	static function set ($name, $value, $time = 0)
	{
		if (class_exists('Memcache', false))
			return self::$cache->set($name, $value, 0, $time);
		elseif (function_exists('eaccelerator_put'))
			return eaccelerator_put($name, $value, $time);
		else
			return false;
	}

	static function delete ($name)
	{
		if (class_exists('Memcache', false))
			return self::$cache->delete($name);
		elseif (function_exists('eaccelerator_rm'))
			return eaccelerator_rm($name);
		else
			return false;
	}
}
 
?>