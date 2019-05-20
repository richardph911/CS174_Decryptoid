<?php
/**
 * Created by PhpStorm.
 * User: andyt
 * Date: 5/7/2019
 * Time: 8:21 PM
 */

//global constant variable
define('X_SIZE', 19);
define('Y_SIZE', 22);
define('Z_SIZE', 23);

$x_register = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1);
$y_register = array(1,1,0,0,1,1,0,0,1,1,0,0,1,1,0,0,1,1,0,0,1,1);
$z_register = array(1,1,1,0,0,0,0,1,1,1,1,0,0,0,0,1,1,1,1,0,0,0,0);


function majority($x, $y, $z) {
    /**
     * get the majority of x, y, and z values
     * @param: $x - (int) value of either 1 or 0
     * @param: $y - (int) value of either 1 or 0
     * @param: $z - (int) value of either 1 or 0
     * @return: (int) if >= 2 one, return 1
     *                if >= 2 zero, return 0
     */
    if (($x + $y + $z) > 1)
        return 1;
    else
        return 0;
}

function produce_keystream($input_size) {
    /**
     * Create the key stream based on the register x, y, and z
     * @param: $input_size - (int) size of the plaintext in binary
     * @return: (array of number) result keystream
     */
    $keystream = array();
    $x_tmp_reg = $GLOBALS['x_register'];
    $y_tmp_reg = $GLOBALS['y_register'];
    $z_tmp_reg = $GLOBALS['z_register'];


    for($i=0; $i<$input_size; $i++) {
        //get majority at the red spot x[8], y[10], z[10]
        $maj = majority($x_tmp_reg[8], $y_tmp_reg[10], $z_tmp_reg[10]);


        //shift if x[8] is with the majority
        if ($x_tmp_reg[8] == $maj) {
            // xor position 13, 16, 17, and 18 of x to get the value of first index 0
            $x_shift_value = $x_tmp_reg[13] ^ $x_tmp_reg[16] ^ $x_tmp_reg[17] ^ $x_tmp_reg[18];

            //shift everything to the right except for x_tmp_reg[0]
            $x_2_tmp_reg = $x_tmp_reg;
            for ($k=1; $k<X_SIZE; $k++)
                $x_tmp_reg[$k] = $x_2_tmp_reg[$k-1];

            //set first position = to new push value
            $x_tmp_reg[0] = $x_shift_value;
        }

        //shift if y[10] is with the majority
        if ($y_tmp_reg[10] == $maj) {
            // xor position 20 and 21 of y to get the value of first index 0
            $y_shift_value = $y_tmp_reg[20] ^ $y_tmp_reg[21];

            //shift everything to the right except for y_tmp_reg[0]
            $y_2_tmp_reg = $y_tmp_reg;
            for ($k=1; $k<Y_SIZE; $k++)
                $y_tmp_reg[$k] = $y_2_tmp_reg[$k-1];

            //set first position = to new push value
            $y_tmp_reg[0] = $y_shift_value;
        }

        //shift if z[10] is with the majority
        if ($z_tmp_reg[10] == $maj) {
            // xor position 7, 20, 21 and 22 of z to get the value of first index 0
            $z_shift_value = $z_tmp_reg[7] ^ $z_tmp_reg[20] ^ $z_tmp_reg[21] ^ $z_tmp_reg[22];

            //shift everything to the right except for z_tmp_reg[0]
            $z_2_tmp_reg = $z_tmp_reg;
            for ($k=1; $k<Z_SIZE; $k++)
                $z_tmp_reg[$k] = $z_2_tmp_reg[$k-1];

            //set first position = to new push value
            $z_tmp_reg[0] = $z_shift_value;
        }

        //after shift, get the last element of x, y, z and xor them
        $keystream[] = $x_tmp_reg[X_SIZE-1] ^ $y_tmp_reg[Y_SIZE-1] ^$z_tmp_reg[Z_SIZE-1];
    }

    return $keystream;





}

function a5_1_encryption($plaintext) {
    /**
     * Use A5/1 to encrypt plaintext
     * @param: $plaintext - (string) data in letter
     * @return: (string) - a sequence of binary that represent ciphertext
     */



    // encryption
    //convert $plaintext into binary
    $bin_plaintext = "";
    for ($i=0; $i<strlen($plaintext); $i++) {
        $bin = decbin(ord($plaintext[$i]));
        $bin = (string)$bin;

        while (strlen($bin) < 8)
            $bin = '0' . $bin;
            $bin_plaintext .= $bin;
    }

    //put it back into an array of int
    $bin_ary = array();
    for ($i=0; $i<strlen($bin_plaintext); $i++) {
        $bin_ary[] = (integer)$bin_plaintext[$i];
    }

    // produce a keystream
    $keystream = produce_keystream(sizeof($bin_ary));

    $ciphertext = '';
    // xor the keystream with the plaintext
    for($i=0; $i<sizeof($bin_ary); $i++) {
        $ciphertext .= (string)($bin_ary[$i] ^ $keystream[$i]);
    }
    

    return $ciphertext;
}
function a5_1_decryption($ciphertext) {
    /**
     * Use A5_1 to decrypt ciphertext back into plaintext
     * @param: $ciphertext - (str) a sequence of 0 and 1
     * @return: (str) plaintext in readable letter
     */
    $bin_plaintext = '';
    $keystream = produce_keystream(strlen($ciphertext));
    $bin_ary = array();

    for ($i=0; $i<strlen($ciphertext); $i++) {
        $bin = (integer)$ciphertext[$i];
        $bin_plaintext .= (string)($bin ^ $keystream[$i]);
    }

    $plaintext = '';
    $remain_len = strlen($bin_plaintext) - 8;
    $i = 0;
    while ($i <= $remain_len) {
        $plaintext .= chr(bindec(substr($bin_plaintext, $i, 8)));
        $i += 8;
    }
    return $plaintext;

}

//
////1010101010101010101110011001100110011001111100001111000011110000
//$cipher = a5_1_encryption("hello #world");
//echo $cipher . "\n";
//echo a5_1_decryption($cipher);





?>