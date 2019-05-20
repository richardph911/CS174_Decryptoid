      <?php

      function sub_cipher_encryption($plaintext, $shift) {
          /**
           * Use simple substitution cipher to encrypt
           * @param: $plaintext - (str)
           * @param: $shift - (int) number of shifting
           * @return (str) ciphertext
           */
          while($shift > 27)
              $shift -= 27;

          $plaintext = strtoupper($plaintext);
          $tmp_letter = range('A', 'Z');
          $tmp_letter[] = ' ';
          $letter = implode('', $tmp_letter);

          $ciphertext = "";

          for($i = 0; $i < strlen($plaintext); ++$i){
              $pos = strpos($letter, $plaintext[$i]) + $shift;
              if($pos >'Z')
                  $pos = $pos % 27;
              $ciphertext .= $letter[$pos];
          }
          return $ciphertext;
      }

      function sub_cipher_decryption($ciphertext, $shift) {
          /**
           * Use simple substitution to decrypt
           * @param: $ciphertext - (str)
           * @param: $shift - (int) number of shift key
           * @return: (str) plaintext
           */
          while($shift > 27)
              $shift -= 27;

          $ciphertext = strtoupper($ciphertext);
          $tmp_letter = range('A', 'Z');
          $tmp_letter[] = ' ';
          $letter = implode('', $tmp_letter);

          $plaintext = "";
          for($i = 0; $i < strlen($ciphertext); ++$i){
              $pos = strpos($letter, $ciphertext[$i]) - $shift;
              if($pos < 'A')
                  $pos = $pos + 27;
              $plaintext .= $letter[$pos];

          }
          return $plaintext;
      }

//      $a = sub_cipher_encryption("Adkfjlkjie", 1);
//      echo  $a . "\n";
//      echo sub_cipher_decryption($a, 1);

?>