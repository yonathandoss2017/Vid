<?php
	
	$Date1 = date('Y-m-d');
	
	$dateLM = new DateTime($Date1);
	$dateLM->modify('-1 month');
	$Date3 = $dateLM->format('Y-m-t');
	echo $Date3Nice = $dateLM->format('d/m/Y');
	echo $Date3Nice = $dateLM->format('t/m/Y');