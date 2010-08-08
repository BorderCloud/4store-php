<?php
require 'init4Store.php';
require '../lib/FourStore/FourStore_Store.php';

//remove this line in your code
//start4store();

$s = new FourStore_Store('http://localhost:8080/sparql/');

//Create the graph
$r = $s->set('http://example/test', "
	@prefix foaf: <http://xmlns.com/foaf/0.1/> .
	<http://github.com/bordercloud/4store-php> foaf:maker <http://www.bordercloud.com/wiki/user:Karima_Rafes> .
");

//AND delete the graph
$r = $s->delete('http://example/test');

var_dump($r);

//remove this line in your code
//stop4store();