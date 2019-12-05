<?php

namespace src\model;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class FaltasModel extends SiepeHandlerModel{

    function __construct()
    {
        $this->chAuth = curl_init();
    }
    private function AuthenticateUser($user , $pass){
        $status = $this->authUserAtSiepe($user, $pass);
        return $status;
    }
    function getFaltasData(Request $req, Response $res, $args): Response{
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

                $response = $this->getFaltasDataConent($boletimId, $ano);
                if(!$response){
                    return $res->withStatus(400)->withJson(array('message'=>'No data returned'));
                }
                return $res->withStatus(200)->withJson($response);
            }

            return $res->withStatus(400)->withJson(array('message'=>'User not avaliable'));
        }

        return $res->withStatus(400)->withJson(array('message'=>'missing parameters'));
    }


    private function getFaltasDataConent($boletimId, $ano){
        $turmaId = $this->getTurmaId($boletimId);
        if(!$turmaId){
            return false;
        }
        $percentFaltas = $this->getPercentFaltas($boletimId,$turmaId->id_turma, $ano);
        $faltasData = $this->getCountFaltas($boletimId,$turmaId->id_turma, $ano);
                
        return array('percent'=>$percentFaltas,'details'=>$faltasData);
        
    }

    private function getPercentFaltas($boletimId,$id_turma, $ano){
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/ws/eol/aluno/documentos/BoletimEscolar/percentualFaltas?idAlunoMatricula=${boletimId}&idTurma=${id_turma}&ano=${ano}&isInterface=true&request.preventCache=");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);

        return json_decode($response);
    }
    private function getCountFaltas($boletimId,$id_turma, $ano){
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/ws/eol/aluno/documentos/BoletimEscolar/componenteCurricular?idAlunoMatricula=$boletimId&idTurma=$id_turma&ano=$ano&isInterface=true&request.preventCache=");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $return = curl_exec($this->chAuth);
        $response = json_decode($return);


        $sanitizedResponse = array();
        foreach($response as $line){
            if(!isset($line->indice)){
                $sanitizedResponse[] = array(
                    'materia'=>$line->descricao,
                    'fnj_p1'=>$line->fnj_p1,
                    'fj_p1'=>$line->fj_p1,
                    'fnj_p2'=>$line->fnj_p2,
                    'fj_p2'=>$line->fj_p2,
                    'fnj_p3'=>$line->fnj_p3,
                    'fj_p3'=>$line->fj_p3,
                    'fnj_p4'=>$line->fnj_p4,
                    'fj_p4'=>$line->fj_p4,
                );
            }

        }


        return $sanitizedResponse;
    }

}