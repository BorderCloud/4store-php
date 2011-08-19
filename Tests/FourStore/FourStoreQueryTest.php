<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/php4store/Endpoint.php');
 
class FourStoreQueryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {       
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$r = $s->delete($graph1); 
    	$r = $s->delete($graph2); 
    	$r = $s->delete("default:");     
    }

    public function testSelect()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
					 		
		$q = 'select * where {?x ?y ?z.} ';
    	
    	$rows = $s->query($q, 'rows');
    	//print_r($rows);
    	$err = $s->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertEquals(3,count($rows));
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testSelectDistinct()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A2 b:Name \"Test2\".
					a:A3 b:Name \"Test3\".
					a:A4 b:Name \"Test4\".
					a:A2 b:Name2 \"t2\".
					a:A3 b:Name2 \"t3\".
					a:A4 b:Name2 \"t4\".
					");
					 		
		$q =  $prefixSparql ."\n select  * where {?x b:Name ?name. ?x b:Name2 ?name2. } ";
    	
    	$rows = $s->query($q, 'rows');
    	print_r($rows);
    	$err = $s->getErrors();
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
    	global $modeSkipProblem,$EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
					 
		$q = 'select (count(?x) AS ?count) where {?x ?y ?z.} ';
  	
    	$rows = $s->query($q, 'rows');
    	//print_r($rows);
    	$err = $s->getErrors();
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
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
					    	
		$q = 'select (count(*) AS ?count) where {?x ?y ?z.} ';
    	
    	$rows = $s->query($q, 'rows');
    	//print_r($rows);
    	$err = $s->getErrors();
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
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test2\".
					a:A b:Name \"Test3\".
					a:A b:Name \"Test4\".
					");
		
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"NON\" .}}";
		$res = $s->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
 	public function testInsertWithoutParser()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
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
		
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test4\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test3\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
 
 	public function testDeleteWithoutParser()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
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
		
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test4\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test3\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $s->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testInsertDefaultGraph()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
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
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
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
				
		//Security block without graph
		$q = $prefixSparql." \n
			DELETE DATA {     
				 a:A b:Name \"Test4\".
    		}";
		$res = $s->query($q,'raw' );
		$err = $s->getErrors();
		print_r($err);
	    $this->assertTrue(count($err)> 0);
	    
		$q = $prefixSparql."\n ASK WHERE{ a:A b:Name \"Test4\" .}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE  { a:A b:Name \"Test3\" .}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE  { a:A b:Name \"Test2\" .}";
		$res = $s->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete("default:");    	
    	$this->checkIfInitialState($s);
    }
    
    public function testInsert()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		
		$q = $prefixSparql." 
			INSERT DATA {  
				GRAPH <".$graph1."> {    
				a:A b:Name \"Test2\" .   
				a:A b:Name \"Test3\" .   
				a:A b:Name \"Test4\" .  
    		}}";
		$res = $s->query($q,'raw' );
		$err = $s->getErrors();
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
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle ."\n
					a:A b:Name \"Test4\".   
					a:A b:Name \"Test2\" .    
					a:A b:Name \"Test3\" . 
					");
		
		$q = $prefixSparql." 
			DELETE DATA {  
				GRAPH <".$graph1."> {    
				a:A b:Name \"Test2\" . 
    		}}";
		$res = $s->query($q,'raw' );
		$err = $s->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertTrue($res);
		
		$this->assertEquals(2, $s->count($graph1));		
		
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test4\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test3\" .}}";
		$res = $s->query($q,'raw');
		$this->assertTrue($res);
		$q = $prefixSparql."\n ASK WHERE { GRAPH <".$graph1."> { a:A b:Name \"Test2\" .}}";
		$res = $s->query($q,'raw');
		$this->assertFalse($res);
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    
//    public function testSelectDBpedia()
//    {
//    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
//		$q = 'select *  where {?x ?y ?z.} LIMIT 5';
//    	$sp = new Endpoint("http://dbpedia.org/",true);
//    	
//    	$rows = $sp->query($q, 'rows');
//    	//print_r($rows);
//    	$err = $sp->getErrors();
//	    if ($err) {
//	    	print_r($err);
//	    	$this->assertTrue(false);
//		}
//    	$this->assertEquals(5,count($rows));
//    }
    
    private function checkIfInitialState($s){
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
		$this->assertEquals(0, $s->count($graph1));
		$this->assertEquals(0, $s->count($graph2));
		$this->assertEquals(0, $s->count());
    }
}