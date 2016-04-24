<?php

  class util{
    public static function getInArray( $pathAStr, $dataRefAArr ){
      $dataRefAArr = &$dataRefAArr;
      $pathListLArr = explode(".",$pathAStr);

      foreach($pathListLArr as $keyIdLUInt => $keyValueLStr)
      {
        $dataRefAArr = $dataRefAArr[ $keyValueLStr ];
      }

      return $dataRefAArr;
    }

    public static function garbageCollector( $garbageListAStr, &$dataRefAArr ){
      $garbageListLArr = explode(".",$garbageListAStr);
      $countLUInt = count( $garbageListLArr ) - 1;
      foreach($garbageListLArr as $keyIdLUInt => $keyValueLStr)
      {
        if( $countLUInt == $keyIdLUInt ){
          unset( $dataRefAArr[ $keyValueLStr ] );
        }
        else{
          $dataRefAArr = &$dataRefAArr[ $keyValueLStr ];
        }
      }
    }
  }