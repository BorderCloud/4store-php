<?php
/** 
 * @version 0.4.1
 * @package Bourdercloud/PHP4store
 * @copyright (c) 2011 Bourdercloud.com
 * @author Karima Rafes <karima.rafes@bordercloud.com>
 * @license http://www.opensource.org/licenses/mit-license.php
 */

//require_once(dirname(__FILE__) . '/../arc2/ARC2.php');
/**
 * bordercloud/arc2 is a lib RDF and Sparql (channel  bordercloud.github.com/pear )
 */
require_once('arc2/ARC2.php');
require_once("Curl.php");
require_once("Net.php");

/**
 * Sparql HTTP Client for 4store Endpoint around basic php function.
 * 
 * 
 * You can send a query to any endpoint sparql 
 * and read the result in an array.
 * 
 * Example : send a simple query to DBpedia
 * <code>
 * <?php  
 *  require_once('php4store/Endpoint.php');
 *  
 * 	$endpoint ="http://dbpedia.org/";
 * 	$sp_readonly = new Endpoint($endpoint);
 *  $q = "select *  where {?x ?y ?z.} LIMIT 5";
 *  $rows = $sp_readonly->query($q, 'rows');
 *  $err = $sp_readonly->getErrors();
 *  if ($err) {
 * 	  print_r($err);
 * 	  throw new Exception(print_r($err,true));
 * 	}
 * 	var_dump($rows);
 * ?>
 * </code>
 * 
 * 
 *  With a query ASK, you can use the parameter 'raw' 
 *  in the function query and read directly the result true or false.
 * 
 * Example : send a query ASK with the parameter raw
 * <code>
 * <?php
 *  require_once('php4store/Endpoint.php');
 *  
 *    $q = "PREFIX a: <http://example.com/test/a/>
 *			PREFIX b: <http://example.com/test/b/> 
 *			ask where { GRAPH <".$graph."> {a:A b:Name \"Test3\" .}} ";
 *    $res = $sp_readonly->query($q, 'raw');
 *    $err = $sp_readonly->getErrors();
 *    if ($err) {
 *	    print_r($err);
 *	    throw new Exception(print_r($err,true));
 *	}
 *	var_dump($res);
 * ?>
 * </code>
 * 
 * 
 * You can insert data also with SPARQL and the function query. 
 * If the graph doesn't exist, 4store will create the graph.
 * 
 * Example : send a query Insert
 * <code>
 * <?php
 *  require_once('php4store/Endpoint.php');
 *  
 * $graph = "http://www.bordercloud.com";	
 * $endpoint ="http://localhost:8080/sparql/";
 * 
 * 	//put argument false to write
 * 	$readonly = false;
 * 	$sp_write = new Endpoint('http://localhost:8080/',$readonly);
 * 	
 * 	$q = " 
 * 			PREFIX a: <http://example.com/test/a/>
 * 			PREFIX b: <http://example.com/test/b/> 
 * 			INSERT DATA {  
 * 				GRAPH <".$graph."> {    
 * 				a:A b:Name \"Test1\" .   
 * 				a:A b:Name \"Test2\" .   
 * 				a:A b:Name \"Test3\" .  
 *     		}}";
 * 	$res = $sp_write->query($q,'raw');
 * 	$err = $sp_write->getErrors();
 *  if ($err) {
 * 	    print_r($err);
 * 	    throw new Exception(print_r($err,true));
 * 	}
 * 	var_dump($res);
 * ?>
 * </code>
 * 	
 * 
 * You can delete data also with SPARQL and the function query.
 * 
 * Example : send a query Delete
 * <code>
 * <?php
 *  require_once('php4store/Endpoint.php');
 *  
 * $graph = "http://www.bordercloud.com";	
 * $endpoint ="http://localhost:8080/sparql/";
 * 	
 * 	$q = " 
 * 			PREFIX a: <http://example.com/test/a/>
 * 			PREFIX b: <http://example.com/test/b/> 
 * 			DELETE DATA {  
 * 				GRAPH <".$graph."> {     
 * 				a:A b:Name \"Test2\" . 
 *     		}}";
 * 	
 * 	$res = $sp_write->query($q,'raw');
 * 	$err = $sp_write->getErrors();
 *  if ($err) {
 * 	    print_r($err);
 * 	    throw new Exception(print_r($err,true));
 * 	}
 * 	var_dump($res);
 * ?>
 * </code>
 * 
 * 
 * You can create or replace a graph with the function set.
 * 
 * Example : 
 * <code>
 * <?php
 *  require_once('php4store/Endpoint.php');
 *  
 * $readonly = false;
 * $s = new Endpoint('http://localhost:8080/',$readonly);
 * 
 * $r = $s->set('http://example/test', "
 * 	@ prefix foaf: <http://xmlns.com/foaf/0.1/> .
 * 	<http://github.com/bordercloud/4store-php> foaf:maker <http://www.bordercloud.com/wiki/user:Karima_Rafes> . ");
 * 
 * ?>
 * </code>
 * 
 * 
 * You can add data (Turtle format) in a graph with the function add.
 * 
 * Example : 
 * <code>
 * <?php
 *  require_once('php4store/Endpoint.php');
 *  
 * $readonly = false;
 * $s = new Endpoint('http://localhost:8080/',$readonly);
 * $r = $s->add('http://example/test', "
 * 	@ prefix foaf: <http://xmlns.com/foaf/0.1/> .
 * 	<http://www.bordercloud.com/wiki/user:Karima_Rafes>  foaf:workplaceHomepage <http://www.bordercloud.com> . ");
 * var_dump($r);
 * 
 * ?>
 * </code>
 * 
 *  
 * You can delete a graph with the function delete.
 * 
 * Example : 
 * <code>
 * <?php
 *  require_once('php4store/Endpoint.php');
 *  
 * $readonly = false;
 * $s = new Endpoint('http://localhost:8080/',$readonly);
 *  
 *   $r = $s->delete('http://example/test');
 *   var_dump($r);
 * ?>
 * </code>
 * 
 * @example examples/1_set.php  Example Set
 * @example examples/2_add.php  Example Add
 * @example examples/3_delete.php  Example Delete
 * @example examples/4_query.php  Example Query
 * @example examples/5_queryDBpedia.php  Example Query with DBpedia
 * @example examples/init4Store.php  Example Start and Stop 4Store
 * @package Bourdercloud/PHP4store
 */
