<?php
/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class user {

	public $data;
	public $bonus_storage 	= 1;
	public $bonus_metal 	= 1;
	public $bonus_crystal 	= 1;
	public $bonus_deuterium = 1;
	public $bonus_energy 	= 1;
	public $bonus_solar 	= 1;

	public $bonus_res_fleet 	= 1;
	public $bonus_res_defence 	= 1;
	public $bonus_res_research 	= 1;
	public $bonus_res_building 	= 1;
	public $bonus_res_levelup 	= 1;

	public $bonus_time_fleet 	= 1;
	public $bonus_time_defence 	= 1;
	public $bonus_time_research = 1;
	public $bonus_time_building = 1;

	public $bonus_fleet_fuel	= 1;
	public $bonus_fleet_speed	= 1;

	public function load_from_array($array, $parse = true)
	{
		$this->data = $array;

		if ($parse)
			$this->ParseUserData();
	}

	public function load_from_id($user_id, $fields = '*', $parse = true)
	{
		if (isset($user_id) && $user_id > 0)
			$this->data = db::query("SELECT ".$fields." FROM {{table}} WHERE id = ".$user_id."", "users", true);
		else
			die('Информация о игроке не может быть загружена #1');

		if ($parse)
			$this->ParseUserData();
	}

	private function ParseUserData()
	{
		if (!isset($this->data['id']))
			die('Информация о игроке не может быть загружена #2');

		if ($this->data['rpg_geologue'] > time())
		{
			$this->bonus_metal 			+= 0.25;
			$this->bonus_crystal 		+= 0.25;
			$this->bonus_deuterium 		+= 0.25;
			$this->bonus_storage 		+= 0.25;
		}
		if ($this->data['rpg_ingenieur'] > time())
		{
			$this->bonus_energy 		+= 0.15;
			$this->bonus_solar 			+= 0.15;
			$this->bonus_res_defence 	-= 0.1;
		}
		if ($this->data['rpg_admiral'] > time())
		{
			$this->bonus_res_fleet 		-= 0.1;
			$this->bonus_fleet_speed	+= 0.25;
		}
		if ($this->data['rpg_constructeur'] > time())
		{
			$this->bonus_time_fleet 	-= 0.25;
			$this->bonus_time_defence 	-= 0.25;
			$this->bonus_time_building 	-= 0.25;
		}
		if ($this->data['rpg_technocrate'] > time())
		{
			$this->bonus_time_research	-= 0.25;
		}
		if ($this->data['rpg_meta'] > time())
		{
			$this->bonus_fleet_fuel		-= 0.1;
		}

		if ($this->data['race'] == 1)
		{
			$this->bonus_metal 			+= 0.15;
			$this->bonus_solar 			+= 0.15;
			$this->bonus_res_levelup 	-= 0.1;
			$this->bonus_time_fleet		-= 0.1;
		}
		elseif ($this->data['race'] == 2)
		{
			$this->bonus_deuterium 		+= 0.15;
			$this->bonus_solar 			+= 0.05;
			$this->bonus_storage 		+= 0.2;
			$this->bonus_res_fleet 		-= 0.1;
		}
		elseif ($this->data['race'] == 3)
		{
			$this->bonus_metal 			+= 0.05;
			$this->bonus_crystal 		+= 0.05;
			$this->bonus_deuterium 		+= 0.05;
			$this->bonus_res_defence 	-= 0.1;
			$this->bonus_res_building 	-= 0.1;
			$this->bonus_time_building	-= 0.1;
		}
		elseif ($this->data['race'] == 4)
		{
			$this->bonus_crystal 		+= 0.15;
			$this->bonus_energy 		+= 0.05;
			$this->bonus_res_research	-= 0.1;
			$this->bonus_fleet_speed	+= 0.1;
		}
	}

}

 ?>