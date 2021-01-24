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

    //Usado na aba "Ramais"
    private function getRamaisAjax()
    {
        $response = $this->Mod->getData('SELECT * FROM ramais;');
        $list = [];
        foreach ($response as $key => $ramais) {
            $list[$key]['nome'] = $ramais['nomecolab_ramal'];
            $list[$key]['ramal'] = $ramais['ramal_ramal'];
            $list[$key]['id'] = $ramais['idcolab_ramal'];
        }
        // get the q parameter from URL
        $wordSearch = $this->dataurl['str'];
        $hint = "";
        // lookup all hints from array if $q is different from ""
        if ($wordSearch !== "") {
            $wordSearch = strtolower($wordSearch);
            $len = strlen($wordSearch);
            foreach ($list as $key => $table) {
                if (stristr($wordSearch, substr($table['nome'], 0, $len))) {
                    if ($hint === "") {
                        $hint = " <tr class='transition-all hover:bg-gray-100 hover:shadow-lg'> <td class='px-6 py-4 whitespace-nowrap'> <div class='flex items-center'> <div class='flex-shrink-0 w-10 h-10'>".$this->ctl->getProfilePicture($table['id'], 'svg-inline--fa fa-user-circle fa-w-16 rounded-full shadow-xl mx-auto h-10 w-10')."</div><div class='ml-4'> <div class='text-sm font-medium text-gray-900'>" . $table['nome'] . "</div><div class='text-sm text-gray-500'></div></div></div></td><td class='px-6 py-4 whitespace-nowrap'> <div class='text-sm text-gray-900'>" . $table['ramal'] . "</div><div class='text-sm text-gray-500'></div></td></tr>";
                    } else {
                        $hint .= " <tr class='transition-all hover:bg-gray-100 hover:shadow-lg'> <td class='px-6 py-4 whitespace-nowrap'> <div class='flex items-center'> <div class='flex-shrink-0 w-10 h-10'> <svg aria-hidden='true' focusable='false' data-prefix='fas' data-icon='user-circle' class='svg-inline--fa fa-user-circle fa-w-16 ' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 496 512'> <path fill='#c6f6d5' d='M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 96c48.6 0 88 39.4 88 88s-39.4 88-88 88-88-39.4-88-88 39.4-88 88-88zm0 344c-58.7 0-111.3-26.6-146.5-68.2 18.8-35.4 55.6-59.8 98.5-59.8 2.4 0 4.8.4 7.1 1.1 13 4.2 26.6 6.9 40.9 6.9 14.3 0 28-2.7 40.9-6.9 2.3-.7 4.7-1.1 7.1-1.1 42.9 0 79.7 24.4 98.5 59.8C359.3 421.4 306.7 448 248 448z'></path> </svg> </div><div class='ml-4'> <div class='text-sm font-medium text-gray-900'>" . $table['nome'] . "</div><div class='text-sm text-gray-500'></div></div></div></td><td class='px-6 py-4 whitespace-nowrap'> <div class='text-sm text-gray-900'>" . $table['ramal'] . "</div><div class='text-sm text-gray-500'></div></td></tr> ";
                    }
                }
            }
            if ($hint == "") {
                $hint = '<div class="w-full p-2 text-left text-green-400 text-llg">Nenhum Ramal encontrado</div>';
            }
            echo $hint;
        }
    }

    private function auth()
    {
        if (empty($this->local['email']) || empty($this->local['pass'])) {
            $this->redirectTo("Login/EmptyFields");
            return false;
        }
        $response = $this->Mod->getData('SELECT * FROM colaboradores WHERE email_colab = "' . $this->local['email'] . '" AND senha_colab = "' . $this->saltPass(md5($this->local['pass'])) . '";');
        if (isset($response[0]['nome_colab'])) {
            $_SESSION['nome'] = $response[0]['nome_colab'];
            $_SESSION['login'] = $response[0]['email_colab'];
            $_SESSION['id'] = $response[0]['id_colab'];
            $_SESSION['tipo'] = $response[0]['tipo_colab'];
            $_SESSION['ativo'] = $response[0]['active_colab'];
            $_SESSION['comum'] = true;
            $this->redirectTo("");
            return true;
        } else {
            $this->redirectTo("Login/IncorrectPass");
            return false;
        }
    }

    protected function lembrar()
    {
        session_unset();
        session_destroy();
        session_cache_expire(1440); //valor em minutos (1440 = 1 dia)
    }

    protected function salt()
    {
        $string = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
        $retorno = '';
        for ($i = 1; $i <= 22; $i++) {
            $rand = mt_rand(1, strlen($string));
            $retorno .= $string[$rand - 1];
        }
        return $retorno;
    }

    protected function saltPass($hash)
    {
        $salt = str_split($hash);
        $out = "";
        foreach ($salt as $key => $letter) {
            $saltCalcule = $key * 394;
            $out .= "" . $saltCalcule . $letter;
        }
        return $out;
    }

    protected function hash($senha)
    {
        return crypt($senha, '$2a$10$' . $this->salt() . '$');
    }

    private function requireKey()
    {
        if (!empty($this->local)) {
            $keylist = str_replace('key', "", $this->local["listKeys"]);
            $keylist = explode("/", $keylist);
            $arrData[1] = $_SESSION['id'];
            $arrData[2] = $this->local["colab"];
            $arrData[3] = $this->local["comentario"];
            $sql = "INSERT INTO `keys_users`(`id_keyu`, `value_keyu`, `user_create_keyu`, `user_recieve_keyu`, `explain_keyu`) VALUES (NULL, ?, ?, ?, ?);";
            foreach ($keylist as $k) {
                $arrData[0] = $k;
                $insert =  $this->Mod->insertDataEncapsule($sql, $arrData);
            }
            /*
            $grafia = '';
            count($keylist) > 1 ? $grafia = 's' : $grafia = '';
            $message = "Chave" . $grafia . " indicada" . $grafia . " com sucesso!"; 
            */
            $this->redirectTo("Chaves/sendKeySuccess");
        } else {
            $this->redirectTo("Chaves/sendKeyFail");
        }
    }

    private function resetPassword()
    {
        if (!empty($this->dataurl)) {
            $email = $this->dataurl['emailreset'];
            $id = $this->getIdByEmail($email);
            $hashUser = $this->generateHashForReset($id);
            $arrData[0] = $hashUser;
            $arrData[1] = date('Y-m-d H:i:s');
            $arrData[2] = $id;
            $insert =  $this->Mod->insertDataEncapsule("INSERT INTO `reset_senha`(`id_reset`, `hash_reset`, `time_reset`, `id_colab_reset`) VALUES (NULL,?,?,?)", $arrData);
            if ($insert) {
                $link = URL . "/resetPassword/?hash=" . $hashUser;
                $corpo = '<!DOCTYPE html><html><title></title><head><style type="text/css"></style></head><body><h3>Reset de senha solicitado pelo JHNET</h3><p>Clique no link e altere sua senha: <a href="' . $link . '">' . URL . '/resetPassword</a></p><Br><h7>Caso não tenha solicitado o reset contate a equipe de TI</h7></body></html>';
                $send = $this->sendEmail($email, "sitejhcg@gmail.com", "JHNET - MENSAGEM AUTOMATICA", "RESET DE SENHA", $corpo);
                echo '{"status": true}';
            } else {
                echo '{"status": false}';
            }
        } else {
            echo '{"status": false}';
        }
    }

    private function changePassReset()
    {
        if (!empty($this->local)) {
            if ($this->checkPassword($this->local['newpass'])) {
                $this->redirectTo('changePassword');
            } else {
                $arrData[0] = $this->saltPass(md5($this->local['newpass']));
                $arrData[1] = $this->local['colab'];
                $insert =  $this->Mod->insertDataEncapsule('UPDATE `colaboradores` SET `senha_colab`= ? WHERE `id_colab`= ?;', $arrData);
                if ($insert) {
                    $arrDataDelete[0] = $this->local['colab'];
                    $this->Mod->insertDataEncapsule('DELETE FROM `reset_senha` WHERE `id_colab_reset` = ?;', $arrDataDelete);
                    $this->redirectTo('Login/?e=reset');
                } else {
                    $this->toHome();
                }
            }
        }
    }



    private function changePass()
    {
        if (!empty($this->local)) {
            if ($this->checkPassword($this->local['newpass'])) {
                $this->redirectTo('changePassword');
            } else {
                $arrData[0] = $this->saltPass(md5($this->local['newpass']));
                $arrData[1] = $_SESSION['id'];
                $insert =  $this->Mod->insertDataEncapsule('UPDATE `colaboradores` SET `senha_colab`= ? ,`active_colab`= 1 WHERE `id_colab`= ?;', $arrData);
                if ($insert) {
                    $_SESSION['ativo'] = 1;
                    $this->toHome();
                } else {
                    $this->toHome();
                }
            }
        }
    }

    private function checkPassword($pwd)
    {
        if (strlen($pwd) < 8) {
            $_SESSION['erroPass'] = "Senha é muito curta, é necessário ter no mínimo 8 caracteres!";
            return true;
        }
        if (!preg_match("#[0-9]+#", $pwd)) {
            $_SESSION['erroPass'] = "Senha precisa conter pelo menos um número!";
            return true;
        }
        if (!preg_match("#[a-zA-Z]+#", $pwd)) {
            $_SESSION['erroPass'] = "Senha precisa conter pelo menos uma letra maiúscula e uma minúscula!";
            return true;
        }
        return false;
    }

    //Altera a foto de perfil do colaborador
    private function changeProfilePicture()
    {
        if (!empty($_FILES)) {
            $dataImage = file_get_contents($_FILES["image-input"]["tmp_name"]);
            $ext = $this->getExtensionFromFile($_FILES["image-input"]);
            if(!in_array($ext,['png','jpeg','jpg'])){$this->redirectTo('Profile/pictureFailExtension/'.$ext);}
            if($_FILES["image-input"]["size"] > 2000000){$this->redirectTo('Profile/pictureFailSize2mb');}
            $arrData[0] = "avatar_".$_SESSION['id'].".".$ext;
            $create = $this->createImageFile($dataImage,$arrData[0],"images/avatar/");
            if ($create) {
                $this->redirectTo('Profile/pictureSuccess');
            } else {
                $this->redirectTo('Profile/pictureFail');
            }
        }
    }

    //Cria a imagem a partir de uma data URL Base 64
    private function createImageFile($data,$nameFile,$path)
    {

		$list = scandir('images/avatar/');
		foreach($list as $file){
			$nameFile = explode(".",$file);
			if($nameFile[0] == "avatar_".$_SESSION['id']){
                unlink("images/avatar/avatar_".$_SESSION['id'].".".$nameFile[1]);
				break;
			}
		}
        $response = file_put_contents($path.$nameFile[0].".".$nameFile[1], $data);
        if(gettype($response) == "boolean"){
            return false;
        }else{
            return true;
        }
    }

    //Obtem a extensão de Data URL em Base64
    private function getExtensionFromFile($fileName)
    {
        $part = explode(".", $fileName["name"]);
        return $part[1];
    }

    private function updateColab()
    {
        if (!empty($this->local)) {
            $arrData[0] = $this->local['cpf'];
            $arrData[1] = $this->local['rg'];
            $arrData[2] = $this->local['data_entrada'];
            $arrData[3] = $this->local['cargo'];
            $arrData[4] = $this->local['filial'];
            $arrData[5] = $this->local['contrato'];
            $arrData[6] = $this->local['id'];
            $insert =  $this->Mod->insertDataEncapsule('UPDATE `colaboradores` SET `cpf_colab`=?, `rg_colab`=?, `dtcontrata_colab`=?,`id_cargo_colab`= ?,`id_escritorio_colab`=?,`id_contrato_colab`=? WHERE id_colab = ?;', $arrData);
            if ($insert) {
                $this->redirectWithMessage('success', 'Dados Atualizados com sucesso');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao atualizar dados');
            }
        }
    }

    private function enviaSugestao()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao executar ação');
        }
        $nome = empty($this->local["nome"]) ? NULL : $this->local["nome"];
        $email = empty($this->local["email"]) ? NULL : $this->local["email"];
        $insert = $this->Mod->insertData('INSERT INTO `sugestoes`(`id_sugest`, `nome_sugest`, `email_sugest`, `sugest_sugest`) VALUES (NULL,"' . $nome . '","' . $email . '","' . $this->local["comentario"] . '");');
        if ($insert) {
            $this->redirectWithMessage('success', 'Sugestão enviada com sucesso!');
        } else {
            $this->redirectWithMessage('fail', 'Falha ao enviar sugestão');
        }
    }

    private function generateCode()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        }
        $email = $this->getEmailColabById($this->local['colab']);
        $nome = $this->getNameColabById($this->local['colab']);
        $codigo = $this->createCode($nome);
        $this->sendEmailCodeSuporte($email, $nome, $codigo);
        $this->redirectWithMessage('success', 'Email de atendimento enviado conforme solicitado');
    }

    private function createCode($name)
    {
        $arrName = explode(' ', $name);
        $person = substr($arrName[0], 0, 3);
        $lastName = substr($arrName[1], 0, 3);
        $code = $person . $lastName . date('H') . date('i') . date('d') . date('m') . date('Y');
        return strtoupper($code);
    }

    private function addVaga()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        }
        if (isset($_SESSION['id'])) {
            $arrData[0] = date('Y-m-d');
            $arrData[1] = $this->local['editordata'];
            $arrData[2] = $this->local['cargo'];
            $arrData[3] = $this->local['area'];

            $insert =  $this->Mod->insertDataEncapsule('INSERT INTO `vagas`(`id_vagas`, `data_vagas`, `desc_vagas`, `cargo_vagas`, `area_vagas`, `status_vagas`, `idgestor_vagas`) VALUES (NULL,?,?,?,?,1,' . $_SESSION['id'] . ');', $arrData);
            if ($insert) {
                $this->redirectWithMessage('success', 'Vaga publicada com sucesso');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao publicar vaga');
            }
        } else {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        }
    }

    private function editVaga()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        }
        if (isset($_SESSION['id'])) {
            $arrData[0] = $this->local['editordata'];
            $arrData[1] = $this->local['cargo'];
            $arrData[2] = $this->local['area'];
            $arrData[3] = $this->local['idForm'];

            $insert =  $this->Mod->insertDataEncapsule('UPDATE `vagas` SET `desc_vagas`= ?,`cargo_vagas`= ?,`area_vagas`= ? WHERE id_vagas = ?;', $arrData);
            if ($insert) {
                $this->redirectWithMessage('success', 'Vaga atualizada com sucesso!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao atualiza vaga!');
            }
        } else {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        }
    }

    private function deleteVaga()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        }
        if (isset($_SESSION['id'])) {
            $arrData[0] = $this->local['idBD'];

            $insert =  $this->Mod->insertDataEncapsule('DELETE FROM `vagas` WHERE id_vagas = ?;', $arrData);
            if ($insert) {
                $this->redirectWithMessage('success', 'Vaga deletada!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao deletar vaga');
            }
        } else {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        }
    }

    private function addPost()
    {
        if (isset($_SESSION['id'])) {
            $arrData[0] = $this->local['titulo'];
            $arrData[1] = $this->local['resumo'];
            $arrData[2] = $this->local['editordata'];
            $arrData[3] = $_SESSION['id'];
            $arrData[4] = $this->local['categoria'];
            $arrData[5] = $_SESSION['token_gestor'];

            $insert =  $this->Mod->insertDataEncapsule('INSERT INTO `posts`(`id_post`, `titulo_post`, `resumo_post`, `conteudo_post`, `idgestor_post`, `cat_post`, `usertoken_post`) VALUES (NULL,?,?,?,?,?,?);', $arrData);
            if ($insert) {
                $this->redirectWithMessage('success', 'Post publicado com sucesso!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao publigar postagem!');
            }
        }
    }

    private function rejectKey()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        } else {
            $solicitante = $this->Mod->getData('SELECT nome_colab,email_colab FROM colaboradores WHERE id_colab = (SELECT user_create_keyu FROM keys_users WHERE id_keyu = ' . $this->local['id_key'] . ');');
            $response = $this->Mod->insertData("DELETE FROM `keys_users` WHERE `keys_users`.`id_keyu` = " . $this->local['id_key'] . " ;");
            if ($response) {
                $this->sendEmailReject($solicitante[0], $this->local['motivo']);
                $this->redirectWithMessage('success', 'Rejeição de chave enviada com sucesso!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao tentar rejeitar chave!');
            }
        }
    }

    private function aproveKey()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        } else {
            $update = $this->Mod->insertData('UPDATE `keys_users` SET `active_keyu` = 1, id_aprove_adm = ' . $_SESSION['id'] . '  WHERE id_keyu = ' . $this->local['id_keyf'] . ';');
            //send a email to user_receive
            if ($update) {
                $this->redirectWithMessage('success', 'Chave aprovada com sucesso!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao tentar aprovar chave!');
            }
        }
    }

    private function declaraInteresse()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        } else {
            $insert =  $this->Mod->insertData('INSERT INTO `interesse_vaga`(`id_interesse`, `vaga_interesse`, `colaborador_interesse`) VALUES (NULL,' . $this->local["vaga"] . ',' . $_SESSION["id"] . ')');
            if ($insert) {
                $this->sendEmailInteresse($this->local["vaga"], $_SESSION["id"]);
                $this->redirectWithMessage('success', 'Email enviado conforme solicitado!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao tentar enviar email!');
            }
        }
    }

    private function criaChapa()
    {
        if (empty($this->local)) {
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        } else {
            $data[0] = $this->local["nome"];
            $data[1] = $this->local["colab1"];
            $data[2] = $this->local["colab2"];
            $data[3] = $this->local["colab3"];
            $insert = $this->Mod->insertDataEncapsule('INSERT INTO `chapa`(`id_chapa`, `nome_chapa`, `id_colab1_chapa`, `id_colab2_chapa`, `id_colab3_chapa`) VALUES (NULL,?,?,?,?);', $data);
            if ($insert) {
                $this->redirectWithMessage('success', 'Candidatura enviada!');
            } else {
                $this->redirectWithMessage('fail', 'Houve um erro ao enviar candidatura!');
            }
        }
    }

    private function generateReportKey()
    {
        $sql = 'SELECT id_keyu,name_key,c1.nome_colab as indicado, c2.nome_colab as solicitante,date(datainsert_keyu) as datainsert,explain_keyu FROM keys_users,keyfix,colaboradores c1 , colaboradores c2 WHERE id_key = value_keyu AND c1.id_colab = user_recieve_keyu AND c2.id_colab = user_create_keyu AND active_keyu = 1 AND month(datainsert_keyu) = ' . date("m") . ' ORDER BY c1.nome_colab;';
        $report = $this->Mod->getData($sql);
        $table = '<style> h1 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 24px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 26.4px; } h3 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 15.4px; } p { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 20px; } blockquote { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 21px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 30px; } pre { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 13px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 18.5667px; } </style>';
        $table .= "<table border='1'>";
        $table .= "<tr><th>Indicado</th><th>Chave</th><th>Solicitante</th><th>Data</th><th>Motivo</th></tr>";
        foreach ($report as $list) {
            $table .= "<tr><th>" . $list['indicado'] . "</th><th>" . $list['name_key'] . "</th><th>" . $list['solicitante'] . "</th><th>" . $list['datainsert'] . "</th><th>" . $list['explain_keyu'] . "</th></tr>";
        }
        $table .= "</table>";
        $file = "Relatorio_Chaves_JHNET_" . date('d_m_Y__H_i_s') . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $table;
        $this->redirectWithMessage('success', 'Relatorio gerado com sucesso!');
    }

    private function generateReportKeyAll()
    {
        $sql = 'SELECT id_keyu,name_key,c1.nome_colab as indicado, c2.nome_colab as solicitante,date(datainsert_keyu) as datainsert,explain_keyu FROM keys_users,keyfix,colaboradores c1 , colaboradores c2 WHERE id_key = value_keyu AND c1.id_colab = user_recieve_keyu AND c2.id_colab = user_create_keyu AND active_keyu = 1 ORDER BY c1.nome_colab;';
        $report = $this->Mod->getData($sql);
        $table = '<style> h1 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 24px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 26.4px; } h3 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 15.4px; } p { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 20px; } blockquote { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 21px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 30px; } pre { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 13px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 18.5667px; } </style>';
        $table .= "<table border='1'>";
        $table .= "<tr><th>Indicado</th><th>Chave</th><th>Solicitante</th><th>Data</th><th>Motivo</th></tr>";
        foreach ($report as $list) {
            $table .= "<tr><th>" . $list['indicado'] . "</th><th>" . $list['name_key'] . "</th><th>" . $list['solicitante'] . "</th><th>" . $list['datainsert'] . "</th><th>" . $list['explain_keyu'] . "</th></tr>";
        }
        $table .= "</table>";
        $file = "Relatorio_Chaves_JHNET_GERAL_" . date('d_m_Y__H_i_s') . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $table;
        $this->redirectWithMessage('success', 'Relatorio gerado com sucesso!');
    }

    private function generateReportDate()
    {
        if (empty($this->local)) {
            //$this->toScreen('fail','realizar','ação');
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        } else {
            $report = $this->Mod->getData('SELECT id_keyu,name_key,c1.nome_colab as indicado, c2.nome_colab as solicitante,date(datainsert_keyu) as datainsert,explain_keyu FROM keys_users,keyfix,colaboradores c1 , colaboradores c2 WHERE id_key = value_keyu AND c1.id_colab = user_recieve_keyu AND c2.id_colab = user_create_keyu AND active_keyu = 1 AND date(datainsert_keyu) between "' . $this->local['dataInit'] . '" AND "' . $this->local['dataFim'] . '" ORDER BY c1.nome_colab;');
            $table = '<style> h1 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 24px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 26.4px; } h3 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 15.4px; } p { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 20px; } blockquote { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 21px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 30px; } pre { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 13px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 18.5667px; } </style>';
            $table .= "<table border='1'>";
            $table .= "<tr><th>Indicado</th><th>Chave</th><th>Solicitante</th><th>Data</th><th>Motivo</th></tr>";
            foreach ($report as $list) {
                $table .= "<tr><th>" . $list['indicado'] . "</th><th>" . $list['name_key'] . "</th><th>" . $list['solicitante'] . "</th><th>" . $list['datainsert'] . "</th><th>" . $list['explain_keyu'] . "</th></tr>";
            }
            $table .= "</table>";
            $file = "Relatorio_Chaves_JHNET_Personalizado_generate_at_" . date('d_m_Y__H_i_s') . ".xls";
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$file");
            echo $table;
            $this->redirectWithMessage('success', 'Relatorio gerado com sucesso!');
        }
    }

    private function generateReportColab()
    {
        if (empty($this->local)) {
            //$this->toScreen('fail','realizar','ação');
            $this->redirectWithMessage('fail', 'Falha ao realizar ação');
        } else {
            $report = $this->Mod->getData('SELECT id_keyu,name_key,c1.nome_colab as indicado, c2.nome_colab as solicitante,date(datainsert_keyu) as datainsert,explain_keyu FROM keys_users,keyfix,colaboradores c1 , colaboradores c2 WHERE id_key = value_keyu AND c1.id_colab = user_recieve_keyu AND c2.id_colab = user_create_keyu AND active_keyu = 1 AND user_recieve_keyu = ' . $this->local['colab'] . ' ORDER BY c1.nome_colab;');
            $table = '<style> h1 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 24px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 26.4px; } h3 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 15.4px; } p { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 20px; } blockquote { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 21px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 30px; } pre { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 13px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 18.5667px; } </style>';
            $table .= "<table border='1'>";
            $table .= "<tr><th>Indicado</th><th>Chave</th><th>Solicitante</th><th>Data</th><th>Motivo</th></tr>";
            foreach ($report as $list) {
                $table .= "<tr><th>" . $list['indicado'] . "</th><th>" . $list['name_key'] . "</th><th>" . $list['solicitante'] . "</th><th>" . $list['datainsert'] . "</th><th>" . $list['explain_keyu'] . "</th></tr>";
            }
            $table .= "</table>";
            $file = "Relatorio_Chaves_JHNET_Personalizado_generate_at_" . date('d_m_Y__H_i_s') . ".xls";
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$file");
            echo $table;
            $this->redirectWithMessage('success', 'Relatorio gerado com sucesso!');
        }
    }

    private function generateReportKeyAllCreate()
    {
        $sql = 'SELECT count(id_keyu) as num_chaves,nome_colab FROM keys_users,colaboradores WHERE user_create_keyu = id_colab AND active_keyu = 1 GROUP BY nome_colab ORDER by num_chaves DESC;';
        $report = $this->Mod->getData($sql);
        $table = '<style> h1 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 24px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 26.4px; } h3 { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 15.4px; } p { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 20px; } blockquote { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 21px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 30px; } pre { font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif; font-size: 13px; font-style: normal; font-variant: normal; font-weight: 400; line-height: 18.5667px; } </style>';
        $table .= "<table border='1'>";
        $table .= "<tr><th>Numero de Chaves</th><th>Colaborador</th></tr>";
        foreach ($report as $list) {
            $table .= "<tr><th>" . $list['num_chaves'] . "</th><th>" . $list['nome_colab'] . "</th></tr>";
        }
        $table .= "</table>";
        $file = "Relatorio_Chaves_JHNET_ENTREGUES_" . date('d_m_Y__H_i_s') . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $table;
        $this->redirectWithMessage('success', 'Relatorio gerado com sucesso!');
    }

    private function sendEmail($para, $de, $de_nome, $assunto, $corpo, $para2 = "")
    {
        require_once("phpmailer/class.phpmailer.php");
        global $error;
        $mail = new PHPMailer();
        $mail->IsSMTP();        // Ativar SMTP
        $mail->SMTPDebug = 0;        // Debugar: 1 = erros e mensagens, 2 = mensagens apenas
        $mail->SMTPAuth = true;        // Autenticação ativada
        $mail->SMTPSecure = 'ssl';    // ssl (deprecated - descontinuada) usar tls
        $mail->Host = 'smtp.gmail.com';    // SMTP utilizado
        $mail->Port = 465;          // A porta do smtp do gmail é 465 para ssl e 587 para tsl
        $mail->Username = 'sitejhcg@gmail.com';
        $mail->Password = 'Galax@1995$';
        $mail->SetFrom($de, $de_nome);
        $mail->Subject = $assunto;
        $mail->CharSet  = 'utf-8';
        $mail->Body = $corpo;
        if ($para2 != "") {
            $mail->AddAddress($para2);
        }
        $mail->AddAddress($para);
        $mail->ContentType = 'text/html';
        if (!$mail->Send()) {
            $error = 'Mail error: ' . $mail->ErrorInfo;
            var_dump($mail->ErrorInfo);
            die();
            return false;
        }
        return true;
    }

    private function sendEmailReject($who, $motivo)
    {
        if (!empty($who)) {
            $corpo = '<!DOCTYPE html> <html> <title>Reject</title> <head> <link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"> <style type="text/css"> .main{ width: 550px; backgorund-color: white; height: 700px; } .div-footer{ height: 180px; background-color: #315240; font-family: "Roboto", sans-serif; color: white; text-align: center; font-size: 30px; } .div-body{ margin: 5px; height: 100%; background-color: white; color: black; text-align: center; font-family: "Roboto", sans-serif; font-size: 30px; } .div-head{ text-align: center; font-size: 50px; height: 150px; color: white; background-color: #315240; font-family: "Impact, Charcoal, sans-serif"; font-weight: 700; } .text-head{ text-transform: uppercase; } </style> </head> <body> <div class="main"> <div class="div-head"> <span class="text-head">SUA SOLICITAÇÃO FOI REJEITADA</span> </div> <div class="div-body"> <br> Caro(a), ' . $who["nome_colab"] . ' <br><br> ' . $motivo . ' <br> </div> <div class="div-footer"><br><br> Agradecemos sua compreensão<br> <span class="sign">Diretoria JHCG</span> </div> </div> </body> </html>';
            $boolSend = $this->sendEmail($who['email_colab'], 'sitejhcg@gmail.com', 'JHNET - MENSAGEM AUTOMATICA', 'Indicação de chave rejeitada', $corpo);
            return $boolSend;
        } else {
            return false;
        }
    }

    private function sendEmailAprove($who, $motivo)
    {
        if (!empty($who)) {
            $corpo = '<!DOCTYPE html> <html> <title>Parabens</title> <head> <link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"> <style type="text/css"> .main{ width: 550px; backgorund-color: white; height: 700px; } .div-footer{ height: 180px; background-color: #315240; font-family: "Roboto", sans-serif; color: white; text-align: center; font-size: 30px; } .div-body{ margin: 5px; height: 100%; background-color: white; color: black; text-align: center; font-family: "Roboto", sans-serif; font-size: 30px; } .div-head{ text-align: center; font-size: 50px; height: 150px; color: white; background-color: #315240; font-family: "Impact, Charcoal, sans-serif"; font-weight: 700; } .text-head{ text-transform: uppercase; } </style> </head> <body> <div class="main"> <div class="div-head"> <span class="text-head">SUA SOLICITAÇÃO FOI REJEITADA</span> </div> <div class="div-body"> <br> Caro(a), ' . $who["nome_colab"] . ' <br><br> ' . $motivo . ' <br> </div> <div class="div-footer"><br><br> Agradecemos sua compreensão<br> <span class="sign">Diretoria JHCG</span> </div> </div> </body> </html>';
            $boolSend = $this->sendEmail($who['email_colab'], 'sitejhcg@gmail.com', 'JHNET - MENSAGEM AUTOMATICA', 'Indicação de chave aprovada', $corpo);
            return $boolSend;
        } else {
            return false;
        }
    }

    private function sendEmailCodeSuporte($email, $nome, $codigo)
    {
        if (!empty($email)) {
            $corpo = '<!DOCTYPE html> <html> <title>Reject</title> <head> <link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"> <style type="text/css"> .main{ width: 550px; backgorund-color: white; height: 500px; } .div-footer{ height: 180px; background-color: #315240; font-family: "Roboto", sans-serif; color: white; text-align: center; font-size: 30px; } .div-body{ margin: 5px; height: 100%; background-color: white; color: black; text-align: center; font-family: "Roboto", sans-serif; font-size: 30px; } .div-head{ text-align: center; font-size: 50px; height: 150px; color: white; background-color: #315240; font-family: "Impact, Charcoal, sans-serif"; font-weight: 700; } .text-head{ text-transform: uppercase; } </style> </head> <body> <div class="main"> <div class="div-head"> <span class="text-head">ATENDIMENTO INICIADO</span> </div> <div class="div-body"> <br> Caro(a), ' . $nome . ' <br><br>  Atendimento iniciado pelo link do <a href="https://sistemas.jhcgadvocacia.com.br/Suporte/">suporte</a>.  Codigo de atendimento: <br> ' . $codigo . ' <br><br><img width="60%" src="https://sistemas.jhcgadvocacia.com.br/midia/LOGO_MASK.png"/> </div> <div class="div-footer"><br><br> Gabriel de Almeida<br> <span class="sign">Equipe TI JHCG™</span> </div> </div> </body> </html>';
            $boolSend = $this->sendEmail($email, 'sitejhcg@gmail.com', 'SUPORTE - TI JHCG', 'Atendimento iniciado', $corpo, 'gd_ti@jhcgadvocacia.com.br');
            return $boolSend;
        } else {
            return false;
        }
    }

    private function sendEmailInteresse($vaga, $colab)
    {
        if (!empty($vaga)) {
            $emailResponsavelVaga = $this->getEmailResponsavelVaga($vaga);
            $nomeVaga = $this->getInfoVaga($vaga);
            $nomeInteressadoColab = $this->getNomeColab($colab);
            $corpo = '<!DOCTYPE html><html><head><style type="text/css">.div-main{color: white; background-color: #2b5e32; padding: 8px;}.aviso{font-weight: 700; color: #e6959d;}.img-email{filter: grayscale(100%); width: 50%;}</style></head> <body> <div class="div-main"> <center> <h3>Declaração de interesse por vaga</h3> <br><p>' . $nomeInteressadoColab . ' declarou interesse pela vaga de ' . $nomeVaga . ' publicada por você na JHNET, não esqueça que a está informação é deletada juntamente com a vaga.</p><img class="img-email" src="https://sistemas.jhcgadvocacia.com.br/midia/LOGO_MASK.png"> <Br> <p class="aviso">E-mail automatico, não responda.</p></center> </div></body></html>';
            $boolSend = $this->sendEmail($emailResponsavelVaga, 'sitejhcg@gmail.com', 'JHNET - MENSAGEM AUTOMATICA', 'Declaração de interesse por vaga', $corpo);
            return $boolSend;
        } else {
            return false;
        }
    }

    private function sendVote()
    {
        if (isset($_SESSION['id'])) {
            $arrData[0] = $_SESSION['id'];
            $arrData[1] = $this->dataurl['r'];
            $insert = $this->Mod->insertDataEncapsule('INSERT INTO `votacao`(`id_votacao`, `id_colab_send_votacao`, `bool_response_votacao`) VALUES (NULL,?,?);', $arrData);
            if ($insert) {
                $this->redirectWithMessage('success', 'Voto realizado com sucesso!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao tentar enviar Voto!');
            }
        }
    }

    private function getNomeColab($id)
    {
        if (!empty($id)) {
            $select = $this->Mod->getData('SELECT nome_colab FROM `colaboradores` WHERE id_colab = ' . $id . ';');
            return $select[0]['nome_colab'];
        } else {
            die('Fatal Error - Nome Colab');
        }
    }

    private function getInfoVaga($idVaga)
    {
        if (!empty($idVaga)) {
            $select = $this->Mod->getData('SELECT nome_cargo FROM vagas,cargo WHERE id_cargo = cargo_vagas AND id_vagas = ' . $idVaga);
            return $select[0]['nome_cargo'];
        } else {
            die('Fatal Error - Info Vaga');
        }
    }

    private function getEmailResponsavelVaga($idVaga)
    {
        $select = $this->Mod->getData('SELECT email_colab FROM `colaboradores` WHERE id_colab = (SELECT idgestor_vagas FROM `vagas` WHERE id_vagas = ' . $idVaga . ');');
        return $select[0]['email_colab'];
    }

    private function getEmailColabById($id)
    {
        $select = $this->Mod->getData('SELECT email_colab FROM `colaboradores` WHERE id_colab = ' . $id . ';');
        return $select[0]['email_colab'];
    }

    private function getNameColabById($id)
    {
        $select = $this->Mod->getData('SELECT nome_colab FROM `colaboradores` WHERE id_colab = ' . $id . ';');
        return $select[0]['nome_colab'];
    }

    private function getColab($id)
    {
        $select = $this->Mod->getData('SELECT `id_colab`,`nome_colab`,`email_colab` FROM `colaboradores` WHERE id_colab = ' . $id . ';');
        return $select[0];
    }

    private function getIdByEmail($email)
    {
        $select = $this->Mod->getDataEncapsule('SELECT `id_colab` FROM `colaboradores` WHERE email_colab = ?;', [$email]);
        return $select[0]['id_colab'];
    }

    private function listColab()
    {
        $select = $this->Mod->getData('SELECT `id_colab`,`nome_colab` FROM `colaboradores`;');
        $return = [];
        foreach ($select as $key => $list) {
            $return[$key]['id_colab'] = $list['id_colab'];
            $return[$key]['nome_colab'] = $list['nome_colab'];
        }
        return $return;
    }

    private function Api()
    {
        $action = $this->routes->getParameter(3);
        switch ($action) {
            case 'checkColab':
                $response = $this->getColab($this->routes->getParameter(4));
                echo json_encode(array_unique($response), JSON_UNESCAPED_UNICODE);
                break;
            case 'listColab':
                $response = $this->listColab();
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                break;
            default:
                echo "{'status': false}";
                break;
        }
        die();
    }

    private function changeRequest()
    {
        if (!empty($this->local)) {
            $emailColab = $this->getEmailColabById($this->local['nome_colaborador']);
            $emailGestor = $this->getEmailColabById($_SESSION['id']);
            $nomeColab = $this->getNomeColab($this->local['nome_colaborador']);
            $nomeGestor = $this->getNomeColab($_SESSION['id']);
            $dataInicio = $this->local['data_inicio'];
            $dataFim = $this->local['data_fim'];
            $horaInicio = $this->local['hora_inicio'];
            $horaFim = $this->local['hora_Fim'];
            $motivo = strtolower($this->local['motivo']);
            $corpoPadrao = '<!DOCTYPE html> <html> <title>Request</title> <head> <link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"> <style type="text/css"> .main{ width: 550px; backgorund-color: white; height: 550px; } .div-footer{ margrin-top:30px;height: 180px; background-color: #315240; font-family: "Roboto", sans-serif; color: white; text-align: center; font-size: 30px; } .div-body{ margin: 5px; height: 100%; background-color: white; color: black; text-align: center; font-family: "Roboto", sans-serif; font-size: 30px; } .div-head{ text-align: center; font-size: 50px; height: 150px; color: white; background-color: #315240; font-family: "Impact, Charcoal, sans-serif"; font-weight: 700; } .text-head{ text-transform: uppercase; } </style> </head> <body> <div class="main"> <div class="div-head"> <span class="text-head">Solicitação Realizada</span> </div> <div class="div-body"> <br> Caro(a), ' . $nomeColab . ' <br><br>  O Gestor(a) ' . $nomeGestor . ' solicitou a troca de seu horário atual para ' . $horaInicio . ' as ' . $horaFim . ' vigente entre os dias ' . $this->Ctl->transformDataBr($dataInicio) . ' e ' . $this->Ctl->transformDataBr($dataFim) . ', por motivo de ' . $motivo . '<br><img width="60%" src="https://sistemas.jhcgadvocacia.com.br/midia/LOGO_MASK.png"/><br><br>Email enviado automaticamente, não responda.  </div> <div class="div-footer"><br><br> <span class="sign">Equipe TI JHCG™</span> </div> </div> </body> </html>';
            $corpoAltera = '<!DOCTYPE html><html><title>Request</title><style>table{border: 0.1rem solid black;}td{border-bottom: 0.1rem solid black}</style><body> <div class="main"> Solicitação - Troca de Horário <br><br><div class="div-body"> <table><tr><td>Gestor: ' . $nomeGestor . '</td></tr> <tr><td>Nome: ' . $nomeColab . '</td></tr><tr> <td>Novo Horario: ' . $horaInicio . ' à ' . $horaFim . '</td></tr><tr> <td>Dias Vigentes: ' . $this->Ctl->transformDataBr($dataInicio) . ' à ' . $this->Ctl->transformDataBr($dataFim) . '</td></tr><tr><td>Motivo: ' . $motivo . '</td></tr></table> <br><img width="60%" src="https://sistemas.jhcgadvocacia.com.br/midia/LOGO_MASK.png"/><br><br>Email enviado automaticamente, não responda. </div><div class="div-footer"><br><br><span class="sign">Equipe TI JHCG™</span> </div></div></body></html>';

            $envio = $this->sendEmail('gd_ti@jhcgadvocacia.com.br', 'sitejhcg@gmail.com', 'JHNET - MENSAGEM AUTOMATICA', 'Solicitação de troca de horário', $corpoAltera, $emailGestor);
            if ($envio) {
                $arrData[0] = $_SESSION['id'];
                $arrData[1] = $this->local['nome_colaborador'];
                $arrData[2] = $this->local['hora_inicio'];
                $arrData[3] = $this->local['hora_Fim'];
                $arrData[4] = $this->local['data_inicio'];
                $arrData[5] = $this->local['data_fim'];
                $arrData[6] = $this->local['motivo'];
                $this->Mod->insertDataEncapsule("INSERT INTO `troca_horario`(`id_troca`, `id_gestor_troca`, `id_colab_troca`, `horario_inicio_troca`, `horario_fim_troca`, `data_inicio_troca`, `data_fim_troca`, `motivo_troca`) VALUES (NULL,?,?,?,?,?,?,?);", $arrData);
                $this->sendEmail($emailColab, 'sitejhcg@gmail.com', 'JHNET - MENSAGEM AUTOMATICA', 'Solicitação de troca de horário', $corpoPadrao);
                $this->redirectWithMessage('success', 'Email enviado com sucesso!');
            } else {
                $this->redirectWithMessage('fail', 'Falha ao enviar email!');
            }
        } else {
            $this->redirectWithMessage('fail', 'Falha ao solicitar mudança de horario!');
        }
    }

    private function generateHashForReset($idColab)
    {
        $name = $this->getNomeColab($idColab);
        $name = $this->Ctl->tirarAcentos($this->Ctl->nameToView($name));
        $tkn = '';
        $aux = str_split($name);
        $lenght = count($aux);
        for ($i = 0; $i < $lenght; $i++) {
            $match = array_search($aux[$i], $this->alphabet);
            $tkn .= $match + $i;
        }
        $hashTkn = $this->saltPass($tkn);

        return md5($this->saltPass($hashTkn)) . "$" . md5($this->saltPass(date("dmYHis")));
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
