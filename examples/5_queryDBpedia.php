<?php
/** 
 * @version 0.4.1
 * @package Bourdercloud/PHP4store
 * @copyright (c) 2011 Bourdercloud.com
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://www.opensource.org/licenses/mit-license.php
 */

/** 
 * @ignore
 */
//require_once('php4store/Endpoint.php');
require '../lib/php4store/Endpoint.php';

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