<?php
session_start();

//print_r($_SESSION['user_id']);
    
if( isset( $_SESSION['user_id'])) {
} else {
  header("Location: http://www.metricpro.cl/tercera_seccion/login.html");
}


//if ($_GET["pass"] <> "m3tr1cpr0") {
//	header("Location: http://www.metricpro.cl");
//}

 
	//
	date_default_timezone_set('america/santiago');
	//

  $gate_name = "";
  $debug = false;
 
  if (isset($_GET["gate_name"])) {
    $gate_name = $_GET["gate_name"];
  }

  if (isset($_GET["debug"])) {
    $debug = $_GET["debug"];
  }
  $yaml_file = yaml_parse_file("metricpro_configuration.yaml");
  if($debug) {
    //var_dump($yaml_file);
  }
  $conf = $yaml_file["Telemetrias"][$gate_name];
  $mac = $conf["mac"];
  $company = $conf["company"];
  $sql_table = $conf["table"];

  $sensor_name = $conf["sensors"][0]["AI1"]["name"];
  $sensor_location = (float)$conf["sensors"][0]["AI1"]["location"];
  $sensor_offset = (float)$conf["sensors"][0]["AI1"]["offset"];

  if($debug) {
    echo "<br>sensor name:";
    echo $sensor_name;
    echo "<br>sql_table:";
    echo $sql_table;
    echo "<br>sensor_offset:";
    echo $sensor_offset;
    $query = "SELECT nombre, data, altura, caudal, fecha FROM metricpro.`" . $sql_table . "` ORDER BY fecha DESC LIMIT 65536";
    echo "<br>sql_query=";
    echo $query;
    //echo "<br>configuration:";
    //print_r($conf);
    //echo "<br>sensor X :";
    //var_dump($conf["sensors"][0]["AI1"]["X"]);
  }


  //$mac = 275749033;
  $file = "/var/www/html/sistema/Clientes/" . $mac . ".txt";
  //echo "file=" .$file;
	$fp = fopen($file, "r");
	$contenido = fread($fp, filesize($file));
	fclose($fp);	
	//
	$ultima_modificacion_fecha = date("d/m/Y", filemtime($file));
	$ultima_modificacion_hora = date("H:i", filemtime($file));
	//
	$fecha1 = new DateTime(date("Y-m-d H:i:s", filemtime($file)));
	$fecha2 = new DateTime(date("Y-m-d H:i:s"));	
	$fecha = $fecha1->diff($fecha2);		
	//
	$raw = (int) $contenido;
    $contenido = $contenido + $sensor_offset;
    if($debug) {
        echo  "<br>sensor raw: ";
        echo  $raw;
        echo  ", sensor offset: ";
        echo  $sensor_offset;
        echo  ", value: ";
        echo  $contenido;
    }

	//$contenido = 128;
	$salida = 0;
	$altura = 0;
	$caudal = 0;


  $url = "http://web02.metricpro.cl:8889/get_sensor_height_interpolation?mac=" . $mac . "&x=" . $contenido;
  $client = curl_init($url);
  curl_setopt($client,CURLOPT_RETURNTRANSFER,true);
  $response = curl_exec($client);
  $interpolation = json_decode($response, true);
  $interpolated_heigh = $interpolation["result"]["y"];

  if($debug) {
      echo  "<br>URL:" . $url;
      echo  "<br>Configuration API response:<br>";
      var_dump($response);
      echo "<br>measured height y=" . $interpolated_heigh;
  }



//	//"24458013" : {"name":"Hijuelas/3ra Seccion","ADC_offset": [0,0,0,0], "distance_coeff":[194.555. -0.651, 0.000480],"flow_coeff":[-86.58, 1575, 5145], "table": "telemetria_hijuelas", "name2": "Hijuelas", "idname": "idhijuelas"},
//	//contenido => RTCU count
//
//	//convert to height 
//  //y = ax^2 + bx + c
//  $a = (float)$conf["distance_coeff"][2];
//  $b = (float)$conf["distance_coeff"][1];
//  $c = (float)$conf["distance_coeff"][0]; 
//  $a = -1.69e-9;
//  $b = 0.00129;
//  $c = - 0.5047;
//  $d = 118.4;
//  if($debug) {
//    echo "<br>Count to Height: Curve fitting parameters";
//    echo "a=". $a . " ";
//    echo "b=". $b . " ";
//    echo "c=". $c . " ";
//    echo "d=". $d . " ";
//
//    echo "x=". $contenido;
//  }
//
//	//$salida = 0.000480*pow($contenido, 2) - 0.651*$contenido + 194.555;
//	//$salida = $a*pow($contenido, 2) + $b*$contenido + $c;
//	$salida = $a*pow($contenido, 3) + $b*pow($contenido,2) + $c*$contenido + $d;
//  if ($debug) {
//    echo "<br>altura medida=". $salida;
//  }

  //we must substract the location of the sensor to the measured height to obtain the height of the water
