<?php
/*  Classe de componentes, realiza as verificações e monta os componentes
 *  
 */
class Components{

    function __construct()
    {

    }

    //Importa o component
    public function importComponent($componentName, $inject, $variables = []){
        extract($variables);
        if(file_exists('components/'.$componentName.'.php')){
            if($inject){
                include_once('components/'.$componentName.'.php');
            }else{
                return file_get_contents('components/'.$componentName.'.php');
            }
        }else{
            throw new error('Component not found: '. $componentName);
        } 
    }

    //Importa a view
    public function importView($viewName, $vars){
        if(file_exists('view/'.$viewName.'.php')){
            extract($vars);
            echo "<div id='app'>";
                include_once('view/'.$viewName.'.php');
            echo "</div>";
        }else{
            throw new error('View not found: '. $viewName);
        } 
    }

}