<?php
require 'init4Store.php';
require '../lib/FourStore/FourStore_Store.php';

//remove this line in your code
//start4store();

$s = new FourStore_Store('http://localhost:8080/sparql/');
$r = $s->add('http://example/test', "
	@prefix foaf: <http://xmlns.com/foaf/0.1/> .
	<http://www.bordercloud.com/wiki/user:Karima_Rafes>  foaf:workplaceHomepage <http://www.bordercloud.com>.
");
var_dump($r);

//remove this line in your code
//stop4store();