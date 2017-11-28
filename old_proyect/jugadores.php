<?php
	
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	require_once('sesion.php');
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
  <title>Tenis Salceda</title>
  <meta name="description" content="Tenis Salceda de Caselas" />
  <meta name="keywords" content="tenis, salceda, jugadores" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="css/lightbox.css" media="screen"/>
<script src="js/jquery-1.10.2.min.js"></script>
<script src="js/lightbox-2.6.min.js"></script>
</head>

<body>
  <div id="main">

    <div id="header"></div><!--close header-->	

	<div id="menubar">
	    
      <div id="welcome">
	    <h1><a href="#">Club de Tenis EDM Salceda</a></h1>
	  </div><!--close welcome-->
        
	  <div id="menu_items">
	    <?php $m=2; include('menu.php'); ?>
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
		  
	if(isset($_SESSION['idtenis'])){
		  
		  $sqll="SELECT * FROM ligas order by id desc;";
		  $resultl=mysqli_query($conexion, $sqll);
		  while ($rowl=mysqli_fetch_array($resultl))
		  {
		  
			  ?>
				<h1 class="encabezado"><?php echo $rowl['nombre']; ?></h1>
                
                
			  <?php
			  
			  $sqld="SELECT * FROM divisiones where idliga=".$rowl['id']." order by id;";
			  $resultd=mysqli_query($conexion, $sqld);
			  while ($rowd=mysqli_fetch_array($resultd))
			  {
			  ?>
			  		<h2 class="divisiones" style="margin-bottom:0;"><?php echo $rowd['nombre']; ?></h2>
                    <div class="resultados" style="margin-top:0; margin-bottom:2em;">
			  <?php
					  $sql="SELECT usuarios.* FROM usuarios inner join ud on usuarios.id=ud.idusuario where ud.iddivision=".$rowd['id']." order by usuarios.nombre;";
					  $result=mysqli_query($conexion, $sql);
					  while ($row=mysqli_fetch_array($result))
					  {
						 ?>
                         <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td>
                            	<div class="titulo" style="font-size:18px;"><?php echo $row['nombre']; ?></div>
							<div style="margin-top:3px; margin-bottom:28px;"><strong><?php echo $row['telefono']; ?></strong> / <a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></div>
                            </td>
                            <td align="center" width="170">
                            	<?php
									if($row['foto']!=''){
								?>
                                	<a href="<?php echo $row['foto']; ?>" data-lightbox="<?php echo $row['nombre']; ?>" title="<?php echo $row['nombre']; ?>">
                                	<img src="<?php echo $row['foto']; ?>" width="150" /></a>
                                 <?php
									}
								?>
                            </td>
                          </tr>
                        </table>

                         
						 
						 <?php  
					  }
					  ?></div><?php
			  }
		  
		  }
	}else{
		echo '<div class="resultados" style="margin-top:0; margin-bottom:2em;">Debe ser usuario registrado para acceder a esta página</div>';	
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
