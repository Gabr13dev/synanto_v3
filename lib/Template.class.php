<?php
/*  Classe de template, realiza os includes da Controller e da Model
 *  Includes realizado conforme o tipo de template passado
 *  Obtem as variaveis da View 
 */
class Template{

    public $variables;
 

    function __construct()
    {
        include_once "controller/Controller.class.php";
	  	include_once "model/Model.class.php";
    }

    //Seta o template a ser usado
    public function setTemplate($templateName,$currentRoute){
        include_once "model/".$templateName."Model.class.php";
        include_once "controller/".$templateName."Ctl.class.php";
        $ctl = $templateName."Ctl";
        $objSite = new $ctl;
        $this->variables = $objSite->$currentRoute();
    }

    public function getVariables(){
        return $this->variables;
    }

}