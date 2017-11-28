<?php
	
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	require_once('sesion.php');
	require('clases.php');
	$clasi = new Clasificacion;
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
  <title>Tenis Salceda</title>
  <meta name="description" content="Tenis Salceda de Caselas" />
  <meta name="keywords" content="tenis, salceda, clasificacion" />
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
	    <?php $m=4; include('menu.php'); ?>
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
		  
		  $sqll="SELECT * FROM ligas order by id desc;";
		  $resultl=mysql_query($sqll,$conexion);
		  while ($rowl=mysql_fetch_array($resultl))
		  {
		  
			  ?>
				<h1 class="encabezado"><?php echo $rowl['nombre']; ?></h1>
			  <?php
			  
			  $sqld="SELECT * FROM divisiones where idliga=".$rowl['id']." order by id;";
			  $resultd=mysql_query($sqld,$conexion);
			  while ($rowd=mysql_fetch_array($resultd))
			  {
				  $division=$rowd['id'];
				  
				  $sqlr="SELECT usuarios.* FROM usuarios inner join ud on usuarios.id=ud.idusuario where ud.iddivision=".$division." order by usuarios.nombre;";
				  $resultr=mysql_query($sqlr,$conexion);
				  while ($rowr=mysql_fetch_array($resultr))
				  {
					  $clasi->nombres($rowr['id'],$rowr['nombre'],$division);
				  }
				  
			  ?>
			  		<h2 class="divisiones" style="margin-bottom:0;"><?php echo $rowd['nombre']; ?></h2>
                    <div class="resultados" style="margin-top:0; margin-bottom:2em;">
			  <?php
			  		  
					 		$puntos=0;
							$cuantosj=0;
							$sqlr="SELECT ganador, idu1, idu2, j11, j12, j21, j22, j31, j32 FROM resultados where division=".$division.";";
							$resultr=mysql_query($sqlr,$conexion);
							while ($rowr=mysql_fetch_array($resultr))
							{	
								if($rowr['j31']==0 && $rowr['j32']==0)
								{
										$ds1=2;
										$ds2=-2;
										$dj1=($rowr['j11']-$rowr['j12'])+($rowr['j21']-$rowr['j22']);
										$dj2=-($dj1);
								}else{
										$ds1=1;
										$ds2=-1;
										$dj1=($rowr['j11']-$rowr['j12'])+($rowr['j21']-$rowr['j22'])+($rowr['j31']-$rowr['j32']);
										$dj2=-($dj1);
								}
								if($rowr['ganador']==1)
								{
									$clasi->puntuacion($rowr['idu1'], 3, $division, $ds1, $dj1);
									$clasi->puntuacion($rowr['idu2'], 1, $division, $ds2, $dj2);
								}else{
									$clasi->puntuacion($rowr['idu2'], 3, $division, $ds1, $dj2);
									$clasi->puntuacion($rowr['idu1'], 1, $division, $ds2, $dj1);
								}
							}
						
						?>
                        	<div style="padding:0 .5em;"><div class="nombres" style="border:dashed 3px white; margin:0;">Jugador</div><div class="puntos" style="border:dashed 3px white; margin:0; margin-left:3px;">Puntos</div><div class="puntos" style="border:dashed 3px white; margin:0; margin-left:3px;">PJ</div><div class="puntos" style="border:dashed 3px white; margin:0; margin-left:3px;">DS</div><div class="puntos" style="border:dashed 3px white; margin:0; margin-left:3px;">DJ</div></div>
                        <?php
						$clasi->pontodo($division);
						?></div><?php
						
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

</script>
  
</body>
</html>
