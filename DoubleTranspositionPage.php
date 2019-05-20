<?php



require_once 'login.php';
require_once 'double_transposition.php';


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
      width: 23%;
      color: white;
      padding: 14px 20px; 
      border: none;
      cursor: pointer;
      opacity: 0.9;
      font-size: 15px;
     
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
    td{
    vertical-align: top;
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
        //allow alphabets, digits, and spaces
        function validate_encryption() {
            let plain = document.forms["fEncryption"]["etext"];
             
            //accept space and alphabets 
            let reg_plain = /^[a-zA-Z0-9 ]+$/;
            let reg_marker = /^\*$/;
             
            if (!reg_marker.test(plain.value) && !reg_plain.test(plain.value)) {
                window.alert("Only space, digits and alphabets are allow for double transposition cipher encryption");
                return false;
            }
            return true;
        }
        
        //allow alphbets, digits, and stars
        function validate_decryption() {
            let cipher = document.forms["fDecryption"]["dtext"];
            let col = document.forms["fDecryption"]["dcol_key"];
            let row = document.forms["fDecryption"]["drow_key"];
             
            //accept space and alphabets 
            let reg_cipher = /^[a-zA-Z0-9*]+$/;
            let reg_marker = /^\*$/;
            let reg_input = /^[0-9,]+/;
            let reg_comma = /,[,]+/;
            let reg_begin_comma = /^,/;
            let reg_end_comma = /,$/;
             
            if (!reg_marker.test(cipher.value) && !reg_cipher.test(cipher.value)) {
                window.alert("Only star (*), digits and alphabets are allow for double transposition cipher decryption");
                return false;
            }
            if(!reg_input.test(col.value) || !reg_input.test(row.value)) {
                window.alert("Only digits and comma(,) are allow for double transposition cipher decryption key");
                return false;
            }
            
            if(reg_comma.test(col.value)  || reg_comma.test(row.value)) {
                window.alert("Can't have more than one comma(,) next to each other");
                return false;
            }
            
            if(reg_begin_comma.test(col.value)  || reg_begin_comma.test(row.value) || reg_end_comma.test(col.value)  || reg_end_comma.test(row.value)) {
                window.alert("Can't have comma(,) at the beginning of the end");
                return false;
            }
            return true;
            
            
        
        }
        </script>
    
    
    <body>
    <h2>Decryptoid</h2>
    Select cipher:
    <div class="tab">
        <button class="SubstitutionPage"> <a href="SubstitutionPage.php"  >Substitution</a></button>
        <button class="DoubleTranspositionPage active"> <a href="DoubleTranspositionPage.php" >Double Transposition</a></button>
        <button class="RC4Page"> <a href="RC4Page.php" >RC4</a></button>
        <button class="A5Page"> <a href="A5_Page.php" >A5/1</a></button>
        
    <table>
     <tr>
     <p></p>
    <th>&nbsp&nbsp&nbsp&nbsp<strong><span span style='color:rgb(0, 204, 255);'>Encryption</span></strong</th>
   <th>&nbsp&nbsp&nbsp&nbsp<strong><span span style='color:rgb(0, 204, 255);'>Decryption</span></strong</th>
   
  </tr>
      <tr>
        <td width="400">
          
        <form name="fEncryption" action="DoubleTranspositionPage.php" style="border:1px solid #ccc" method="post" enctype="multipart/form-data" onsubmit="return validate_encryption();">
              <div class="container">
                <label for="plaintext"><b>Enter a plain text:</b></label><br>
                <input type="text" name="etext" placeholder="Plaintext (put * in this field if want to use upload file else this field will be choose as encrypt file)" required>
                or
                <input type="file" name="eimage" accept=".txt" />
                     
                <br><br>
                <label for="column"><b>Enter number of Columns:</b></label><br>
                <input type="number" placeholder="Number of Column" name="ecolumn" min = "0" required>
                
    
    
                <br><br>
                
                <button type="submit" class="encryptbtn" name="encryptbtn">Encrypt</button>
             
              
               
                
              </div>
        </form>
       </td>
         <td width="400">
       
        <form name="fDecryption" action="DoubleTranspositionPage.php" style="border:1px solid #ccc"  method="post"  enctype="multipart/form-data" onsubmit="return validate_decryption();">
              <div class="container">
                <label for="ciphertext"><b>Enter a cipher text:</b></label><br>
                <input type="text" name="dtext" placeholder="Cipher text (put * in this field if want to use upload file else this field will be choose as decrypt file)" required>
                or
                <input type="file" name="dimage" accept=".txt" />
                
                <br><br>
                <label for="column"><b>Enter number of Columns:</b></label><br>
                <input type="number" placeholder="Number of Column" name="dcolumn" min = "1" required>
                
                     
                <br>
                <label for="drow_key"><b>Enter your row key:</b></label><br>
                <input type="text" placeholder="Sequence of numbers represent row key: 2,0,1" name="drow_key" required>
                
                <br>
                <label for="dcol_key"><b>Enter your column key:</b></label><br>
                <input type="text" placeholder="Sequence of numbers represent column key: 2,0,1" name="dcol_key" required>
    
    
                <br><br>
                <button type="submit" class="decryptbtn" name="decryptbtn">Decrypt</button>
               
              </div>
        </form>
       </td>
    </tr>
      
    </table>
   
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
        if( preg_match("/^\*$/", $_POST['etext']) or preg_match("/^[a-zA-Z0-9 ]+$/", $_POST['etext'])) {

            $ciper_type = "double transposition cipher encryption";
            $dt_e_text = sanitizeMySQL($conn, $_POST['etext']);
            $dt_e_column = sanitizeMySQL($conn, $_POST['ecolumn']);

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
                $dt_e_text = sanitizeMySQL($conn, file_get_contents($f_name));
            }

//!!!!!!!!!!!!!!!!!!!!!!!!! BEGIN   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $my_result_converted_array = dt_cipher_encryption($dt_e_column, $dt_e_text);

            //in the format of 1,5,2,3,4
            $dt_ciphertext_result = $my_result_converted_array[0]; //string
            $dt_row_key = $my_result_converted_array[1]; //array of number
            $dt_col_key = $my_result_converted_array[2]; //array of number

            //PRINT THIS
            //echo "ciphertext:    ".  $dt_ciphertext_result;

            $str_row = "";
            foreach ($dt_row_key as $i) {
                $str_row .= $i . ",";
            }
            //$str_row = rtrim($str_row, ',');
            $str_col = "";
            foreach ($dt_col_key as $i) {
                $str_col .= $i . ",";
            }
           // $str_col = rtrim($str_col, ',');
            echo "<br>";
            print"<lable>";
            echo "<span style='margin-left: 2%'><strong><span span style='color:rgb(0, 204, 255);'>Result</span></span></strong>";

            echo"<br>";
            print"</lable>";
            print"&nbsp&nbsp&nbsp&nbsp&nbsp<textarea name = 'eresult' row = '10' cols = '88'>";
            echo "&nbsp Cihertext: ".$dt_ciphertext_result;
            echo "&#13&#10&nbsp Row key: ".$str_row;
            echo "&#13&#10&nbsp Column key: ".$str_col;
            print"</textarea>";


//!!!!!!!!!!!!!!!!!!!!!!!!!   END   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


            //if table exist, add the content
            if ($result) {
                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$dt_e_text', '$email', NULL)";
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
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$dt_e_text', '$email', NULL)";
                $result = $conn->query($query);
                if (!$result) die("Database access failed:" . $conn->error);
            }

        }//server side encryption validate input end
        else
            echo "<script type='text/javascript'>alert('Only space, digits, and alphabets are allow for double transposition ciper encryption');</script>";




