<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/FourStore/FourStore_Store.php');
 
class FourStoreOfflineTest extends PHPUnit_Framework_TestCase
{
    public function testIsOffline()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
		$this->assertFalse($s->check());
    }
        
    public function testSelectOffline()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
					 		
		$q = 'select * where {?x ?y ?z.} ';

    	$sp = new FourStore_StorePlus($EndPointSparql,true,$modeDebug);
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
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
		$sp = new FourStore_StorePlus($EndPointSparql,false,$modeDebug);
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