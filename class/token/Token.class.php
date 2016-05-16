<?php
  
  class Token{
    /** Gera um valor aleatório para ser usado como token.
     * 
     * @param $charCounterAUInt int unsigned, Quantidade de dígitos do token. 
     * @param $outputAUInt int unsigned, forma como o token vai ser gerado. 0 - Formato humano para celular; 1 - Formato
     *        para segurança máquina/máquina; Formato banco de dados. 
     *
     * @return string
     */
    public static function make( $charCounterAUInt = 32, $outputAUInt = 0 ){
      if( $outputAUInt == 0 ){
        $charListLStr = "abcdefghijklmnopqrstuvwxyz";
      }
      else if ( $outputAUInt == 1 ){
        $charListLStr = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789()[]{}!@#$%*-_=+~^;:,";
      }
      else if ( $outputAUInt == 2 ){
        $charListLStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      }
    
      $keyLStr = "";
    
      for ( $counterLUInt = 0; $counterLUInt < $charCounterAUInt; $counterLUInt++ ){
        $keyLStr .= $charListLStr[ rand ( 0, strlen ( $charListLStr ) - 1 ) ];
      }
    
      if ( $outputAUInt == 0 ){
        $keyLStr = substr ( chunk_split ( $keyLStr, 4, " " ), 0, -1 );
      }
    
      return $keyLStr;
    }
  }