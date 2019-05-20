<?php
/**
 * User: Phu Tran
 * Date: 5/6/2019
 */


//

function dt_cipher_encryption($key, $plaintext) {
    /**
     * Using double transposition cipher to encrypt a plaintext
     * @param: $key (int) - the key that use to encrypt or decrypt
     * @param: $plaintext (string) - the message that is being encrypt or decrypt
     * @return: (array) - array[0] -> hold the (string) the ciphertext
     *                    array[1] -> hold the (array) the random order of integer of rows being shuffle
     *                    array[2] -> hold the (array) the random order of integer of columns being shuffle
     */

    // set up the necessary information
    $tmp_ary = setup_helper($key, $plaintext);
    $ary = $tmp_ary[0];
    $row = $tmp_ary[1];
    $col = $tmp_ary[2];

    // create a 2-dimension array with * as default
    $new_ary = array(array());

    // shuffle row
    $row_range = range(0, $row-1);
    shuffle($row_range);

    // swap/rearrange the row
    $counter = 0;
    foreach($row_range as $index) {
        $new_ary[$counter] = $ary[$index];
        $counter++;
    }


    // shuffle col
    $col_range = range(0, $col-1);
    shuffle($col_range);

    $final_ary = $new_ary;

    // swap/rearrange the column
    $counter = 0;
    foreach ($col_range as $index) {
        for($i=0; $i<$row; $i++) {
            $final_ary[$i][$counter] = $new_ary[$i][$index];
        }
        $counter++;
    }


    // convert 2D array back to string
    $ciphertext = "";
    for($i=0; $i<$row; $i++) {
        for($k=0; $k<$col; $k++) {
            $ciphertext .= $final_ary[$i][$k];
        }
    }

    return array($ciphertext, $row_range, $col_range);
}

//no space allow
function dt_cipher_decryption($key, $ciphertext, $row_key, $col_key) {
    /**
     * Using double transposition cipher to decrypt a ciphertext
     * @param: $key (int) - the key that use to encrypt or decrypt
     * @param: $ciphertext (string) - the message that is being encrypt or decrypt
     * @param: $row_key (array) - hold the array the random order of integer of rows that being shuffle
     * @param: $col_key (array) - hold the array the random order of integer of columns that being shuffle
     * @return: (string) - plaintext if correct
     *          (boolean) - if false
     */

    // set up the necessary information
    $tmp_ary = setup_helper($key, $ciphertext);
    $ary = $tmp_ary[0];
    $row = $tmp_ary[1];
    $col = $tmp_ary[2];

    //check if row or column key have the same number as row and column in array
    if(max($row_key)+1 != $row or max($col_key)+1 != $col)
        return false;
    //check if the size of the 2d array is the same as the key row and col
    if(count($ary) != count($row_key) or count($ary[0]) != count($col_key))
        return false;

    $final_ary = $ary;

    $r_index = 0;
    $c_index = 0;
    foreach ($row_key as $r) {
        foreach ($col_key as $c) {
            $final_ary[$r][$c] = $ary[$r_index][$c_index];
            $c_index++;
        }
        $c_index = 0;
        $r_index++;
    }

    // convert 2D array back to string
    $plaintext = "";
    for($i=0; $i<$row; $i++) {
        for($k=0; $k<$col; $k++) {
            if ($final_ary[$i][$k] == "*")
                $plaintext .= ' ';
            else
                $plaintext .= $final_ary[$i][$k];
        }
    }

    return $plaintext;

}

function setup_helper($key, $message) {
    /**
     * Help setup the 2D array for encrypting and decrypting
     * @param: $key (int) - the key that use to encrypt or decrypt
     * @param: $message (string) - the message that is being encrypt or decrypt
     * @return: (array) - array[0] -> hold the (2D array) of the message where column is based on the $key size
     *                    array[1] -> hold the (int) represent # of rows
     *                    array[2] -> hold the (int) represent # of columns
     */
    // set up the value of row and column
    $col = $key;
    $row = floor(strlen($message)/$col);

    if (strlen($message) % $col != 0)
        $row += 1;

    // create a 2-dimension array with * as default
    $ary = array_fill(0, $row, array_fill(0, $col, '*'));

    // put message inside the array
    $counter = 0;
    for ($i=0; $i<$row; $i++) {
        for($k=0; $k<$col; $k++) {
            if($counter < strlen($message) and $message[$counter] != ' ')
                $ary[$i][$k] = $message[$counter];
            $counter++;
        }
    }

    return array($ary, $row, $col);
}


//function main() {
//    $mykey = 3;
//    $plaintext = "hello world 4231";
//
//
//    $list = dt_cipher_encryption($mykey, $plaintext);
//
//    $cipher = $list[0];
//    $dkey_row = $list[1];
//    $dkey_col = $list[2];
//
//    echo 'decrypt row_key is: ';
//    foreach ($dkey_row as $i) {
//        echo $i;
//    } echo "\n";
//
//    echo 'decrypt col_key is: ';
//    foreach ($dkey_col as $i) {
//        echo $i;
//    } echo "\n";
//
//    echo "this is the cipher: $cipher\n\n";
//    echo dt_cipher_decryption($mykey, $cipher, $dkey_row, $dkey_col);
//
//}
//main();







?>