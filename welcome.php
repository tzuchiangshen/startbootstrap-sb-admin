<?php 

print_r($_POST);
print_r($_SERVER);

session_start();

/* move this to something more secure!!! */
$email = $_POST["email"];
$pass = $_POST["password"];

echo "email:" . $email . "<br>";
echo "pass:" . $pass;


if(strcmp($email, 'tercera_seccion@gmail.com') == 0)  {
    if(strcmp($pass, 't3rc3r9') == 0) {
        $_SESSION['user_id'] = $email;
        header('location: http://www.metricpro.cl/tercera_seccion/index.php');
    }
} else {
    header('location: http://www.metricpro.cl/tercera_seccion/login.html');
}


?>


