<?php
	
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	require_once('sesion.php');
	
	if(isset($_POST['comentario']))
	{
			$sqlr="insert into comentarios (comentario, usuario, fecha) values  
									('".addslashes($_POST['comentario'])."', '".$_SESSION['usutenis']."', '".date('Y-m-d H:i:s')."');";
			$resultr=mysqli_query($conexion, $sqlr);
			if($resultr)
			{
				$mensaje="comentario añadido";
			}else{
				$error="error al introducir comentario";
			}
									
	}
	
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
  <script type="text/javascript" src="tinymce/tinymce.min.js"></script>
				<script type="text/javascript">
                tinymce.init({
                    selector: "textarea",
                    plugins: [
                        "advlist autolink lists link image charmap print preview anchor",
                        "searchreplace visualblocks code fullscreen",
                        "insertdatetime media table contextmenu paste"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
                });
                </script>
</head>

<body>
  <div id="main">

    <div id="header"></div><!--close header-->	

	<div id="menubar">
	    
      <div id="welcome">
	    <h1><a href="#">Club de Tenis EDM Salceda</a></h1>
	  </div><!--close welcome-->
        
	  <div id="menu_items">
	    <?php $m=6; include('menu.php'); ?>
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
		  
	if(isset($_SESSION['idtenis']))
	{
		  
		  ?>
          	<div><h1 class="encabezado">Comentarios</h1>
            <form name="formcomentario" method="post">
            	<textarea name="comentario"></textarea>
                <input type="submit" style="cursor:pointer; padding:.2em 1em;" value="Enviar" />
            </form>
            </div>
            <br />
          <?php
		  
		  $sqll="SELECT * FROM comentarios order by fecha desc;";
		  $resultl=mysqli_query($conexion, $sqll);
		  while ($rowl=mysqli_fetch_array($resultl))
		  {
			  $dtime = new DateTime($rowl['fecha']);
			  ?>
              		<h2 class="divisiones"><?php print $dtime->format("d/m/Y  H:i:s"); ?> | <?php echo $rowl['usuario']; ?></h2>
                    <div class="resultados" style="margin-top:0; margin-bottom:2em; font-weight:normal;">
                    	<?php echo nl2br($rowl['comentario']); ?>
                    </div>
              <?php
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
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46792901-1', 'esy.es');
  ga('send', 'pageview');

</script>
  
</body>
</html>
