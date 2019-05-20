<?php
/**
 * Created by PhpStorm.
 * User: andyt
 * Date: 5/7/2019
 * Time: 4:43 PM
 */

//global constant variable
define('N_BITS', 256);

function rc4_encryption($key_str, $input_text ) {
    /**
     * Use RC4 cipher to encrypt and decrypt
     * if plaintext is pass in as arg along with key, ciphertext is produced
     * if ciphertext is pass in as arg along with the same key as encrypt, plaintext is produced
     * @param: $key_str (string): the key to encrypt or decrypt
     * @param: $input_text (string): the messages to be encrypt or decrypt into plaintext/ciphertext
     * @return: (string) plaintext or ciphertext
     */
    $key_ary = array();
    $input_ary = array();

    // convert each char of key to int
    for ( $i = 0; $i < strlen($key_str); $i++ ) {
        $key_ary[] = ord($key_str{$i});
    }

    // convert each char of input to int
    for ( $i = 0; $i < strlen($input_text); $i++ ) {
        $input_ary[] = ord($input_text{$i});
    }


    // inintialize an array from zero to 255
    $S = range(0, N_BITS-1);

    //////////KSA///////
    $len = strlen($key_str);
    $i = $j = 0;
    for($index = 0; $index < N_BITS; $index++ ){
        $j = ($key_ary[$i] + $S[$index] + $j) % N_BITS;

        // swap
        $tmp = $S[$index];
        $S[$index] = $S[$j];
        $S[$j] = $tmp;

        $i = ($i + 1) % $len;
    }

    /////////PRGA - encryption//////
    $len = strlen($input_text);
    $i = 0;
    $j = 0;
    for ($index = 0; $index < $len; $index++) {
        $i = ($i + 1) % N_BITS;
        $j = ($S[$i] + $j) % N_BITS;

        //swap
        $tmp = $S[$i];
        $S[$i] = $S[$j];
        $S[$j] = $tmp;

        $input_ary[$index] ^= $S[($S[$i] + $S[$j]) % N_BITS];
    }

    // convert output back to a string
    $result_text = "";
    for ( $i = 0; $i < $len; $i++ ) {
        $result_text .= chr($input_ary[$i]);
    }
    return $result_text;
}

function rc4_decryption($key_str, $input_text ) {
    return rc4_encryption($key_str, $input_text);
}
//
//$t = rc4_encryption("Secret", "Attack at dawn");
//echo $t . "\n";
//echo  rc4_decryption('Secret', $t);


?>