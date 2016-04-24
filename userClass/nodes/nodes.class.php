<?php

  class nodes extends normalize
  {
    function __construct()
    {
      parent::__construct();
      self::connect();
    }

    public function getNamesOfPublicMethods(){
      return array(
        "get"
      );
    }

    //http://localhost/nodes/nodes/get/amenity:school/-8.1313/-34.9146/-8.1047/-34.8710
    //http://localhost/nodes/nodes/get/type:value/bottom/left/upper/right
    public function get(){
      global $parameterGArr;
      $this->setOutputJSonMobile();
      $this->setCollection( "nodes" );
      $this->setGarbageCollector( array( "tags.highway", "_id", "id_node", "id_changesets", "id_user", "version", "visible" ) );
      //$this->setAppendFirst( array( "lat", "lgn", "maxSpeed" ) );
      //$this->setRescueCollection( array( "location.0", "location.1", "tags.maxspeed.val" ) );

      $filterLArr = array();

      if( strlen( $parameterGArr[ 0 ] ) > 0 ){
        $parameterGArr[ 0 ] = explode( ";", $parameterGArr[ 0 ] );

        foreach( $parameterGArr[ 0 ] as $filterDataLStr ){
          $filterDataLArr = explode( ":", $filterDataLStr );

          if( is_numeric( $filterDataLArr[ 1 ] ) )
          {
            if( $filterDataLArr[ 1 ] == ( int )$filterDataLArr[ 1 ] )
            {
              $filterDataLArr[ 1 ] = ( int )$filterDataLArr[ 1 ];
            }
            else if( $filterDataLArr[ 1 ] == ( double )$filterDataLArr[ 1 ] )
            {
              $filterDataLArr[ 1 ] = ( double )$filterDataLArr[ 1 ];
            }
          }

          if( isset( $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ] ) ){
            $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ] = array_merge( $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ], array( $filterDataLArr[ 1 ] ) );
          }
          else{
            $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ] = array( $filterDataLArr[ 1 ] );
          }
        }

        $orLArr = array();
        foreach( $filterLArr as $filterKeyLStr => $filterValueLArr ){
          $orLArr[] = array( $filterKeyLStr => $filterValueLArr );
        }

        $filterLArr = array( '$or' => $orLArr );
      }


      if( ( is_numeric( $parameterGArr[ 1 ] ) ) && ( is_numeric( $parameterGArr[ 2 ] ) ) && ( is_numeric( $parameterGArr[ 3 ] ) ) && ( is_numeric( $parameterGArr[ 4 ] ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$geoWithin' => array(
                '$box' => array(
                  array( ( float ) $parameterGArr[ 1 ], ( float ) $parameterGArr[ 2 ] ),
                  array( ( float ) $parameterGArr[ 3 ], ( float ) $parameterGArr[ 4 ] )
                )
              )
            )
          )
        );
      }

      return $this->MongoFindToOutput( $filterLArr );
    }
  }