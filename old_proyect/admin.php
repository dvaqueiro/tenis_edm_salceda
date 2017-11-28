<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	
if($_SESSION['idtenis']==12 || $_SESSION['idtenis']==23 || $_SESSION['idtenis']==36 || $_SESSION['idtenis']==14){
	
	if(isset($_POST['resultados']))
	{
		$sqld="SELECT iddivision FROM ud where idusuario=".$_GET['usuario']." order by id desc limit 1;";
			$resultd=mysql_query($sqld,$conexion);
			$rowd=mysql_fetch_array($resultd);
            $division=$rowd['iddivision'];
			
			$sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario where ud.iddivision=".$division." order by usuarios.nombre;";
			$result=mysql_query($sql,$conexion);
			while ($row=mysql_fetch_array($result))
			{
				if($row['uid']!=$_GET['usuario'])
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
								((idu1=".$_GET['usuario']." or idu2=".$_GET['usuario'].") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");";
								$resultr=mysql_query($sqlr,$conexion);
								$rowr=mysql_fetch_array($resultr);
								if($rowr['id']!='')
								{	
									
									
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
									$sqlr="delete from resultados where division=".$division." and idu1=".$_GET['usuario']." and idu2=".$row['uid'].";";
									$resultr=mysql_query($sqlr,$conexion);
									$sqlr="delete from resultados where division=".$division." and idu2=".$_GET['usuario']." and idu1=".$row['uid'].";";
									$resultr=mysql_query($sqlr,$conexion);
									$sqlr="insert into resultados (idu1, idu2, division, j11, j12, j21, j22, j31, j32, ganador) values  
									(".$_GET['usuario'].", ".$row['uid'].", ".$division.", ".$j11.", ".$j12.", ".$j21.", ".$j22.", ".$j31.", ".$j32.", ".$ganador.");";
									$resultr=mysql_query($sqlr,$conexion);
									$rowr=mysql_fetch_array($resultr);
									$mensaje="resultado introducido";
								
								
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
									(".$_GET['usuario'].", ".$row['uid'].", ".$division.", ".$j11.", ".$j12.", ".$j21.", ".$j22.", ".$j31.", ".$j32.", ".$ganador.");";
									$resultr=mysql_query($sqlr,$conexion);
									$rowr=mysql_fetch_array($resultr);
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
	 
	  <div id="content">
        
		<div class="content_item">
		  
		  
	      
          <?php
		  
		  	if($mensaje)
			{
				echo "<div class='mensaje'>".$mensaje."</div>";
			}
			
			if($error)
			{
				echo "<div class='error'>".$error."</div>";
			}
		  
		  
		  ?>
          
          
          <?php
           
            if($row['uid']!=$_SESSION['idtenis'])
				{ ?>
            <div class="resultados">
            <h1>Resultados</h1> 
            
            
            <?php
            $sqld="SELECT id FROM ligas order by id desc limit 1;";
			$resultd=mysql_query($sqld,$conexion);
			$rowd=mysql_fetch_array($resultd);
            $idliga=$rowd['id'];
			
			if(!isset($_GET['usuario']))
			{
				$sqlx="select id, nombre from divisiones where idliga=".$idliga.";";
				$resultx=mysql_query($sqlx,$conexion);
				while ($rowx=mysql_fetch_array($resultx))
				{
					echo '<h2 class="divisiones">'.$rowx['nombre'].'</h2>';
					$sqly="SELECT usuarios.nombre as n, usuarios.id as uid, ud.iddivision as divid FROM usuarios inner join ud on usuarios.id=ud.idusuario where ud.iddivision=".$rowx['id']." order by usuarios.nombre;";
					$resulty=mysql_query($sqly,$conexion);
					while ($rowy=mysql_fetch_array($resulty))
					{
						echo '<div style="margin-left:1em; margin-bottom:1em;"><a href="admin.php?usuario='.$rowy['uid'].'&divid='.$rowy['divid'].'">'.$rowy['n'].'</a></div>';
					}
				}
				
			}else{
				
			echo '<div align="right"><a href="admin.php">volver a usuarios</a></div><br>';
			
			$sqlx="select id, nombre from divisiones where id=".$_GET['divid'].";"; 
			$resultx=mysql_query($sqlx,$conexion);
			while ($rowx=mysql_fetch_array($resultx))
			{
				echo '<h2 class="divisiones">'.$rowx['nombre'].'</h2>';
				$division=$rowx['id'];
				echo '<form method="post" name="divi'.$division.'">';
				
				$sqld="select nombre from usuarios where id=".$_GET['usuario'].";";	
					$resultd=mysql_query($sqld,$conexion);
					$rowd=mysql_fetch_array($resultd);
				$primusu = $_GET['usuario'];
				$primnombre = $rowd['nombre'];
			
			$sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario where ud.iddivision=".$division." order by usuarios.nombre;";
			$result=mysql_query($sql,$conexion);
			while ($row=mysql_fetch_array($result))
			{
				if($row['uid']!=$primusu){
					$sqld="SELECT * FROM resultados where 
								((idu1=".$primusu." or idu2=".$primusu.") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");";
								
					$resultd=mysql_query($sqld,$conexion);
					$rowd=mysql_fetch_array($resultd);
					
					if($primusu!=$rowd['idu1'] && $rowd['j11']!=''){
					
					?>
                    	<table width="100%" border="0" cellspacing="3" cellpadding="0">
                          <tr>
                            <td height="30" width="35%" align="right"><?php echo $primnombre; ?></td>
                            <td width="10%" align="center"><input type="text" name="j12_<?php echo $row['uid']; ?>" <?php if($rowd['j12']!=''){ ?>  value="<?php echo $rowd['j12']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" />-<input type="text" name="j11_<?php echo $row['uid']; ?>" <?php if($rowd['j11']!=''){ ?>  value="<?php echo $rowd['j11']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" /></td>
                            <td width="10%" align="center"><input type="text" name="j22_<?php echo $row['uid']; ?>" <?php if($rowd['j22']!=''){ ?>  value="<?php echo $rowd['j22']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" />-<input type="text" name="j21_<?php echo $row['uid']; ?>" <?php if($rowd['j21']!=''){ ?>  value="<?php echo $rowd['j21']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" /></td>
                            <td width="10%" align="center"><input type="text" name="j32_<?php echo $row['uid']; ?>" <?php if($rowd['j32']!=''){ ?>  value="<?php if($rowd['j31']!=0 && $rowd['j32']!=0){echo $rowd['j32'];} ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" />-<input type="text" name="j31_<?php echo $row['uid']; ?>" <?php if($rowd['j31']!=''){ ?>  value="<?php if($rowd['j31']!=0 && $rowd['j32']!=0){echo $rowd['j31'];} ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" /></td>
                            <td width="35%" align="left"><?php echo $row['n']; ?></td>
                          </tr>
                        </table>
			
                    <?php
					}else{ ?>
				
                        <table width="100%" border="0" cellspacing="3" cellpadding="0">
                          <tr>
                            <td height="30" width="35%" align="right"><?php echo $primnombre; ?></td>
                            <td width="10%" align="center"><input type="text" name="j11_<?php echo $row['uid']; ?>" <?php if($rowd['j11']!=''){ ?> value="<?php echo $rowd['j11']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" />-<input type="text" name="j12_<?php echo $row['uid']; ?>" <?php if($rowd['j12']!=''){ ?>  value="<?php echo $rowd['j12']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" /></td>
                            <td width="10%" align="center"><input type="text" name="j21_<?php echo $row['uid']; ?>" <?php if($rowd['j21']!=''){ ?>  value="<?php echo $rowd['j21']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" />-<input type="text" name="j22_<?php echo $row['uid']; ?>" <?php if($rowd['j22']!=''){ ?>  value="<?php echo $rowd['j22']; ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" /></td>
                            <td width="10%" align="center"><input type="text" name="j31_<?php echo $row['uid']; ?>" <?php if($rowd['j31']!=''){ ?>  value="<?php if($rowd['j31']!=0 && $rowd['j32']!=0){echo $rowd['j31'];} ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" />-<input type="text" name="j32_<?php echo $row['uid']; ?>" <?php if($rowd['j32']!=''){ ?>  value="<?php if($rowd['j31']!=0 && $rowd['j32']!=0){echo $rowd['j32'];} ?>"<?php } ?> class="juegos" onchange="compruebajuego(this);" /></td>
                            <td width="35%" align="left"><?php echo $row['n']; ?></td>
                          </tr>
                        </table>
                        <?php
					}
				}
			} ?>
            <input type="hidden" name="resultados" value="<?php echo $primusu; ?>" />
            <div><input type="submit" value="guardar" name="resultados" style="cursor:pointer; width:10em; margin-top:.5em; padding:.5em;" /></div><br /><br />
            </form>
            
            <?php
			}
				}
				}
       		  ?>
        
		</div><!--close content_item-->
      
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
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46792901-1', 'esy.es');
  ga('send', 'pageview');

</script>

<?php } ?>
</body>
</html>
