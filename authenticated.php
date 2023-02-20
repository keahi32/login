<?php
session_start();

include ('config.php');
// Change this to your connection info.
$con=$conn;
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['email'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('No me intentes hackear!');
    header( "Refresh:5; url=../index.php", true, 303);
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password FROM usuarios WHERE email = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['email']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        
        $email=mysqli_real_escape_string($con,$_POST['email']);
        $jamon=mysqli_real_escape_string($con,$_POST['password']);
        if (password_verify($jamon, $password)) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $md5=md5($email);
            setcookie("name", $md5, time()+3600);
            $sql = "INSERT INTO sesiones (sesion, email) VALUES ('$md5','$email')";
            if (mysqli_query($con, $sql)){

            }else{
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
            echo 'Bienvenido ' . $email . '!';
            $web=$_SERVER['SERVER_NAME'];
            header( "refresh:5; url=http://$web:80" ); 

        } else {
            // Incorrect password
            echo 'Usuario o contraseña incorrecto!';
            $web=$_SERVER['SERVER_NAME'];
            header( "refresh:5; url=http://$web:8080/index.php" ); 
            
        }
    } else {
        // Incorrect username
        echo 'Usuario o contraseña incorrecto!';
        $web=$_SERVER['SERVER_NAME'];
        header( "refresh:5; url=http://$web:8080/index.php" );
        
    }

	$stmt->close();
}
?>