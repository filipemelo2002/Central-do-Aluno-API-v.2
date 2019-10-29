<?php

namespace src\controller;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use function src\get_string_between;
use function src\getContents;

class BoletinsController extends Controllers{

    public function view(Request $req, Response $res, $args): Response{
        $this->chAuth = curl_init();
        $userToken = $req->getHeader('userToken'); 
        $filter = $req->getQueryParams();
        
        if(isset($userToken[0])&&isset($filter['boletimId'])&&isset($filter['ano'])){
            $boletimId = $filter['boletimId'];
            $ano = $filter['ano'];
            $user = $this->verifyUserInDatabase($userToken[0]);
            if($user){
                $response = $this->getBoletinViewData($user['email'], $user['senha'], $boletimId, $ano);
                if($response){
                    return $res->withStatus(200)->withJson($response);
                }
                return $res->withStatus(400)->withJson(array('message'=>'No data returned'));
            }

            return $res->withStatus(400)->withJson(array('message'=>'User not avaliable'));
        }
        return $res->withStatus(400)->withJson(array('message'=>'Parameters missing'));
    }


    private function getBoletinViewData($user, $pass, $boletimId, $ano){
        $status = $this->authUserAtSiepe($user, $pass);
        if($status){
            $turmaId = $this->getTurmaId($boletimId);
            if($turmaId){
                $response = $this->getBoletimData($boletimId,$turmaId->id_turma, $ano);
                $sanitizedResponse = $this->sanitizeResponse($response);
                
                return array('info'=>$turmaId,'data'=>$sanitizedResponse);
            }
            return false;
        }
        return false;
    }
    private function sanitizeResponse($response){
        $return = array();
        foreach($response as $line){
            if(!isset($line->indice)){
                $return[] = array(
                    'materia'=>$line->descricao,
                    'nota_p1'=>$line->nota_p1,
                    'nota_p2'=>$line->nota_p2,
                    'nota_p3'=>$line->nota_p3,
                    'nota_p4'=>$line->nota_p4,
                    'nota_rf'=>$line->nota_rf,
                    'nota_rec'=>$line->nota_rec,
                );
            }

        }
        return $return;
    }
    private function getBoletimData($boletimId, $turmaId, $ano){
        curl_setopt($this->chAuth, CURLOPT_URL, "http://www.siepe.educacao.pe.gov.br/ws/eol/aluno/documentos/BoletimEscolar/componenteCurricular?idAlunoMatricula=$boletimId&idTurma=$turmaId&ano=$ano&isInterface=true&request.preventCache=");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);

        return json_decode($response);
    }
 




    public function index(Request $req, Response $res, $args): Response{
        $this->chAuth = curl_init();
        $userToken = $req->getHeader('userToken');
        if(isset($userToken[0])){
            $user = $this->verifyUserInDatabase($userToken[0]);
            
            if($user){
                $response = $this->getBoletinDatas($user['email'], $user['senha']);
                if($response){
                    return $res->withStatus(200)->withJson($response) ;
                }
                return $res->withStatus(400)->withJson(array('message'=>'Nothing was found'));
            }

            return $res->withStatus(400)->withJson(array('message'=>'User not avaliable'));
            
        }
        return $res->withStatus(400)->withJson(array('message'=>'Parameters missing'));
    }


    private function  getBoletinDatas($email, $senha){
        $status = $this->authUserAtSiepe($email, $senha);
        
        if($status){
            $boletimPage  = $this->getBoletinPage();

            $response = $this->getSanitizedData($boletimPage);
            return $response;
        }

        return false;
    }
    private function getBoletinPage(){
        
        curl_setopt($this->chAuth, CURLOPT_URL, "http://www.siepe.educacao.pe.gov.br/WebModuleBoletim/interfaceBoletimAction.do?actionType=exibirImplementacao&idAtor=1");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);
        
        return $response;
    }
    

    
    private function getSanitizedData($html){
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
}