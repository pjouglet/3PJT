<?php
/**
 * Created by PhpStorm.
 * User: Luciole
 * Date: 02/02/2016
 * Time: 15:21
 */

class WebService
{
    function Hello(){
        return "hello world";
    }
}

$options = array('uri' => "http://train-commander.dev");

$server = new SoapServer(NULL, $options);
$server->setClass("WebService");
$server->handle();

echo 'coucou';
/*
  /*test web-API

        $options = array('location' => "http://train-commander.dev/webservice.php", 'uri' => "http://train-commander.dev");
        $api = new \SoapClient(NULL, $options);

        echo $api->Hello();
*/
?>


