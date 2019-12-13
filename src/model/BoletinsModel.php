<?php
namespace src\model;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

use function src\get_string_between;
use function src\getContents;

class BoletinsModel extends SiepeHandlerModel{

    
    function __construct()
    {
        $this->chAuth = curl_init();
    }
    function requestBoletinList(Request $req, Response $res, $args): Response{
        $userToken = $req->getHeader('userToken');
        if(isset($userToken[0])){
            $user = $this->verifyUserInDatabase($userToken[0]);
            if($user){

                $authUser = $this->AuthenticateUser($user['email'], $user['senha']);
                if(!($authUser)){
                    
                    return $res->withStatus(400)->withJson(array('message'=>'User not authorized'));
                }

                $response = $this->getBoletinListData();
                if($response){
                    return $res->withStatus(200)->withJson($response) ;
                }
                return $res->withStatus(400)->withJson(array('message'=>'Nothing was found'));
            }
            return $res->withStatus(400)->withJson(array('message'=>'User not found'));
        }
        return $res->withStatus(400)->withJson(array('message'=>'Parameters missing'));
    }

    function requestBoletinsData(Request $req, Response $res, $args): Response{
        $userToken = $req->getHeader('userToken'); 
        $filter = $req->getQueryParams();

        if(isset($userToken[0])&&isset($filter['boletimId'])&&isset($filter['ano'])){
            $boletimId = $filter['boletimId'];
            $ano = $filter['ano'];
            $user = $this->verifyUserInDatabase($userToken[0]);
            if($user){

                $authUser = $this->AuthenticateUser($user['email'], $user['senha']);
                if(!($authUser)){
                    return $res->withStatus(400)->withJson(array('message'=>'User not authorized'));
                }
                $response = $this->getBoletinData($boletimId, $ano);
                if($response){
                    return $res->withStatus(200)->withJson($response);
                }
                return $res->withStatus(400)->withJson(array('message'=>'No data returned'));
            }

            return $res->withStatus(400)->withJson(array('message'=>'User not found'));
        }

        return $res->withStatus(400)->withJson(array('message'=>'Parameters missing'));
    }

    private function getBoletinListData(){
        $boletimPage  = $this->getBoletinPage();

        $response = $this->getSanitizedBoletinListData($boletimPage);
        return $response;
    }
    private function getBoletinPage(){
        
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/WebModuleBoletim/interfaceBoletimAction.do?actionType=exibirImplementacao&idAtor=1");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);
        
        return $response;
    }

    private function getSanitizedBoletinListData($html){
        $table = get_string_between($html, '<ul id="divBoletim_', '</ul>');
        $lines = getContents($table, '<li>','</li>');
        
        $content = array();
        foreach($lines as $line){
            $label = get_string_between($line, ">","<");
            $ano = get_string_between($line, "','","'");
            $boletimId = get_string_between($line, "('","'");

            $content[] = array('label'=>trim(preg_replace('/(\r\n|\n|\r|\t)/', '',$label)),
                                'ano'=>intval($ano),
                                'boletimId'=>intval($boletimId));
        }

        return $content;
    }

    private function getBoletinData($boletimId, $ano){
        $turmaId = $this->getTurmaId($boletimId);
       
        if($turmaId){
            $response = $this->getBoletinContentData($boletimId,$turmaId->id_turma, $ano);
            $sanitized = $this->sanitizeBoletinListData($response);
            return array('info'=>$turmaId,'data'=>$sanitized);
        }
        return false;
    }


    private function getBoletinContentData($boletimId, $turmaId, $ano){
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/ws/eol/aluno/documentos/BoletimEscolar/componenteCurricular?idAlunoMatricula=$boletimId&idTurma=$turmaId&ano=$ano&isInterface=true&request.preventCache=");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);

        return json_decode($response);
    }
    private function AuthenticateUser($user , $pass){
        $status = $this->authUserAtSiepe($user, $pass);
        return $status;
    }

    private function sanitizeBoletinListData($response){
        $return = array();
        foreach($response as $line){
            if(!isset($line->indice)){
                $return[] = array(
                    'materia'=>$line->descricao,
                    'nota_p1'=>strval($line->nota_p1)?:"-",
                    'nota_p2'=>strval($line->nota_p2)?: "-",
                    'nota_p3'=>strval($line->nota_p3)?:"-",
                    'nota_p4'=>strval($line->nota_p4)?:"-",
                    'nota_rf'=>strval($line->nota_rf)?:"-",
                    'nota_rec'=>strval($line->nota_rec)?:"-",
                );
            }

        }
        return $return;
    }
}