<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\ApiModel;
use App\Models\MasterModel;
use App\Models\DatalistModel;

class Api extends BaseController
{

	public function initController(
		RequestInterface $request,
		ResponseInterface $response,
		LoggerInterface $logger
	) {
		parent::initController($request, $response, $logger);
		$this->setCORSHeaders();
	}

	private $ApiModel = NULL;
	private $MasterModel = NULL;
	private $DatalistModel = NULL;

	private $rawdata = NULL;


	function __construct()
	{
		$this->ApiModel 			= 	new ApiModel();
		$this->MasterModel			= new MasterModel();
		$this->DatalistModel			= new DatalistModel();
		$temp = file_get_contents("php://input");
		$this->rawdata = !empty($temp) ? json_decode($temp, true) : NULL;
	}

	private function setCORSHeaders()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, token');
	}

	function response($success, $message, $data = array(), $status_code = 200)
	{
		echo json_encode(array('success' => $success, 'message' => $message, 'result' => (count($data) < 1) ? new \stdClass() : $data, 'status_code' => $status_code));
		exit;
	}

	function getPolicyDetails()
	{
		$res = $this->ApiModel->getData($_POST);
		echo json_encode($res);
		die;
	}


	function userLogin()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->response(FALSE, "Invalid Request Method", array(), 404);
		}
		$data = json_decode(file_get_contents('php://input'));
		if (!$data) {
			$userdata = $this->ApiModel->checklogin($_POST['user_login'], md5($_POST['user_pass']));
		} else {
			$userdata = $this->ApiModel->checklogin($data->formData->user_login, md5($data->formData->user_pass));
		}
		if (!$userdata) {
			$this->response(FALSE, "Invalid Username or Password", $userdata, 400);
		} else {
			$return_data['user_data']	= array(
				'user_id'			=> $userdata[0]->id,
				'user_first_name'	=> $userdata[0]->first_name,
				'user_last_name'		=> $userdata[0]->last_name,
				'user_email_id'		=> $userdata[0]->email
			);


			$this->response(TRUE, "Logged In successfully", $return_data, 200);
		}
	}

	function logout()
	{
		$this->response(TRUE, 'Logged out successfully', array(), 200);
	}


	//MASTERS DATALIST METHOD
	function getMasterList()
	{
		if ($this->rawdata) {
			$retData	= $this->DatalistModel->getDataList($this->rawdata['page']);
			if (!empty($retData)) {
				$this->response(TRUE, '', $retData, 200);
			} else {
				$this->response(FALSE, 'Data Not Found', array(), 400);
			}
		} else {
			$this->response(FALSE, 'Data Not Found', array(), 400);
		}
	}

	//MASTERS COMMON METHOD
	function getMasterDetail()
	{
		if (!empty($this->rawdata['master_name']) && !empty($this->rawdata['id'])) {
			$masterName = strtoupper($this->rawdata['master_name']);
			if ($masterName == 'MASTER_USER') {
				$userData	= $this->ApiModel->getUserMasterData($this->rawdata['id']);
				$respArr	= $userData;
			} else {
				$this->response(FALSE, '', array(), 200);
			}
			if (!empty($respArr)) {
				$this->response(TRUE, '', $respArr, 200);
			} else {
				$this->response(FALSE, 'Data not found', array(), 400);
			}
		} else {
			$this->response(FALSE, 'Invalid Request', array(), 400);
		}
	}

	//MASTERS RECORD DELETE METHOD
	function deleteMasterDetail()
	{
		if (!empty($this->rawdata)) {
			if (!empty($this->rawdata['master_name'])) {
				$masterName = strtoupper($this->rawdata['master_name']);
				$dbData  = $this->rawdata;
				$respMsg = '';

				$primaryKey = 'id';
				if ($masterName == 'MASTER_MENUS') {
					$primaryKey = 'menu_id';
				} elseif ($masterName == 'MASTER_USER') {
					$primaryKey = 'id';
				}
				$retarr = $this->MasterModel->deleteProcess($masterName, $this->rawdata, $primaryKey);
				if (!empty($retarr['error'])) {
					$respMsg = $retarr['message'];
				} else {
					$master_title = str_replace('_', ' ', $masterName);
					$master_title = ucwords(strtolower($master_title));
					$respMsg = $master_title . ' deleted successfully';
				}
				$this->response(TRUE, $respMsg, array(), 200);
			} else {
				$this->response(FALSE, 'Invalid Request', array(), 400);
			}
		}
	}
}
