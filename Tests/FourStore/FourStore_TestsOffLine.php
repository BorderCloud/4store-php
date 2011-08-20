<?php
require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/Framework.php';
 
require_once 'FourStoreOfflineTest.php';

class FourStore_TestsOffLine extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
    	$suite = new FourStore_TestsOffLine('Tests OffLine');
    	
    	$suite->addTestSuite('FourStoreOfflineTest');
    	
		return $suite;
    }
    

}