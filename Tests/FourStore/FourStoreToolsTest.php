<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/FourStore/FourStore_Store.php');
 
class FourStoreToolsTest extends PHPUnit_Framework_TestCase
{
    public function testTest()
    {
    	//test PHPunit ;)
		$this->assertTrue(true);
    }
    
    public function testCheck()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
		$this->assertTrue($s->check());
    }
    
    /**
     * @depends testCheck
     */
    public function testCount()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
		$this->assertEquals(0, $s->count($graph1));
    }
   
}