<?php

  /** Esta é uma classe de exemplo de como usar o framework.
   * Para usar este exemplo, depois de instalado, use os endereço
   * http://dominio.com/nodes/nodes/getMobileOutput/amenity:school/-8.1313/-34.9146/-8.1047/-34.8710
   * http://dominio.com/folder/class/method/type:value;type:value;type:value/bottom/left/upper/right
   */
  class nodes extends normalize
  {
    function __construct()
    {
      parent::__construct();
      self::connect();
    }

    /** Define quais métodos são de acesso externo pelo servidor.
     *
     * @return array de string com os nomes de métodos públicos
     */
    public function getNamesOfPublicMethods(){
      return array(
        "get"
      );
    }

    /** Faz uma consulta por pontos de interesse dentro de uma área retangular.
     * http://dominio.com/nodes/nodes/ByBox/mobile/amenity:school/-8.1313/-34.9146/-8.1047/-34.8710
     * http://dominio.com/folder/class/method/output/type:value;type:value;type:value/bottom/left/upper/right
     * @return array com os dados a serem passados à saída.
     */
    public function get(){
      // Recebe todos os parâmetros passados por URL
      global $parameterGArr;
  
      // Define o formato de saída.
      if( $parameterGArr[ 0 ] == "json" ){
        $this->setOutputJSon();
      }
      else if( $parameterGArr[ 0 ] == "csv" ){
        $this->setOutputCsv();
      }
      else{
        $this->setOutputJSonMobile();
      }

      if( ( is_numeric( $parameterGArr[ 2 ] ) ) && ( is_numeric( $parameterGArr[ 3 ] ) ) && ( is_numeric( $parameterGArr[ 4 ] ) ) && ( is_numeric( $parameterGArr[ 5 ] ) ) ){
        return $this->getAll( $parameterGArr[ 1 ], ( float ) $parameterGArr[ 2 ], ( float ) $parameterGArr[ 3 ], ( float ) $parameterGArr[ 4 ], ( float ) $parameterGArr[ 5 ], $parameterGArr[ 6 ] );
      }

      $boxLArr = utilGeo::coordinatesByDistance( ( float ) $parameterGArr[ 2 ], ( float ) $parameterGArr[ 3 ], ( int ) $parameterGArr[ 4 ] );
      return $this->getAll( $parameterGArr[ 1 ], $boxLArr[ "latitudeBottom" ], $boxLArr[ "longitudeLeft" ], $boxLArr[ "latitudeUpper" ], $boxLArr[ "longitudeRight" ], $parameterGArr[ 5 ] );
    }

    public function getAll( $typeAStr, $latitudeBottomASFlt, $longitudeLeftASFlt, $latitudeUpperASFlt, $longitudeRightASFlt, $rescueAStr ){
      // Define o nome da coleção do MongoDB
      $this->setCollection( "nodes" );

      // Define as chaves desnecessárias
      $this->setGarbageCollector( array( "tags.highway", "_id", "id_node", "id_changesets", "id_user", "version", "visible" ) );
      //$this->setAppendFirst( array( "lat", "lgn", "maxSpeed" ) );

      if( !is_null( $rescueAStr ) ){
        $this->setRescueCollection( explode( ";", $rescueAStr ) );
      }

      $filterLArr = array();

      // Explode o primeiro parâmetro, type:value
      if( strlen( $typeAStr ) > 0 ){
        // Procura pelo ';', usado como separador de tipo.
        $typeAStr = explode( ";", $typeAStr );

        // Para cada tipo, separa o tipo em chave:valor
        foreach( $typeAStr as $filterDataLStr ){
          // Separa chave:valor
          $filterDataLArr = explode( ":", $filterDataLStr );
          // O MongoDB necessita que valores numéricos sejam realmente numéricos, e não string numéricas
          if( is_numeric( $filterDataLArr[ 1 ] ) )
          {
            // Monta o tipo int
            if( $filterDataLArr[ 1 ] == ( int )$filterDataLArr[ 1 ] )
            {
              $filterDataLArr[ 1 ] = ( int )$filterDataLArr[ 1 ];
            }
            // Monta o tipo double/float
            else if( $filterDataLArr[ 1 ] == ( double )$filterDataLArr[ 1 ] )
            {
              $filterDataLArr[ 1 ] = ( double )$filterDataLArr[ 1 ];
            }
          }

          // Veja a documentação de como o banco foi montado.
          // As chaves são formadas por tags => key1 => key2 => keyN => val: valor

          // Monta um $or caso a chave já exista
          if( isset( $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ] ) ){
            $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ] = array_merge( $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ], array( $filterDataLArr[ 1 ] ) );
          }
          else{
            if( is_null( $filterDataLArr[ 1 ] ) ){
              $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ] = array( array( '$exists' => true ) );
            }
            else
            {
              $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ] = array( $filterDataLArr[ 1 ] );
            }
          }
        }

        // Monta o array da busca de tags.
        $orLArr = array();
        foreach( $filterLArr as $filterKeyLStr => $filterValueLArr ){
          $orLArr[] = array( $filterKeyLStr => $filterValueLArr );
        }

        $filterLArr = array( '$or' => $orLArr );
      }

      // Monta a query $geoWithin.$box para limitar a busca
      if( ( is_numeric( $latitudeBottomASFlt ) ) && ( is_numeric( $longitudeLeftASFlt ) ) && ( is_numeric( $latitudeUpperASFlt ) ) && ( is_numeric( $longitudeRightASFlt ) ) ){
        $filterLArr = array_merge(
          $filterLArr,
          array(
            'location' => array(
              '$geoWithin' => array(
                '$box' => array(
                  array( ( float ) $latitudeBottomASFlt, ( float ) $longitudeLeftASFlt ),
                  array( ( float ) $latitudeUpperASFlt, ( float ) $longitudeRightASFlt )
                )
              )
            )
          )
        );
      }

      return $this->MongoFindToOutput( $filterLArr );
    }
  }