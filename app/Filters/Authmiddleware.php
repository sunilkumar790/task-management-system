<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
// use CodeIgniter\Security\Security;

class Authmiddleware implements FilterInterface
{
    public $pageName;
    public $userDetails;
    public $rights;
    public $masterType;
    public $userId;
    public $perentId;
    protected $uri;
    public $AccModel;
    public $pageID;
    public $session;
    public $roleId;
    public $pageController;
    private $notSessionPage    = array(
        'login',
        'processlogin',
        'forgetpassword',
        'sendotp',
        'verifyOtp',
        'resetpassword',
        'changepassword',
        'refreshcaptcha',
        'logout',
        'downloadzip/',
        'Unauthorized',

    );
    private $notSessionController = [
        'Api',
    ];
    // before function
    public function before(RequestInterface $request, $arguments = null)
    {
        // echo 'here';die;
        
        // $response = service('response');

        // $response->setHeader('Access-Control-Allow-Origin', '*');
        // $response->setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        // $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');

        

        $this->session = session();
        $this->AccModel     = model('App\Models\AccModel', false);
        $this->uri  = current_url(true);

        $this->pageName = uri_string();
        $this->pageController = $request->uri->getSegment(1);

        $methodName     = $this->uri->getSegment(2);
        $CRM     = $this->session->get('CRM');
        $request = \Config\Services::request();

        if ($this->pageName == '/') {
            if (session()->get('CRM')) {
            $CRM->perms = new  \stdClass;
                if ($CRM->master_type == 1) {
                    $CRM->perms->view     = 1;
                    $cperm = $this->AccModel->checkUserPermWithoutPageID($CRM->user_id);
                    if ($CRM->perms->view == 1) {
                        $cperm = $this->AccModel->getAllMenu();
                        $newdata = (object) array('cmenu' => $cperm['MENU_NAME'], 'pmenu' => $cperm['PMENU_NAME']);
                        $this->session->set('Menu', $newdata);
                        return redirect()->to(base_url($cperm['MENU_URL']));
                    } else {
                        return redirect()->to(base_url('Unauthorized'));
                    }
                } else {
                    $cperm = $this->AccModel->checkUserPermWithoutPageID($CRM->user_id);
                    if ($cperm['VIEW_RIGHT'] == 0) {
                        $CRM->type = 'rolewise';
                        $cperm = $this->AccModel->checkRolePermWithoutPageID($CRM->roleid);
                        if ($cperm['VIEW_RIGHT'] == 0) {
                            return redirect()->to(base_url('Unauthorized'));
                        } else {
                            $newdata = (object) array('cmenu' => $cperm['MENU_NAME'], 'pmenu' => $cperm['PMENU_NAME']);
                            $this->session->set('Menu', $newdata);
                            return redirect()->to(base_url($cperm['MENU_URL']));
                        }
                    } else {
                        $newdata = (object) array('cmenu' => $cperm['MENU_NAME'], 'pmenu' => $cperm['PMENU_NAME']);
                        $this->session->set('Menu', $newdata);
                        return redirect()->to(base_url($cperm['MENU_URL']));
                    }
                }
            } else {
                return redirect()->to(base_url('login'));
            }
        }

        if (in_array($this->pageController, $this->notSessionController)) {
            // return redirect()->to($this->pageName);
        } else {
            if ($request->isAJAX() && $this->pageController != 'processlogin') {
                if ($this->pageController != 'refreshcaptcha') {
                    $csrfCookie = $request->getCookie('csrf_cookie');
                    // Replace with your CSRF cookie name.
                    $ajaxHeader = $request->getHeaderLine('X-CSRF-TOKEN');
                    if (!$csrfCookie || !$ajaxHeader || $csrfCookie !== $ajaxHeader) {
                        return $this->handleCSRFMismatch();
                    } elseif (empty($ajaxHeader)) {
                        return $this->handleCSRFMismatch();
                    }
                }
            }

            if (
                !in_array($this->pageController, $this->notSessionController) &&
                !in_array($this->pageName, $this->notSessionPage) &&
                !empty($this->pageName)
            ) {
                if (session()->has('CRM')) {
                    $CRM = $this->session->get('CRM');

                    if (count((array)$CRM) == 0) {
                        if ($request->isAJAX()) {
                            echo json_encode(array('statuscode' => 101, 'message' => 'access A denied!'));
                            die;
                        } else {

                            return redirect()->to(base_url('login'));
                        }
                    }
                    $this->userId = !empty($CRM->user_id) ? $CRM->user_id : 0;
                    $this->roleId = !empty($CRM->roleid) ? $CRM->roleid : 0;
                    $this->perentId = !empty($CRM->parent_id) ? $CRM->parent_id : 0;
                    $CRM->perm = new  \stdClass;
                    $CRM->type = '';
                    if (isset($CRM->master_type) && $CRM->master_type == 1) {
                        $CRM->perm->view     = 1;
                        $CRM->perm->add     = 1;
                        $CRM->perm->edit     = 1;
                        $CRM->perm->delete = 1;
                        $CRM->perm->excel     = 1;
                    } else {
                        $this->pageID = $this->AccModel->getPageId($methodName);

                        if ($this->pageID == 0 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                            $this->pageID = $this->AccModel->getPageId(str_replace(base_url(), '', $_SERVER['HTTP_REFERER']));
                        }
                        if ($this->pageID == 0) {
                            $this->pageID = $this->AccModel->getPageId($methodName);
                        }

                        if ($this->pageID == 0) {
                            $this->pageID = $this->AccModel->getPageId(str_replace(base_url(), '', $_SERVER['HTTP_REFERER']));
                        }

                        if ($this->pageID == 0 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                            $refUrl = explode('/', str_replace(base_url(), "", $_SERVER['HTTP_REFERER']));
                            $refUrlChk = isset($refUrl[0]) ? $refUrl[0] : '';
                            $refUrlChk = isset($refUrl[1]) ? ($refUrlChk . '/' . $refUrl[1]) : $refUrlChk;
                            $this->pageID = $this->AccModel->getPageId($refUrlChk);
                        }

                        $perm = $this->AccModel->checkUserPerm($this->userId, $this->pageID);

                        if ($perm['VIEW_RIGHT'] == 0) {
                            $perm = $this->AccModel->checkRolePerm($this->roleId, $this->pageID);

                            if ($perm['VIEW_RIGHT'] == 0) {
                                return redirect()->to(base_url('Unauthorized'));
                            } else {
                                $CRM->type = 'rolewise';
                            }
                        }

                        $CRM->perm->view    = $perm['VIEW_RIGHT'];
                        $CRM->perm->add        = $perm['ADD_RIGHT'];
                        $CRM->perm->edit    = $perm['EDIT_RIGHT'];
                        $CRM->perm->delete    = $perm['DELETE_RIGHT'];
                        $CRM->perm->excel    = $perm['EXCEL_RIGHT'];
                    }
                    if (!empty($CRM)) {
                        $this->rights            = $CRM->perm;
                    } else {
                        $this->rights            = NULL;
                    }

                    if ($this->rights != NULL && $this->rights->view != 1) {
                        if ($request->isAJAX()) {
                            echo json_encode(array('statuscode' => 101, 'message' => 'access denied!'));
                            die;
                        } else {
                            return redirect()->to(base_url('login'));
                        }
                    }
                } else {
                    if ($request->isAJAX()) {
                        echo json_encode(array('statuscode' => 101, 'message' => 'access denied!'));
                        die;
                    } else {
                        return redirect()->to(base_url('login'));
                    }
                }
            } else {
            }
        }
    }

    // after function
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        print_r("hello");
        die;
    }

    protected function handleCSRFMismatch()
    {
        // Handle the CSRF token mismatch, e.g., return a JSON response or redirect.
        $response = [
            'status' => 'false',
            'statuscode' => '405',
            'message' => 'CSRF token mismatch.',
        ];

        return service('response')->setJSON($response);
        // return redirect()->to(base_url('login'));
    }
}
