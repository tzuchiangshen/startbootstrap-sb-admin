<?php 
  require "predis/autoload.php";
  //global variables
  //global variables
	date_default_timezone_set('america/santiago');
	//



  //global variables
  $TOTAL_RTCU=0;
  $RTCU_GOOD=0;
  $RTCU_BAD=0;
  $ALARM=0;

  $table = array();

  Predis\Autoloader::register();
  try {
    $redis = new Predis\Client();
    $mqtt_message = $redis->keys('2*');
    //print_r ($mqtt_message);
    foreach($mqtt_message as $k) {
      $val = $redis->get($k);
      $row = explode(",",$val);
      $mac = $row[0];
      $name = $row[1];

      if(strpos($name,'3ra Seccion') != false) {
        $timestamp = $row[4];
        $name2 = explode("/",$name);
        $row[1] = $name2[0];
        $diff = time()-$timestamp;
        //echo " diff=" . $diff . "<br>";
        if($diff < 3600) {
          $RTCU_GOOD++;
        } else {
          $RTCU_BAD++;
        }
        $table[$mac] = $row;
        //echo $val;
      }
    }

    ksort($table);

    //print_r($table);


  } catch (Exception $e) {
    die($e->getMessage());
  }





  $mac = 275749050;
  $file = "/var/www/html/sistema/Clientes/" . $mac . ".txt";
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
	//$contenido = (int) $contenido;
	//
	$caudal = 0;
	//
	$nombre = "NULL";
	$distancia = 0;
	$nivel = 0;
	//$volumen = 0;
	$temperatura1 = 0;
	$temperatura2 = 0;
	//$estatus = "NULL";
	//
	$data = explode(",", $contenido);
	//
	//$nombre = $data[0];
	$nombre = "Calle Larga";
  $distancia = $data[1];
	$nivel = $data[2];
	//$volumen = $data[4];
	$temperatura1 = $data[5];
	$temperatura2 = $data[6];
	//$estatus = $data[7];
	//
	$nivel = (float) $nivel;
	
	//
	switch ($nivel) {
	case ($nivel == 0):       
		$caudal = 0;	
        break;				
    case ($nivel > 0 and $nivel < 0.047):       
		$caudal = 0;	
        break;
    case ($nivel >= 0.047 and $nivel < 0.073):        
		$caudal = 60;	
        break;
    case ($nivel >= 0.073 and $nivel < 0.096):      
		$caudal = 120;	
        break;
    case ($nivel >= 0.096 and $nivel < 0.118):
        $caudal = 180;	
        break;	
    case ($nivel >= 0.118 and $nivel < 0.139):
		$caudal = 240;	
        break;	
    case ($nivel >= 0.139 and $nivel < 0.157):
		$caudal = 300;	
        break;	
    case ($nivel >= 0.157 and $nivel < 0.178):
		$caudal = 360;	
        break;	
    case ($nivel >= 0.178 and $nivel < 0.195):
		$caudal = 420;	
        break;	
    case ($nivel >= 0.195 and $nivel < 0.211):      
		$caudal = 480;	
        break;	
    case ($nivel >= 0.211 and $nivel < 0.227):
		$caudal = 540;	
        break;	
    case ($nivel >= 0.227 and $nivel < 0.241):
		$caudal = 600;	
        break;	
    case ($nivel >= 0.241 and $nivel < 0.255):
		$caudal = 660;	
        break;	
    case ($nivel >= 0.255 and $nivel < 0.268):
		$caudal = 720;	
        break;	
    case ($nivel >= 0.268 and $nivel < 0.281):
		$caudal = 780;	
        break;	
    case ($nivel >= 0.281 and $nivel < 0.293):
		$caudal = 840;	
        break;	
    case ($nivel >= 0.293 and $nivel < 0.305):
		$caudal = 900;	
        break;	
    case ($nivel >= 0.305 and $nivel < 0.316):
		$caudal = 960;	
        break;	
    case ($nivel >= 0.316 and $nivel < 0.327):
		$caudal = 1020;	
        break;	
    case ($nivel >= 0.327 and $nivel < 0.338):
		$caudal = 1080;	
        break;	
    case ($nivel >= 0.338 and $nivel < 0.348):
		$caudal = 1140;	
        break;	
    case ($nivel >= 0.348 and $nivel < 0.359):
		$caudal = 1200;	
        break;	
    case ($nivel >= 0.359 and $nivel < 0.369):
		$caudal = 1260;	
        break;	
    case ($nivel >= 0.369 and $nivel < 0.379):
		$caudal = 1320;	
        break;	
    case ($nivel >= 0.379 and $nivel < 0.388):
		$caudal = 1380;	
        break;	
    case ($nivel >= 0.388 and $nivel < 0.398):
		$caudal = 1440;	
        break;	
    case ($nivel >= 0.398 and $nivel < 0.407):
		$caudal = 1500;	
        break;	
    case ($nivel >= 0.407 and $nivel < 0.417):
		$caudal = 1560;	
        break;	
    case ($nivel >= 0.417 and $nivel < 0.426):
		$caudal = 1620;	
        break;	
    case ($nivel >= 0.426 and $nivel < 0.435):
		$caudal = 1680;	
        break;	
    case ($nivel >= 0.435 and $nivel < 0.443):
		$caudal = 1740;	
        break;	
    case ($nivel >= 0.443 and $nivel < 0.451):
		$caudal = 1800;	
        break;	
    case ($nivel >= 0.451 and $nivel < 0.459):
		$caudal = 1860;	
        break;
    case ($nivel >= 0.459 and $nivel < 0.467):
		$caudal = 1920;	
        break;
    case ($nivel >= 0.467 and $nivel < 0.475):
		$caudal = 1980;	
        break;	
    case ($nivel >= 0.475 and $nivel < 0.481):
		$caudal = 2040;	
        break;	
    case ($nivel >= 0.481 and $nivel < 0.488):
		$caudal = 2100;	
        break;	
    case ($nivel >= 0.488 and $nivel < 0.484):
		$caudal = 2160;	
        break;	
    case ($nivel >= 0.494 and $nivel < 0.500):
		$caudal = 2220;	
        break;	
    case ($nivel >= 0.500 and $nivel < 0.506):
		$caudal = 2280;	
        break;	
    case ($nivel >= 0.506 and $nivel < 0.512):
		$caudal = 2340;	
        break;	
    case ($nivel >= 0.512 and $nivel < 0.518):
		$caudal = 2400;	
        break;	
    case ($nivel >= 0.518 and $nivel < 0.524):
		$caudal = 2460;	
        break;	
    case ($nivel >= 0.524 and $nivel < 0.530):
		$caudal = 2520;	
        break;	
    case ($nivel >= 0.530 and $nivel < 0.536):
		$caudal = 2580;	
        break;	
    case ($nivel >= 0.536 and $nivel < 0.542):
		$caudal = 2640;	
        break;	
    case ($nivel >= 0.542 and $nivel < 0.548):
		$caudal = 2700;	
        break;	
    case ($nivel >= 0.548 and $nivel < 0.554):
		$caudal = 2760;	
        break;	
    case ($nivel >= 0.554 and $nivel < 0.560):
		$caudal = 2820;	
        break;	
    case ($nivel >= 0.560 and $nivel < 0.565):
		$caudal = 2880;	
        break;	
    case ($nivel >= 0.565 and $nivel < 0.571):
		$caudal = 2940;	
        break;	   				
    case ($nivel >= 0.571):
        $caudal = 3000;
        break;				
	}	
	//
	