class Endpoint {
	/**
	 * URL of Endpoint to read
	 * @access private
	 * @var string
	 */
	private $_endpoint;
		
	/**
	 * in the constructor set debug to true in order to get usefull output
	 * @access private
	 * @var string
	 */
	private $_debug;
	
	/**
	 * in the constructor set the right to write or not in the store
	 * @access private
	 * @var string
	 */
	private $_readOnly;
	
	/**
	 * in the constructor set the proxy_host if necessary
	 * @access private
	 * @var string
	 */
	private $_proxy_host;
	
	/**
	 * in the constructor set the proxy_port if necessary
	 * @access private
	 * @var int
	 */
	private $_proxy_port;
	
	/** For Arc2 **/
	private $_arc2_RemoteStore;
	private $_arc2_Reader;
	private $_config;

	/**
	 * Constructor of Graph
	 * @param string $endpoint : url of endpoint, example : http://lod.bordercloud.com/sparql
	 * @param boolean $readOnly : true by default, if you allow the function query to write in the database
	 * @param boolean $debug : false by default, set debug to true in order to get usefull output
	 * @param string $proxy_host : null by default, IP of your proxy
	 * @param string $proxy_port : null by default, port of your proxy
	 * @access public
	 */
	public function __construct($endpoint,
								$readOnly = true,
								$debug = false,
								$proxy_host = null,
								$proxy_port = null)
	{
		$this->_debug = $debug;
		$this->_endpoint = $endpoint;
		$this->_readOnly = $readOnly;
		
		$this->_proxy_host = $proxy_host;
		$this->_proxy_port = $proxy_port;		
		
		if($this->_proxy_host != null && $this->_proxy_port != null){
			$this->_config = array(
				/* remote endpoint */
			  'remote_store_endpoint' => $this->_endpoint."sparql/",
				  /* network */
			  'proxy_host' => $this->_proxy_host,
			  'proxy_port' => $this->_proxy_port,			
			);
		}else{
			$this->_config = array(
			/* remote endpoint */
			  'remote_store_endpoint' => $this->_endpoint."sparql/",
			);			
		}

		$this->_arc2_RemoteStore = ARC2::getRemoteStore($this->_config);		
	}
	
