<?php


require_once 'login.php';
require_once 'RC4.php';


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

    $my_result = "";

    echo <<<_END
    
    <html>
    <style>
    
    body {font-family: Arial, Helvetica, sans-serif;}
    * {box-sizing: border-box}
    
    /* Full-width input fields */
    input[type=number], input[type=text]  {
      width: 100%;
      padding: 15px;
      margin: 5px 0 22px 0;
      display: inline-block;
      background: #f1f1f1;
    }
    
    input[type=text]:focus, input[type=password]:focus {
      background-color: #ddd;
      outline: none;
    }
    
    button:hover {
      opacity:1;
      background-color: red;
    }
    
    h3{
    color: rgb(0, 204, 255);
    }
    
    button{
      background-color:black;
      font-size: 15px;
      width: 23%;
      color: white;
      padding: 10px 20px; 
      border: none;
      cursor: pointer;
      opacity: 0.9;
    }
    
    a{
        color: white;
    }
    /* Add padding to container elements */
    .container {
      padding: 16px;
    }
    
    table {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }
    
    td, th {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
     
     
    }
    
    tr:nth-child(even) {
      background-color: #dddddd;
    }
    
    .active { 
      background-color: #3498db;
      color: white;
      text-decoration: underline;
     
    }
    a:link {
      text-decoration: none;
    }
    </style>
    
    
    <script>
    //No need for this cipher, but still put in for Fabio takes a look
    function validate_encryption() {
        let plain = document.forms["fEncryption"]["etext"];
         
        //accept space and alphabets 
       // let reg_plain = /^[a-zA-Z ]+$/;
        let reg_marker = /^\*$/;
        // if (!reg_marker.test(plain.value) && !reg_plain.test(plain.value)) {
        if (!reg_marker.test(plain.value) && !reg_plain.test(plain.value)) {
            window.alert("Only space and alphabets are allow for RC4 cipher");
            return false;
        }
        return true;
    
    }
    function validate_decryption() {
        let cipher = document.forms["fDecryption"]["dtext"];
         
        //accept space and alphabets 
        let reg_cipher = /^[a-zA-Z ]+$/;
        let reg_marker = /^\*$/;
         
        if (!reg_marker.test(cipher.value) && !reg_cipher.test(cipher.value)) {
            window.alert("Only space and alphabets are allow for Rc4 cipher");
            return false;
        }
        return true;
    
    }
    </script>
    
    
    
    <body>
    <h2>Decryptoid</h2>
    Select cipher:
    <div class="tab">
        <button class="SubstitutionPage "> <a href="SubstitutionPage.php"  >Substitution</a></button>&nbsp;
        <button class="DoubleTranspositionPage"> <a href="DoubleTranspositionPage.php" >Double Transposition</a></button>
        <button class="RC4Page active"> <a href="RC4Page.php" >RC4</a></button>
        <button class="A5Page "> <a href="A5_1Page.php" >A5/1</a></button>
    <table>
      <tr>
        <td width="400">
        <h3>&nbsp&nbsp&nbsp Encryption</h3>
<!--       <form name="fEncryption" action="RC4Page.php" style="border:1px solid #ccc" method="post" enctype="multipart/form-data" onsubmit="return validate_encryption();" >-->
            
            <form name="fEncryption" action="RC4Page.php" style="border:1px solid #ccc" method="post" enctype="multipart/form-data">
            <div class="container">
    
                <label for="plaintext"><b>Enter a plain text:</b></label><br>
                <input type="text" name="etext" placeholder="Plaintext (put * in this field if want to use upload file else this field will be choose as encrypt file)" name="plaintext" required>
                or
               <input type="file" name="eimage" accept=".txt" />
    
    
                <br><br>
                            <label for="shift"><b>Enter key string:</b></label><br>
                <input type="text" placeholder="Enter key" name="ekey" required><br><br>
                <button type="submit" class="encryptbtn" name="encryptbtn">Encrypt</button>
     
              </div>
        </form>
       </td>
         <td width="400">
        <h3>&nbsp&nbsp&nbsp Decryption</h3>
<!--    <form name="fDecryption" action="RC4Page.php" style="border:1px solid #ccc" method="post"  enctype="multipart/form-data" onsubmit="return validate_decryption();" >-->
        
        <form name="fDecryption" action="RC4Page.php" style="border:1px solid #ccc" method="post"  enctype="multipart/form-data" >
              <div class="container">
    
                <label for="ciphertext"><b>Enter a cipher text:</b></label><br>
                <input type="text" name="dtext" placeholder="Cipher text (put * in this field if want to use upload file else this field will be choose as decrypt file)" name="ciphertext" required>
                or
                <input type="file" name="dimage" accept=".txt" />
    
    
                <br><br>
                           <label for="key"><b>Enter key string:</b></label><br>
                <input type="text" placeholder="Enter key" name="dkey"required><br><br>
    
                <button type="submit" class="decryptbtn" name="decryptbtn">Decrypt</button>

              </div>
        </form>
       </td>
    </tr>
      
    </table>
   
    </div>
    
    
    </body>
    </html>
