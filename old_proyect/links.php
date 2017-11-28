<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	
	if($_POST['modificar']==1)
	{
				
			if($_FILES["foto"]["name"]!=''){
				$rutafoto='';
				$allowedExts = array("gif", "jpeg", "jpg", "png");
				$temp = explode(".", $_FILES["foto"]["name"]);
				$extension = end($temp);
				 if ((($_FILES["foto"]["type"] == "image/gif")
				 || ($_FILES["foto"]["type"] == "image/jpeg")
				 || ($_FILES["foto"]["type"] == "image/jpg")
				|| ($_FILES["foto"]["type"] == "image/pjpeg")
				|| ($_FILES["foto"]["type"] == "image/x-png")
				 || ($_FILES["foto"]["type"] == "image/png"))
				 && ($_FILES["foto"]["size"] < 30000000)
				 && in_array($extension, $allowedExts))
				   {
				   if ($_FILES["foto"]["error"] > 0)
					 {
					 $error = "Error: " . $_FILES["foto"]["error"];
					 }
				   else
					 {
					   $randomizao=rand(1,1000);
					   $rutafoto="fotos/" .$randomizao."_". $_FILES["foto"]["name"];
					   move_uploaded_file($_FILES["foto"]["tmp_name"], $rutafoto);
					 }
				   }
				 else
				   {
						$error="Formato de o tamaño de imagen no válido";
				   }
			}
			
		
		
		if($rutafoto=='')
		{
		$sql="update usuarios set dni='".$_POST['dni']."', nombre='".$_POST['nombre']."', telefono='".$_POST['telefono']."', email='".$_POST['email']."', contrasena='".$_POST['contrasena']."' where id=".$_SESSION['idtenis'].";";
		}else{
		$sql="update usuarios set dni='".$_POST['dni']."', nombre='".$_POST['nombre']."', telefono='".$_POST['telefono']."', email='".$_POST['email']."', contrasena='".$_POST['contrasena']."', foto='".$rutafoto."' where id=".$_SESSION['idtenis'].";";
		}
				
				$result=mysqli_query($sql,$conexion);
				if($result)
				{
					$mensaje="Datos modificados";
					$_SESSION['usutenis']=	$_POST['nombre'];
				}else{
					$error="Error en la modificación";	
				}
	}
	
	
	if($_GET['datos']==1)
	{
		$sql="SELECT * FROM usuarios where id='".$_SESSION['idtenis']."';";
			$result=mysqli_query($sql,$conexion);
			$row=mysqli_fetch_array($result);
		$dni=$row['dni'];
		$nombre=$row['nombre'];
		$telefono=$row['telefono'];
		$email=$row['email'];
		$contrasena=$row['contrasena'];
	}
	
	
	
	if(isset($_POST['dni']) && $_POST['modificar']!=1)
	{
			$sql="SELECT dni, nombre FROM usuarios where dni='".$_POST['dni']."';";
			$result=mysqli_query($sql,$conexion);
			$row=mysqli_fetch_array($result);
			if($row['dni']!='')
			{
				$error="Ya se encuentra registrado el usuario ".$row['nombre']." con DNI ".$row['dni'];
			}else{
				$sql="insert into usuarios (dni, nombre, telefono, email, contrasena) 
				values ('".$_POST['dni']."', '".$_POST['nombre']."', '".$_POST['telefono']."', '".$_POST['email']."', '".$_POST['contrasena']."');";
				
				$result=mysqli_query($sql,$conexion);
				if($result)
				{
					$sql="SELECT id FROM usuarios where dni='".$_POST['dni']."' and contrasena='".$_POST['contrasena']."';";
					$result=mysqli_query($sql,$conexion);
					$row=mysqli_fetch_array($result);
					$mensaje="Se ha inscrito correctamente";
					$_SESSION['usutenis']=	$_POST['nombre'];
					$_SESSION['idtenis']=	$row['id'];
				}else{
					$error="Error en la inscripción";	
				}
			}
	}
	
	if(isset($_POST['resultados']))
	{
		$sqld="SELECT iddivision FROM ud where idusuario=".$_SESSION['idtenis']." order by id desc limit 1;";
			$resultd=mysqli_query($sqld,$conexion);
			$rowd=mysqli_fetch_array($resultd);
            $division=$rowd['iddivision'];
			
			$sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario where ud.iddivision=".$division." order by usuarios.nombre;";
			$result=mysqli_query($sql,$conexion);
			while ($row=mysqli_fetch_array($result))
			{
				if($row['uid']!=$_SESSION['idtenis'])
				{
					$j11=$_POST["j11_".$row['uid']];
					$j12=$_POST["j12_".$row['uid']];
					$j21=$_POST["j21_".$row['uid']];
					$j22=$_POST["j22_".$row['uid']];
					$j31=$_POST["j31_".$row['uid']];
					$j32=$_POST["j32_".$row['uid']];
					
					if($j11!='' || $j12!='' || $j21!='' || $j22!='' || $j31!='' || $j32!='')
					{
						if($j11!='' && $j12!='' && $j21!='' && $j22!='')
						{
							if(($j11<6 && $j12<6) || ($j21<6 && $j22<6) || ($j11==7 && ($j12!=5 && $j12!=6)) || ($j12==7 && ($j11!=5 && $j11!=6)) || ($j22==7 && ($j21!=5 && $j21!=6)) || ($j21==7 && ($j22!=5 && $j22!=6)) || ($j32==7 && ($j31!=5 && $j31!=6)) || ($j31==7 && ($j32!=5 && $j32!=6)))
							{
								$error='resultado incorrecto';
							}else{
								$sqlr="SELECT id FROM resultados where 
								((idu1=".$_SESSION['idtenis']." or idu2=".$_SESSION['idtenis'].") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");";
								$resultr=mysqli_query($sqlr,$conexion);
								$rowr=mysqli_fetch_array($resultr);
								if($rowr['id']!='')
								{	
								}else{
									if($j31==''){$j31=0;}
									if($j32==''){$j32=0;}
									if($j31==0 && $j32==0)
									{
										if($j11>$j12 && $j21>$j22)
										{
											$ganador=1;	
										}
										if($j11<$j12 && $j21<$j22)
										{
											$ganador=2;	
										}
									}else{
										$gana1=0;
										$gana2=0;
										if($j11>$j12)
										{
											$gana1+=1;
										}else{
											$gana2+=1;
										}
										if($j21>$j22)
										{
											$gana1+=1;
										}else{
											$gana2+=1;
										}
										if($j31>$j32)
										{
											$gana1+=1;
										}else{
											$gana2+=1;
										}
										if($gana1>$gana2)
										{
											$ganador=1;
										}else{
											$ganador=2;
										}
									}
									$sqlr="insert into resultados (idu1, idu2, division, j11, j12, j21, j22, j31, j32, ganador) values  
									(".$_SESSION['idtenis'].", ".$row['uid'].", ".$division.", ".$j11.", ".$j12.", ".$j21.", ".$j22.", ".$j31.", ".$j32.", ".$ganador.");";
									$resultr=mysqli_query($sqlr,$conexion);
									$rowr=mysqli_fetch_array($resultr);
									$mensaje="resultado introducido";
								}
							}
						}else{
							$error='debe cubrir el resultado completo';	
						}
					}
					
				}
				
			}
	}
	
	require_once('sesion.php');
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
  <title>Tenis Salceda</title>
  <meta name="description" content="Tenis Salceda de Caselas" />
  <meta name="keywords" content="tenis, salceda" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
  <div id="main">

    <div id="header"></div><!--close header-->	

	<div id="menubar">
	    
      <div id="welcome">
	    <h1><a href="#">Club de Tenis EDM Salceda</a></h1>
	  </div><!--close welcome-->
        
	  <div id="menu_items">
	    <?php $m=1; include('menu.php'); ?>
      </div><!--close menu-->
      
	</div><!--close menubar-->	
	
	<div id="site_content">		

	  <div class="sidebar_container">       
		
		<div class="sidebar">
            <?php include_once('sidebar.php'); ?>
        </div><!--close sidebar--><!--close sidebar--><!--close sidebar--><!--close sidebar-->  
		
      </div><!--close sidebar_container-->		  
	 
	  <div id="content"><!--close content_item-->
      <div class="content_item" style="line-height:2em;">
      <div><a href="http://presuntamentetenis.blogspot.com.es">Blog Presuntamentetenis</a></div>
      <div><a href="https://www.facebook.com/pages/Club-tenis-EDM-Salceda/546914465384980?fref=ts">Facebook Club tenis EDM Salceda</a></div>
      <div><a href="http://www.leelotodo.com">LeeloTodo</a></div>
      </div>
	  </div><!--close content--> 
	  
	</div><!--close site_content--> 
  
  </div><!--close main-->
  
  <div id="footer">
	  EDM Salceda - Tenis - <a href="pabellonadm.php">ADM</a>
  </div><!--close footer-->  
  
  <script>
  	function comprueba()
	{
		if(document.forminscripcion.dni.value=='')
		{
			alert('Debe rellenar el DNI');	
			document.forminscripcion.dni.focus();
			return false;
		}
		if(document.forminscripcion.nombre.value=='')
		{
			alert('Debe rellenar el nombre y apellidos');	
			document.forminscripcion.nombre.focus();
			return false;
		}
		if(document.forminscripcion.telefono.value=='')
		{
			alert('Debe rellenar el teléfono de contacto');	
			document.forminscripcion.telefono.focus();
			return false;
		}
		if(document.forminscripcion.email.value=='')
		{
			alert('Debe rellenar el e-mail');	
			document.forminscripcion.email.focus();
			return false;
		}
		if(document.forminscripcion.contrasena.value=='')
		{
			alert('Debe rellenar la contraseña');	
			document.forminscripcion.contrasena.focus();
			return false;
		}
	}
	
	function compruebajuego(juego)
	{
		if(juego.value.length>1)
		{
			alert('solo puede introducir una cifra');
			juego.value='';
			juego.focus();	
		}
		if(juego.value>7)
		{
			alert('no puede introducir una cifra mayor a 7');
			juego.value='';
			juego.focus();
		}
	}
  </script>
  
  
  
  <script>

</script>
</body>
</html>
