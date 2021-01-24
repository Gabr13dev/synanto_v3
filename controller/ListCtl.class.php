<?php
/*
 *
 * @author Gabriel de Almeida
 */
class ListCtl extends Controller
{
	public $data = [];
    private $model,$controller, $components;

	function __construct()
	{
		$this->model = new ListModel();
		$this->ctl = new parent();
		$this->components = new Components();
		$this->routes = new Routes();
	}

	public function Home(){
		$data['null'] = null;
		return $data;
	}


	public function notFoundPage(){
		$data['null'] = null;
		return $data;
	}

	public function getMessageHome()
	{
		switch ($this->routes->getParameter(2)) {
			default:
				return "";
				break;
		}
	}


}