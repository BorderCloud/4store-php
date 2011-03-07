<?php
require '../lib/4store/Endpoint.php';

	$endpoint ="http://dbpedia.org/";
	
// READ ONLY  ******************************************
	
	$sp_readonly = new Endpoint($endpoint);

    $q = "select *  where {?x ?y ?z.} LIMIT 5";
    $rows = $sp_readonly->query($q, 'rows');
    $err = $sp_readonly->getErrors();
    if ($err) {
	    print_r($err);
	    throw new Exception(print_r($err,true));
	}
	var_dump($rows);