//  $salida = $sensor_location - $salida;

  if($sensor_name == "VegaPuls61") {
    $salida = $sensor_location - $interpolated_heigh;
  } else if($sensor_name == "VegaSon61") {
    $salida = $interpolated_heigh;
  } else {
    $salida = $interpolated_heigh;
  }

  if ($debug) {
    echo "<br>altura del sensor=". $sensor_location;
    echo "<br>altura del agua=". $salida;
  }
	if($salida < 0.0) {
	    $salida = 0.0;
	}
	$altura = $salida;
 
  $altura = round($altura,3);
  $altura_in_meter = $salida / 100;

  // check the coeficient at the repo data_acquisition/src/curve_fitting/tercera_seccion/terceraseccion_mauco.ipynb
	//convert to flow
  //y = ax^2 + bx + c
  //$aa = (float)$conf["flow_coeff"][2];
  //$bb = (float)$conf["flow_coeff"][1];
  //$cc = (float)$conf["flow_coeff"][0]; 
  //if($debug) {
  //  echo "<br>Height to Flow: Curve fitting parameters";
  //  echo "a=". $aa . " ";
  //  echo "b=". $bb . " ";
  //  echo "c=". $cc . " ";
  //  echo "x=". $altura_in_meter;
  //}
	//
	//$caudal = $aa*pow($altura_in_meter, 2) + $bb*$altura_in_meter + $cc;

  $url = "http://web02.metricpro.cl:8889/get_sensor_flow_interpolation?mac=" . $mac . "&x=" . $altura_in_meter;
  $client = curl_init($url);
  curl_setopt($client,CURLOPT_RETURNTRANSFER,true);
  $response = curl_exec($client);
  $interpolation = json_decode($response, true);
  $interpolated_flow = $interpolation["result"]["y"];

  if($debug) {
      echo  "<br>URL:" . $url;
      echo  "<br>Configuration API response:<br>";
      var_dump($response);
      echo "<br>calculated flow y=" . $interpolated_flow;
  }



  $caudal = round($interpolated_flow, 2);
  if ($debug) {
    echo "<br>caudal=". $caudal;
  }
  //$caudal = 153.2;
	if($caudal < 0.0) {
	    $caudal = 0.0;
	}
	if($altura_in_meter  <= 0.0) {
	    $caudal = 0.0;
	}


	//
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<link rel="stylesheet" href="css/bootstrap.min.css">  
	  

    <script src="js/jquery-3.3.1.min.js" ></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/moment-with-locales.min.js"></script>  
	<script src="js/tempusdominus-bootstrap-4.min.js"></script>
	
	<link rel="stylesheet" href="css/tempusdominus-bootstrap-4.min.css" />	  
	  
	
	<link rel="stylesheet" href="css/all.css">    
	<script src="js/all.js"></script>    
	  
	<title>MetricPro</title>
  </head>
  <body>
	<nav class="navbar navbar-dark bg-dark">
  		<!-- Navbar content -->
		<a class="navbar-brand" href="index.php">MetricPRO</a>				
	</nav>	
	<div class="container">
  	<!-- Content here -->
		<div class="row">
    		<div class="col-sm">
				<div class="text-center">
					<h1><i class="fas fa-clipboard"></i> PANEL DE CONTROL <i class="fas fa-clipboard"></i></h1>
				</div>	
			</div>
		</div>	
		
		<div class="row">
    		<div class="col-sm">
				<div class="alert alert-info" role="alert">
					<h5><i class="fas fa-briefcase"></i> Cuenta:</h5>	
				  	<p><strong>Cliente:</strong> <?php echo $company; ?></br>
					<strong>Estado:</strong> Activa</br>
					</p>
	  
					<h5><i class="fas fa-address-card"></i> Perfil:</h5>	
  					<p><strong>Usuario:</strong> Demo</br>
  					<strong>Perfil:</strong> Administrador</p>		  	  
				</div>			
			</div>
		</div>	

		<div class="row">
    		<div class="col-sm">
				<div class="alert alert-warning" role="alert">
					<h5><i class="fas fa-exclamation-triangle"></i> Noticias:</h5>	
					<p><i class="fas fa-info-circle"></i> Actualmente el sistema funciona en modo desarrollo</p>
					<p><i class="fas fa-clock"></i> Ultima recepcion de datos: <i class="fas fa-calendar text-warning"></i> <?php echo $ultima_modificacion_fecha; ?> <i class="fas fa-clock text-warning"></i> <?php echo $ultima_modificacion_hora; ?>  hrs.<br>Hace <?php printf('%d años, %d meses, %d días, %d horas, %d minutos %d segundos', $fecha->y, $fecha->m, $fecha->d, $fecha->h, $fecha->i, $fecha->s); ?></p>					
				</div>			
			</div>
		</div>		

		<div class="row">
    		<div class="col-sm">				
				<h2 class="text-center"><i class="fas fa-chart-bar"></i> <?php echo $gate_name; ?></h2>
				<hr class="my-4">	
				
				<div class="alert alert-success" role="alert">
					<h5><i class="fas fa-chart-pie"></i> Telemetria:</h5>	
					<p>
					Altura del agua en centimetros: <strong><?php echo $altura; ?> cm</strong></br>
					Caudal en litros por segundo: <strong><?php echo $caudal; ?> L/s</strong></p>
			
			
						<p>
				<i class="fas fa-download"></i> Descargar el historico completo de los registros:<br> <strong><?php echo '<a href="tTelemetriaExcel.php?clave=m3tr1cpr0&table=' . $sql_table . '" title="bocatoma.csv" target="_blank">clic aquí</a>'; ?></strong>
			</p>
			<p>
				<i class="fas fa-download"></i> Descargar el historico personalizado de los registros:<br> <strong><a href="tTelemetriaExcelDateTime.php" data-toggle="modal" data-target="#HistoricoPersonalizado">clic aquí</a></strong>
			</p>
			
					<p>
						<i class="fas fa-info-circle"></i> Ultima lectura del sensor: <strong><?php echo $contenido; ?> cuentas</strong><br>
						<i class="fas fa-info-circle"></i> Ubicacion del sensor c/r al suelo: <strong><?php echo $sensor_location; ?> cm</strong><br>
						<i class="fas fa-info-circle"></i> <?php echo "Sensor: " . $sensor_name; ?><br>
					        <i class="fas fa-info-circle"></i> RTCU : <strong><?php echo $mac; ?></strong><br> 
					  				   
					</p>
				</div>				
				
			</div>
		</div>
