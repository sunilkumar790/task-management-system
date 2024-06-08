<?php 
// app/Filters/CSRFMiddleware.php

namespace App\Filters;
// use CodeIgniter\HTTP\Message;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CSRFMiddleware
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $request = \Config\Services::request();
        $csrfCookie = $request->getCookie('csrf_cookie'); // Replace with your CSRF cookie name.
        $csrfHeaderName = 'X-CSRF-Token'; // Replace with your header name.
        $csrfHeader = $request->header($csrfHeaderName);  // Replace with your header name.
        // print_r($csrfHeader);die;

        if (!$csrfCookie || !$csrfHeader || $csrfCookie !== $csrfHeader) {
            // Handle CSRF token mismatch.
            return $this->handleCSRFMismatch();
        }

        return $request;
    }

    protected function handleCSRFMismatch()
    {
        // Handle the CSRF token mismatch, e.g., return a JSON response or redirect.
        $response = [
            'status' => 'error',
            'message' => 'CSRF token mismatch.',
        ];

        return service('response')->setJSON($response);
    }
}
