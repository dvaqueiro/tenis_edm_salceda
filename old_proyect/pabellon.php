<?php
	
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	require_once('cnx/cnx.php');
	require_once('sesion.php');

	$fecha = date('d-m-Y');
	$nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
	$nuevafecha = date ( 'd-m-Y' , $nuevafecha );
	$nuevafecha2 = strtotime ( '+2 day' , strtotime ( $fecha ) ) ;
	$nuevafecha2 = date ( 'd-m-Y' , $nuevafecha2 );
	
	
	if($_POST['inputField']!='')
	{
		$pasa=1;
		if(date('w', mktime (0, 0, 0, date('m'), date('d'), date('Y')))==5 && ($_POST['inputField'] == date('d-m-Y')))
		{
			if(date ( "H" )>11)
			{
				$error='Debe reservar la pista antes del viernes a las 12h';
				$pasa=0;	
			}
		}
		if(date('w', mktime (0, 0, 0, date('m'), date('d'), date('Y')))==5 && ($_POST['inputField']==$nuevafecha || $_POST['inputField']==$nuevafecha2))
		{
				$error='Debe reservar la pista antes del viernes a las 12h';
				$pasa=0;
		}
		if(date('w', mktime (0, 0, 0, date('m'), date('d'), date('Y')))==6 && ($_POST['inputField'] == date('d-m-Y') || $_POST['inputField']==$nuevafecha))
		{
				$error='Debe reservar la pista antes del viernes a las 12h';
				$pasa=0;	
		}
		if(date('w', mktime (0, 0, 0, date('m'), date('d'), date('Y')))==7 && ($_POST['inputField'] == date('d-m-Y')))
		{
				$error='Debe reservar la pista antes del viernes a las 12h';
				$pasa=0;	
		}
		
		if($pasa==1)
		{
			if($_POST['hora']!='')
			{
				if($_POST['pista']!=''){
				if(isset($_SESSION['idtenis'])){
					$fecha = $_POST['inputField'];
					list($dia,$mes,$ano)=explode("-",$fecha);
					$fecha="$ano-$mes-$dia";
					$sql="select id from pabellon where hora=".$_POST['hora']." and fecha='".$fecha."' and pista=".$_POST['pista'].";";
					$result=mysqli_query($conexion, $sql);
					$row=mysqli_fetch_array($result);
					if($row['id']!='')
					{
						$error="Fecha ya solicitada";	
					}else{
						
							$sql="insert into pabellon (idusuario, hora, fecha, pista) values (".$_SESSION['idtenis'].", ".$_POST['hora'].", '".$fecha."', ".$_POST['pista'].");";
							$result=mysqli_query($conexion, $sql);
							if($result)
							{
								switch($_POST['hora'])
								{
									case 1:
										$horamail=' de 10 a 12';
									break;
									case 2:
										$horamail=' de 12 a 14';
									break;
									case 3:
										$horamail=' de 14 a 16';
									break;
									case 4:
										$horamail=' de 16 a 18';
									break;
									case 5:
										$horamail=' de 18 a 20';
									break;
									case 6:
										$horamail=' de 20 a 22';
									break;
								}
								
								$sql="select nombre, email, telefono from usuarios where id=".$_SESSION['idtenis'].";";
								$result=mysqli_query($conexion, $sql);
								$row=mysqli_fetch_array($result);
								
								//mail al usuario que reserva
								$to3 = 'jfloureiro@gmail.com';
								$to = 'deportes@edmsalceda.es';
								if($_POST['pista']==1){
									$subject = "Reserva Pabellón de Parderrubias";
								}else{
									$subject = "Reserva pista exterior de Parderrubias";
								}
								$message = '
								<table width="500" border="0" cellspacing="5" cellpadding="5">
								  <tr>
									<td width="98" bgcolor="#CDFDC6"><strong>Nombre</strong></td>
									<td width="387" bgcolor="#EEFFDF">'.$row['nombre'].'</td>
								  </tr>
								  <tr>
									<td width="98" bgcolor="#CDFDC6"><strong>E-Mail</strong></td>
									<td width="387" bgcolor="#EEFFDF">'.$row['email'].'</td>
								  </tr>
								  <tr>
									<td width="98" bgcolor="#CDFDC6"><strong>Teléfono</strong></td>
									<td width="387" bgcolor="#EEFFDF">'.$row['telefono'].'</td>
								  </tr>
								  <tr>
									<td bgcolor="#CDFDC6"><strong>Fecha y hora reservadas</strong></td>
									<td bgcolor="#EEFFDF">'.$_POST['inputField'].$horamail.'</td>
								  </tr>
								</table>
								';
								
								$headers.='MIME-Version: 1.0'."\r\n";
								 $headers.='Content-type: text/html; charset=utf-8'."\r\n";
								 $headers.='From: '.$row['email']."\r\n";
								$envio=mail($to3,$subject,$message,$headers);
								$envio=mail($to,$subject,$message,$headers);
								
								
								$mensaje="Reserva hecha correctamente";
							}else{
								$error="Error al reservar, intentelo más tarde";	
							}
					}
				}else{
					$error="Debe estar registrado para reservar";
				}
				}else{
					$error="Escoja una pista";
				}
			}
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
  <link rel="stylesheet" type="text/css" media="all" href="jsdate/jsDatePick_ltr.min.css" />
  <script type="text/javascript" src="jsdate/jsDatePick.full.1.0.spain.js"></script>
  <script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"inputField",
			dateFormat:"%d-%m-%Y"
		});
	};
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
	    <?php $m=7; include('menu.php'); ?>
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
          
          <form name="pabellon" method="post">
          <div class="titulo">
          	<span class="pista"><input class="radiopista" <?php if($_POST['pista']==1){ ?> checked="checked" <?php } ?> type="radio" name="pista" value="1" /> 
            Parderrubias pabellón</span> 
            <span class="pista"><input class="radiopista" <?php if($_POST['pista']==2){ ?> checked="checked" <?php } ?> type="radio" name="pista" value="2" /> 
            Parderrubias pista exterior</span>
          </div>
          <div class="titulo">
          	Fecha
          </div>
          <input type="text" style="text-align:center; width:8em; font-size:25px;" id="inputField" name="inputField" value="<?php echo $_POST['inputField']; ?>" class="formulario" /> 
          <input type="button" value="Ver" class="formulario" style="width:10em; cursor:pointer;" onclick="document.pabellon.submit();" />
          
          <?php
		  	if($_POST['inputField']!='' && $_POST['pista']==''){
				echo "<div class='error'>Escoja una pista</div>";	
			}
		  
		  	if($_POST['inputField']!='' && $_POST['pista']!=''){
				
				$fecha = $_POST['inputField'];
				list($dia,$mes,$ano)=explode("-",$fecha);
				$fecha="$ano-$mes-$dia";
				$sql="select pabellon.*, usuarios.nombre from pabellon inner join usuarios on pabellon.idusuario=usuarios.id where fecha='".$fecha."' and pista=".$_POST['pista'].";";
				$result=mysqli_query($conexion, $sql);
				while ($row=mysqli_fetch_array($result))
				{
					switch($row['hora'])
					{
						case 1:
							$uno='Solicitado por '.$row['nombre'];
							if($row['aprobado']==1){
								$clase1="verde";
								$uno='Solicitado por '.$row['nombre'].'. Reserva confirmada.';
							}
							if($row['aprobado']==2){
								$clase1="rojo";
								$uno='Pista ocupada. Solicitud denegada';
							}
						break;
						case 2:
							$dos='Solicitado por '.$row['nombre'];
							if($row['aprobado']==1){
								$clase2="verde";
								$dos='Solicitado por '.$row['nombre'].'. Reserva confirmada.';
							}
							if($row['aprobado']==2){
								$clase2="rojo";
								$dos='Pista ocupada. Solicitud denegada';
							}
						break;
						case 3:
							$tres='Solicitado por '.$row['nombre'];
							if($row['aprobado']==1){
								$clase3="verde";
								$tres='Solicitado por '.$row['nombre'].'. Reserva confirmada.';
							}
							if($row['aprobado']==2){
								$clase3="rojo";
								$tres='Pista ocupada. Solicitud denegada';
							}
						break;
						case 4:
							$cuatro='Solicitado por '.$row['nombre'];
							if($row['aprobado']==1){
								$clase4="verde";
								$cuatro='Solicitado por '.$row['nombre'].'. Reserva confirmada.';
							}
							if($row['aprobado']==2){
								$clase4="rojo";
								$cuatro='Pista ocupada. Solicitud denegada';
							}
						break;
						case 5:
							$cinco='Solicitado por '.$row['nombre'];
							if($row['aprobado']==1){
								$clase5="verde";
								$cinco='Solicitado por '.$row['nombre'].'. Reserva confirmada.';
							}
							if($row['aprobado']==2){
								$clase5="rojo";
								$cinco='Pista ocupada. Solicitud denegada';
							}
						break;
						case 6:
							$seis='Solicitado por '.$row['nombre'];
							if($row['aprobado']==1){
								$clase6="verde";
								$seis='Solicitado por '.$row['nombre'].'. Reserva confirmada.';
							}
							if($row['aprobado']==2){
								$clase6="rojo";
								$seis='Pista ocupada. Solicitud denegada';
							}
						break;
					}
				}
		  ?>
          <div>
          	<?php if($uno!=''){ ?>
            <div class="reservado <?php echo $clase1; ?>"> <?php
            	echo $uno; 
				?></div><?php
                
             }else{ ?>
          	<input type="button" name="hora1" value="Reservar el <?php echo $_POST['inputField']; ?> de 10 a 12" class="formulario" style="width:27em; cursor:pointer;" onclick="compru(1);" />
            <?php } ?>
          </div>
          
          <div>
          <?php if($dos!=''){  ?>
            <div class="reservado <?php echo $clase2; ?>"> <?php
            	echo $dos;
				?></div><?php
             }else{ ?>
          	<input type="button" name="hora2" value="Reservar el <?php echo $_POST['inputField']; ?> de 12 a 14" class="formulario" style="width:27em; cursor:pointer;" onclick="compru(2);" />
            <?php } ?>
          </div>
          
          <div>
          <?php if($tres!=''){  ?>
            <div class="reservado <?php echo $clase3; ?>"> <?php
            	echo $tres;
				?></div><?php
             }else{ ?>
          	<input type="button" name="hora3" value="Reservar el <?php echo $_POST['inputField']; ?> de 14 a 16" class="formulario" style="width:27em; cursor:pointer;" onclick="compru(3);" />
            <?php } ?>
          </div>
          
          <div>
          <?php if($cuatro!=''){  ?>
            <div class="reservado <?php echo $clase4; ?>"> <?php
            	echo $cuatro;
				?></div><?php
             }else{ ?>
          	<input type="button" name="hora4" value="Reservar el <?php echo $_POST['inputField']; ?> de 16 a 18" class="formulario" style="width:27em; cursor:pointer;" onclick="compru(4);" />
            <?php } ?>
          </div>
          
          <div>
          <?php if($cinco!=''){  ?>
            <div class="reservado <?php echo $clase5; ?>"> <?php
            	echo $cinco;
				?></div><?php
             }else{ ?>
          	<input type="button" name="hora5" value="Reservar el <?php echo $_POST['inputField']; ?> de 18 a 20" class="formulario" style="width:27em; cursor:pointer;" onclick="compru(5);" />
            <?php } ?>
          </div>
          
          <div>
          <?php if($seis!=''){  ?>
            <div class="reservado <?php echo $clase6; ?>"> <?php
            	echo $seis;
				?></div><?php
             }else{ ?>
          	<input type="button" name="hora6" value="Reservar el <?php echo $_POST['inputField']; ?> de 20 a 22" class="formulario" style="width:27em; cursor:pointer;" onclick="compru(6);" />
            <?php } ?>
          </div>
          
          </div>
          <input type="hidden" name="hora" value="" />
         <?php } ?>
          </form>
		<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
		</div><!--close content_item-->
      
	  </div><!--close content--> 
	  
	</div><!--close site_content--> 
  
  </div><!--close main-->
  
  <div id="footer">
	  EDM Salceda - Tenis - <a href="pabellonadm.php">ADM</a>
  </div><!--close footer-->  
 
  <script>
  	function compru(hora)
	{
		switch(hora)
		{
			case 1:
				cuantas='10 a 12';
			break;	
			case 2:
				cuantas='12 a 14';
			break;	
			case 3:
				cuantas='14 a 16';
			break;	
			case 4:
				cuantas='16 a 18';
			break;	
			case 5:
				cuantas='18 a 20';
			break;	
			case 6:
				cuantas='20 a 22';
			break;	
		}
		document.pabellon.hora.value=hora;
		if(confirm('Va a reservar el día '+document.pabellon.inputField.value+' de '+cuantas))
		{
			document.pabellon.submit();	
		}
		
	}
  </script>
  
  
  <script>

</script>
  
</body>
</html>
