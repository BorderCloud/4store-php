<?php
require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/php4store/Endpoint.php');
 
class FourStoreToolsTest extends PHPUnit_Framework_TestCase
{
    public function testTest()
    {
    	//test PHPunit ;)
		$this->assertTrue(true);
    }
    
    public function testCheck()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new Endpoint($EndPoint4store,true,$modeDebug);
		$this->assertTrue($s->check());
    }
    
    /**
     * @depends testCheck
     */
    public function testCount()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new Endpoint($EndPoint4store,true,$modeDebug);
		$this->assertEquals(0, $s->count($graph1));
    }
}