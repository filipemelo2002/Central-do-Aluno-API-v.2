# API Restful Central do Aluno

## Descrição
API RESTFul escrita em PHP + Slim Framework. Este sistema realiza *Webscrapping* 
no site do [SIEPE](http://www.siepe.educacao.pe.gov.br/) (Sistema de Informações da Educação de Pernambuco) utilizando as credenciais do usuários.

## Arquitetura
A API possui a arquitetura padrão de Design de APIs, onde, pode-se encontrar as suas principais camadas: _Controllers, Model, e View (JSON response)_

## Rotas da API
* `$app->get('/',SessionController::class .':index');` Testar se a API está funcionando
* `$app->post('/sessions', SessionController::class.':authUser');` Recebe os dados de Login e Senha realizar a autenticação do usuário
  * Os dados são enviados em formato JSON: `{"email":"seuusuario", "senha":"suasenha"}`
  * A API pode responder de duas formas: uma mensagem de ERRO com _status code `400`_ caso o usuário tenha preenchido seus dados erroneamente, ou retornará um JSON com os dados inseridos, assim também com uma chave de autenticação, chamada de `userToken`, que deverá ser utilizada para as próximas requisições.
* `$app->get('/boletins',BoletinsController::class .':index');` Retorna todos os boletins do Usuário
* `$app->get('/boletins/view', BoletinsController::class.':view');` Recebe como _Query Parameters_ `boletimId` e `ano`, que serão utilizados para identificar o Boletin desejado e retornar seus dados
* `$app->get('/faltas',FaltasController::class .':index');` Recebe como _Query Parameters_ `boletimId` e `ano` para retornar os dados do Percentual de Faltas e a Quantidade de Faltas
* `$app->get('/horarios', HorarioController::class.':index');` Retorna o Horário de Aulas do usuário

* OBS: * Todas as rotas, com excessão da `index` e `/sessions`, devem conter no cabeçalho da requisição o parâmetro obrigatório `userToken`, com o valor que é retornado da API, após o usuário estar devidamente autenticado. 
