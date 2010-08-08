<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/FourStore/FourStore_Store.php');
 
class FourStoreDataTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {       
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$r = $s->delete($graph1); 
    	$r = $s->delete($graph2);  
    }

    public function testSet()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
					 
		$this->assertTrue($r);		
		$this->assertEquals(3, $s->count($graph1));
		
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					");	 
		$this->assertTrue($r);		
		$this->assertEquals(2, $s->count($graph1));
    }
    
	/**
     * @depends testSet
     */
    public function testDelete()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	
    	$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
					 
    	//delete data of testSet
		$r = $s->delete($graph1);
		$this->assertTrue($r);				
    	$this->checkIfInitialState($s);
    }
    
    /**
     * @depends  testDelete
     */
    public function testAdd()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	
    	$this->checkIfInitialState($s);
    	
    	//set and after add
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
						a:A b:Name \"Test0\".\n");
		$this->assertTrue($r);		
		$this->assertEquals(1, $s->count($graph1));
    	
		$r = $s->add($graph1, 
					 $prefixTurtle ."\n
						a:A b:Name \"Test1\".\n");
		$this->assertTrue($r);
		$this->assertEquals(2, $s->count($graph1));
				
		//create graph and add in same time
		$r = $s->add($graph2, 
					 $prefixTurtle ."\n
						a:A b:Name \"Test0\".\n");
		$this->assertTrue($r);
		$this->assertEquals(1, $s->count($graph2));
		
		$this->assertEquals(3, $s->count());
		
    	$r = $s->delete($graph1);
		$r = $s->delete($graph2);
		
    	$this->checkIfInitialState($s);
    }
  
    private function checkIfInitialState($s){
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
		$this->assertEquals(0, $s->count($graph1));
		$this->assertEquals(0, $s->count($graph2));
		$this->assertEquals(0, $s->count());
    }
}