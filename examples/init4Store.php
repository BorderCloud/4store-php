<?php
// DONT USE THIS FILE IN PRODUCTION, it's only to test the examples.
$NameBackend = "bordercloudExample";

function start4store(){
	global $NameBackend;

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
	$last_line = system('4s-httpd '.$NameBackend, $retval);
	print $retval . "\n";
	print "********************END SETUP***********************\n";
}

function stop4store(){
	global $NameBackend;

	print "\n********************CLEAN***********************\n";
	print "# Kill process 4store :\n";
	$last_line = system('killall 4s-httpd', $retval);
	print $retval . "\n";
	$last_line = system('killall 4s-backend', $retval);
	print $retval . "\n";

	print "# Destroy backend ".$NameBackend ." :\n";
	print '4s-backend-destroy '.$NameBackend ."\n";
	$last_line = system('4s-backend-destroy '.$NameBackend, $retval);
	print $retval . "\n";
	print "********************END CLEAN***********************\n";
}