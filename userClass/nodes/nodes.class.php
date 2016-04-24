<?php

  class nodes extends normalize
  {
    public function getNamesOfPublicMethods(){
      return array(
        "get"
      );
    }

    public function get(){
      global $parameterVgArr;
      $this->setCollection( "nodes" );
      $this->setGarbageCollector( array( "_id", "id_node", "id_changesets", "id_user", "version", "visible" ) );

      $filterLArr = array();

      if( strlen( $parameterVgArr[ 0 ] ) > 0 ){
        $parameterVgArr[ 0 ] = explode( ";", $parameterVgArr[ 0 ] );

        foreach( $parameterVgArr[ 0 ] as $filterDataVlStr ){
          $filterDataLArr = explode( ":", $filterDataVlStr );

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

      if( ( is_numeric( $parameterVgArr[ 1 ] ) ) && ( is_numeric( $parameterVgArr[ 2 ] ) ) && ( is_numeric( $parameterVgArr[ 3 ] ) ) && ( is_numeric( $parameterVgArr[ 4 ] ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$near' => array(
                ( float ) $parameterVgArr[ 1 ], ( float ) $parameterVgArr[ 2 ]
              ),
              '$maxDistance' => ( ( float ) $parameterVgArr[ 3 ] ),
              '$minDistance' => ( ( float ) $parameterVgArr[ 4 ] )
            )
          )
        );
      }
      else if( ( is_numeric( $parameterVgArr[ 1 ] ) ) && ( is_numeric( $parameterVgArr[ 2 ] ) ) && ( is_numeric( $parameterVgArr[ 3 ] ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$near' => array(
                ( float ) $parameterVgArr[ 1 ], ( float ) $parameterVgArr[ 2 ]
              ),
              '$maxDistance' => ( ( float ) $parameterVgArr[ 3 ] )
            )
          )
        );
      }
      else if( ( is_numeric( $parameterVgArr[ 1 ] ) ) && ( is_numeric( $parameterVgArr[ 2 ] ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$near' => array(
                ( float ) $parameterVgArr[ 1 ], ( float ) $parameterVgArr[ 2 ]
              )
            )
          )
        );
      }

      return $this->MongoFindToOutput( $filterLArr );
    }
  }