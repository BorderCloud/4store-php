<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/FourStore/FourStore_StorePlus.php');
 
class FourStoreMatchingTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {       
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$r = $s->delete($graph1); 
    	$r = $s->delete($graph2); 
    	$r = $s->delete("default:");   
    	$r = $s->delete("http://fr.example.com/test");   
    	$r = $s->delete("http://en.example.com/test");   
    }
    
 	public function testMatchingLiteralsWithoutLanguageTags()
    {            
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
					 
		$sp = new FourStore_StorePlus($EndPointSparql,false,$modeDebug);
		
		
		$q = $prefixSparql." \n
			INSERT DATA {  
				GRAPH <".$graph1."> {    
				a:A1 b:Name \"Test\".  
    		}}";
		$res = $sp->query($q,'raw' );
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertTrue($res);
		
    	$q = $prefixSparql." \n select * where {  
				GRAPH <".$graph1."> {?a ?b \"Test\" .}} ";
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}		
		$this->assertEquals(1, count($rows));		
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }

    public function testMatchingLiteralsWithLanguageTags()
    {
    	global $modeSkipProblem;
    	if($modeSkipProblem)
    		$this->markTestSkipped(              
              "Matching Literals with Language Tags doesn't work."
            );
            
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    	    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
					 
		$sp = new FourStore_StorePlus($EndPointSparql,false,$modeDebug);
		
		$q = $prefixSparql." \n
			INSERT DATA {  
				GRAPH <".$graph1."> {    
				a:A1 b:Name \"Test\"@en.  
    		}}";
		$res = $sp->query($q,'raw' );
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertTrue($res);
		
    	$q = $prefixSparql." \n select * where {  
				GRAPH <".$graph1."> {?a ?b \"Test\"@en .}} ";
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}		
		$this->assertEquals(1, count($rows));	
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testHackLanguage()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
					 
		$sp = new FourStore_StorePlus($EndPointSparql,false,$modeDebug);
		
		$graphfr = "http://fr.example.com/test";
		$graphen = "http://en.example.com/test";
		
		$r = $s->set($graphen, 
					 $prefixTurtle ."\n
					 <http://en.example.com/test/Apple> b:Name \"Apple\"."
					);
					$this->assertTrue($r);
		$r = $s->set($graphfr, 
					 $prefixTurtle ."\n
					 <http://fr.example.com/test/Pomme> b:Name \"Pomme\".
					<http://fr.example.com/test/Pomme> <http://www.w3.org/2002/07/owl#sameas> <http://en.example.com/test/Apple>.
					");
    	$this->assertTrue($r);
		$this->assertEquals(3, $s->count());
		
		//Not bidirectional
        $q = $prefixSparql." \n SELECT * WHERE 
    	{?id  b:Name \"Apple\" .
    	?id  <http://www.w3.org/2002/07/owl#sameas> ?idfr . 
	    	{GRAPH <".$graphfr."> {?idfr b:Name ?c .}}
	    } ";
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
		$this->assertEquals(0,count($rows));
		
        $q = $prefixSparql." \n SELECT * WHERE 
    	{?id  b:Name \"Apple\" .
    	{{?id  <http://www.w3.org/2002/07/owl#sameas> ?idfr . }
    	UNION
    	{?idfr  <http://www.w3.org/2002/07/owl#sameas> ?id . }}.
	    	{GRAPH <".$graphfr."> {?idfr b:Name ?c .}}
	    } ";
    	$rows = $sp->query($q, 'rows');
    	//print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
		
    	$this->assertEquals(1,count($rows));
		
    	$r = $s->delete($graphfr);    
    	$r = $s->delete($graphen);    	
    	$this->checkIfInitialState($s);
    }
   
    public function testMatchingLiteralsWithDateTypes()
    {
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new FourStore_Store($EndPointSparql,$modeDebug);
    	$this->checkIfInitialState($s);
					 
		$sp = new FourStore_StorePlus($EndPointSparql,false,$modeDebug);
		
		$prefixt = $prefixTurtle . "\n@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .\n";
		$prefix = $prefixSparql . "\n PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> \n";
		
		$r = $s->set($graph1, 
					 $prefixt ."\n
					 a:id5 b:date \"2010-03-09T21:30:00Z\"^^xsd:dateTime .
					 a:id6 b:date \"2010-03-09T22:30:00Z\"^^xsd:dateTime .
					 a:id7 b:date \"2010-03-09\"^^xsd:dateTime .
					 a:id8 b:date \"2010-03-10\"^^xsd:dateTime .
					 "
					);
					
		$this->assertTrue($r);
		
		//$this->printAll();
		
		$q = $prefix."\n ASK WHERE { GRAPH <".$graph1."> { 
			a:id5 b:date ?date1.
			a:id6 b:date ?date2.
		 FILTER ( ?date1 < ?date2 ) .}}";
		$res = $sp->query($q,'raw');		
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}				
		$this->assertTrue($res);
		
		$q = $prefix."\n ASK WHERE { GRAPH <".$graph1."> { 
			a:id5 b:date ?date1.
			a:id6 b:date ?date2.
		 FILTER ( ?date1 > ?date2 ) .}}";
		$res = $sp->query($q,'raw');		
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}				
		$this->assertFalse($res);
		
		
		$q = $prefix."\n ASK WHERE { GRAPH <".$graph1."> { 
			a:id7 b:date ?date1.
			a:id8 b:date ?date2.
		 FILTER ( ?date1 < ?date2 ) .}}";
		$res = $sp->query($q,'raw');		
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}				
		$this->assertTrue($res);
		
		$q = $prefix."\n ASK WHERE { GRAPH <".$graph1."> { 
			a:id7 b:date ?date1.
			a:id8 b:date ?date2.
		 FILTER ( ?date1 > ?date2 ) .}}";
		$res = $sp->query($q,'raw');		
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}				
		$this->assertFalse($res);
		
		$q = $prefix."\n ASK WHERE { GRAPH <".$graph1."> { 
			a:id7 b:date ?date1.
			a:id6 b:date ?date2.
		 FILTER ( ?date1 < ?date2 ) .}}";
		$res = $sp->query($q,'raw');		
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}				
		$this->assertTrue($res);
		
		$q = $prefix."\n ASK WHERE { GRAPH <".$graph1."> { 
			a:id7 b:date ?date1.
			a:id6 b:date ?date2.
		 FILTER ( ?date1 > ?date2 ) .}}";
		$res = $sp->query($q,'raw');		
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}				
		$this->assertFalse($res);
		
		//example with text
		$q = $prefix."\n ASK WHERE { GRAPH <".$graph1."> { 
			a:id7 b:date ?date.
		 FILTER ( ?date <  \"2010-03-09T00:00:01Z\"^^xsd:dateTime ) .}}";
		$res = $sp->query($q,'raw');		
		$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}				
		$this->assertTrue($res);
		  
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
 	private function checkIfInitialState($s){
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
		$this->assertEquals(0, $s->count($graph1));
		$this->assertEquals(0, $s->count($graph2));
		$this->assertEquals(0, $s->count());
    } 
    
    private function printAll(){
    	global $EndPointSparql,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
		$sp = new FourStore_StorePlus($EndPointSparql,false,$modeDebug);
	
		$q = $prefixSparql." \n select * where {  
				GRAPH ?graph {?o ?p ?v .}} ";
    	$rows = $sp->query($q, 'rows');
    	print_r($rows);
    	$err = $sp->getErrors();
	    if ($err) {
	    	print_r($err);
		}	
    }
}