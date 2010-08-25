<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/FourStore/FourStore_NTriplesSerializer.php');
 
class FourStore_NTriplesSerializerTest extends PHPUnit_Framework_TestCase
{
    public function testLiteral()
    {
    	$ser = new FourStore_NTriplesSerializer();
    	$v = array();
    	$v['type'] = 'literal';
    	
    	$v['value'] = 'basilic';
    	$res = $ser->getTerm($v);
		$this->assertEquals("\"basilic\"", $res);		
		
    	$v['value'] = 'fraise';
    	$res = $ser->getTerm($v);
		$this->assertEquals("\"fraise\"", $res);
		
    	$v['value'] = 'rat';
    	$res = $ser->getTerm($v);
		$this->assertEquals("\"rat\"", $res);
    }
   
}