<!--		
		<div class="row">
    		<div class="col-sm">
				<div class="alert alert-secondary" role="alert">
					<h5><i class="fas fa-tasks"></i> Menu telemetrias:</h5>					
					<p><i class="fas fa-info"></i> Seleccione la telemetria que desee observar</p>					

					
<div class="dropdown">
  <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Seleccion de telemetria
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
   <a class="dropdown-item" href="http://www.metricpro.cl/alpha/tElcajon.php?pass=m3tr1cpr0">El Cajon</a>    
	<a class="dropdown-item" href="http://www.metricpro.cl/alpha/tLluilliu.php?pass=m3tr1cpr0">Lluilliu</a>
	<a class="dropdown-item" href="http://www.metricpro.cl/alpha/tOjosbuenos.php?pass=m3tr1cpr0">Ojos Buenos</a>
	<a class="dropdown-item" href="http://www.metricpro.cl/alpha/tSantacruz.php?pass=m3tr1cpr0">Santa Cruz</a>
</div>
</div>					
-->
										
					

				</div>			
			</div>
		</div>			
	
		</div>			
	</div>  


<!-- Modal -->
<div class="modal fade" id="HistoricoPersonalizado" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-download"></i> Historico personalizado de registros</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  	<p>Seleccione el rango de tiempo que desea consultar.</p>
		<form action="tTelemetriaExcelDateTime.php?clave=m3tr1cpr0" method="post" target="_blank" class="form-horizontal"  role="form">
        	<fieldset>
            
            <input name="table" type="hidden" required="required" id="table" <?php echo 'value="' . $sql_table . '"'; ?></input>
            <div class="form-group col-6">
              <label for="dtp_input1" class="control-label"><i class="fas fa-calendar-alt"></i> Desde:</label>

				
           <div class="input-group date form_date" id="datetimepicker7" data-target-input="nearest">
                <input name="fecha1" type="text" required="required" class="form-control datetimepicker-input" id="fecha1" data-target="#datetimepicker7"/>
                <div class="input-group-append" data-target="#datetimepicker7" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
              </div>
            </div>
 	
				
            </div>
			<div class="form-group col-6">
              <label for="dtp_input2" class="control-label"><i class="fas fa-calendar-alt"></i> Hasta:</label>

				
           <div class="input-group date form_date" id="datetimepicker8" data-target-input="nearest">
                <input name="fecha2" type="text" required="required" class="form-control datetimepicker-input" id="fecha2" data-target="#datetimepicker8"/>
                <div class="input-group-append" data-target="#datetimepicker8" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
              </div>
            </div>
       
    
            </div>			  								
        	</fieldset>
    	 
      </div>
		

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		<input class="btn btn-dark" name="Descargar" type="submit" id="Descargar" title="Descargar" value="Descargar" >
      </div>
		  </form> 
    </div>
  </div>
</div>

<script type="text/javascript">
    $(function () {								
		
		$('#datetimepicker7').datetimepicker({
            locale: 'es'
        });
		
		$('#datetimepicker7').datetimepicker('minDate', '09/10/2018 00:00');		
		
		$('#datetimepicker8').datetimepicker({
            locale: 'es'
        });

        $("#datetimepicker7").on("change.datetimepicker", function (e) {
            $('#datetimepicker8').datetimepicker('minDate', e.date);
        });
        $("#datetimepicker8").on("change.datetimepicker", function (e) {
            $('#datetimepicker7').datetimepicker('maxDate', e.date);
        });	
		
    });
	
	
</script>

</body>
</html>
