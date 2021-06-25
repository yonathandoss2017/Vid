<?php
	session_start();
	if(@$_SESSION['Admin'] != 1){
		header('Location: login.php');
		exit(0);
	}
	if(isset($_GET['iduser'])){
		$idUser = intval($_GET['iduser']);
		if($idUser > 0){
			$_SESSION['login'] = $idUser;
			
			header('Location: ../estadisticas.php');
		}else{
			header('Location: index.php');
			exit(0);
		}		
	}else{
		header('Location: index.php');
		exit(0);
	}
?>