<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Security\Security;
use App\Traits\Commontraits;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */

    protected $helpers = ['session', 'cookie', 'date', 'form', 'url', 'security', 'csrf','view'];
    protected $sessionData;
    protected $Main;
    protected $session;
    protected $security;
    protected $csrf;
    protected $validation;

    use Commontraits;
    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->security = \Config\Services::security();
        $this->validation   = \Config\Services::validation();
        $this->csrf = \Config\Services::csrf();
        $library = $this->libraryAccess();
        $this->Main = $library->Main;
        helper('global_helper');
        // helper($this->helpers);
        // if ($this->session == false) {
        //     echo json_encode(array('statuscode' => 101, 'message' => 'access denied!'));
        //     die;
        // }
    }

    // public function test(){
    //     echo "dfsdf";die;
    // }
}