//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    }//if decrypt button is clicked
    else if (isset($_POST['decryptbtn'])) {


        //server side check
        if( preg_match("/^\*$/", $_POST['dtext']) or preg_match("/^[a-zA-Z0-9*]+$/", $_POST['dtext'])) {

            $ciper_type = "2Xtransposition cipher decryption";
            $dt_d_numofColumn = $_POST['dcolumn'];
            $dt_d_text = sanitizeMySQL($conn, $_POST['dtext']);
            $dt_d_kcolumn = sanitizeMySQL($conn, $_POST['dcol_key']);
            $dt_d_krow = sanitizeMySQL($conn, $_POST['drow_key']);

            //Upload file is selected, if upload file exist and text field is *
            if ($_FILES and $_POST['dtext'] == "*") {
                //open file
                $f_name = $_FILES['dimage']['name'];
                move_uploaded_file($_FILES['dimage']['tmp_name'], $f_name);

                if (!file_exists($f_name)){
                    echo "<script type='text/javascript'>alert('No upload file is selected');</script>";
                    die("File does not exist");
                }

                //sanitized the file content
                //replace the content with upload content
                $dt_d_text = sanitizeMySQL($conn, file_get_contents($f_name));
            }

//!!!!!!!!!!!!!!!!!!!!!!       BEGIN    !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $my_result =$dt_d_text;
            $row_ary = explode(',', $dt_d_krow);
            $col_ary = explode(',', $dt_d_kcolumn);

            //CHECK CONDITIONS BEFORE CALL CIPHER
            //Cond 1  ->  if the highest number in col key+1 is not equal to number of col: end program
            if(max($col_ary)+1 != $dt_d_numofColumn) die("<script type='text/javascript'>alert('Column number must be equal to the highest number in column key series');</script>");

            //Cond 2  ->  check if the sequence is in a series
            $flag_equal = true;
            $temp_col = $col_ary;
            sort($temp_col);
            for($i=0; $i<count($temp_col); $i++){
                if ($temp_col[$i] != $i)
                    $flag_equal = false;
            }

            $temp_row = $row_ary;
            sort($temp_row);
            for($i=0; $i<count($temp_row); $i++) {
                if ($temp_row[$i] != $i)
                    $flag_equal = false;
            }

            if(!$flag_equal) die("<script type='text/javascript'>alert('The sequence of row/column key is/are missing some element(s) in the middle');</script>");


            //call decryption function
            $my_result_str = dt_cipher_decryption($dt_d_numofColumn, $dt_d_text, $row_ary, $col_ary);
            if($my_result_str == false)  die("<script type='text/javascript'>alert('Either row or column not match with the size of the message');</script>");


            //PRINT THIS
           // echo $my_result_str;

            print"<lable>";
            echo "<span style='margin-left: 52%'><strong><span style='color:rgb(0, 204, 255);'>Result</span></span></strong>";
            echo"<br>";
            print"</lable>";

            print"<span style='margin-left: 52%' ><textarea name = 'dresult' row = '4' cols = '88'>";
            echo $my_result_str;
            print"</textarea></span>";
            echo "<br><br>";


//!!!!!!!!!!!!!!!!!!!!!!!!!!!!   END   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


            //if table exist, add the content
            if ($result) {
                //INSERT STEP
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$dt_d_text', '$email', NULL)";
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
                $query = "INSERT INTO A5Table_contents VALUES(NULL, '$ciper_type', '$dt_d_text', '$email', NULL)";
                $result = $conn->query($query);
                if (!$result) die("Database access failed:" . $conn->error);
            }

        }//server side encryption validate input end
        else
            echo "<script type='text/javascript'>alert('pppOnly star (*), digits and alphabets are allow for double transposition cipher decryption');</script>";



    }
    echo "<br><br>";
    echo<<<_logout
    <html>
    <body>
     <button style="width: 144px ; margin-left: 2%"> <a href="Logout.php" class="logout">Logout</a></button>
    </body>
    </html>
_logout;

}
else header("location: A5_authenticate1.php");
