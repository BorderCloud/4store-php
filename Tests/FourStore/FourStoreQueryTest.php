<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/FourStore/FourStore_StorePlus.php');
 
class FourStoreQueryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {       
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$r = $s->delete($graph1); 
    	$r = $s->delete($graph2); 
    	$r = $s->delete("default:");     
    }

    public function testSelect()
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
					 		
		$q = 'select * where {?x ?y ?z.} ';

    	$sp = new FourStore_StorePlus($EndPointSparql,true,$modeDebug);
    	
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertEquals(3,count($rows));
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testCount()
    {
    	global $modeSkipProblem,$EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
					 
		$q = 'select count(?x) AS count where {?x ?y ?z.} ';

    	$sp = new FourStore_StorePlus($EndPointSparql,true,$modeDebug);
    	
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertEquals(1,count($rows));
    	$this->assertEquals(3,$rows[0]["count"]);
    	
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testCountBug()
    {
    	global $modeSkipProblem;
    	if($modeSkipProblem)
    		$this->markTestSkipped(
              "select count(*) AS count where {?x ?y ?z.} \n
               doesn't work. 
               Fix : select count(?x) AS count where {?x ?y ?z.}"
            );
            
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
					    	
		$q = 'select count(*) AS count where {?x ?y ?z.} ';

    	$sp = new FourStore_StorePlus($EndPointSparql,$modeDebug);
    	
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertEquals(1,count($rows));
    	$this->assertEquals(3,$rows[0]["count"]);
    	
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }

    public function testAsk()
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
					 
		$sp = new FourStore_StorePlus($EndPointSparql,$modeDebug);
		
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"NON\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
 	public function testInsertWithoutParser()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test4\".
					");

		$q = $prefixSparql." \n
			INSERT DATA {  
				GRAPH <".$graph1."> {    
				a:A b:Name \"Test2\" .    
				a:A b:Name \"Test3\" .  
    		}}";
		$res = $s->queryUpdate($q );
		//print_r($res);
		$this->assertEquals(3, $s->count($graph1));		
		
		$sp = new FourStore_StorePlus($EndPointSparql,$modeDebug);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test4\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test3\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
 
 	public function testDeleteWithoutParser()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test4\".   
					a:A b:Name \"Test2\" .    
					a:A b:Name \"Test3\" . 
					");

		$q = $prefixSparql." \n
			DELETE DATA {  
				GRAPH <".$graph1."> {    
				a:A b:Name \"Test2\" . 
    		}}";
		$res = $s->queryUpdate($q );
		//print_r($res);
		$this->assertEquals(2, $s->count($graph1));		
		
		$sp = new FourStore_StorePlus($EndPointSparql,$modeDebug);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test4\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test3\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testInsertDefaultGraph()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		
		$q = $prefixSparql." \n
			INSERT DATA {      
				a:A b:Name \"Test2\" .    
				a:A b:Name \"Test3\" .  
    		}";
		$res = $s->queryUpdate($q );
		//print_r($res);
		
		$this->assertEquals(2, $s->count());
		$this->assertEquals(2, $s->count("default:"));	
		
    	$r = $s->delete("default:");    	
    	$this->checkIfInitialState($s);
    }
    
    public function testDeleteDefaultGraph()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		
		$r = $s->set("default:", 
					 $prefixTurtle ."\n
					a:A b:Name \"Test4\".   
					a:A b:Name \"Test2\" .    
					a:A b:Name \"Test3\" . 
					");

		$q = $prefixSparql." \n
			DELETE DATA {      
				a:A b:Name \"Test2\" . 
    		}";
		$res = $s->queryUpdate($q );
		//print_r($res);
		$this->assertEquals(2, $s->count());		
		
		$sp = new FourStore_StorePlus($EndPointSparql);
		
		//Security block without graph
		$q = $prefixSparql." \n
			DELETE DATA {     
				GRAPH <".$graph1."> {a:A b:Name \"Test4\".} 
    		}";
		$res = $sp->query($q,'raw' );
		$err = $sp->getErrors();
		//print_r($err);
	    $this->assertTrue(count($err)> 0);
	    
		$q = $prefixSparql."\n ASK WHERE{ a:A b:Name \"Test4\" .}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE  { a:A b:Name \"Test3\" .}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE  { a:A b:Name \"Test2\" .}";
		$res = $sp->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete("default:");    	
    	$this->checkIfInitialState($s);
    }
    
    public function testInsert()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
					 
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
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertTrue($res);
		
		$this->assertEquals(3, $s->count($graph1));	
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
   public function testDelete()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test4\".   
					a:A b:Name \"Test2\" .    
					a:A b:Name \"Test3\" . 
					");
    	
		$sp = new FourStore_StorePlus($EndPointSparql,false,$modeDebug);
		
		$q = $prefixSparql." \n
			DELETE DATA {  
				GRAPH <".$graph1."> {    
				a:A b:Name \"Test2\" . 
    		}}";
		$res = $sp->query($q,'raw' );
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertTrue($res);
		
		$this->assertEquals(2, $s->count($graph1));		
		
		$sp = new FourStore_StorePlus($EndPointSparql,$modeDebug);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test4\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test3\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $sp->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testSelectDBpedia()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
		$q = 'select *  where {?x ?y ?z.} LIMIT 5';
    	$sp = new FourStore_StorePlus("http://dbpedia.org/sparql");
    	
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertEquals(5,count($rows));
    }
    
    private function checkIfInitialState($s){
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
		$this->assertEquals(0, $s->count($graph1));
		$this->assertEquals(0, $s->count($graph2));
		$this->assertEquals(0, $s->count());
    }
}