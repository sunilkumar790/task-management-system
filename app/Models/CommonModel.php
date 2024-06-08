<?php
namespace App\Models;

class CommonModel {
    protected $userTable;
    protected $documentypeTable;
    protected $menuTable;
    protected $userrightTable;
    protected $userrolerightTable;
    protected $companyTable;
    protected $userroleTable;
	
	function __construct() {
        $this->userTable           = "crm_users";
	}
	
}
?>
