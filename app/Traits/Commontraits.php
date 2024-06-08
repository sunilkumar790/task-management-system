<?php

namespace App\Traits;
use App\Libraries\Main;
use App\Controllers\BaseController;
// use App\Traits\demo;



trait Commontraits{
    
    // public function check(){
    //    $obj = new BaseController();
    //    return $obj->test();
    // }
    
    public function getSession() {
        $this->sessionData = \Config\Services::session();
        return $this->sessionData->get('CRM');
    }  

    public function libraryAccess(){
        return (object) array('Main' => new Main());
    }
}



