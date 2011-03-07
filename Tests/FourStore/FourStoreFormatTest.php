<?php
require_once 'PHPUnit/Framework.php';

require_once (dirname(__FILE__) . '/../../lib/4store/Endpoint.php');
require_once (dirname(__FILE__) . '/../../lib/4store/SparqlTools.php');
 
class FourStoreFormatTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {       
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,$modeDebug);
    	$r = $s->delete($graph1); 
    	$r = $s->delete($graph2); 
    	$r = $s->delete("default:");     
    }
    
   public function testSelectSerializeAndDelete()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
		$r = $s->set($graph1, 
					 $prefixTurtle . "\n@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
					a:A b:Name \"Test2\"@en.
					a:A b:Name \"Test3\"@fr.
					a:A b:Name \"Test4\".
					a:A b:date \"2010-03-09T22:30:00Z\"^^xsd:dateTime .
					");
					 		
		$q = $prefixSparql. "\n select * where {GRAPH <".$graph1."> {a:A ?p ?o.}} ";
    	
    	$triples = $s->query($q,'rows');
    	
    	$err = $s->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
		
	    for ($i = 0, $i_max = count($triples); $i < $i_max; $i++) {
		 $triples[$i]['s'] = "http://example.com/test/a/A";
		 $triples[$i]['s type'] = "uri";
		}
    	//print_r($triples);
    	/* Serializer instantiation */
		$ser = ARC2::getNTriplesSerializer();
			
		/* Serialize a triples array */
		$docd = $ser->getSerializedTriples($triples,1);
				
		$q = "DELETE DATA {  
				GRAPH <".$graph1."> {    
				$docd 
    		}}";
		//print_r($q);
		$res = $s->query($q,'raw' );
		$err = $s->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertTrue($res);
    	  	
    	$this->checkIfInitialState($s);
    }
    
    public function testValueStringUTF8()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
    	
    	$uri = "http://fr.test2.daria/wiki/Spécial:URIResolver/Accueil";
    	$turtle ='<http://fr.test2.daria/wiki/Spécial:URIResolver/Accueil> <http://semantic-mediawiki.org/swivt/1.0#wikiPageModificationDate> "2010-08-16T17:01:33"^^<http://www.w3.org/2001/XMLSchema#dateTime> .';
    	SparqlTools::insert($turtle,$graph1,$EndPoint4store);
    	SparqlTools::deleteTriples($uri,$graph1,$EndPoint4store);	
		
		$this->assertEquals(0, $s->count($graph1));		
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
        
    public function testValueStringUTF8WithQuote()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
    	
    	$uri = "http://fr.test2.daria/wiki/Spécial:URIResolver/Accueil";
    	$coord = addcslashes("40° 42' 42\" N, 74° 0' 44\" O",'\t\n\r\b\f\"\'\\') ;
    	$turtle ="<http://fr.test2.daria/wiki/Spécial:URIResolver/Accueil> <http://fr.test2.daria/wiki/Spécial:URIResolver/Attribut:Coordonnée> \"".$coord."\" .";
    	SparqlTools::insert($turtle,$graph1,$EndPoint4store);
    	SparqlTools::deleteTriples($uri,$graph1,$EndPoint4store);	
		
		$this->assertEquals(0, $s->count($graph1));		
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testIRIUTF8WithQuote()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
    	
    	$uri = "http://fr.test2.daria/wiki/Spécial:URIResolver/Tête_d'or";
    	$turtle ="<http://fr.test2.daria/wiki/Spécial:URIResolver/Tête_d'or> <http://fr.test2.daria/wiki/Spécial:URIResolver/Attribut:Truc> \"test\" .";
    	SparqlTools::insert($turtle,$graph1,$EndPoint4store);
    	$this->assertEquals(1, $s->count($graph1));
    	SparqlTools::deleteTriples($uri,$graph1,$EndPoint4store);	
		
		$this->assertEquals(0, $s->count($graph1));		
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    
    public function testSelectIRIUTF8WithQuote()
    {
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
    			
    	$s = new Endpoint($EndPoint4store,false,$modeDebug);
    	$this->checkIfInitialState($s);
    	
    	$uri = "http://fr.test2.daria/wiki/Spécial:URIResolver/Tête_d'or";
    	$turtle ="<http://fr.test2.daria/wiki/Spécial:URIResolver/Tête_d'or> <http://fr.test2.daria/wiki/Spécial:URIResolver/Attribut:Truc> \"test\" .";
    	SparqlTools::insert($turtle,$graph1,$EndPoint4store);
    	$this->assertEquals(1, $s->count($graph1));
    	
    	$q = "select *  where {<".$uri."> ?y ?z.}";
    	
    	$rows = $s->query($q, 'rows');
    	print_r($rows);
    	$err = $s->getErrors();
	    if ($err) {
	    	print_r($err);
	    	$this->assertTrue(false);
		}
    	$this->assertEquals(1,count($rows));    	
    	
    	SparqlTools::deleteTriples($uri,$graph1,$EndPoint4store);	
		
		$this->assertEquals(0, $s->count($graph1));		
		
    	$r = $s->delete($graph1);    	
    	$this->checkIfInitialState($s);
    }
    
    public function testURItoIRI()
    {
		$uri ="http://fr.test2.daria/wiki/Spécial:URIResolver/Coordonn-C3-A9e";
		$iri ="http://fr.test2.daria/wiki/Spécial:URIResolver/Coordonnée";
		$res_iri = SparqlTools::decodeURItoIRI( $uri );
		
		//echo $res_iri;
		$this->assertEquals($iri,$res_iri);		
		
    }
    
    private function checkIfInitialState($s){
    	global $EndPoint4store,$modeDebug,$prefixSparql,$prefixTurtle,$graph1,$graph2;
		$this->assertEquals(0, $s->count($graph1));
		$this->assertEquals(0, $s->count($graph2));
		$this->assertEquals(0, $s->count());
    }
}