	/**
	 * Check if the server is up.
	 * @return boolean true if the triplestore is up.
	 * @access public
	 */
	public function check() {
		return Net::ping($this->_endpoint) != -1;
	}
	
	/**
	 * Create or replace the data in a graph.
	 * @param string $graph : name of the graph
	 * @param string $turtle : list of the triples
	 * @return boolean : true if it did
	 * @access public
	 */
	public function set($graph, $turtle) {
		if($this->_readOnly){
				return $this->_arc2_RemoteStore->addError('No right to write in the triplestore.');
		}
		
		$client = $this->initCurl();

		$headers = array( 'Content-Type: application/x-turtle' );
		$sUri    = $this->_endpoint. "data/" . $graph;

		$response = $client->send_put_data($sUri,$headers, $turtle);
		$code = $client->get_http_response_code();

		if($code == 201)
		{
			return true;
		}
		else
		{
			$datastr = print_r($turtle, true);
			$headerstr = print_r($headers, true);
			$this->errorLog("Set:\nHeader :".$headerstr."\n Data:".$datastr,$sUri,$code,$response);
			return false;
		}
	}

	/**
	 * Add new data in a graph.
	 * @param string $graph : name of the graph
	 * @param string $turtle : list of the triples
	 * @return boolean : true if it did
	 * @access public
	 */
	public function add($graph, $turtle) {
		if($this->_readOnly){
				return $this->_arc2_RemoteStore->addError('No right to write in the triplestore.');
		}
		
		$data = array( "graph" => $graph, "data" => $turtle , "mime-type" => 'application/x-turtle' );
		$sUri    = $this->_endpoint. "data/";

		$client = $this->initCurl();
		$response = $client->send_post_data($sUri, $data);
		$code = $client->get_http_response_code();

		if($code == 200)
		{
			return true;
		}
		else
		{
			$datastr = print_r($data, true);
			$this->errorLog("Add:\n".$datastr,$sUri,$code,$response);
			return false;
		}
	}

	/**
	 * Delete a graph with its data.
	 * @param string $graph : name of the graph
	 * @return boolean : true if it did
	 * @access public
	 */
	public function delete($graph) {	
		if($this->_readOnly){
				return $this->_arc2_RemoteStore->addError('No right to write in the triplestore.');
		}
		
		$client = $this->initCurl();
		$sUri    = $this->_endpoint. "data/". $graph ;
		$response = $client->send_delete($sUri);
		$code = $client->get_http_response_code();

		if($code == 200)
		{
			return true;
		}
		else
		{
			$this->errorLog("DELETE:<".$graph.">",$sUri,$code,$response);
			return false;
		}
	}
	
	/**
	 * This function parse a SPARQL query, send the query and parse the SPARQL result in a array. 
	 * You can custom the result with the parameter $result_format : 
	 * <ul>
	 * <li>rows to return array of results
	 * <li>row to return array of first result
	 * <li>raw to return boolean for request ask, insert and delete
	 * </ul>
	 * @param string $q : Query SPARQL 
	 * @param string $result_format : Optional,  rows, row or raw
	 * @return array|boolean in function of parameter $result_format
	 * @access public
	 */
	public function query($q, $result_format = '') {
		if($this->_debug){
			print date('Y-m-d\TH:i:s\Z', time()) . ' : ' . $q . '' . "\n\n";
		}

		$p =  ARC2::getSPARQLPlusParser();
		$p->parse($q);
		$infos = $p->getQueryInfos();
		$t1 = ARC2::mtime();		
		if (!$errs = $p->getErrors()) {
			$qt = $infos['query']['type'];
			$r = array('query_type' => $qt, 'result' => $this->runQuery($q, $qt, $infos));
		}
		else {
			$r = array('result' => '');		
			if($this->_debug){
				print date('Y-m-d\TH:i:s\Z', time()) . ' : ERROR ' . $q . '' . "\n\n";
				print_r($errs);
			}
			return $this->_arc2_RemoteStore->addError($p->getErrors() );
		}
		$t2 = ARC2::mtime();
		$r['query_time'] = $t2 - $t1;
	  
		/* query result */
		if ($result_format == 'raw') {
			return $r['result'];
		}
		if ($result_format == 'rows') {
			return $this->_arc2_RemoteStore->v('rows', array(), $r['result']);
		}
		if ($result_format == 'row') {
			if (!isset($r['result']['rows'])) return array();
			return $r['result']['rows'] ? $r['result']['rows'][0] : array();
		}
		return $r;
	}
		
