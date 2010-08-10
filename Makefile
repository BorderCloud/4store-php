test:
		phpunit --colors --verbose Tests/AllTests.php 
		
clean:
		killall 4s-httpd  
		killall 4s-backend  
		4s-backend-destroy test