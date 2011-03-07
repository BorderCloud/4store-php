<?php
require 'init4Store.php';
require '../lib/4store/Endpoint.php';

//remove this line in your code
//start4store();

$readonly = false;
$s = new Endpoint('http://localhost:8080/',$readonly); 
$r = $s->set('http://example/test', "
	@prefix foaf: <http://xmlns.com/foaf/0.1/> .
	<http://github.com/bordercloud/4store-php> foaf:maker <http://www.bordercloud.com/wiki/user:Karima_Rafes> .
");
var_dump($r);

//remove this line in your code
//stop4store();