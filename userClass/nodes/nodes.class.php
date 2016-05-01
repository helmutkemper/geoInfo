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

    //SET @orig_lat=-8.116137;
    //SET @orig_lon=-34.897488;
    //SET @dist=1000;
    //SELECT *,(((acos(sin((@orig_lat*pi()/180)) * sin((dest.latitude*pi()/180))+cos((@orig_lat*pi()/180))*cos((dest.latitude*pi()/180))*cos(((@orig_lon-dest.longitude)*pi()/180))))*180/pi())*60*1.1515*1609.344) as distance FROM nodes AS dest HAVING distance < @dist ORDER BY distance ASC LIMIT 100;

    /** Define quais métodos são de acesso externo pelo servidor.
     *
     * @return array de strinf com os nomes de métodos públicos
     */
    public function getNamesOfPublicMethods(){
      return array(
        "getMobileOutput",
        "info"
      );
    }

    /** Faz uma consulta por pontos de interesse dentro de uma área retangular.
     * http://dominio.com/nodes/nodes/getMobileOutput/amenity:school/-8.1313/-34.9146/-8.1047/-34.8710
     * http://dominio.com/folder/class/method/type:value;type:value;type:value/bottom/left/upper/right
     * @return array com os dados a serem passados à saída.
     */
    public function getMobileOutput(){
      // Recebe todos os parâmetros passados por URL
      global $parameterGArr;

      // Define o formato de saída.
      $this->setOutputJSonMobile();

      // Define o nome da coleção do MongoDB
      $this->setCollection( "nodes" );

      // Define as chaves desnecessárias
      $this->setGarbageCollector( array( "tags.highway", "_id", "id_node", "id_changesets", "id_user", "version", "visible" ) );
      //$this->setAppendFirst( array( "lat", "lgn", "maxSpeed" ) );
      //$this->setRescueCollection( array( "location.0", "location.1", "tags.maxspeed.val" ) );

      $filterLArr = array();

      // Explode o primeiro parâmetro, type:value
      if( strlen( $parameterGArr[ 0 ] ) > 0 ){
        // Procura pelo ';', usado como separador de tipo.
        $parameterGArr[ 0 ] = explode( ";", $parameterGArr[ 0 ] );

        // Para cada tipo, separa o tipo em chave:valor
        foreach( $parameterGArr[ 0 ] as $filterDataLStr ){
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
            $filterLArr[ "tags.{$filterDataLArr[ 0 ]}.val" ][ '$in' ] = array( $filterDataLArr[ 1 ] );
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