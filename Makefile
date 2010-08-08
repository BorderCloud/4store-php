test:
		export LD_LIBRARY_PATH=/usr/local/lib
		phpunit --colors --verbose Tests/AllTests.php 
		
clean:
		killall 4s-httpd  
		killall 4s-backend  
		export LD_LIBRARY_PATH=/usr/local/lib
		4s-backend-destroy test