?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Tercera Sección del Río Aconcagua</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">


	<link href="bootstrap-slider.min.css" rel="stylesheet">    
	<style>
	  #ex1 .slider-selection {
	    background: #BABABA;
    }
	  
	</style>
	
</head>

<body id="page-top">

  <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <a class="navbar-brand mr-1" href="index.html">Tercera Sección del Río Aconcagua</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search -->
<!--
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
        <div class="input-group-append">
          <button class="btn btn-primary" type="button">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>
-->

    <!-- Navbar -->
<!--
    <ul class="navbar-nav ml-auto ml-md-0">
      <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-bell fa-fw"></i>
          <span class="badge badge-danger">9+</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="alertsDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
      <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-envelope fa-fw"></i>
          <span class="badge badge-danger">7</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="messagesDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-user-circle fa-fw"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
          <a class="dropdown-item" href="#">Settings</a>
          <a class="dropdown-item" href="#">Activity Log</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
        </div>
      </li>
    </ul>
-->

  </nav>

  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="index.html">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-fw fa-folder"></i>
          <span>Telemetria</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <h6 class="dropdown-header">Telemetrias</h6>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tBoco.php?pass=m3tr1cpr0">Boco</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tMauco.php?pass=m3tr1cpr0">Mauco</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tRauten.php?pass=m3tr1cpr0">Rautén</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tHijuelas.php?pass=m3tr1cpr0">Hijuelas</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tSanpedro.php?pass=m3tr1cpr0">San Pedro</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tPurutun.php?pass=m3tr1cpr0">Purutún</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tMelon.php?pass=m3tr1cpr0">Melón</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tCandelaria.php?pass=m3tr1cpr0">Candelaria</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tWaddington.php?pass=m3tr1cpr0">Waddington</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tSerrano.php?pass=m3tr1cpr0">Serrano</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tOvalle.php?pass=m3tr1cpr0">Ovalle</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tCallelarga.php?pass=m3tr1cpr0">Calle Larga</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha/tOcoa.php?pass=m3tr1cpr0">Ocoa</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-fw fa-folder"></i>
          <span>Compuertas</span>
        </a>
        <div class="dropdown-menu" aria-labelledby="pagesDropdown">
          <h6 class="dropdown-header">Compuertas</h6>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cBoco.php?pass=m3tr1cpr0">Boco</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cMauco.php?pass=m3tr1cpr0">Mauco</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cRauten.php?pass=m3tr1cpr0">Rautén</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cHijuelas.php?pass=m3tr1cpr0">Hijuelas</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cSanpedro.php?pass=m3tr1cpr0">San Pedro</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cPurutun.php?pass=m3tr1cpr0">Purutún</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cMelon.php?pass=m3tr1cpr0">Melón</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cCandelaria.php?pass=m3tr1cpr0">Candelaria</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cWaddington.php?pass=m3tr1cpr0">Waddington</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cSerrano.php?pass=m3tr1cpr0">Serrano</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cOvalle.php?pass=m3tr1cpr0">Ovalle</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cCallelarga.php?pass=m3tr1cpr0">Calle Larga</a>
          <a class="dropdown-item" href="http://web02.metricpro.cl/alpha2/cOcoa.php?pass=m3tr1cpr0">Ocoa</a>
        </div>
      </li>
