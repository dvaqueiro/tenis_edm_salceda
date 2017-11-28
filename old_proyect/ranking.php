<?php
	
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	require_once('sesion.php');
	require('clases.php');
	$rank = new Ranking;
	
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
	    <?php $m=9; include('menu.php'); ?>
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
		  
          <h1 class="encabezado">Ranking</h1>
	      
          <?php
		  $sqll="SELECT id FROM ligas order by id desc limit 3;";
		  $resultl=mysqli_query($conexion, $sqll);
		  while ($rowl=mysqli_fetch_array($resultl))
		  {
			  ?>
				
			  <?php
			  
			  $sqlu="SELECT usuarios.id, usuarios.nombre FROM usuarios;";
			  $resultu=mysqli_query($conexion, $sqlu);
			  while ($rowu=mysqli_fetch_array($resultu))
			  {
				$rank->pondatos($rowu['id'], $rowu['nombre']);
			  }
			  
			  $sqld="SELECT id, categoria FROM divisiones where idliga=".$rowl['id']." and categoria<5 order by id;";
			  $resultd=mysqli_query($conexion, $sqld);
			  while ($rowd=mysqli_fetch_array($resultd))
			  {
                              
				  	$sqlr="SELECT * from resultados where division=".$rowd['id'].";";
			  		$resultr=mysqli_query($conexion, $sqlr);
			  		while ($rowr=mysqli_fetch_array($resultr))
				  	{
                                            
								if($rowr['ganador']==1)
								{
									$rank->puntos($rowr['idu1'], 3, 1);
									$rank->puntos($rowr['idu2'], 1, 0);
								}

                                                                if($rowr['ganador']==2)
                                                                {
									$rank->puntos($rowr['idu2'], 3, 1);
									$rank->puntos($rowr['idu1'], 1, 0);
								}
                                                                
                                                                if($rowr['ganador']==0)
                                                                {
									$rank->puntos($rowr['idu2'], 0, 0);
									$rank->puntos($rowr['idu1'], 0, 0);
								}
					}

					$sqlr="SELECT idusuario from ud where iddivision=".$rowd['id'].";";
			  		$resultr=mysqli_query($conexion, $sqlr);
			  		while ($rowr=mysqli_fetch_array($resultr))
				  	{
						$puntospordiv=0;
						if($rowd['categoria']==1)
							$puntospordiv=50;
						if($rowd['categoria']==2)
							$puntospordiv=40;
						if($rowd['categoria']==3)
							$puntospordiv=30;
                                                if($rowd['categoria']==4)
							$puntospordiv=20;
                                                if($rowd['categoria']==5)
							$puntospordiv=10;
						$rank->puntosdivision($rowr['idusuario'], $puntospordiv);
					}
					
			  }
		  		
		  }
                 
		  ?>
          <div class="resultados" style="margin-top:0; margin-bottom:2em;">
			  <?php
                $rank->pontodo();
              ?>
          </div>
          			  
		
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
