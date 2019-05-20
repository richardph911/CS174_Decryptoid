<?php
/**
 * Author: Phu Tran
 * Date: 4/22/2019
 */

// if signup is press
require_once 'login.php';

function sanitizeMySQL($connection, $var)
{
    $var = $connection->real_escape_string($var);
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var, ENT_QUOTES);
    return $var;
}
if(isset($_POST['signup']) or isset($_POST['register'])) {

    echo <<<_END
    <html>
        <head>
        <script>
        function ValidateEmail() {
            email = document.forms["signup"]["email"];
            username = document.forms["signup"]["username"];
            password = document.forms["signup"]["password"];

             str = "";
             reg_email = /^\w+@[a-z]+\.(edu|com)$/;
             reg_user = /^[\w_-]+$/;
             
             if (!reg_email.test(email.value)  || !reg_user.test(username.value) || !reg_user.test(password.value)) {
                 window.alert("Either email, username, and/or password is in an incorrect format" +
                                "\\n\\nUsername/Password contains only alphabets, digit, underscore, and dash");
                 return false;
             }
                 
            return true;

        }
        </script>

        <style>
        input[type=text], input[type=password] {
          padding: 15px;
          display: inline-block;
          border-radius: 10px;
          background: white;
		}
        input[type=text]:focus, input[type=password]:focus {
          background-color: #FFFFE0;
          outline: none;
          color: blue;
        }
        button:hover {
          opacity:1;
          background-color: #FFFFE0;
        }

            p.solid {
                border-style: solid;
                border-width: 15px;
            }
        </style>
        <title>PHP Sign Up</title>
        </head>
        <table>
        <p class="solid">
        <table cellpadding="5" cellspacing="10" align="center">
       
        
        <form id="signup1" name="signup" method="post" action="A5_authenticate2(signup).php" enctype="multipart/form-data" onsubmit="return ValidateEmail();">
                <tr><td colspan="2" align="center">Enter your email </td> 
                    <td colspan="2" align="center"><input type="text" name="email" placeholder = "Enter email" required></td> 
                </tr>
                <tr>
                    <td colspan="2" align="center">Enter your username</td>
                    <td colspan="2" align="center"> <input type="text" name="username"placeholder = "Enter username" required ></td>
                </tr>
                <tr>
                    <td colspan="2" align="center">Enter your password</td>
                    <td colspan="2" align="center"> <input type="password" name="password" placeholder = "Enter password" required> </td>
                </tr>
                <tr>
                    <td colspan="2" align="center"> <input type="submit" name="register" value="Register" > </td>
                </tr>
        </form>
        </table>
        </p></body>
        <p class="solid"></p>
        </html>
_END;

    $exist_flag = false;
    $success_flag = false;

    if (isset($_POST['email']) and isset($_POST['username']) and isset($_POST['password'])) {
        if ($_POST['email'] and $_POST['username'] and $_POST['password']) {
            $conn = new mysqli($hn, $un, $pw, $db);
            if ($conn->connect_error) die($conn->connect_error);

            $sani_username = sanitizeMySQL($conn, $_POST['username']);
            $sani_email = sanitizeMySQL($conn, $_POST['email']);
            $salt = "salting";
            $sani_password = hash("ripemd128", $salt . sanitizeMySQL($conn, $_POST['password']) . $salt);

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
            } else {
                $rows = $result->num_rows;

                for ($i = 0; $i < $rows; ++$i) {
                    $result->data_seek($i);
                    $each_row = $result->fetch_array(MYSQLI_ASSOC);

                    if ($each_row['username'] == $sani_username or $each_row['email'] == $sani_email) {
                        $exist_flag = true;
                    }
                }
            }

            if (!$exist_flag) {
                //INSERT STEP
                $query = "INSERT INTO A5Table_users VALUES(NULL, '$sani_username', '$sani_password', '$sani_email')";
                $result = $conn->query($query);
                if (!$result) die("Database access failed0:" . $conn->error);
                $success_flag = true;
            }

            $conn->close();

        } else {
            echo <<<_END
            <html>
            <head>
            <p style="color:red;" align="center">One of the field(s) is/are missing </p>
            </head></html>
_END;
        }
    }

    if ($exist_flag) {
        echo <<<_END
            <html>
            <head>
            <p style="color:red;" align="center">Unsuccessfully creating new user account: either account already exist or conflict have occur <br></p>
            <p align="center"><a href=A5_authenticate3(login).php>Click here to go to Login page</a> </p>
            </head></html>
_END;
    } else if ($success_flag) {
        echo <<<_END
            <html>
            <head>
            <p style="color:red;" align="center">Successfully creating new user account<br></p>
            <p align="center"><a href=A5_authenticate3(login).php>Click here to go to Login page</a> </p>
            </head></html>
_END;
    }
}
else {
    header("location: A5_authenticate1.php");
}








?>