<?php
	
	session_start();
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	
	if($_SESSION['idtenis']==62 || $_SESSION['idtenis']==12){
	require_once('cnx/cnx.php');
	require_once('sesion.php');
	
	if($_POST['hora']!='' || $_POST['horad']!='' || $_POST['horab']!='')
	{
		if($_POST['pista']!=''){
		if(isset($_SESSION['idtenis'])){
			
			//borrar
			if($_POST['horab']!=''){
					$fecha = $_POST['inputField'];
					list($dia,$mes,$ano)=explode("-",$fecha);
					$fecha="$ano-$mes-$dia";
					$sql="delete from pabellon where hora=".$_POST['horab']." and fecha='".$fecha."' and pista=".$_POST['pista'].";";
					$result=mysqli_query($sql,$conexion);
			}
			
			//aprobar
			if($_POST['hora']!=''){
			$fecha = $_POST['inputField'];
			list($dia,$mes,$ano)=explode("-",$fecha);
    		$fecha="$ano-$mes-$dia";
			
				
					$sql="update pabellon set aprobado=1 where hora=".$_POST['hora']." and fecha='".$fecha."' and pista=".$_POST['pista'].";"; 
					$result=mysqli_query($sql,$conexion);
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
						
						$sql="select usuarios.email, usuarios.nombre from usuarios inner join pabellon on pabellon.idusuario=usuarios.id where pabellon.fecha='".$fecha."' and pabellon.pista=".$_POST['pista']." and hora=".$_POST['hora'].";";
						$result=mysqli_query($sql,$conexion);
						$row=mysqli_fetch_array($result);
						
						//mail al usuario que reserva
						$to3 = $row['email'];
						if($_POST['pista']==1){
							$subject = "Reserva Pabellón de Parderrubias (Aprobada)";
						}else{
							$subject = "Reserva pista exterior de Parderrubias (Aprobada)";
						}
						$message = '
						<table width="500" border="0" cellspacing="5" cellpadding="5">
						  <tr>
							<td width="98" bgcolor="#CDFDC6"><strong>Nombre</strong></td>
							<td width="387" bgcolor="#EEFFDF">'.$row['nombre'].'</td>
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
						
						
						
						$mensaje="Pista aprobada correctamente";
					}else{
						$error="Error al aprobar, intentelo más tarde";	
					}
			}
			
			//rechazar
			if($_POST['horad']!=''){
			$fecha = $_POST['inputField'];
			list($dia,$mes,$ano)=explode("-",$fecha);
    		$fecha="$ano-$mes-$dia";
			
				
					$sql="update pabellon set aprobado=2 where hora=".$_POST['horad']." and fecha='".$fecha."' and pista=".$_POST['pista'].";";
					$result=mysqli_query($sql,$conexion);
					if($result)
					{
						switch($_POST['horad'])
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
						
						$sql="select usuarios.email, usuarios.nombre from usuarios inner join pabellon on pabellon.idusuario=usuarios.id where pabellon.fecha='".$fecha."' and pabellon.pista=".$_POST['pista']." and hora=".$_POST['horad'].";";
						$result=mysqli_query($sql,$conexion);
						$row=mysqli_fetch_array($result);
						
						//mail al usuario que reserva
						$to3 = $row['email'];
						if($_POST['pista']==1){
							$subject = "Reserva Pabellón de Parderrubias (Rechazada)";
						}else{
							$subject = "Reserva pista exterior de Parderrubias (Rechazada)";
						}
						$message = '
						<table width="500" border="0" cellspacing="5" cellpadding="5">
						  <tr>
							<td width="98" bgcolor="#FFECEA"><strong>Nombre</strong></td>
							<td width="387" bgcolor="#FFECEA">'.$row['nombre'].'</td>
						  </tr>
						  <tr>
							<td bgcolor="#FFECEA"><strong>Fecha y hora reservadas</strong></td>
							<td bgcolor="#FFECEA">'.$_POST['inputField'].$horamail.'</td>
						  </tr>
						</table>
						';
						
						$headers.='MIME-Version: 1.0'."\r\n";
						 $headers.='Content-type: text/html; charset=utf-8'."\r\n";
						 $headers.='From: '.$row['email']."\r\n";
						$envio=mail($to3,$subject,$message,$headers);
						
						
						
						$mensaje="Pista rechazada correctamente";
					}else{
						$error="Error al rechazar, intentelo más tarde";	
					}
			}
		}else{
			$error="Debe estar registrado para reservar";
		}
		}else{
			$error="Escoja una pista";
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
				$result=mysqli_query($sql,$conexion);
				while ($row=mysqli_fetch_array($result))
				{
					switch($row['hora'])
					{
						case 1:
							$uno='Solicitado por '.$row['nombre'].'<br /> de 10 a 12';
							if($row['aprobado']==1){
								$clase1="verde";
							}
							if($row['aprobado']==2){
								$clase1="rojo";
							}
						break;
						case 2:
							$dos='Solicitado por '.$row['nombre'].'<br /> de 12 a 14';
							if($row['aprobado']==1){
								$clase2="verde";
							}
							if($row['aprobado']==2){
								$clase2="rojo";
							}
						break;
						case 3:
							$tres='Solicitado por '.$row['nombre'].'<br /> de 14 a 16';
							if($row['aprobado']==1){
								$clase3="verde";
							}
							if($row['aprobado']==2){
								$clase3="rojo";
							}
						break;
						case 4:
							$cuatro='Solicitado por '.$row['nombre'].'<br /> de 16 a 18';
							if($row['aprobado']==1){
								$clase4="verde";
							}
							if($row['aprobado']==2){
								$clase4="rojo";
							}
						break;
						case 5:
							$cinco='Solicitado por '.$row['nombre'].'<br /> de 18 a 20';
							if($row['aprobado']==1){
								$clase5="verde";
							}
							if($row['aprobado']==2){
								$clase5="rojo";
							}
						break;
						case 6:
							$seis='Solicitado por '.$row['nombre'].'<br /> de 20 a 22';
							if($row['aprobado']==1){
								$clase6="verde";
							}
							if($row['aprobado']==2){
								$clase6="rojo";
							}
						break;
					}
				}
		  ?>
          <div>
          	<?php if($uno!=''){ ?>
            <div class="reservado <?php echo $clase1; ?>"> <?php
            	echo $uno; 
				echo "<br />";
				?>
                <input type="button" name="hora1" value="Aprobar" class="formulario" style="width:10em; cursor:pointer; background-color:#E4FCE6;" onclick="compru(1);" /> 
                <input type="button" name="hora1d" value="Denegar" class="formulario" style="width:10em; cursor:pointer; background-color:#FFF1F0;" onclick="comprud(1);" />
                <input type="button" name="hora1b" value="Borrar" class="formulario" style="width:10em; cursor:pointer;" onclick="comprub(1);" />
                </div><?php
                
             } ?>
          </div>
          
          <div>
          <?php if($dos!=''){  ?>
            <div class="reservado <?php echo $clase2; ?>"> <?php
            	echo $dos;
				echo "<br />";
				?>
                <input type="button" name="hora2" value="Aprobar" class="formulario" style="width:10em; cursor:pointer; background-color:#E4FCE6;" onclick="compru(2);" /> 
                <input type="button" name="hora2d" value="Denegar" class="formulario" style="width:10em; cursor:pointer; background-color:#FFF1F0;" onclick="comprud(2);" />
                <input type="button" name="hora2b" value="Borrar" class="formulario" style="width:10em; cursor:pointer;" onclick="comprub(2);" />
                </div><?php
             } ?>
          </div>
          
          <div>
          <?php if($tres!=''){  ?>
            <div class="reservado <?php echo $clase3; ?>"> <?php
            	echo $tres;
				echo "<br />";
				?>
                <input type="button" name="hora3" value="Aprobar" class="formulario" style="width:10em; cursor:pointer; background-color:#E4FCE6;" onclick="compru(3);" /> 
                <input type="button" name="hora3d" value="Denegar" class="formulario" style="width:10em; cursor:pointer; background-color:#FFF1F0;" onclick="comprud(3);" />
                <input type="button" name="hora3b" value="Borrar" class="formulario" style="width:10em; cursor:pointer;" onclick="comprub(3);" />
                </div><?php
             } ?>
          </div>
          
          <div>
          <?php if($cuatro!=''){  ?>
            <div class="reservado <?php echo $clase4; ?>"> <?php
            	echo $cuatro;
				echo "<br />";
				?>
                <input type="button" name="hora4" value="Aprobar" class="formulario" style="width:10em; cursor:pointer; background-color:#E4FCE6;" onclick="compru(4);" /> 
                <input type="button" name="hora4d" value="Denegar" class="formulario" style="width:10em; cursor:pointer; background-color:#FFF1F0;" onclick="comprud(4);" />
                <input type="button" name="hora4b" value="Borrar" class="formulario" style="width:10em; cursor:pointer;" onclick="comprub(4);" />
                </div><?php
             } ?>
          </div>
          
          <div>
          <?php if($cinco!=''){  ?>
            <div class="reservado <?php echo $clase5; ?>"> <?php
            	echo $cinco;
				echo "<br />";
				?>
                <input type="button" name="hora5" value="Aprobar" class="formulario" style="width:10em; cursor:pointer; background-color:#E4FCE6;" onclick="compru(5);" /> 
                <input type="button" name="hora5d" value="Denegar" class="formulario" style="width:10em; cursor:pointer; background-color:#FFF1F0;" onclick="comprud(5);" />
                <input type="button" name="hora5b" value="Borrar" class="formulario" style="width:10em; cursor:pointer;" onclick="comprub(5);" />
                </div><?php
             } ?>
          </div>
          
          <div>
          <?php if($seis!=''){  ?>
            <div class="reservado <?php echo $clase6; ?>"> <?php
            	echo $seis;
				echo "<br />";
				?>
                <input type="button" name="hora6" value="Aprobar" class="formulario" style="width:10em; cursor:pointer; background-color:#E4FCE6;" onclick="compru(6);" /> 
                <input type="button" name="hora6d" value="Denegar" class="formulario" style="width:10em; cursor:pointer; background-color:#FFF1F0;" onclick="comprud(6);" />
                <input type="button" name="hora6b" value="Borrar" class="formulario" style="width:10em; cursor:pointer;" onclick="comprub(6);" />
                </div><?php
             } ?>
          </div>
          
          </div>
          <input type="hidden" name="hora" value="" />
          <input type="hidden" name="horad" value="" />
          <input type="hidden" name="horab" value="" />
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
		if(confirm('Va a aprobar el día '+document.pabellon.inputField.value+' de '+cuantas))
		{
			document.pabellon.submit();	
		}
		
	}
	function comprud(hora)
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
		document.pabellon.horad.value=hora;
		if(confirm('Va a rechazar el día '+document.pabellon.inputField.value+' de '+cuantas))
		{
			document.pabellon.submit();	
		}
		
	}
	function comprub(hora)
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
		document.pabellon.horab.value=hora;
		if(confirm('Va a borrar el día '+document.pabellon.inputField.value+' de '+cuantas))
		{
			document.pabellon.submit();	
		}
		
	}
  </script>
  
  
  <script>

</script>
  
</body>
</html>
<?php } ?>