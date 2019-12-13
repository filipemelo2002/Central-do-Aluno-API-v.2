<?php

namespace src\model;

class DatabaseHandler{
    private $db;
    function __construct($dbName, $host, $user, $pass){
        try{
            $dns = "mysql:dbname=$dbName;host=$host"; 
            $this->db = new \PDO($dns, $user,$pass);
        }catch(PDOException $e){
            echo 'Connection failed: ' . $e->getMessage();
            exit();
        }
    }
    function consultarUsuarioAutenticado($userToken){
        $table =  'users';

        $sql = "SELECT email, senha FROM $table WHERE userToken = '$userToken' LIMIT 1";
        $retorno = $this->db->query($sql);
        if($retorno){
            return $retorno->fetch(\PDO::FETCH_ASSOC);
        }
        return false;
    }
    function cadastrarUsuario($userToken, $email,$senha){
        $table = 'users';
        
        $sql = "INSERT INTO $table (userToken, email, senha) VALUES(:userToken, :email, :senha)";
        
        $stmt = $this->db->prepare($sql);
        $status = $stmt->execute(array(
            ':userToken'=>$userToken,
            ':email'=>$email,
            ':senha'=>$senha,
        ));
        
        return $status;
    }



}