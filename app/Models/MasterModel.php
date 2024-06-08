<?php 
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;

class MasterModel
{

    private $tableName = array(
		'MASTER_MENUS'			=> 'comp_client_menu_master',
		'MASTER_USER'			=> 'user',
		
	);
	protected $db;
	public function __construct()
	{
		$db = db_connect('default');
		$this->db = $db;
	}

    function deleteProcess($masterName, $data, $pKey = 'id')
	{
        // print_r($masterName);die;
		try {
			$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
            //check table exist in array key
			if (!empty($table)) {
				$data = is_array($data) ? $data : (array)$data;
                $id = $data[$pKey];
				if (!empty($id)) {
                    $builder = $this->db->table($this->tableName);
					if ($builder->delete(array($pKey => $id))) {
						$res = array('error' => false, 'message' => 'Record deleted');
					} else {
						$error = $this->db->error();
						if ($error['code'] == '') {
							throw new \Exception('Something goes wrong.');
						} else {
							throw new \Exception('Something goes wrong.');
						}
					}
				} else { 
					throw new \Exception('Invalid Request');
				}
			} else {
				throw new \Exception('Invalid Request');
			}
		} catch (\Exception $e) {
            // print_r($e->getMessage());die;
			$res = array('error' => true, 'message' => $e->getMessage());
		}
		return $res;
	}
}
?>