<?php

  class nodes extends normalize
  {
    function __construct()
    {
      self::connect();
    }

    public function getNamesOfPublicMethods(){
      return array(
        "get"
      );
    }

    public function get(){
      global $parameterGArr;
      $this->setOutputCsv();
      $this->setCollection( "nodes" );
      //$this->setGarbageCollector( array( "tags.highway", "_id", "id_node", "id_changesets", "id_user", "version", "visible" ) );
      $this->setAppendFirst( array( "lat", "lgn", "maxSpeed" ) );
      $this->setRescueCollection( array( "location.0", "location.1", "tags.maxspeed.val" ) );

      $filterLArr = array();

      if( strlen( $parameterGArr[ 0 ] ) > 0 ){
        $parameterGArr[ 0 ] = explode( ";", $parameterGArr[ 0 ] );

        foreach( $parameterGArr[ 0 ] as $filterDataLStr ){
          $filterDataLArr = explode( ":", $filterDataLStr );

          if( $filterDataLArr[ 1 ] == ( int ) $filterDataLArr[ 1 ] ){
            $filterDataLArr[ 1 ] = ( int ) $filterDataLArr[ 1 ];
          }
          else if( $filterDataLArr[ 1 ] == ( double ) $filterDataLArr[ 1 ] ){
            $filterDataLArr[ 1 ] = ( double ) $filterDataLArr[ 1 ];
          }

          if( isset( $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ] ) ){
            $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ] = array_merge( $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ], array( $filterDataLArr[ 1 ] ) );
          }
          else{
            $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ] = array( $filterDataLArr[ 1 ] );
          }
        }
      }

      if( ( is_numeric( $parameterGArr[ 1 ] ) ) && ( is_numeric( $parameterGArr[ 2 ] ) ) && ( is_numeric( $parameterGArr[ 3 ] ) ) && ( is_numeric( $parameterGArr[ 4 ] ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$near' => array(
                ( float ) $parameterGArr[ 1 ], ( float ) $parameterGArr[ 2 ]
              ),
              '$maxDistance' => ( ( float ) $parameterGArr[ 3 ] ),
              '$minDistance' => ( ( float ) $parameterGArr[ 4 ] )
            )
          )
        );
      }
      else if( ( is_numeric( $parameterGArr[ 1 ] ) ) && ( is_numeric( $parameterGArr[ 2 ] ) ) && ( is_numeric( $parameterGArr[ 3 ] ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$near' => array(
                ( float ) $parameterGArr[ 1 ], ( float ) $parameterGArr[ 2 ]
              ),
              '$maxDistance' => ( ( float ) $parameterGArr[ 3 ] )
            )
          )
        );
      }
      else if( ( is_numeric( $parameterGArr[ 1 ] ) ) && ( is_numeric( $parameterGArr[ 2 ] ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$near' => array(
                ( float ) $parameterGArr[ 1 ], ( float ) $parameterGArr[ 2 ]
              )
            )
          )
        );
      }

      return $this->MongoFindToOutput( $filterLArr );
    }
  }