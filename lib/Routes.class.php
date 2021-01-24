<?php
/*  Classe de rotas, controla as rotas e torna a URL amigavel
 *  
 */
class Routes{

    public $arrayRoutesCurrent,$arrayRoutesList = [];

    function __construct()
    {
        $this->arrayRoutesCurrent = $this->getRoutes();
        $this->routesList();
    }

    //Lista de rotas, alterar somente esta função
    private function routesList(){
        $this->createRoute('Home','List', false);

    }

    //Adiciona a rota ao array de controle e seu template
    private function createRoute($routeName,$template,$protection){
        $route['name'] = $routeName;
        $route['template'] = $template;
        $route['protection'] = $protection;
        array_push($this->arrayRoutesList,$route); 
    }

    //Obtem as rotas da URL atual
    private function getRoutes(){
        $url = ltrim(parse_url($_SERVER['REQUEST_URI'] , PHP_URL_PATH ) , '/' );
        $rota = explode( '/' , $url );
        empty($rota[1]) ? $rota[1] = 'Home' : '';
        return $rota;
    }

    //Obtem um valor especifico da URL atual
    public function getParameter($i){
        $rotas = $this->getRoutes();
            if(count($rotas) > $i){
                return $rotas[$i];
            }else{
                return false;
            }
    }

    //Verifica se a rota existe
    public function routeExist($routeName){
        foreach($this->arrayRoutesList as $routes){
            if($routes['name'] == $routeName){return true;} 
        }
        return false;
    }

    //Obtem o template da rota passada
    public function getTemplateForRoute($routeName){
        foreach($this->arrayRoutesList as $routes){
            if($routes['name'] == $routeName){return $routes['template'];} 
        }
        throw new error('Template not found on route: '. $routeName);
    }

    //Obtem da rota passada se a pagina é publica
    public function getprotectionForRoute($routeName){
        foreach($this->arrayRoutesList as $routes){
            if($routes['name'] == $routeName){return $routes['protection'];} 
        }
        throw new error('Protection not found on route: '. $routeName);
    }


}