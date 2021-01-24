<?php
/*  Classe que realiza a montagem da view e chama os componentes padrões [Menu,Footer] 
 *  Realiza a injeção das dependencias
 */
class Html{
    public $component;

    function __construct()
    {
        include_once 'lib/Components.class.php';
        $this->component = new Components();
    }

    //Realiza a montagem da pagina WEB
    public function render($view,$variables){
        $ctl = new Controller();
        SSL ? $this->forceSslLink() : '';
        $this->component->importComponent('Default/Head', true);
        $this->resolveTitle($view);
        $this->injectDependences();
        $this->importDefaultStructure() ? $this->component->importComponent('Default/Menu', true, [$view,$ctl]) : '';
        $this->component->importView($view,$variables);
        $this->injectDependencesAfter();
        $this->importDefaultStructure() ? $this->component->importComponent('Default/Footer', true) : '';
    }

    //Realiza a inclusão e injeção de dependencias
    private function injectDependences(){
        $files_array_link = ['css/font-awesome.min.css'];
        $files_array_insert = ['css/bootstrap-datetimepicker.css','css/pNnotify.css'];
        $files_arrayjs = ['js/jquery-3.3.1.min.js'];

        //DEPENDENCIAS QUE SO FUNCIONAM ATRAVES DA TAG <LINK>
        foreach ($files_array_link as $archive) {
            print_r('<link rel="stylesheet" type="text/css" href="'.URL."/".$archive.'">');
        }
        //INJETA AS DEPENDENCIAS CSS INLINE PARA MELHOR CARREGAMAENTO
        echo '<style type="text/css">';
        foreach ($files_array_insert as $archive) {
            echo file_get_contents(URL."/".$archive);
        }
        echo '</style>';
        //NECESSARIO PARA EXECUTAR jQUERY ANTES DA PAGINA
        echo '<!-- jQuery --><script type="text/javascript">';
        foreach ($files_arrayjs as $archivejs) {
            echo file_get_contents(URL."/".$archivejs);
        }
        echo '</script>';
    }

    //Realiza a inclusa e injeção de dependencias depois da pagina
    private function injectDependencesAfter(){
        $files_arrayjs = ['js/main.js'];
        echo '<!-- intra --><script type="text/javascript">';
        foreach ($files_arrayjs as $archivejs) {
            echo file_get_contents(URL."/".$archivejs);
        }
        echo '</script>';
    }

    //Gera o titulo html da pagina
    private function resolveTitle($name){
		$arr_rp = ['-','_'];
		$result = str_replace($arr_rp," ",$name);
	    echo "<title>".  BASE_TITLE . " - " . $result . "</title>";
    }

    //Verificar se deve-se importar a estrutura padrão
    private function importDefaultStructure(){
        return $_SESSION['defaultStructure'];
    }

    //Força o uso do http/SSL
    private function forceSslLink(){
        $uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $arrayUrl = parse_url($uri);
        if($arrayUrl["scheme"] != 'https'){
            echo "<script>window.location.href = '".URL."';</script>";
        }
    }


}