_END;

    //connect to mysql
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($conn->connect_error);

    $query = "SELECT * FROM A5Table_contents";
    $result = $conn->query($query);


    //if encryption button is clicked
    if (isset($_POST['encryptbtn'])) {

        //server side check
        if( preg_match("/^\*$/", $_POST['etext']) or preg_match("/^[a-zA-Z ]+$/", $_POST['etext'])) {

            $ciper_type = "RC4 Cipher Encryption";
            $rc4_e_text = sanitizeMySQL($conn, $_POST['etext']);
            $rc4_e_key = sanitizeMySQL($conn, $_POST['ekey']);

            //Upload file is selected, if upload file exist and text field is *
            if ($_FILES and $_POST['etext'] == "*") {
                //open file
                $f_name = $_FILES['eimage']['name'];
                move_uploaded_file($_FILES['eimage']['tmp_name'], $f_name);

                if (!file_exists($f_name)){
                    echo "<script type='text/javascript'>alert('No upload file is selected');</script>";
                    die("File does not exist");
                }

                //sanitized the file content
                //replace the content with upload content
                $rc4_e_text = sanitizeMySQL($conn, file_get_contents($f_name));
            }



            //echo $sub_e_text;
            $my_result =$rc4_e_text;

            $my_result_converted = rc4_encryption($rc4_e_key, $rc4_e_text);

//print result
            print"<lable>";
            echo "<span style='margin-left: 2%'><strong><span span style='color:rgb(0, 204, 255);'>Result</span></span></strong>";

            echo"<br>";
            print"</lable>";
            print"&nbsp&nbsp&nbsp&nbsp&nbsp<textarea name = 'eresult' row = '4' cols = '88'>";
            echo $my_result_converted;
            print"</textarea>";
            echo "<br><br>";

            if ($result) {
                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$rc4_e_text', '$email', NULL)";
                $result = $conn->query($query);
                if (!$result) die("Database access failed:" . $conn->error);
            } else { //if table not exist, create the table
                $query = "CREATE TABLE A5Table_contents(
                          id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT KEY,
                          ciper VARCHAR(32) NOT NULL,
                          content LONGTEXT NOT NULL,
                          email VARCHAR (32) NOT NULL,
                          timesp TIMESTAMP NOT NULL
                          )";

                $result = $conn->query($query);
                if (!$result){
                    echo "<script type='text/javascript'>alert('No upload file is selected');</script>";
                    die("File does not exist");
                }

                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$rc4_e_text', '$email', NULL)";
                $result = $conn->query($query);
                if (!$result) die("Database access failed:" . $conn->error);
            }

        }//server side encryptinon validate input end
        else
            echo "<script type='text/javascript'>alert('Only space and alphabets are allow for substitution cipher');</script>";


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    }//if decrypt button is clicked
    else if (isset($_POST['decryptbtn'])) {

        //server side check
//        if( preg_match("/^\*$/", $_POST['dtext']) or preg_match("/^[a-zA-Z ]+$/", $_POST['dtext'])) {

            $ciper_type = "RC4 Cipher Decryption";
//            $rc4_d_text = sanitizeMySQL($conn, $_POST['dtext']);
            $rc4_d_text = $_POST['dtext'];
            $rc4_d_key = sanitizeMySQL($conn, $_POST['dkey']);

            //Upload file is selected, if upload file exist and text field is *
            if ($_FILES and $_POST['dtext'] == "*") {
                //open file
                $f_name = $_FILES['dimage']['name'];
                move_uploaded_file($_FILES['dimage']['tmp_name'], $f_name);

                if (!file_exists($f_name)) {
                    echo "<script type='text/javascript'>alert('No upload file is selected');</script>";
                    die("File does not exist");
                }

                //sanitized the file content
                //replace the content with upload content
                $rc4_d_text = sanitizeMySQL($conn, file_get_contents($f_name));
            }



            //echo $sub_d_text;
            $my_result =$rc4_d_text;

            $my_result_converted = rc4_decryption( $rc4_d_key,$rc4_d_text);

            print"<lable>";
            echo "<span style='margin-left: 52%'><strong><span style='color:rgb(0, 204, 255);'>Result</span></span></strong>";
            echo"<br>";
            print"</lable>";

            print"<span style='margin-left: 52%' ><textarea name = 'dresult' row = '4' cols = '88'>";
            echo $my_result_converted;
            print"</textarea></span>";
            echo "<br><br>";

            //if table exist, add the content
            if ($result) {
                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$rc4_d_text', '$email', NULL)";
                $result = $conn->query($query);
                if (!$result) die("Database access failed:" . $conn->error);
            } else { //if table not exist, create the table
                $query = "CREATE TABLE A5Table_contents(
                          id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT KEY,
                          ciper VARCHAR(32) NOT NULL,
                          content LONGTEXT NOT NULL,
                          email VARCHAR (32) NOT NULL,
                          timesp TIMESTAMP NOT NULL
                          )";

                $result = $conn->query($query);
                if (!$result) die("Database access failed:" . $conn->error);

                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$rc4_d_text', '$email', NULL)";
                $result = $conn->query($query);
                if (!$result) die("Database access failed:" . $conn->error);
            }

//        }//server side decryption validate input end
//        else
//            echo "<script type='text/javascript'>alert('Only space and alphabets are allow for substitution cipher');</script>";




    }
    echo<<<_logout
    <html>
    <body>
     <button style="width: 144px ; margin-left: 2%"> <a href="Logout.php" class="logout">Logout</a></button>
    </body>
    </html>
_logout;

}
else header("location: A5_authenticate1.php");




?>