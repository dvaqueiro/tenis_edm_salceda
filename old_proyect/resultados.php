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
  <meta name="keywords" content="tenis, salceda, resultados" />
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
	    <?php $m=3; include('menu.php'); ?>
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
				  $division=$rowd['id'];
			  ?>
			  		<h2 class="divisiones" style="margin-bottom:0;"><?php echo $rowd['nombre']; ?></h2>
                    <div class="resultados" style="margin-top:0; margin-bottom:2em;">
			  <?php
			  		$primero='';
					  $sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario where ud.iddivision=".$division." order by usuarios.nombre;";
						
						$result=mysqli_query($conexion, $sql);
						while ($row=mysqli_fetch_array($result))
						{
							if($primero=='')
							{
								$primero=$row['uid'];
								$primeron=$row['n'];
							}else{
								$sqlr="SELECT * FROM resultados where 
											((idu1=".$primero." or idu2=".$primero.") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");"; //echo $sqlr;
								$resultr=mysqli_query($conexion, $sqlr);
								$rowr=mysqli_fetch_array($resultr);
								
									if($rowr['id']!='')
									{
									if($primero == $rowr['idu1'])
										{
										
									?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $primeron; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j11']; ?>-<?php echo $rowr['j12']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j21']; ?>-<?php echo $rowr['j22']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j31'];} ?>-
											<?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j32'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
										}else{
											
										?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $primeron; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j12']; ?>-<?php echo $rowr['j11']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j22']; ?>-<?php echo $rowr['j21']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j32'];} ?>-
											<?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j31'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
											
										}
									}
								
							}
						} 
						
						$segundo='';
					  $sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario 
					  		where ud.iddivision=".$division." and usuarios.id<>".$primero." order by usuarios.nombre;";
						
						$result=mysqli_query($conexion, $sql);
						while ($row=mysqli_fetch_array($result))
						{
							if($segundo=='')
							{
								$segundo=$row['uid'];
								$segundon=$row['n'];
							}else{
								$sqlr="SELECT * FROM resultados where 
											((idu1=".$segundo." or idu2=".$segundo.") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");"; //echo $sqlr;
								$resultr=mysqli_query($conexion, $sqlr);
								$rowr=mysqli_fetch_array($resultr);
								
									if($rowr['id']!='')
									{
										if($segundo == $rowr['idu1'])
											{
											
										?>
											<table width="100%" border="0" cellspacing="3" cellpadding="0">
											  <tr>
												<td height="30" width="42%" align="right"><strong><?php echo $segundon; ?></strong></td>
												<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j11']; ?>-<?php echo $rowr['j12']; ?></td>
												<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j21']; ?>-<?php echo $rowr['j22']; ?></td>
												<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j31'];} ?>-
												<?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j32'];} ?></td>
												<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
											  </tr>
											</table>
								
										<?php
											}else{
												
											?>
											<table width="100%" border="0" cellspacing="3" cellpadding="0">
											  <tr>
												<td height="30" width="42%" align="right"><strong><?php echo $segundon; ?></strong></td>
												<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j12']; ?>-<?php echo $rowr['j11']; ?></td>
												<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j22']; ?>-<?php echo $rowr['j21']; ?></td>
												<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j32'];} ?>-
												<?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j31'];} ?></td>
												<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
											  </tr>
											</table>
								
										<?php
												
											}
									}
								
							}
						} 
						
						$tercero='';
					  $sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario 
					  		where ud.iddivision=".$division." and usuarios.id<>".$primero." and usuarios.id<>".$segundo." order by usuarios.nombre;";
						
						$result=mysqli_query($conexion, $sql);
						while ($row=mysqli_fetch_array($result))
						{
							if($tercero=='')
							{
								$tercero=$row['uid'];
								$terceron=$row['n'];
							}else{
								$sqlr="SELECT * FROM resultados where 
											((idu1=".$tercero." or idu2=".$tercero.") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");"; //echo $sqlr;
								$resultr=mysqli_query($conexion, $sqlr);
								$rowr=mysqli_fetch_array($resultr);
								
									if($rowr['id']!='')
									{
										
										if($tercero == $rowr['idu1'])
										{
										
									?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $terceron; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j11']; ?>-<?php echo $rowr['j12']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j21']; ?>-<?php echo $rowr['j22']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j31'];} ?>-
											<?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j32'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
										}else{
											
										?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $terceron; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j12']; ?>-<?php echo $rowr['j11']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j22']; ?>-<?php echo $rowr['j21']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j32'];} ?>-
											<?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j31'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
											
										}
									
									}
								
							}
						} 
						
						
						
						
						$cuarto='';
					  $sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario 
					  		where ud.iddivision=".$division." and usuarios.id<>".$primero." and usuarios.id<>".$segundo." and usuarios.id<>".$tercero." order by usuarios.nombre;";
						
						$result=mysqli_query($conexion, $sql);
						while ($row=mysqli_fetch_array($result))
						{
							if($cuarto=='')
							{
								$cuarto=$row['uid'];
								$cuarton=$row['n'];
							}else{
								$sqlr="SELECT * FROM resultados where 
											((idu1=".$cuarto." or idu2=".$cuarto.") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");"; //echo $sqlr;
								$resultr=mysqli_query($conexion, $sqlr);
								$rowr=mysqli_fetch_array($resultr);
								
									if($rowr['id']!='')
									{
									if($cuarto == $rowr['idu1'])
										{
										
									?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $cuarton; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j11']; ?>-<?php echo $rowr['j12']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j21']; ?>-<?php echo $rowr['j22']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j31'];} ?>-
											<?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j32'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
										}else{
											
										?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $cuarton; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j12']; ?>-<?php echo $rowr['j11']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j22']; ?>-<?php echo $rowr['j21']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j32'];} ?>-
											<?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j31'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
											
										}
									}
								
							}
						} 
						
						
						
						
						$quinto='';
					  $sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario 
					  		where ud.iddivision=".$division." and usuarios.id<>".$primero." and usuarios.id<>".$segundo." 
							and usuarios.id<>".$tercero." and usuarios.id<>".$cuarto." order by usuarios.nombre;";
						
						$result=mysqli_query($sql,$conexion);
						while ($row=mysqli_fetch_array($result))
						{
							if($quinto=='')
							{
								$quinto=$row['uid'];
								$quinton=$row['n'];
							}else{
								$sqlr="SELECT * FROM resultados where 
											((idu1=".$quinto." or idu2=".$quinto.") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");"; //echo $sqlr;
								$resultr=mysqli_query($sqlr,$conexion);
								$rowr=mysqli_fetch_array($resultr);
								
									if($rowr['id']!='')
									{
									if($quinto == $rowr['idu1'])
										{
										
									?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $quinton; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j11']; ?>-<?php echo $rowr['j12']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j21']; ?>-<?php echo $rowr['j22']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j31'];} ?>-
											<?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j32'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
										}else{
											
										?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $quinton; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j12']; ?>-<?php echo $rowr['j11']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j22']; ?>-<?php echo $rowr['j21']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j32'];} ?>-
											<?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j31'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
											
										}
									}
								
							}
						} 
						
						
						
						$sexto='';
					  $sql="SELECT usuarios.nombre as n, usuarios.id as uid FROM usuarios inner join ud on usuarios.id=ud.idusuario 
					  		where ud.iddivision=".$division." and usuarios.id<>".$primero." and usuarios.id<>".$segundo." 
							and usuarios.id<>".$tercero." and usuarios.id<>".$cuarto." and usuarios.id<>".$quinto." order by usuarios.nombre;";
						
						$result=mysqli_query($sql,$conexion);
						while ($row=mysqli_fetch_array($result))
						{
							if($sexto=='')
							{
								$sexto=$row['uid'];
								$sexton=$row['n'];
							}else{
								$sqlr="SELECT * FROM resultados where 
											((idu1=".$sexto." or idu2=".$sexto.") and (idu1=".$row['uid']." or idu2=".$row['uid'].") and division=".$division.");"; //echo $sqlr;
								$resultr=mysqli_query($conexion, $sqlr);
								$rowr=mysqli_fetch_array($resultr);
								
									if($rowr['id']!='')
									{
									if($sexto == $rowr['idu1'])
										{
										
									?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $sexton; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j11']; ?>-<?php echo $rowr['j12']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j21']; ?>-<?php echo $rowr['j22']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j31'];} ?>-
											<?php if($rowr['j31']!=0 || $rowr['j32']!=0){echo $rowr['j32'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
										}else{
											
										?>
										<table width="100%" border="0" cellspacing="3" cellpadding="0">
										  <tr>
											<td height="30" width="42%" align="right"><strong><?php echo $sexton; ?></strong></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j12']; ?>-<?php echo $rowr['j11']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php echo $rowr['j22']; ?>-<?php echo $rowr['j21']; ?></td>
											<td width="5%" align="center" style="font-weight:normal;"><?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j32'];} ?>-
											<?php if($rowr['j32']!=0 || $rowr['j31']!=0){echo $rowr['j31'];} ?></td>
											<td width="42%"><strong><?php echo $row['n']; ?></strong></td>
										  </tr>
										</table>
							
									<?php
											
										}
									}
								
							}
						} 
						
						
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
