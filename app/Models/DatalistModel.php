<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;

class DatalistModel{
	
	protected $db;

	private $tableName = array(
		'MASTER_USER'			=> 'user'
	);


    public function __construct() {
		$db = db_connect('default');
        $this->db = $db;
    }
    function getDataList($list_name)
	{
		$ret_data	= array();
		$listname	= strtoupper($list_name);
		switch ($listname) {
			case 'MASTER_MENUS':
				$ret_data	= $this->menuList();
				break;
				case 'MASTER_USER':
					$ret_data	= $this->userList();
					break;
			default:
				break;
		}

		return $ret_data;
	}

    function menuList()
	{
        $builder = $this->db->table($this->tableName['MASTER_MENU'].' t1');
		$builder->select("t1.*, IF(t1.status=1, 'Active', 'Not Active') as status_name, IF(t1.menu_parent=0, '', t2.menu_name) AS menu_parent_name");
		$builder->join($this->tableName['MASTER_MENU']." t2", "t2.menu_id=t1.menu_parent", "left");
		$data	= $builder->get();
		$ret_data	= array(
			'records'	=> $data->getResultArray()
		);
		return $ret_data;
	}

	function userList()
	{
		$builder = $this->db->table($this->tableName['MASTER_USER'].' t1');
		$builder->select("t1.*, CONCAT(t1.first_name, ' ', t1.last_name) AS user_name, IF(t1.status=1, 'Active', 'Inactive') AS status_name, IF(t1.parent_id> 0, CONCAT(t4.first_name, ' ', t4.last_name), '') AS parent_user");
		$builder->join($this->tableName['MASTER_USER'] . " t4", "t4.id = t1.parent_id", 'left');
		$data	= $builder->get();
		$ret_data	= array(
			'records'	=> $data->getResultArray()
		);
		return $ret_data;
	}
}