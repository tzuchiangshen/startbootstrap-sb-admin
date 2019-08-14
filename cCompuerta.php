<?php

//	if ($_GET["pass"] <> "m3tr1cpr0") {
//		header("Location: http://www.metricpro.cl");
//	}

    session_start();
    //print_r($_SESSION['user_id']);

    if( isset( $_SESSION['user_id'])) { 
    } else { 
      header("Location: http://www.metricpro.cl/tercera_seccion/login.html");
    } 

	//global variables
  //$mac = "372515192";
	$gate_name = "";
  $mqtt_name = "";

  if (isset($_GET["gate_name"])) {
    $gate_name = $_GET["gate_name"];
    $mqtt_name = "METRICPRO/Compuertas/" . $gate_name . "/3ra Seccion/gatePosition1";
  }

  date_default_timezone_set('america/santiago');
	$MAC  = 0;
	$MODE = 0;
	$POS  = 0;
	$ALARM = 0;
	$TEMPERATURE = 0;
	$VOLTAGE = 0;
	$AI1 = 0;
	$AI2 = 0;
	$AI3 = 0;
	$AI4 = 0;
	$TIMESTAMP =0;

	$state  ="";
	$modo = "";
  $nivel = 0;
  $posicion = 0;
  $x2 = 0;
  $mover = 0;
  $actual = 0;
  $vueltas = -1;

	//reach here when the form to move was sent 
	//tshen: replaced it by sending a command using MQTT
	//
	if (isset($_POST["actual"])) {
    //echo "actual=" . $_POST["actual"] . "<br>"; 
		$actual = $_POST["actual"];
	}

	if (isset($_POST["vueltas"])) {
    //sending messages to move the gateway
    //echo "vueltas=" . $_POST["vueltas"] . "<br>";
		$vueltas = $_POST["vueltas"];
	}	  

 
  
  //REDIS 
	require "predis/autoload.php";
	Predis\Autoloader::register();
  try {
    $redis = new Predis\Client();

    // This connection is for a remote server
    /*
            $redis = new PredisClient(array(
                "scheme" => "tcp",
                "host" => "153.202.124.2",
                "port" => 6379
            ));
     */
		//$mqtt_message = $redis->get("METRICPRO/Compuertas/Rauten Admision/3ra Seccion/gatePosition1");
		$mqtt_message = $redis->get($mqtt_name);
		
		//echo ($mqtt_message);
		$info = explode(";",$mqtt_message);
		//print_r($info);

		$MAC  = $info[1];
		$MODE = $info[3];
		$POS  = $info[5];
		$ALARM = $info[6];
		$TEMPERATURE = $info[11];
		$VOLTAGE = $info[12];
		$AI1 = $info[16];
		$AI2 = $info[17];
		$AI3 = $info[18];
		$AI4 = $info[19];

		if($MODE) {
			$modo = "Local";
		} else {
			$modo = "Automatico";
		}

		if($ALARM) {
			$state = "Alarma de Bloqueo";
		} else {
			$state = "Operativo";
		}

		$a = 700; 
		$b = 100;
		$c = $POS;
		$x = ($b*$c)/$a;

		//echo "MAC=".$MAC . "<br>";
		//echo "MODE=".$MODE . "<br>";
		//echo "POS=".$POS . "<br>";
		//echo "ALARM=".$ALARM . "<br>";
		//echo "TEMPERATURE=".$TEMPERATURE . "<br>";
		//echo "VOLTAGE=".$VOLTAGE . "<br>";
		//echo "AI1=".$AI1 . "<br>";
		//echo "AI2=".$AI2 . "<br>";
		//echo "AI3=".$AI3 . "<br>";
		//echo "AI4=".$AI4 . "<br>";

		$mqtt_message = $redis->get($MAC);
		//echo($mqtt_message);
		$info = explode(",", $mqtt_message);
		$TIMESTAMP = $info[4];

		//echo "TIMESTAMP=".$TIMESTAMP . "<br>";
  } catch (Exception $e) {
    die($e->getMessage());
  }

  //MQTT callbacks 
  use Mosquitto\Client;
  //$MAC = $MAC;
  $mid = 0;
  $c = new Mosquitto\Client("PHP");
  //$c->onLog('var_dump');
  $c->onConnect(function() use ($c, &$mid, $MAC, $vueltas) {
    //echo "onConnect....";
    $topic  = "METRICPRO/V1/RTCU/" . $MAC . "/C/1/targetPos";
    //echo "topic=" .$topic; 
    $val = $vueltas;
    $mid = $c->publish($topic, $val, 2);
  });
  
  $c->onPublish(function($publishedId) use ($c, $mid) {
    //echo "onPublish ...." . $publishedId;
    if ($publishedId == $mid) {
        $c->disconnect();
    }
    $c->exitLoop();
  });

  if($vueltas >=0) {
    $c->connect("localhost", 11883);
    $c->loopForever();
    //echo "Finished";
  } 

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.4/css/all.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">  
    
	  <link href="bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">  
	  <link href="bootstrap-slider.min.css" rel="stylesheet">    
	  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	
	  <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous">
    </script>
	  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>  
	  
	  <style>
	    #ex1 .slider-selection {
	      background: #BABABA;
      }
	  </style>
	  
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
				  	<p><strong>Cliente:</strong> 3ra sección</br>
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
					<!-- <p><i class="fas fa-info"></i>  el sistema funciona en modo desarrollo</p> -->
					<p><i class="fas fa-clock"></i> Ultima recepcion de datos: <?php echo date('Y-m-d H:i:s', $TIMESTAMP); ?> </p>
				</div>			
			</div>
		</div>		

		<div class="row">
    	<div class="col-sm">
		    <h2 class="text-center"><i class="fas fa-sliders-h"></i> <?php echo $gate_name; ?> </h2>
		    <hr class="my-4">
			  <div id="accordion" role="tablist">
		 		  <div class="card">
				    <div class="card-header bg-dark" role="tab" id="headingTwo">
			  		  <h5 class="mb-0 text-center">
					      <a class="collapsed text-white text-center" data-toggle="collapse" href="#collapseTwo" role="button" aria-expanded="false" aria-controls="collapseTwo">
			  			    <i class="fas fa-cog"></i> COMPUERTA 
					      </a>
					    </h5>
				    </div>
				    <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion">
				      <div class="card-body">
				        <h5><i class="fas fa-wrench"></i> Estado: <span class="badge badge-warning"> <?php echo $state; ?> </span></h5>
					      <h5><i class="fas fa-toggle-on"></i> Modo: <span class="badge badge-info"> <?php echo $modo; ?> </span></h5>
					      <h5><i class="fas fa-signal"></i> MAC: <span class="badge badge-info"> <?php echo $MAC; ?> </span></h5>
					      <h5><i class="fas fa-battery-full"></i> Bateria: <span class="badge badge-info"> <?php echo $VOLTAGE/10.0; ?>v </span></h5>	
