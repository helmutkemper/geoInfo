<?php

  class Crypt
  {
    public static function encrypt ( $dataVaX )
    {
      return bin2hex ( mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $_SERVER[ "crypt_key" ], serialize ( $dataVaX ), MCRYPT_MODE_ECB, mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
    }
  
    public static function decrypt ( $dataVaX )
    {
      return unserialize ( mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $_SERVER[ "crypt_key" ], hex2bin ( $dataVaX ), MCRYPT_MODE_ECB, mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
    }

    /*
    public static function makePassword ( $userPasswordVaStr, $vauiInteractions = 10 )
    {
      $charListVlStr = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      $vlsSalt = sprintf ( '$2a$%02d$', $vauiInteractions );
      
      for ( $counterVlUInt = 0; $counterVlUInt < 22; $counterVlUInt++ )
      {
        $vlsSalt .= $charListVlStr[ rand ( 0, strlen ( $charListVlStr ) - 1 ) ];
      }
      
      return crypt ( $userPasswordVaStr, $vlsSalt );
    }

    public static function testPassword ( $userPasswordVaStr, $cryptPasswordVaStr )
    {
      if ( crypt ( $userPasswordVaStr, $cryptPasswordVaStr ) === $cryptPasswordVaStr )
      {
        return true;
      }
      
      return false;
    }
    
    public static function makeToken ( $charCounterVaUInt = 31, $humanFormatVaBoo = false )
    {
      if ( $humanFormatVaBoo == true )
      {
        $charListVlStr = "abcdefghijklmnopqrstuvwxyz";
      }
      else
      {
        $charListVlStr = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789()[]{}!@#$%*-_=+~^;:,";
      }
      
      $keyVlStr = "";
      
      for ( $counterVlUInt = 0; $counterVlUInt < $charCounterVaUInt; $counterVlUInt += 1 )
      {
        $keyVlStr .= $charListVlStr[ rand ( 0, strlen ( $charListVlStr ) - 1 ) ];
      }
      
      if ( $humanFormatVaBoo == true )
      {
        $keyVlStr = substr ( chunk_split ( $keyVlStr, 4, " " ), 0, -1 );
      }
      
      return $keyVlStr;
    }

    // 84^128=2.031181e+246
    public static function makeUserToken ( $userIdVaX = null, $charCounterVaUInt = 72 )
    {
      global $sessionVgArr;

      $userTokenVlStr = self::makeToken( $charCounterVaUInt );

      if ( is_null ( $userIdVaX ) )
      {
        $userIdVaX = $sessionVgArr[ "id" ];
      }

      if ( !is_object ( $userIdVaX ) )
      {
        $userIdVaX = new MongoId ( $userIdVaX );
      }

      //todo corrigir isto
      //MongoUtil::deleteByQuery( array ( 'idowner.$id' => $userIdVaX ), "TokenList" );
      //MongoUtil::insertData ( array ( "access_token" => self::makePassword( $userTokenVlStr ) ), "TokenList" );

      return $userTokenVlStr;
    }
    
    public static function deleteTmpPassword ( $vasAccessEmail )
    {
      global $MongoDbVgObj;

      $MongoDbVgObj->deleteQuery(
        array (
          "table" => "Tmppassword",
          "criteria" => array ( 'access_email' => $vasAccessEmail )
        )
      );
    }
    
    public static function testTmpPassword ( $vasAccessId, $userPasswordVaStr )
    {
      $vlaQuery = array ( array ( 'access_email' => $vasAccessId ), array ( 'hash' => $vasAccessId ) );

      if ( preg_match ( "/^[a-fA-F0-9]{24}$/", $vasAccessId ) )
      {
        $vlaQuery[] = array ( 'idowner.$id' => new MongoId ( $vasAccessId ) );
      }

      $vlxCursor = MongoUtil::getCursorByQuery ( array ( '$or' => $vlaQuery ), "Tmppassword" );
      $vlxCursor->sort ( array ( "_id" => -1 ) );
      $vlxCursor->limit ( 1 );
      $vlxCursor = MongoUtil::getArrayByCursor( $vlxCursor );
      
      return self::testPassword ( $userPasswordVaStr, $vlxCursor[ "password" ] );
    }
    */
  }
    