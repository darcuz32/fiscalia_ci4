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
        
        $url = strtolower((string) $request->getUri()->getSegment(1));
        if ($request->getUri()->getSegment(2) !== '') {
            $url .= '/' . $request->getUri()->getSegment(2);
        }

        // Cargar los menús
        $this->head         = view('head_view');
        $this->sidebar      = view('sidebar_view', ['active' => $url]);
        $this->header       = view('header_view');
        $this->rightsidebar = view('rightsidebar_view'); 
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


}