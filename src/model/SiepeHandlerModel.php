<?php


namespace src\model;

use function src\getDatabaseConfigs;

class SiepeHandlerModel{
    protected $chAuth;
    protected $cookies;

    protected function getTurmaId($boletimId){
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/ws/eol/aluno/documentos/BoletimEscolar/alunoMatricula?idAlunoMatricula=$boletimId&isInterface=true&request.preventCache=");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);
        
        return json_decode(json_encode(json_decode($response)[0]));
    }
    protected function cURL_Setup($set){
        curl_setopt($set, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Host: www.siepe.educacao.pe.gov.br';
        $headers[] = 'Origin: https://www.siepe.educacao.pe.gov.br';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = 'Accept: */*';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Referer: https://www.siepe.educacao.pe.gov.br/';
        $headers[] = 'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7';
        curl_setopt($set, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($set, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($set, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($set, CURLOPT_COOKIEFILE,$this->cookies);
        curl_setopt($set, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($set, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($set, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($set, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($set, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($set, CURLOPT_TIMEOUT, 20);
        //curl_setopt($set, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
        //$index = array_rand($this->proxies,1);
        //curl_setopt($set, CURLOPT_PROXY, $this->proxies[$index]);      
    }
    protected function authUserAtSiepe($email, $senha){
        
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/GerenciadorAcessoWeb/segurancaAction.do?actionType=ajaxLogin");
        curl_setopt($this->chAuth, CURLOPT_POST, true);
        curl_setopt($this->chAuth, CURLOPT_POSTFIELDS, http_build_query(
        		array(
           			"login"=>$email,
           			"senha" => $senha
     				)));

        $this->cURL_Setup($this->chAuth);
        $dadosAuth  = curl_exec($this->chAuth);
        if((strlen($dadosAuth) == 134||strlen($dadosAuth) == 140)){
            return true;
        }else if(strpos($dadosAuth,'manutenção')){
            return false;
        }
        return false;
    }
    protected function verifyUserInDatabase($userToken){
        $configs = getDatabaseConfigs();
        $database = new DatabaseHandler($configs['dbName'], $configs['host'], $configs['user'], $configs['pass']);

        $userCredentials  = $database->consultarUsuarioAutenticado($userToken);
        
        return $userCredentials;
    }
}