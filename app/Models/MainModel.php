<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;

class AccapiModel{
	
	protected $db;

    public function __construct() {
		$db = db_connect('default');
        $this->db = $db;
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

}
