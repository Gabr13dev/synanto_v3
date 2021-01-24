<?php
/*  Controller principal
 *  Funções utilizadas em todas as paginas
 */
class Controller
{

	function __construct()
	{
		
	}

	//Obtem a foto de perfil de um colaborador (caso não seja passado o id retorna do colaborador logado)
	//Opicional, caso queira adicionar uma classe a imagem gerada
	public function getProfilePicture($id = "", $class = ""){
		$id = empty($id) ? $_SESSION['id'] : $id;
		$list = scandir('images/avatar/');
		$find = false;
		foreach($list as $file){
			$nameFile = explode(".",$file);
			if($nameFile[0] == "avatar_".$id){
				$find = true;
				$ext = $nameFile[1];
				break;
			}
		}
		if($find){
			return "<img src='".URL."/images/avatar/avatar_".$id.".".$ext."' class='".$class."' />";
		}else{
			return '<svg xmlns="http://www.w3.org/2000/svg" class="'.$class.'" aria-hidden="true" focusable="false" data-icon="user-circle" role="img" viewBox="0 0 496 512"><path fill="#c6f6d5" d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 96c48.6 0 88 39.4 88 88s-39.4 88-88 88-88-39.4-88-88 39.4-88 88-88zm0 344c-58.7 0-111.3-26.6-146.5-68.2 18.8-35.4 55.6-59.8 98.5-59.8 2.4 0 4.8.4 7.1 1.1 13 4.2 26.6 6.9 40.9 6.9 14.3 0 28-2.7 40.9-6.9 2.3-.7 4.7-1.1 7.1-1.1 42.9 0 79.7 24.4 98.5 59.8C359.3 421.4 306.7 448 248 448z"/></svg>';
		}
	}

	public function profilePictureExists($id = ""){
		$id = empty($id) ? $_SESSION['id'] : $id;
		$list = scandir('images/avatar/');
		foreach($list as $file){
			$nameFile = explode(".",$file);
			if($nameFile[0] == "avatar_".$id){
				return true;
				break;
			}
		}
		return false;
	}

	public function getExtensionProfilePicture($id = ""){
		$id = empty($id) ? $_SESSION['id'] : $id;
		$list = scandir('images/avatar/');
		foreach($list as $file){
			$nameFile = explode(".",$file);
			if($nameFile[0] == "avatar_".$id){
				return $nameFile[1];
				break;
			}
		}
	}

	//Transofrma data americana para Pt-br
	public function transformDataBr($data){
		$newData = explode('-', $data);
		return $newData[2].'/'.$newData[1].'/'.$newData[0];
	}

	//Obtem o dia de uma data americana
	public function getDayOnDate($data){
		$result = explode('-', $data);
		return $result[2];
	}

	public function formatName($name){
		return ucwords(mb_strtolower($name, 'UTF-8'));
	}

	public function limitName($name,$limit){
		$arrName = explode(" ",$name);
		$out = "";
		foreach($arrName as $key => $word){
			if($key == $limit){
				break;
			}
			$out .= $word." ";
		}
		return $out;
	}
	
	public function removeAccents($string){
    	$string = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    	return strtolower($string);
	}

	public function getOptions($option,$idName,$display,$selected = ""){
		$html = "";
		foreach($option as $line){
			$select = $line[$idName] == $selected ? 'selected':'';
			$html .= "<option value='".$line[$idName]."' ".$select.">".$line[$display]."</option>";
		}
		echo $html;
	}

	public function formatMoneyBR($value){
		return  'R$' . number_format($value,2,",",".");
	}

	public function formatMoneyUSD($value){
		$rtr = str_replace(",",".",number_format($value, 2));
		return substr($rtr,0,-3);	
	}

	public function getMonthName($numberMonth){
		$mes[1] = 'Janeiro';
		$mes[2] = 'Fevereiro';
		$mes[3] = 'Março';
		$mes[4] = 'Abril';
		$mes[5] = 'Maio';
		$mes[6] = 'Junho';
		$mes[7] = 'Julho';
		$mes[8] = 'Agosto';
		$mes[9] = 'Setembro';
		$mes[10] = 'Outubro';
		$mes[11] = 'Novembro';
		$mes[12] = 'Dezembro';
		return $mes[$numberMonth];
	}

	//Verifica se um registro está atrasado (está no passado)
	public function isLate($dateBase){
		$dt_atual = date("Y-m-d"); // data atual
		$timestamp_dt_atual 	= strtotime($dt_atual); // converte para timestamp Unix
		$dt_expira = $dateBase; // data de expiração
		$timestamp_dt_expira = strtotime($dt_expira); // converte para timestamp Unix
		// data atual é maior que a data de expiração
		if ($timestamp_dt_atual > $timestamp_dt_expira)
  			return true; // atrasado
				else
			return false; //não atrasado
	}

	public function getTemplateMessage($message,$type){
		$html = "";
		if($type == "fail"){
			$html .= '<div id="alert" class="z-50 bg-red-300 border-t-4 border-red-700 rounded-b text-red-700 px-4 py-3 shadow-md my-2 w-1/4 absolute ml-4 " role="alert">
			<div class="flex">
			  <svg class="h-6 w-6 mr-4 mt-2" fill="currentColor"
						 viewBox="0 0 20 20"
						 class="h-6 w-6">
						<path fill-rule="evenodd"
							  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
							  clip-rule="evenodd"></path>
					</svg>
			  <div>
				<p class="font-bold">Erro</p>
				<p class="text-sm">'.$message.'</p>
			  </div>
			</div>
		  </div>';
		}
		if($type == "success"){
			$html .= '<div id="alert" class="z-50 bg-green-300 border-t-4 border-green-700 rounded-b text-green-700 px-4 py-3 shadow-md my-2 w-1/4 absolute ml-4" role="alert">
			<div class="flex">
					<svg class="h-6 w-6 mr-4 mt-2" fill="currentColor"
						 viewBox="0 0 20 20"
						 class="h-6 w-6">
						<path fill-rule="evenodd"
							  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
							  clip-rule="evenodd"></path>
					</svg>
			  <div>
				<p class="font-bold">Sucesso</p>
				<p class="text-sm">'.$message.'</p>
			  </div>
			</div>
		  </div>';
		}
	  $html .= '<script>  $("#alert").delay(4000).fadeOut(3000);</script>';
	  return $html;
	}

}