<?php

class CaesarCipher {

    public $offset;
    public $response;
    private static $instance;

    public function __construct() {
        $this->response = array();
    }

    // Método singleton
    public static function CaesarSngltn() {
        if (!isset(self::$instance)) {
            $CaesarCipher = __CLASS__;
            self::$instance = new $CaesarCipher;
        }
        return self::$instance;
    }
    
    // Evita que el objeto se pueda clonar
    public function __clone() {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }
    
    /* *************************************************************************************** */

    function encode($aString, $offset) {
        return str_replace(" ", "$", $this->cipher($aString, $offset));
    }

    function cipher($aString, $offset) {
        return $this->cipherString(strtoupper($aString), $offset);
    }

    private function cipherString($aString, $offset) {
        $ciphered = "";
        for ($l = 0; $l < strlen($aString); $l++) {
            $ciphered .= $this->cipherLetter($aString[$l], $offset);
        }
        return $ciphered;
    }

    private function cipherLetter($aLetter, $offset) {
        if (!$this->isAlphabeticLetter($aLetter)) {
            return $aLetter;
        }
        return $this->cipherAlphabeticLetter($aLetter, $offset);
    }

    private function isAlphabeticLetter($aLetter) {
        $ascii = ord($aLetter);
        return ($ascii >= ord('A') && $ascii <= ord('Z'));
    }

    private function cipherAlphabeticLetter($aLetter, $offset) {
        $originalAscii = ord($aLetter);
        $alphabetSize = ord('Z') - ord('A') + 1;
        $encodedAscii = $originalAscii + $offset;
        $offsetFromA = ($alphabetSize + $encodedAscii - ord('A')) % $alphabetSize;
        return chr(ord('A') + $offsetFromA);
    }

    public function decode($aString, $offset) {
        return str_replace("$", " ", $this->cipher($aString, -$offset));
    }

}
