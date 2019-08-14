<?php 
    session_start();
    $_SESSION = array();

    session_destroy(); 
    header('location: http://www.metricpro.cl/tercera_seccion/login.html');
?>
