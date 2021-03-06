<?php
require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/Framework.php';
 
require_once 'FourStoreToolsTest.php';
require_once 'FourStoreDataTest.php';
require_once 'FourStoreQueryTest.php';
require_once 'FourStoreMatchingTest.php';
require_once 'FourStoreFormatTest.php';

class FourStore_TestsOnLine extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
    	$suite = new FourStore_TestsOnLine('Tests OnLine');
    	
    	$suite->addTestSuite('FourStoreToolsTest');
    	$suite->addTestSuite('FourStoreDataTest');
    	$suite->addTestSuite('FourStoreQueryTest');
    	$suite->addTestSuite('FourStoreMatchingTest');
    	$suite->addTestSuite('FourStoreFormatTest');
		return $suite;
    }
    
    protected function setUp()
    {
    	global $SetupEndPoint,$NameBackend ;
    	
    	if($SetupEndPoint){
    		
	        print "\n********************SETUP***********************\n";
	        print "# Create Backend test : \n";
	        print '4s-backend-setup '.$NameBackend ."\n";
	        $last_line = system('4s-backend-setup '.$NameBackend, $retval);
	        print $retval . "\n";
	        
	        print "# Start Backend test :\n";
	        print '4s-backend '.$NameBackend."\n";
	        $last_line = system('4s-backend '.$NameBackend, $retval);
	        print $retval . "\n";
	        
	        print "# Start daemon http for the backend test :\n";
	        print '4s-httpd '.$NameBackend."\n";
	        $last_line = system('4s-httpd '.$NameBackend, $retval); //-H 127.0.0.1 -p 8080 
	        print $retval . "\n";
	        print "********************END SETUP***********************\n";
    	}
    	
    	print "\n********************TESTS***********************\n";
    }
 
    protected function tearDown()
    {		    	
    	global $CleanEndPoint,$NameBackend ;
    	
    	
		print "\n********************END TESTS***********************\n";
		
    	if($CleanEndPoint){
	        print "\n********************CLEAN***********************\n";
	    	print "# Kill process 4store :\n";	    	
	        $last_line = system('killall 4s-httpd', $retval);
	        print $retval . "\n";
	        $last_line = system('killall 4s-backend', $retval);
	        print $retval . "\n";
		        
	        print "# Destroy backend test :\n";
	        print '4s-backend-destroy '.$NameBackend ."\n";
	        $last_line = system('4s-backend-destroy '.$NameBackend, $retval);
	        print $retval . "\n";
	        print "********************END CLEAN***********************\n";
	    }
    }

}