	/**
	 * Give the errors 
	 * @return array
	 * @access public
	 */
	public function getErrors() {
		return $this->_arc2_RemoteStore->getErrors();
	}

	/**
	 * Count the number of triples in a graph or in the endpoint.
	 * @param string $graph : put name of the graph or nothing to count all triples in the endpoint
	 * @return number
	 * @access public
	 */
	public function count($graph= null ) {
		$r="";
		$count = 0;
		if($graph != null){
			//FIXME count(*) doesn't work
			$r = $this->queryRead("SELECT (count(?a) AS ?count) WHERE  { GRAPH <".$graph."> {?a ?b ?c}}");
		}else{
			$r = $this->queryRead("SELECT (count(?a) AS ?count) WHERE {?a ?b ?c}");
		}
		if(preg_match_all('%<binding name="count"><literal[^>]*>([0-9]+)<%m',$r,$countResponse))
			$count = $countResponse[1][0];

		return $count;
	}
	
	/**
	 * Send a request SPARQL of type select or ask to endpoint directly and output the response
	 * of server. If you want parse the result of this function, it's better and simpler
	 * to use the function query().
	 * 
	 * if you want use another format, you can use directly the function queryReadJSON and queryReadTabSeparated
	 * @param string $query : Query Sparql
	 * @param string $typeOutput by default "application/sparql-results+xml",
	 * @return string response of server or false if error (to do getErrors())
	 * @access public
	 */
	public function queryRead($query,$typeOutput=null ) {
		$client = $this->initCurl();
		$sUri    = $this->_endpoint."sparql/";
		
		$data = array("query" =>   $query);	
		if($typeOutput == null){	
			$response = $client->send_post_data($sUri,$data);
		}else{
			$response = $client->fetch_url($sUri."?query=".$query."&output=".$typeOutput);
		}
 
		$code = $client->get_http_response_code();
			
		$this->debugLog($query,$sUri,$code,$response);

		if($code != 200)
		{
			$error = $this->errorLog($query,$data,$sUri,$code,$response);
			$this->_arc2_RemoteStore->addError($error);
			return false;
		}
		return $response;
	}
	
	/**
	 * Send a request SPARQL of type select or ask to endpoint directly and output the response
	 * of server in the format JSON
	 * @param string $query : Query Sparql
	 * @return string response of server in the format JSON
	 * @access public
	 */
	public function queryReadJSON($query ){
		return $this->queryRead($query,"application/sparql-results+json" );
	}

	/**
	 * Send a request SPARQL of type select or ask to endpoint directly and output the response
	 * of server in the format TabSeparated
	 * @param string $query : Query Sparql
	 * @return string response of server in the format TabSeparated
	 * @access public
	 */
	public function queryReadTabSeparated ($query ){
		return $this->queryRead($query,"text" );
	}

	/**
	 * Send a request SPARQL of type insert data or delete data to endpoint directly.
	 * If you want check the query before to send, it's better to use the function query()
	 *  in the class StorePlus.
	 * <ul>
	 * <li>Example insert : PREFIX ex: <http://example.com/> INSERT DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
	 * <li>Example delete : PREFIX ex: <http://example.com/> DELETE DATA { GRAPH <http://mygraph> { ex:a ex:p 12 .}}
	 * </ul>
	 * @param string $query : Query Sparql of type insert data or delete data only
	 * @return boolean true if it did or false if error (to do getErrors())
	 * @access public
	 */
	public function queryUpdate($query) { 
		$sUri  =   $this->_endpoint . "update/";
		$data =array("update" =>    $query) ;

		$this->debugLog($query,$sUri);
			
		$client = $this->initCurl();
		$response = $client->send_post_data($sUri, $data);
		$code = $client->get_http_response_code();

		$this->debugLog($query,$sUri,$code,$response);
			
		if($code == 200 )
		{
			return true;
		}
		else
		{
			$error = $this->errorLog($query,$data,$sUri,$code,$response);
			$this->_arc2_RemoteStore->addError($error);
			return false;
		}
	}
	
