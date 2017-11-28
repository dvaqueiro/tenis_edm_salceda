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
	    <?php $m=8; include('menu.php'); ?>
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
		  
	    <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/gl_ES/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<br />
<div class="fb-like-box" data-href="https://www.facebook.com/pages/Club-tenis-EDM-Salceda/546914465384980" data-width="665" data-height="850" data-colorscheme="light" data-show-faces="false" data-header="true" data-stream="true" data-show-border="true" style="background-color:#eee;"></div>
          
          			  
		
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
