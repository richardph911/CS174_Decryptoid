<?php
/**
 * Author: Phu Tran
 * Date: 4/22/2019
 */

//login and signup buttons


echo <<<_END
        <html>
        <head>
        <style>
            p.solid {
                border-style: solid;
                border-width: 15px;
            }
        </style>
        <title>PHP Sign Up/ Login page</title></head><table>
        <p class="solid"></p>
        <h3 align="center" >Welcome to Decryptoid Website!!</h3>
        <table cellpadding="5" cellspacing="10" align="center">
        <form method="post" action="A5_authenticate3(login).php" enctype="multipart/form-data">
                <tr><td colspan="2" align="center"> <input type="submit" name="login" value="Log In" > </td></tr>
        </form>
        <form method="post" action="A5_authenticate2(signup).php" enctype="multipart/form-data">
               <tr><td  colspan="2" align="center"> <input type="submit" name="signup" value="Sign Up" >  </td></tr>
        </form>
        </table></body>
        <p class="solid"></p>
_END;



?>