	/************************************************************************/
	//PRIVATE Function
	
	/**
	 * Execute the query 
	 * @access private
	 */
	private function runQuery($q, $qt = '', $infos = '') {

		/* ep */
		$ep = $this->_arc2_RemoteStore->v('remote_store_endpoint', 0, $this->_arc2_RemoteStore->a);
		if (!$ep) return $this->_arc2_RemoteStore->addError('No Endpoint defined.');
		/* prefixes */
		$q = $this->_arc2_RemoteStore->completeQuery($q);
		/* custom handling */
		$mthd = 'run' . $this->_arc2_RemoteStore->camelCase($qt) . 'Query';
		if (method_exists($this, $mthd)) {
			return $this->_arc2_RemoteStore->$mthd($q, $infos);
		}
		if(in_array($qt, array('insert', 'delete'))){
			if($this->_readOnly){
				return $this->_arc2_RemoteStore->addError('No right to write in the triplestore.');
			}else{
				$r = $this->queryUpdate($q);
				if(! $r){
					$errmsg = "Error unknown.";
					if(Net::ping($ep) == -1)
						$errmsg = "Could not connect to ".$ep;
												
					return $this->_arc2_RemoteStore->addError($errmsg );
				}
			}
		}else{
			$resp = $this->queryRead($q );
			
			if($resp == ""){
					$errmsg = "Error unknown.";
					if(Net::ping($ep) == -1)
						$errmsg = "Could not connect to ".$ep;
						
					return $this->_arc2_RemoteStore->addError($errmsg );
			}

			if(preg_match_all('%<!--(.*error.*)-->%m',$resp,$errorResponse)){
				$message4s = $errorResponse[1][0];
				return $this->_arc2_RemoteStore->addError("5Store message : ".$message4s ."\n query :\n".$q );
			}

			$parser = @ARC2::getSPARQLXMLResultParser() ;
			$parser->parse('', $resp);
			$err = $parser->getErrors();
			if($err)
				return $this->_arc2_RemoteStore->addError($err);
			
			if ($qt == 'ask') {
				$bid = $parser->getBooleanInsertedDeleted();
				$r = $bid['boolean'];
			}
			/* select */
			elseif (($qt == 'select') && !method_exists($parser, 'getRows')) {
				$r = $resp;
			}
			elseif ($qt == 'select') {
				$r = array('rows' => $parser->getRows(), 'variables' => $parser->getVariables());
			}
			/* any other */
			else {
				$r = $parser->getSimpleIndex(0);
			}
			unset($parser);
		}
		return $r;
	}

	/**
	 * write error for human
	 * @param string $query
	 * @param string $endPoint
	 * @param number $httpcode
	 * @param string $response
	 * @access private
	 */
	private function errorLog($query,$data,$endPoint,$httpcode=0,$response=''){
		$error = 	"Error query  : " .$query."\n" .
					"Error endpoint: " .$endPoint."\n" .
					"Error http_response_code: " .$httpcode."\n" .
					"Error message: " .$response."\n";			
		if($this->_debug)
		{
			echo '=========================>>>>>>'.$error ;
		}else{
			error_log($error);
		}
	}

	/**
	 * Print infos
	 * @param unknown_type $query
	 * @param unknown_type $endPoint
	 * @param unknown_type $httpcode
	 * @param unknown_type $response
	 * @access private
	 */
	private function debugLog($query,$endPoint,$httpcode='',$response=''){
		if($this->_debug)
		{
			$error = 	"\n#######################\n".
						"query				: " .$query."\n" .
                        "endpoint			: " .$endPoint."\n" .
                        "http_response_code	: " .$httpcode."\n" .
                        "message			: " .$response.
                        "\n#######################\n";

			echo $error ;
		}
	}
	
	/**
	 * Init an object Curl in function of proxy.
	 * @return an object of type Curl
	 * @access private
	 */
	private function initCurl(){
		$objCurl = new Curl();
		if($this->_proxy_host != null && $this->_proxy_port != null){
			$objCurl->set_proxy($this->_proxy_host.":".$this->_proxy_port);	
		}
		return $objCurl;
	}
}
