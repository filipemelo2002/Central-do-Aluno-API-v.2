<?php


namespace src;

function getConfigs(): \Slim\Container {
    $configs = array('settings'=>array('displayErrorDetails'=>true));

    return new \Slim\Container($configs);
}
function getDatabaseConfigs(){
    return array(
      'host'=>'sql10.freesqldatabase.com',
        'dbName'=>'sql10317385',
        'user'=>'sql10317385',
        'pass'=>'TEdmaxrDME',
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

