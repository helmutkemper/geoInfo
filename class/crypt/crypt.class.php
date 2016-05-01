<?php

  class Crypt
  {
    /** Criptografa um Array ou String e devolve uma String em formato string hexadecimal.
     *
     * @see decrypt
     * @param $dataAX array|String com o dado a ser protegido.
     * @return string hexadecimal de char.
     */
    public static function encrypt ( $dataAX )
    {
      return bin2hex ( mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $_SERVER[ "crypt_key" ], serialize ( $dataAX ), MCRYPT_MODE_ECB, mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
    }

    /** Descriptografa uma String em formato string hexadecimal e devolve o dado original.
     *
     * @see encrypt
     * @param $dataAX String hexadecimal com o dado a ser descriptografado.
     * @return mixed Array|String com o dado original.
     */
    public static function decrypt ( $dataAX )
    {
      return unserialize ( mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $_SERVER[ "crypt_key" ], hex2bin ( $dataAX ), MCRYPT_MODE_ECB, mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
    }
  }
    