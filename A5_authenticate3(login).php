<?php
/**
 * Author: Phu Tran
 * Date: 4/22/2019
 */

require_once 'login.php';

function sanitizeMySQL($connection, $var)
{
    $var = $connection->real_escape_string($var);
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var, ENT_QUOTES);
    return $var;
}

session_start();

if (isset($_SESSION['check']))
    if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] .$_SERVER['HTTP_USER_AGENT'])) {
        header("location: A5_authenticate1.php");
        exit;
    }

//login page
//set up user
$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);

$query = "SELECT * FROM A5Table_users";
$result = $conn->query($query);

//if table not exist: create table
if (!$result) {
    $query = "CREATE TABLE A5Table_users(
                                  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT KEY,
                                  username VARCHAR(32) NOT NULL UNIQUE ,
                                  password VARCHAR (32) NOT NULL,
                                  email VARCHAR(32) NOT NULL
                                  )";

    $result = $conn->query($query);
    if (!$result) die("Database access failed:" . $conn->error);
}


if(isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW'])) {
    $sani_username = sanitizeMySQL($conn, $_SERVER['PHP_AUTH_USER']);
    $salt = "salting";
    $sani_password = hash("ripemd128", $salt . sanitizeMySQL($conn, $_SERVER['PHP_AUTH_PW']) . $salt);

    //look for exact username
    $query = "SELECT * FROM A5Table_users WHERE username='$sani_username'";
    $result = $conn->query($query);

    if (!$result) die("Database access failed:" . $conn->error);
    elseif($result->num_rows) {
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();

        //$row[2] = password
        if($sani_password == $row[2]) {

            $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] .$_SERVER['HTTP_USER_AGENT']);
            $_SESSION['username'] = $sani_username;
            $_SESSION['password'] = $sani_password;
            $_SESSION['email'] = $row[3];
            echo "You are now log in...<br>";
            die("<p><a href=A5_inputpage.php>Click here to continue</a>");
        }
        else die("Invalid username/password combination   <p><a href=A5_authenticate1.php>Click here to go back</a></p>");
    }
    else die("Invalid username/password combination   <p><a href=A5_authenticate1.php>Click here to go back</a></p>");
    $conn->close();
}
else {
    header("WWW-Authenticate: Basic realm='Restricted Section");
    header("HTTP/1.0 401 Unauthorized");
    die("Please enter your username and password");
}

?>