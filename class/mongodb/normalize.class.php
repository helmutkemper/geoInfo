<?php

  class normalize extends db{
    function MongoFindToOutput( $toFindAArr ){
      global $pageLimitVgUInt;
      global $pageOffsetVgUInt;
      global $pageTotalVgObj;
      global $pagePreviousQueryVgStr;

      $pagePreviousQueryVgStr = $toFindAArr;

      $passLBoo = false;

      if( isset( $_REQUEST[ "query" ] ) ){
        $dataQueryLArr = Crypt::decrypt( $_REQUEST[ "query" ] );

        if( isset( $dataQueryLArr[ "cy" ] ) ){
          $cursorLObj = db::$collectionCObj->find( $dataQueryLArr[ "cy" ] );
          $pageTotalVgObj = $cursorLObj->count();
          $cursorLObj->skip( $dataQueryLArr[ "offset" ] );
          $cursorLObj->limit( $dataQueryLArr[ "limit" ] );

          $passLBoo = true;
        }
      }

      if( $passLBoo == false ) {
        $cursorLObj = db::$collectionCObj->find( $toFindAArr );
        $pageTotalVgObj = $cursorLObj->count();
        $cursorLObj->skip( $pageOffsetVgUInt );
        $cursorLObj->limit( $pageLimitVgUInt );
      }

      $returnLArr = array();
      foreach( $cursorLObj as $lineLArr ){

        if( is_array( db::$garbageCollectorCObj ) ){
          foreach( db::$garbageCollectorCObj as $garbageVlStr ){
            unset( $lineLArr[ $garbageVlStr ] );
          }
        }

        $returnLArr[] = $lineLArr;
      }

      return $returnLArr;
    }
  }