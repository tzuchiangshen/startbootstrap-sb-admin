<?php 
    session_start();
    $_SESSION = array();

    session_destroy(); 
    print_r($_SESSION['user_id']);
    
    header('location: http://www.metricpro.cl/tercera_seccion/login.html');
?>
