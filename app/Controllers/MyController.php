<?php

namespace App\Controllers;

use App\Libraries\Permission;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class MyController extends BaseController
{
    protected $permissions = [];
    protected $head;
    protected $sidebar;
    protected $header;
    protected $rightsidebar = '';
    protected $js;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        
        $uri = $request->getUri();
        $url = strtolower((string) $uri->getSegment(1));
        if ($uri->getTotalSegments() > 1) {
            $url .= '/' . $uri->getSegment(2);
        }

        // Cargar los menús
        $this->head         = view('head_view');
        $this->sidebar      = view('sidebar_view', ['active' => $url]);
        $this->header       = view('header_view');
        // Igual que CI3 (MY_Controller:86): el panel derecho nunca se mostró
        // y el render de rightsidebar_view se descartaba. Se deja vacío para
        // no inyectar el demo dentro de .page-content.
        $this->rightsidebar = '';
        $this->js           = view('js_view');
    }

    protected function render(string $view, array $data = []): string
    {
        return view($view, array_merge([
            'head'    => $this->head,
            'header'  => $this->header,
            'sidebar' => $this->sidebar,
            'rightsidebar' => $this->rightsidebar,
            'js'      => $this->js,
        ], $data));
    }

    function utf8_encode_deep(&$array) {
	    array_walk_recursive($array, function(&$item, $key){
	    	if(is_null($item)){$item='';}
	        if(!mb_detect_encoding($item, 'utf-8', true)){
	            $item = mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1');
	        }
    	});
    	return $array;
	}


    function encrypt_password($password) {
		$cipher = "AES-256-CBC"; 
		$key = env('app.encryption_passwords_key'); 
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); 
		
		$encrypted = openssl_encrypt($password, $cipher, $key, 0, $iv);
		return base64_encode($iv . $encrypted);
	}

	function decrypt_password($encrypted_password, $key) {
		$cipher = "AES-256-CBC";
		
		$data = base64_decode($encrypted_password);
		$iv_length = openssl_cipher_iv_length($cipher);
		$iv = substr($data, 0, $iv_length);
		$encrypted = substr($data, $iv_length);
	
		return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
	}


}