<?php
require 'init4Store.php';
require '../lib/FourStore/FourStore_StorePlus.php';

//remove this line in your code
//start4store();

	$graph = "http://www.bordercloud.com";	
	$endpoint ="http://localhost:8080/sparql/";
	
// WRITE in 4STORE ******************************************

	//put argument false to write
	$sp_write = new FourStore_StorePlus($endpoint,false );
	
	echo "\nInsert :";
	$q = " 
			PREFIX a: <http://example.com/test/a/>
			PREFIX b: <http://example.com/test/b/> 
			INSERT DATA {  
				GRAPH <".$graph."> {    
				a:A b:Name \"Test1\" .   
				a:A b:Name \"Test2\" .   
				a:A b:Name \"Test3\" .  
    		}}";
	$res = $sp_write->query($q,'raw');
	$err = $sp_write->getErrors();
    if ($err) {
	    print_r($err);
	    throw new Exception(print_r($err,true));
	}
	var_dump($res);
	
	echo "\nDelete :";
	$q = " 
			PREFIX a: <http://example.com/test/a/>
			PREFIX b: <http://example.com/test/b/> 
			DELETE DATA {  
				GRAPH <".$graph."> {     
				a:A b:Name \"Test2\" . 
    		}}";
	
	$res = $sp_write->query($q,'raw');
	$err = $sp_write->getErrors();
    if ($err) {
	    print_r($err);
	    throw new Exception(print_r($err,true));
	}
	var_dump($res);
	
// READ ONLY in 4STORE ******************************************
	
	//put argument false to write
	$sp_readonly = new FourStore_StorePlus($endpoint);
	
	echo "\nPrint :";
    $q = "select * where { GRAPH <".$graph."> {?x ?y ?z.}} ";
    $rows = $sp_readonly->query($q, 'rows');
    $err = $sp_readonly->getErrors();
    if ($err) {
	    print_r($err);
	    throw new Exception(print_r($err,true));
	}
	var_dump($rows);
	
	echo "\nASK  :";
    $q = "PREFIX a: <http://example.com/test/a/>
			PREFIX b: <http://example.com/test/b/> 
			ask where { GRAPH <".$graph."> {a:A b:Name \"Test3\" .}} ";
    $res = $sp_readonly->query($q, 'raw');
    $err = $sp_readonly->getErrors();
    if ($err) {
	    print_r($err);
	    throw new Exception(print_r($err,true));
	}
	var_dump($res);

//remove this line in your code
//stop4store();