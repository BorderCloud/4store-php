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
require 'init4Store.php';
//require_once('php4store/Endpoint.php');
require '../lib/php4store/Endpoint.php';

//remove this line in your code
start4store();

$readonly = false;
$s = new Endpoint('http://localhost:8080/',$readonly); 
$r = $s->set('http://example/test', "
	@prefix foaf: <http://xmlns.com/foaf/0.1/> .
	<http://github.com/bordercloud/4store-php> foaf:maker <http://www.bordercloud.com/wiki/user:Karima_Rafes> .
");
var_dump($r);

echo "test " . $s->count();

//remove this line in your code
stop4store();
