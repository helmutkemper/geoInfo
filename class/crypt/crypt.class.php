<?php

  class Crypt
  {
    public static function encrypt ( $dataAX )
    {
      return bin2hex ( mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $_SERVER[ "crypt_key" ], serialize ( $dataAX ), MCRYPT_MODE_ECB, mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
    }
  
    public static function decrypt ( $dataAX )
    {
      return unserialize ( mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $_SERVER[ "crypt_key" ], hex2bin ( $dataAX ), MCRYPT_MODE_ECB, mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND ) ) );
    }

    /*
    public static function makePassword ( $userPasswordAStr, $vauiInteractions = 10 )
    {
      $charListLStr = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      $vlsSalt = sprintf ( '$2a$%02d$', $vauiInteractions );
      
      for ( $counterLUInt = 0; $counterLUInt < 22; $counterLUInt++ )
      {
        $vlsSalt .= $charListLStr[ rand ( 0, strlen ( $charListLStr ) - 1 ) ];
      }
      
      return crypt ( $userPasswordAStr, $vlsSalt );
    }

    public static function testPassword ( $userPasswordAStr, $cryptPasswordAStr )
    {
      if ( crypt ( $userPasswordAStr, $cryptPasswordAStr ) === $cryptPasswordAStr )
      {
        return true;
      }
      
      return false;
    }
    
    public static function makeToken ( $charCounterAUInt = 31, $humanFormatABoo = false )
    {
      if ( $humanFormatABoo == true )
      {
        $charListLStr = "abcdefghijklmnopqrstuvwxyz";
      }
      else
      {
        $charListLStr = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789()[]{}!@#$%*-_=+~^;:,";
      }
      
      $keyLStr = "";
      
      for ( $counterLUInt = 0; $counterLUInt < $charCounterAUInt; $counterLUInt += 1 )
      {
        $keyLStr .= $charListLStr[ rand ( 0, strlen ( $charListLStr ) - 1 ) ];
      }
      
      if ( $humanFormatABoo == true )
      {
        $keyLStr = substr ( chunk_split ( $keyLStr, 4, " " ), 0, -1 );
      }
      
      return $keyLStr;
    }

    // 84^128=2.031181e+246
    public static function makeUserToken ( $userIdAX = null, $charCounterAUInt = 72 )
    {
      global $sessionGArr;

      $userTokenLStr = self::makeToken( $charCounterAUInt );

      if ( is_null ( $userIdAX ) )
      {
        $userIdAX = $sessionGArr[ "id" ];
      }

      if ( !is_object ( $userIdAX ) )
      {
        $userIdAX = new MongoId ( $userIdAX );
      }

      //todo corrigir isto
      //MongoUtil::deleteByQuery( array ( 'idowner.$id' => $userIdAX ), "TokenList" );
      //MongoUtil::insertData ( array ( "access_token" => self::makePassword( $userTokenLStr ) ), "TokenList" );

      return $userTokenLStr;
    }
    
    public static function deleteTmpPassword ( $vasAccessEmail )
    {
      global $MongoDbGObj;

      $MongoDbGObj->deleteQuery(
        array (
          "table" => "Tmppassword",
          "criteria" => array ( 'access_email' => $vasAccessEmail )
        )
      );
    }
    
    public static function testTmpPassword ( $vasAccessId, $userPasswordAStr )
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
      
      return self::testPassword ( $userPasswordAStr, $vlxCursor[ "password" ] );
    }
    */
  }
    