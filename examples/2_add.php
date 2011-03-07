<?php
require 'init4Store.php';
require '../lib/4store/Endpoint.php';

//remove this line in your code
//start4store();

$readonly = false;
$s = new Endpoint('http://localhost:8080/',$readonly);
$r = $s->add('http://example/test', "
	@prefix foaf: <http://xmlns.com/foaf/0.1/> .
	<http://www.bordercloud.com/wiki/user:Karima_Rafes>  foaf:workplaceHomepage <http://www.bordercloud.com>.
");
var_dump($r);

//remove this line in your code
//stop4store();