<!--
      <li class="nav-item">
        <a class="nav-link" href="charts.html">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Charts</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="tables.html">
          <i class="fas fa-fw fa-table"></i>
          <span>Tables</span></a>
      </li>
-->
    </ul>

    <div id="content-wrapper">

      <div class="container-fluid">

        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Dashboard</a>
          </li>
          <li class="breadcrumb-item active">Overview</li>
        </ol>

        <!-- Icon Cards-->
        <div class="row">
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-primary o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-comments"></i>
                </div>
                <div class="mr-5"><?php echo $RTCU_GOOD . " actualizados" ?></div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-warning o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-list"></i>
                </div>
                <div class="mr-5"><?php echo $RTCU_BAD . " no actualizados" ?></div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-success o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-shopping-cart"></i>
                </div>
                <div class="mr-5">-</div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-life-ring"></i>
                </div>
                <div class="mr-5">0 Alarmas</div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
        </div>

        <!-- Area Chart Example-->
<!--
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-chart-area"></i>
            Area Chart Example</div>
          <div class="card-body">
            <canvas id="myAreaChart" width="100%" height="30"></canvas>
          </div>
          <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
        </div>
-->

        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-table"></i>
            Telemetria</div>
          <div class="card-body">
            <div class="table-responsive">
              <!-- seccion noticias -->
            	<div class="row">
    		        <div class="col-sm">
				          <div class="alert alert-warning" role="alert">
					          <h5><i class="fas fa-exclamation-triangle"></i> Noticias:</h5>	
					          <!-- <p><i class="fas fa-info"></i>  el sistema funciona en modo desarrollo</p> -->
					          <p><i class="fas fa-clock"></i> Ultima recepcion de datos: <?php echo date('Y-m-d H:i:s', $TIMESTAMP); ?> </p>
				          </div>			
			          </div>
		          </div>		
              <!-- seccion nombre RTCU  -->
		          <div class="row">
    		        <div class="col-sm">				
				          <h2 class="text-center"><i class="fas fa-chart-bar"></i> Calle Larga</h2>
				          <hr class="my-4">	
				          <div class="alert alert-success" role="alert">
					          <h5><i class="fas fa-chart-pie"></i> Telemetria:</h5>	
					          <p>
					            Altura del agua en centimetros: <strong><?php echo $nivel; ?> m</strong></br>
					            Caudal en litros por segundo: <strong><?php echo $caudal; ?> l/s</strong></p>
			              <p>
				              <i class="fas fa-download"></i> Descargar el historico completo de los registros:<br> <strong><a href="http://www.metricpro.cl/alpha/tCallelargaExcel.php?clave=m3tr1cpr0" title="robleria.csv" target="_blank">clic aquí</a></strong>
			              </p>
			              <p>
			              	<i class="fas fa-download"></i> Descargar el historico personalizado de los registros:<br> <strong><a href="#" data-toggle="modal" data-target="#HistoricoPersonalizado">clic aquí</a></strong>
			              </p>
				            <p>Sensor VegaPuls 61 / RTCU</p>
					          <p>
					            <i class="fas fa-info-circle"></i> Friendly name: <strong><?php echo $nombre; ?></strong><br>
					          	<i class="fas fa-info-circle"></i> Measure distance: <strong><?php echo $distancia; ?></strong><br>
					          	<i class="fas fa-info-circle"></i> Measure level: <strong><?php echo $nivel; ?></strong><br>
					          	<i class="fas fa-info-circle"></i> Internal temperature: <strong><?php echo $temperatura1; ?></strong><br>
					          	<i class="fas fa-info-circle"></i> External temperature: <strong><?php echo $temperatura2; ?></strong><br>
				              <i class="fas fa-info-circle"></i> Valor del sensor: <strong><?php echo $contenido; ?></strong><br>
					            <i class="fas fa-info-circle"></i> RTCU : <strong><?php echo $mac; ?></strong><br> 
					          </p>
				          </div>				
			          </div>
              </div>
              <!-- -->
	
            </div>
          </div>
          <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
        </div>

      </div>
      <!-- /.container-fluid -->

      <!-- Sticky Footer -->
      <footer class="sticky-footer">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright © MetricPro 2019</span>
          </div>
        </div>
      </footer>

    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login.html">Logout</a>
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
		<form action="tCallelargaExcelDataTime.php?clave=m3tr1cpr0" method="post" target="_blank" class="form-horizontal"  role="form">
        	<fieldset>
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
		
		$('#datetimepicker7').datetimepicker('minDate', '28/03/2018 00:00');		
		
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


  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Page level plugin JavaScript-->
  <!--
  <script src="vendor/chart.js/Chart.min.js"></script>
  -->
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin.min.js"></script>

  <!-- Demo scripts for this page-->
  <!--
  <script src="js/demo/datatables-demo.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>
  -->
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>  

</body>

</html>
