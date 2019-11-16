<?php


namespace src;

function getConfigs(): \Slim\Container {
    $configs = array('settings'=>array('displayErrorDetails'=>true));

    return new \Slim\Container($configs);
}
function getDatabaseConfigs(){
    return array(
      'host'=>'ec2-54-197-238-238.compute-1.amazonaws.com',
        'dbName'=>'d7ujai2rpota8v',
        'user'=>'tltglxkmuphebp',
        'pass'=>'c713ae3b4078a94e6ea8a1b46378012fd87733f6a457fc27d579c3e308eb6a7b',
    );
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getContents($str, $startDelimiter, $endDelimiter) {
    $contents = array();
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = $contentStart = $contentEnd = 0;
    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
      $contentStart += $startDelimiterLength;
      $contentEnd = strpos($str, $endDelimiter, $contentStart);
      if (false === $contentEnd) {
        break;
      }
      $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
      $startFrom = $contentEnd + $endDelimiterLength;
    }

    return $contents;
  }