<!--
					      <hr class="my-4">
					      <h5><i class="fas fa-info-circle"></i> Nivel: valor del sensor: <?php echo $nivel; ?> <span class="badge badge-warning"><?php echo $x2; ?>cm</span></h6>
					      <hr class="my-4">
					      <h5><i class="fas fa-info-circle"></i> Caudal:   </span></h5>
					      <hr class="my-4">
-->
					      <h5><i class="fas fa-arrows-alt-v"></i> Posicion: <span class="badge badge-info"> <?php echo $POS; ?> mm </span></h5>
					      <div class="progress" style="height: 20px;">				  			
						    <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $x; ?>%" aria-valuenow="<?php echo $posicion; ?>" aria-valuemin="0" aria-valuemax="700">
						  </div>
					  </div> 						
					 <?php 	if ($mover <> 0) {
		echo '
		
<div class="alert alert-danger role="alert">
					<h6>La compuerta esta ejecutando un movimiento, debe esperar a que termine antes de poder moverla nuevamente</h6>					  		  	  
				</div>			
		';
	} else {
	
	echo '						<button class="btn btn-dark btn-sm btn-block" type="button" data-toggle="modal" data-target="#exampleModalCenter2">
								<i class="fas fa-plus"></i> Mover
							</button>';
} 
						  ?>

				  </div>
				</div>
			</div>
		</div>				
	</div>
				
				
			</div>
		



	<div class="row">			
    	<div class="col-sm">
		<hr class="my-4">
		<div class="text-center">
			<a class="btn btn-dark" href="http://www.metricpro.cl/tercera_seccion" role="button"><i class="fas fa-home"></i> VOLVER</a>
		</div>
		<hr class="my-4">
	</div>			
	</div>		


</div>			
</div>  


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle2" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-arrows-alt-v"></i> Mover</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		  
		  <p>Por favor seleccione la nueva posicion de la compuerta. El valor esta expredado en milimetros, Esta compuerta tiene un rango de movmiento desde 0 a 700 milimetros</p>

		  <h5><i class="fas fa-info-circle"></i> Posicion actual: <span class="badge badge-info"> <?php echo $POS; ?> mm </span></h5>	
		  <p>
		  <form method="post" target="_self">
			  <i class="fas fa-minus-square" onclick='document.getElementById("vueltas").value = Number(document.getElementById("vueltas").value)-1;'></i> 
			  <input name="vueltas" type="number" required="required" id="vueltas" max="700" min="0" step="1" title="vueltas" value="<?php echo $POS; ?>">
			  <i class="fas fa-plus-square" onclick='document.getElementById("vueltas").value = Number(document.getElementById("vueltas").value)+1;'></i>
		  
		  </p>
		  	<p>
		  <input type="submit">
		  </p>
		  <input name="actual" type="hidden" id="actual" value="<?php echo $posicion; ?>">
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

      </div>
    </div>
  </div>
</div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous">
    </script>
  </body>
</html>
