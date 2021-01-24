<?php

/**
 * @author Gabriel de Almeida
 */
class ListModel extends Model
{
	private $Model;
	
	function __construct()
	{
		$this->Model = new parent();
	}

	//Obtem os dados cadastrados
	public function getSomething()
	{
		$sql = "SELECT * FROM table;";
		return $this->Model->getData($sql);
	}
	

}