test:
		phpunit --colors --verbose Tests/AllTests.php 
		
clean:
		killall 4s-httpd  
		killall 4s-backend  
		4s-backend-destroy test

importarc2:
		rm -rf lib/arc2
		cp -rv ../arc2/lib/arc2 lib/ 
		rm -rf lib/arc2/.g*

