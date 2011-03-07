<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/4store/Endpoint.php');
 
class FourStoreOfflineTest extends PHPUnit_Framework_TestCase
{
    public function testIsOffline()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new Endpoint($EndPoint4store,true,$modeDebug);
		$this->assertFalse($s->check());
    }
        
    public function testSelectOffline()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
					 		
		$q = 'select * where {?x ?y ?z.} ';

    	$sp = new Endpoint($EndPoint4store,true,$modeDebug);
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	//print_r($err);
		}
    	$this->assertTrue(count($err)>0);
    }
    
   public function testInsertWhenOffline()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
		$sp = new Endpoint($EndPoint4store,false,$modeDebug);
		$q = $prefixSparql." \n
			INSERT DATA {  
				GRAPH <".$graph1."> {    
				a:A b:Name \"Test2\" .   
				a:A b:Name \"Test3\" .   
				a:A b:Name \"Test4\" .  
    		}}";
		$res = $sp->query($q,'raw' );
		$err = $sp->getErrors();
	    if ($err) {
	    	//print_r($err);
		}
		
    	$this->assertTrue(count($err)>0);
    	$this->assertFalse($res);
    }

}