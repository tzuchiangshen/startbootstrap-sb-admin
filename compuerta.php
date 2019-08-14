<?php 
  require "predis/autoload.php";
  //global variables
  //global variables
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
  $nivel = 0;
  $x2= 0;
  $posicion = 0;

  $state  ="";
  $modo = "";
  $gate_name = "Ocoa";
  if($ALARM) {
    $state = "Alarma de Bloqueo";
  } else {
    $state = "Operativo";
  }

  $a = 700; 
  $b = 100;
  $c = $POS;
  $x = ($b*$c)/$a;


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
					          <hr class="my-4">
					          <h5><i class="fas fa-info-circle"></i> Nivel: valor del sensor: <?php echo $nivel; ?> <span class="badge badge-warning"><?php echo $x2; ?>cm</span></h6>
					          <hr class="my-4">
					          <h5><i class="fas fa-info-circle"></i> Caudal:   </span></h5>
					          <hr class="my-4">
					          <h5><i class="fas fa-arrows-alt-v"></i> Posicion: <span class="badge badge-info"> <?php echo $POS; ?> mm </span></h5>
					          <div class="progress" style="height: 20px;">				  			
					          	<div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $x; ?>%" aria-valuenow="<?php echo $posicion; ?>" aria-valuemin="0" aria-valuemax="700">
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
