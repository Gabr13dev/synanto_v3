<?php
/*  
 *  Classe principal, realiza as verificações e monta as paginas exibidas
 *  Realiza os includes necessarios
 */
class Core{

    public $routes,$html,$template;

    function __construct()
    {
        include_once "lib/Routes.class.php";
        $this->routes = new Routes();

        include_once "lib/Html.class.php";
        $this->html = new Html();

        include_once "lib/Template.class.php";
        $this->template = new Template();
    }

    //Função de arranque do Synanto
    public function start(){
        //Obtem a rota principal
        $thisRoute = $this->routes->getParameter(1);
        //Evita algumas rotas especificas serem renderizadas
        if($this->isToRender($thisRoute)){
            //Verifica se a rota é uma request
            $this->isRequest($thisRoute);
            //Verifica se a rota existe
            if($this->routes->routeExist($thisRoute)){
                //Se existir ela verifica se a pagina é protegida
                if($this->protectedPage($thisRoute)){
                    //Se for protegida ela verifica se o usuario está logado
                    $this->isLoggedIn();
                }
                //Seta o template a ser usado pela View
                $this->template->setTemplate($this->routes->getTemplateForRoute($thisRoute),$thisRoute);
                //Obtem as variaveis da Ctl
                $variables = $this->template->getVariables();
                //Chama a função de montagem da View da rota
                $this->html->render($thisRoute,$variables);
            }else{
                //Se a rota não existir setamos o template List
                $this->template->setTemplate('List','notFoundPage');
                //Montamos a pagina de 404
                $this->html->render('404',['null']);
            }
        }
    }

    //Habilita/Desabilita o import dos Menu/Footer na pagina
    private function useDefaultStructure(bool $switch)
    {
        $_SESSION['defaultStructure'] = $switch;
    }

    //Verifica se a pagina é publica
    private function protectedPage(string $route)
    {
        $isProtected = $this->routes->getprotectionForRoute($route);
        $this->useDefaultStructure($isProtected);
        return $isProtected;
    }

    private function isToRender(string $route)
    {
       $isNoRoute = ["components","controller","css","documents","fonts","images","js","lib","model","phpmailer","scss","view","images"];
        return !(in_array($route,$isNoRoute));
    }

    private function isRequest(string $route)
    {
        if(strtolower($route) == "request"){
            include_once "lib/Request.class.php";
            $req = new Request($this->routes->getParameter(2));
        }
    }

    //Verifica se o usuario está logado e redireciona
    private function isLoggedIn()
    {
        if(!isset($_SESSION['comum'])){
            echo "<script> window.location = '".URL."/Login'</script>";
        }
    }



}