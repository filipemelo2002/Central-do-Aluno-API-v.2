<?php

namespace src\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function src\get_string_between;
use function src\getContents;

class HorarioController extends Controllers{

    function index(Request $req, Response $res, $args): Response{
        $this->chAuth = curl_init();
        $userToken = $req->getHeader('userToken'); 
        if($userToken){
            $user = $this->verifyUserInDatabase($userToken[0]);
            if($user){
                $response = $this->getHorarioData($user['email'], $user['senha']);
                return $res->withStatus(200)->withJson($response);
            }
            return $res->withStatus(400)->withJson(array('message'=>"User avaliable"));
        }
        return $res->withStatus(400)->withJson(array('message'=>"User not authenticated"));
    }

    private function getHorarioData($email, $senha){
        $status = $this->authUserAtSiepe($email, $senha);
        if($status){
            curl_setopt($this->chAuth, CURLOPT_URL, "http://www.siepe.educacao.pe.gov.br/quadrodehorarios/DetalharQuadroHorarioPortal.do");
            curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
            $this->cURL_Setup($this->chAuth);
            $response = curl_exec($this->chAuth);
            
            $ewBase = get_string_between($response,"EW.loadController(",",");
            $ewId = get_string_between($response,"turmasQuadroDeHorario', ",")");
            
            curl_setopt($this->chAuth, CURLOPT_URL, "http://www.siepe.educacao.pe.gov.br/quadrodehorarios/EWServlet.ew?EWBase=".$ewBase."&EWId=".$ewId."&EWAction=loadController&EWHome=turmasQuadroDeHorario");
            curl_setopt($this->chAuth, CURLOPT_CUSTOMREQUEST, "GET");
            $this->cURL_Setup($this->chAuth);
            $response = curl_exec($this->chAuth);

            $sanitizedData = $this->sanitizeResponse($response);


            return $sanitizedData;
        }
        
        return false;
    }

    private function sanitizeResponse($response){
        $tableHorarios = get_string_between($response, '<div class="TabelaHorarios">', '</div>');
       
        $tableHorariosBody = get_string_between($tableHorarios, '<tbody>', '</tbody>');
        $tableHorariosColumns = getContents($tableHorariosBody, '<tr class', '</tr>');
        $tableHorariosRows = array();

        foreach($tableHorariosColumns as $line){
            $tableHorariosRows[] = getContents($line, '<td>', '</td>');
        }  
        
        
        $sanitizedJson = array();

        $rows  = count($tableHorariosRows);
        $columns = count($tableHorariosRows[0]);

        $indexDia=0;
        $indexAula=0;

        $lengendaDia = array('seg','ter','quar','qui','sex','sab','domi');
        
        while($indexDia<$columns){
            $aulaNesteDia = array();
            while($indexAula<$rows){
                $aulaNesteDia[] =  $tableHorariosRows[$indexAula][$indexDia];;
                $indexAula++;
            }
            $indexAula=0;
            $sanitizedJson[$lengendaDia[$indexDia]] = $aulaNesteDia;
            $indexDia++;
        }
        

        return $sanitizedJson;
    }
   
}