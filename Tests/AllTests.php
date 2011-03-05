<?php
require_once 'PHPUnit/Framework.php';

require_once 'FourStore/FourStore_TestsOnLine.php';
require_once 'FourStore/FourStore_TestsOffLine.php';

//Config PHPunit

$EndPointSparql = "http://localhost:8080/sparql/";
$NameBackend = "test";
$SetupEndPoint = true;
$CleanEndPoint = true;
$modeDebug = false;
$modeSkipProblem = true;

//Global Vars (private)
$prefixSparql = "PREFIX a: <http://example.com/test/a/>
PREFIX b: <http://example.com/test/b/> \n";	
$prefixTurtle = "@prefix a: <http://example.com/test/a/> .
@prefix b: <http://example.com/test/b/> .\n";	
$graph1 = "http://example.com/test";
$graph2 = "http://example.com/test2";

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Project');
 
        $suite->addTest(FourStore_TestsOffLine::suite());
        $suite->addTest(FourStore_TestsOnLine::suite());
 
        return $suite;
    }
}