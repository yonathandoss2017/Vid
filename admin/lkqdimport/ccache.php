<?php
	
	$mem_var = new Memcached('reps');
	$mem_var->addServer("localhost", 11211);
	$mem_var->flush(1);
?>