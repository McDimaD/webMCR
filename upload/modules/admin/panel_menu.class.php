<?php

if(!defined("MCR")){ exit("Hacking Attempt!"); }

class submodule{
	private $core, $db, $config, $user, $lng;

	public function __construct($core){
		$this->core = $core;
		$this->db	= $core->db;
		$this->config = $core->config;
		$this->user	= $core->user;
		$this->lng	= $core->lng;

		$this->core->title = $this->lng['t_admin'];

		$bc = array(
			$this->lng['t_admin'] => BASE_URL."?mode=admin",
			'Главная' => BASE_URL."?mode=admin&do=main"
		);

		$this->core->bc = $this->core->gen_bc($bc);
	}

	private function get_items_array(){
		$query = $this->db->query("SELECT `m`.id, `m`.gid, `m`.title, `m`.`text`, `m`.`url`, `m`.`target`, `m`.`access`, `i`.img
									FROM `mcr_menu_adm` AS `m`
									LEFT JOIN `mcr_menu_adm_icons` AS `i`
										ON `i`.id=`m`.icon
									ORDER BY `priority` ASC");

		if(!$query || $this->db->num_rows($query)<=0){ return array(); }

		$items = array();

		while($ar = $this->db->fetch_assoc($query)){
			$gid = intval($ar['gid']);

			$array = array(
				"id" => $ar['id'],
				"gid" => $ar['gid'],
				"title" => $ar['title'],
				"text" => $ar['text'],
				"url" => $ar['url'],
				"target" => $ar['target'],
				"access" => $ar['access'],
				"icon" => $ar['img']
			);

			if(!isset($items[$gid])){
				$items[$gid] = array();
				array_push($items[$gid], $array);
			}else{
				array_push($items[$gid], $array);
			}
		}

		return $items;
	}

	private function item_array($items){
		ob_start();

		foreach($items as $key => $ar){

			if(!$this->core->is_access($ar['access'])){ continue; }

			$data = array(
				"ID" => intval($ar['id']),
				"GID" => intval($ar['gid']),
				"TITLE" => $this->db->HSC($ar['title']),
				"TEXT" => $this->db->HSC($ar['text']),
				"URL" => $this->db->HSC($ar['url']),
				"TARGET" => $this->db->HSC($ar['target']),
				"ICON" => $this->db->HSC($ar['icon']),
			);

			echo $this->core->sp(MCR_THEME_MOD."admin/panel_menu/menu-items/item-id.html", $data);
		}

		return ob_get_clean();
	}

	private function item_list($items=array()){

		if(empty($items)){ return $this->core->sp(MCR_THEME_MOD."admin/panel_menu/menu-items/item-none.html"); }

		$data = array(
			"ITEMS" => $this->item_array($items),
		);

		ob_start();

		echo $this->core->sp(MCR_THEME_MOD."admin/panel_menu/menu-items/item-list.html", $data);

		return ob_get_clean();
	}

	private function group_array(){

		$items = $this->get_items_array();

		$query = $this->db->query("SELECT id, title, `text`, `access`
									FROM `mcr_menu_adm_groups`
									ORDER BY `priority` ASC");

		ob_start();
		if(!$query || $this->db->num_rows($query)<=0){
			echo $this->core->sp(MCR_THEME_MOD."admin/panel_menu/menu-groups/group-none.html");
			return ob_get_clean();
		}

		while($ar = $this->db->fetch_assoc($query)){
			$id = intval($ar['id']);

			if(!$this->core->is_access($ar['access'])){ continue; }

			$list = (isset($items[$id])) ? $items[$id] : array();

			$sid = 'ajx_spl_adm_grp_'.$id;

			$status = (isset($_SESSION[$sid]) && $_SESSION[$sid]==='true') ? "closed" : "opened";

			$data = array(
				"ID"		=> $id,
				"TITLE"		=> $this->db->HSC($ar['title']),
				"TEXT"		=> $this->db->HSC($ar['text']),
				"ITEMS"		=> $this->item_list($list),
				"STATUS"	=> $status
			);

			echo $this->core->sp(MCR_THEME_MOD."admin/panel_menu/menu-groups/group-id.html", $data);
		}

			//echo $this->core->sp(MCR_THEME_MOD."admin/panel_menu/menu-groups/group-id.html", $data);

		return ob_get_clean();
	}

	private function group_list(){

		$data = array(
			"GROUPS" => $this->group_array()
		);

		ob_start();

			echo $this->core->sp(MCR_THEME_MOD."admin/panel_menu/menu-groups/group-list.html", $data);

		return ob_get_clean();
	}

	public function content(){

		ob_start();

		echo $this->group_list();

		return ob_get_clean();
	}
}

?>