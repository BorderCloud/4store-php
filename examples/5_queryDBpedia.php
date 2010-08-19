<?php
require '../lib/FourStore/FourStore_StorePlus.php';

	$endpoint ="http://dbpedia.org/sparql";
	
// READ ONLY  ******************************************
	
	//put argument false to write
	$sp_readonly = new FourStore_StorePlus($endpoint);

    $q = "select *  where {?x ?y ?z.} LIMIT 5";
    $rows = $sp_readonly->query($q, 'rows');
    $err = $sp_readonly->getErrors();
    if ($err) {
	    print_r($err);
	    throw new Exception(print_r($err,true));
	}
	var_dump($rows);