<?php

namespace src\model;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;

use function src\get_string_between;
use function src\getContents;

class HorarioModel extends SiepeHandlerModel{


    function __construct(){

        $this->chAuth = curl_init();

    }



    function getHorarioRequest(Request $req, Response $res, $args): Response{
        $userToken = $req->getHeader('userToken'); 


        if($userToken){
            $user = $this->verifyUserInDatabase($userToken[0]);
            if(!$user){
                return $res->withStatus(400)->withJson(array('message'=>"User unavaliable"));
            }

            $status = $this->authUserAtSiepe($user['email'], $user['senha']);

            if(!$status){
                return $res->withStatus(400)->withJson(array('message'=>'User not authorized'));
            }

            $response = $this->getHorarioData();
            return $res->withStatus(200)->withJson($response);
        }
        return $res->withStatus(400)->withJson(array('message'=>"User not authenticated"));
    }

    private function getHorarioData(){
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/quadrodehorarios/DetalharQuadroHorarioPortal.do");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);
        
        $ewBase = get_string_between($response,"EW.loadController(",",");
        $ewId = get_string_between($response,"turmasQuadroDeHorario', ",")");
        
        curl_setopt($this->chAuth, CURLOPT_URL, "https://www.siepe.educacao.pe.gov.br/quadrodehorarios/EWServlet.ew?EWBase=".$ewBase."&EWId=".$ewId."&EWAction=loadController&EWHome=turmasQuadroDeHorario");
        curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
        $this->cURL_Setup($this->chAuth);
        $response = curl_exec($this->chAuth);

        $sanitizedData = $this->sanitizeResponse($response);


        return $sanitizedData;
    }

    private function sanitizeResponse($response){
        $tableHorarios = get_string_between($response, '<div class="TabelaHorarios">', '</div>');
       
        $tableHorariosBody = get_string_between($tableHorarios, '<tbody>', '</tbody>');
        $tableHorariosColumns = getContents($tableHorariosBody, '<tr class', '</tr>');
        $tableHorariosRows = array();

        foreach($tableHorariosColumns as $line){
            $tableHorariosRows[] = getContents($line, '<td>', '</td>');
        }  

        if(isset($tableHorariosRows[0])){
            $weekdays = array("seg","ter","quar","quin", "sext", "sab", "domi");
            $sanitizedResponse = array();
            $sanitizedResponse[0] = array("seg"=>"Seg", "ter"=>"Terç", "quar"=>"Qua", "quin"=>"Qui", "sext"=>"Sex","sab"=>"Sáb","domi"=>"Dom");
            
            for($i=0; $i<count($tableHorariosRows);$i++){
                $row = array();
                for($j=0; $j<count($tableHorariosRows[$i]); $j++){
                    $row[$weekdays[$j]] = $tableHorariosRows[$i][$j];
                }
                $sanitizedResponse[] = $row;
            }
            return $sanitizedResponse;
        }
       
        return array('message'=>'Error getting data');
    }

}