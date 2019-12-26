<?php 

namespace src\model;



class FirebaseHandler{

    private $data;
    private $apiKeys =  array(
        'serverKey'=>"AAAACStN-5U:APA91bEVQ8ltnWXOk8pHKyBv986PkGP1rCpPlVV-JvVWdYX3xR20itUkFldfzgrrE8JA8SzwiSFMZvBMKw4OsE_PI8BM7esFZutouXq2FTTleK8colbc-YyRRBNZXZuP-sg6GAHZ7ceW",
        'inheritedKey'=>'AIzaSyCGFGfHaH7MDLmuieYQVfzjGO9kLj5SWg4',
        
      );
    function __construct($title, $message){
        $this->data=array(
            'to'=>"/topics/all",
            'notification'=> array(
                'title'=>$title,
                'body'=>$message,
                
            )
        );
        
    }

    function notifyAllUsers(){
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        $headers = array(
            'Authorization: key=' . $this->apiKeys['inheritedKey'],
            'Content-Type: application/json'
        );

        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
           return false;
        }
 
        // Close connection
        curl_close($ch);
 
        return json_decode($result);

    }
}