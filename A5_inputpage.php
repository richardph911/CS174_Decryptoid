<?php
/**
 * Author: Phu Tran
 * Date: 4/22/2019
 */

require_once 'login.php';

function destroy_session_and_data() {
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
}

function sanitizeMySQL($connection, $var)
{
    $var = $connection->real_escape_string($var);
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var, ENT_QUOTES);
    return $var;
}


//start session
session_start();

//ini_set('session.gc_maxlifetime', 60 * 60 * 24);

if(isset($_SESSION['username'])) {

    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $email = $_SESSION['email'];

    //destroy_session_and_data();

    echo "<h3>Welcome $username!!</h3><br>";


//    require_once  'htlm_ciphers.php';

    //upload file
    echo "<br><br>";
    echo<<<_END
            <html><head><title>PHP Form Upload</title></head><body>
            <form method="post" action="A5_inputpage.php" enctype="multipart/form-data">
                    <input type="button" name="logout" value="logout"><br><br>
                    Enter File Name: <input type="text" name="textname" size="20"> <br> <br>
                    Select File: <input type="file" name="filename" size="20" accept=".txt">
                    <input type="submit" name="submit" value="Submit Answer">
                    </form></body>
_END;


    //connect to mysql
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($conn->connect_error);

    $query = "SELECT * FROM A5Table_contents";
    $result = $conn->query($query);



    if($_FILES) {
        //check to make sure both fields is entered
        if($_FILES['filename']['name'] and $_POST['textname']) {
            //sanitized text input
            $t_name = sanitizeMySQL($conn, $_POST['textname']);

            //open file
            $f_name = $_FILES['filename']['name'];
            move_uploaded_file($_FILES['filename']['tmp_name'], $f_name);

            if(!file_exists($f_name))
                die("File does not exist");
            //sanitized the file content
            $f_content = sanitizeMySQL($conn, file_get_contents($f_name));


            //if table exist, add the content
            if($result) {
                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$t_name', '$f_content', '$email')";
                $result = $conn->query($query);
                if(!$result) die("Database access failed:" . $conn->error);
            }
            else { //if table not exist, create the table
                $query = "CREATE TABLE A5Table_contents(
                              id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT KEY,
                              name VARCHAR(32) NOT NULL,
                              content LONGTEXT NOT NULL,
                              email VARCHAR (32) NOT NULL
                              )";

                $result = $conn->query($query);
                if(!$result) die("Database access failed:" . $conn->error);

                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$t_name', '$f_content', '$email')";
                $result = $conn->query($query);
                if(!$result) die("Database access failed:" . $conn->error);
            }

        }//end of the check
        else echo "Please enter in both of the fields and click Submit Answer button";
    } else echo "Please enter in both of the fields and click Submit Answer button";



    //display data from database
    echo "<br><br><br><br><br>";
    echo "Here is/are the data from the database: <br><br>";

    //reestablish connection
    $query = "SELECT * FROM A5Table_contents where email='$email'";
    $result = $conn->query($query);

    if($result) {
        $rows = $result->num_rows;

        for($i=0; $i<$rows; ++$i) {
            $result->data_seek($i);
            $each_row = $result->fetch_array(MYSQLI_ASSOC);


            echo $i+1 . ".<br>";
            echo "Name: " . $each_row['name'] . "<br>";
            echo "Content: " . $each_row['content'] . "<br><br>";
        }

        $result->close();
    }
    else echo "There is no data exist yet!";

    $conn->close();

}
else header("location: A5_authenticate1.php");

?>