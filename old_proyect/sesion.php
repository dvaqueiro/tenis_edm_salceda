<?php

	if($_GET['salir']==1)
	{
		session_destroy();	
		$_SESSION['usutenis']=	'';
		$_SESSION['idtenis']=	'';
	}
	
	if($_POST['dnilog'])
	{
		$sql="SELECT nombre, id FROM usuarios where dni='".$_POST['dnilog']."' and contrasena='".$_POST['contrasenalog']."';";
		$result=mysqli_query($conexion, $sql);
		$row=mysqli_fetch_array($result);
		if($row['nombre'])
		{
			$mensaje= "Bienvenido, ".$row['nombre'];
			$_SESSION['usutenis']=	$row['nombre'];
			$_SESSION['idtenis']=	$row['id'];
		}else{
			$error= "usuario incorrecto";
		}
	}
	
	
	
?>