<?php
/*  Classe de requisições, aqui é feito todas as ações do sistema
*   (CREATE,READ,UPDATE,DELETE)
*   (CONSULTA DE APIS)
*   ()
*/

class Request
{

    public $routes, $Mod, $local, $dataurl;

    //Construtor verifica se a função existe e executa ela
    function __construct($actionRequest)
    {
        if (method_exists($this, $actionRequest)) {
            $this->routes = new Routes();
            include_once "model/Model.class.php";
            include_once "controller/Controller.class.php";
            $this->Mod = new Model();
            $this->local = $_POST;
            $this->dataurl = $_GET;
            $this->ctl = new Controller();
            $this->$actionRequest();
            die();
        } else {
            throw new error('Action request not found: ' . $actionRequest);
        }
    }

    private function logoff()
    {
        session_unset();
        session_destroy();
        clearstatcache();
        echo "<script>window.location.href = '" . URL . "';</script>";
    }

    private function redirectWithMessage($type, $content)
    {
        $callBack_link = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : URL;
        $_SESSION['showMessage'] = true;
        $_SESSION['content'] = $content;
        $_SESSION['type'] = $type;
        echo "<script>window.location.href = '" . $callBack_link . "';</script>";
    }

    private function redirectTo($route)
    {
        echo "<script>window.location.href = '" . URL . "/" . $route . "';</script>";
    }

    private function toHome()
    {
        echo "<script>window.location.href = '" . URL . "';</script>";
    }
}
