<?php
	
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	require_once('sesion.php');
	
	if($_POST['enviacontacto'])
	{
		$to = "presuntamentetenis@gmail.com";
		$to2 = "nachomonitordetenis@gmail.com";
		$to3 = "jfloureiro@gmail.com";
		$subject = "Mensaje de contacto desde la web de tenis Salceda";
		$message = '
		<table width="500" border="0" cellspacing="5" cellpadding="5">
		  <tr>
			<td width="98" bgcolor="#CDFDC6"><strong>Nombre</strong></td>
			<td width="387" bgcolor="#EEFFDF">'.$_POST['nombre'].'</td>
		  </tr>
		  <tr>
			<td bgcolor="#CDFDC6"><strong>E-mail</strong></td>
			<td bgcolor="#EEFFDF">'.$_POST['email'].'</td>
		  </tr>
		  <tr>
			<td valign="top" bgcolor="#CDFDC6"><strong>Comentario</strong></td>
			<td bgcolor="#EEFFDF">'.nl2br($_POST['comentario']).'</td>
		  </tr>
		</table>
		';
		$from = "presuntamentetenis@gmail.com";
		$headers.='MIME-Version: 1.0'."\r\n";
		 $headers.='Content-type: text/html; charset=utf-8'."\r\n";
		 $headers.='From: '.$_POST['email']."\r\n";
		 $headers.='To: '.$email."\r\n";
		$envio=mail($to,$subject,$message,$headers);
		mail($to2,$subject,$message,$headers);
		mail($to3,$subject,$message,$headers);
		if($envio)
		{
			$mensaje="mensaje enviado";
		}else{
			$error='error en el envío';	
		}
	}
	
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
	    <?php $m=5; include('menu.php'); ?>
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
          
          <form name="contacto" method="post">
          
          	 <div class="titulo">
          	Nombre
          </div>
          <div>
          <input type="text" name="nombre" class="formulario" />
          </div>
          
          <div class="titulo">
          	E-mail
          </div>
          <div>
          <input type="text" name="email" class="formulario" />
          </div>
          
          <div class="titulo">
          	Comentario
          </div>
          <div>
          <textarea name="comentario" class="formulario" style="width:95%; height:200px;"></textarea>
          </div>
          <input type="submit" value="Enviar" onclick="return compru();" name="enviacontacto" style="padding:.5em 3em; cursor:pointer;" />
          </form>
		
		</div><!--close content_item-->
      
	  </div><!--close content--> 
	  
	</div><!--close site_content--> 
  
  </div><!--close main-->
  
  <div id="footer">
	  EDM Salceda - Tenis - <a href="pabellonadm.php">ADM</a>
  </div><!--close footer-->  
  
  <script>
  	function compru()
	{
		if(document.contacto.nombre.value=='')
		{
			alert('Debe rellenar el campo nombre');	
			document.contacto.nombre.focus();
			return false;
		}
		if(document.contacto.email.value=='')
		{
			alert('Debe rellenar el campo e-mail');	
			document.contacto.email.focus();
			return false;
		}
		if(document.contacto.comentario.value=='')
		{
			alert('Debe rellenar el campo comentario');	
			document.contacto.comentario.focus();
			return false;
		}
	}
  </script>
  
  
  <script>

</script>
  
</body>
</html>
