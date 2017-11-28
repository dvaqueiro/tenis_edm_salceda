<?php

class Ranking
{

	private $jugador;
	
	function pondatos($id, $nombre)
	{
		$this->jugador[$id]['nombre']=$nombre;
		$this->jugador[$id]['id']=$id;
	}
	
	function puntos($jugador, $puntos, $victorias)
	{
		$this->jugador[$jugador]['puntos']+=$puntos;
                $this->jugador[$jugador]['victorias']+=$victorias;
	}
	
	function puntosdivision($jugador, $puntospordiv)
	{
		$this->jugador[$jugador]['puntos']+=$puntospordiv;
	}
	
	function pontodo()
	{		
		foreach ($this->jugador as $val)
 			$tmp[] = $val['puntos'];
			
		array_multisort($tmp, SORT_DESC, $this->jugador);
		
		$i = 1;

		foreach($this->jugador as $ju)
		{
			if($ju['puntos']!=''){
                                echo "<div class='cadaclasi'><div class='nombres'>".$i." - ".$ju['nombre']. "</div><div class='puntos'>".$ju['puntos']."</div> &nbsp;&nbsp; <span style='font-weight:normal;'>".$ju['victorias']." victorias</span></div>";
				$i++;
			}
		}
	}
		
}


class Clasificacion
{
	
	private $divisiones;
	
	function pondivision($cual)
	{
		$this->divisiones[$cual]=$cual;
	}
	
	function verdivision($cual)
	{
		echo $this->divisiones[$cual];
	}
	
	function puntuacion($jugador, $puntos, $divi, $ds, $dj, $pj)
	{
		$this->divisiones[$divi][$jugador]['puntos']+=$puntos;
		$this->divisiones[$divi][$jugador]['ds']+=$ds;
		$this->divisiones[$divi][$jugador]['dj']+=$dj;
		$this->divisiones[$divi][$jugador]['pj']+=$pj;
	}
	
	function quepuntos($jugador, $divi)
	{
		return $this->divisiones[$divi][$jugador]['puntos'];	
	}
	
	function nombres($id, $nombre, $divi)
	{
		$this->divisiones[$divi][$id]['nombre']=$nombre;
                $this->divisiones[$divi][$id]['puntos']=0;
                $this->divisiones[$divi][$id]['ds']=0;
		$this->divisiones[$divi][$id]['dj']=0;
		$this->divisiones[$divi][$id]['pj']=0;
	}
	
	function pontodo($divi)
	{
		$this->aasort($this->divisiones[$divi]);
		foreach($this->divisiones[$divi] as $di)
		{
			if($di['puntos']!='')
			{
				$puntos=$di['puntos'];
			}else{
				$puntos=0;
			}
			if($di['ds']!='')
			{
				$ds=$di['ds'];
			}else{
				$ds=0;
			}
			if($di['dj']!='')
			{
				$dj=$di['dj'];
			}else{
				$dj=0;
			}
			if($di['pj']!='')
			{
				$pj=$di['pj'];
			}else{
				$pj=0;
			}
			echo "<div class='cadaclasi'><div class='nombres'>".$di['nombre']. "</div><div class='puntos'>".$puntos."</div><div class='puntos'>".$pj."</div><div class='puntos'>".$ds."</div><div class='puntos'>".$dj."</div></div>";
		}
	}
	
	
	
	
	
	function aasort (&$array) {
		/*$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		arsort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		$array=$ret;*/
		/*foreach ($array as $key => $row) {
			$aux[$key] = $row['dj'];
		}
		array_multisort($aux, SORT_DESC, $array);*/
		
		/*foreach ($array as $key => $row) {
			$aux[$key] = $row['ds'];
		}
		array_multisort($aux, SORT_ASC, $array);*/
		
		/*foreach ($array as $key => $row) {
			$auxj[$key] = $row['dj'];
			$auxs[$key] = $row['ds'];
			$aux[$key] = $row['puntos'];
		}*/
		//array_multisort($auxj, SORT_DESC, $array);
		//array_multisort($auxs, SORT_DESC, $array);
		//array_multisort($aux, SORT_DESC, $array);
		
		//Create index rows
		/*echo "<pre>";
		var_dump($array);
		echo "<pre>";*/
            

		foreach ($array as $row) {
		  foreach ($row as $key => $value){
			${$key}[]  = $value; //Creates $volume, $edition, $name and $type arrays.
		  }

		}

		
		//ex: sort by edition asc, then by name DESC:
		//echo print_r($puntos);
		array_multisort($puntos, SORT_DESC, $ds, SORT_DESC, $dj, SORT_DESC, $array);
	}
	
}


?>