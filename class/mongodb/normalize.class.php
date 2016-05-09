<?php

  class normalize extends db{
    protected $garbageCollectorCArr;
    protected $rescueCollectionCArr;

    protected $appendFirstCArr;
    protected $appendLastCArr;

    public function __construct()
    {
      parent::__construct();
      $this->garbageCollectorCArr = null;
    }

    protected function setPageOffset( $valueAUInt ){
      global $pageOffsetGUInt;

      $pageOffsetGUInt = $valueAUInt;
    }

    protected function setPageNextLink( $valueAUInt ){
      global $pageNextLinkGStr;

      $pageNextLinkGStr = $valueAUInt;
    }

    protected function setPageLimit( $valueAUInt ){
      global $pageLimitGUInt;

      $pageLimitGUInt = $valueAUInt;
    }

    protected function setPageTotal( $valueAUInt ){
      global $pageTotalGObj;

      $pageTotalGObj = ceil( $valueAUInt );
    }

    protected function getPageTotal(){
      global $pageTotalGObj;

      return $pageTotalGObj;
    }

    protected function debugEnable( $enableABoo ){
      global $debugEnableGBoo;

      $debugEnableGBoo = $enableABoo;
    }

    protected function debug( &$dataAX ){
      global $debugEnableGBoo;
      global $debugDataGArr;

      if( $debugEnableGBoo == false ){
        return;
      }

      $debugDataGArr[] = $dataAX;
    }

    protected function setError( $errorAStr ){
      global $errorGArr;

      $errorGArr[] = $errorAStr;
    }

    protected function setProcessEnd(){
      global $processEndGBoo;

      $processEndGBoo = true;
    }

    protected function getProcessEnd(){
      global $processEndGBoo;

      return $processEndGBoo;
    }

    /** Define a saída em formato json com cabeçalho complementar para paginação. [ padrão ]
     * @code
      {
        "meta":
        {
          "limit":unsigned int,
          "next":"string URL or null",
          "offset":unsigned int,
          "previous":string URL or null,
          "total_count":unsigned int,
          "success":boolean,
          "action":array of string,
          "error":array of string
        },
        "objects":
          array de objetos.
      }
     * @endcode
     */
    protected function setOutputJSonMobile(){
      // Arquiva o formato de saída.
      global $outputTypeGStr;

      $outputTypeGStr = "json_mobile";
    }

    /** Define a saída como um simples json, sem formatação de dados.
     * Este formato é muito útil para se trabalhar com classes javascript de terceiros. Perceba porém que não são
     * passados dados complementares.
     */
    protected function setOutputJSon(){
      // Arquiva o formato de saída.
      global $outputTypeGStr;

      $outputTypeGStr = "json";
    }

    /** Define a saída como sendo um arquivo separado por vírgula, um formato mais fácil de ser importado para alguns
     * dispositivos IoT.
     */
    protected function setOutputCsv(){
      // Arquiva o formato de saída.
      global $outputTypeGStr;

      $outputTypeGStr = "csv";
    }

    /** Executa uma query e compatibiliza a saída com o framework.
     *
     * @param $toFindAArr array com a query do MongoDB
     * @return array no formato especificado.
     * @see setOutputJSonMobile
     * @see setOutputJSon
     * @see setOutputCsv
     */
    protected function MongoFindToOutput( $toFindAArr ){
      // Limite passado ao MongoDB
      global $pageLimitGUInt;

      // Offset passado ao MongoDB
      global $pageOffsetGUInt;

      // Total de dados encontrados
      global $pageTotalGObj;

      // Query anterior passada via query string, criptografada.
      global $pagePreviousQueryGStr;

      // Prepara a query atual para ser criptografada
      $pagePreviousQueryGStr = $toFindAArr;

      // Testa se há uma query anterior
      $passLBoo = false;

      if( isset( $_REQUEST[ "query" ] ) ){
        $dataQueryLArr = Crypt::decrypt( $_REQUEST[ "query" ] );

        if( isset( $dataQueryLArr[ "cy" ] ) ){

          $pageOffsetGUInt += $dataQueryLArr[ "offset" ];
          $pagePreviousQueryGStr = $dataQueryLArr[ "cy" ];

          $cursorLObj = $this->collectionCObj->find( $dataQueryLArr[ "cy" ] );
          $pageTotalGObj = $cursorLObj->count();
          $cursorLObj->skip( $dataQueryLArr[ "offset" ] );
          $cursorLObj->limit( $dataQueryLArr[ "limit" ] );

          $passLBoo = true;
        }
      }

      // Caso não haja, monta a query atual
      if( $passLBoo == false ) {
        $cursorLObj = $this->collectionCObj->find( $toFindAArr );
        $pageTotalGObj = $cursorLObj->count();
        $cursorLObj->skip( $pageOffsetGUInt );
        $cursorLObj->limit( $pageLimitGUInt );
      }

      // Monta o dado a ser enviado para a saída.
      $returnLArr = array();
      foreach( $cursorLObj as $lineLArr ){

        // Verifica se há dados a serem recuperados
        if( is_array( $this->rescueCollectionCArr ) ){
          $returnLineLArr = array();
          foreach( $this->rescueCollectionCArr as $rescueDataLStr ){
            if( is_string( $rescueDataLStr ) ){
              $returnLineLArr[] = util::getInArray( $rescueDataLStr, $lineLArr );
            }
            else{
              $returnLineLArr[] = array( $rescueDataLStr[ 0 ] => util::getInArray( $rescueDataLStr[ 1 ], $lineLArr ) );
            }

          }
          $returnLArr[] = $returnLineLArr;
        }
        // Caso não haja dados a serem recuperados,monta o dado original.
        else
        {
          // Procura por dados a serem excluídos.
          if( is_array( $this->garbageCollectorCArr ) )
          {
            foreach( $this->garbageCollectorCArr as $garbageLStr )
            {
              util::garbageCollector( $garbageLStr, $lineLArr );
            }
          }
          else if( is_string( $this->garbageCollectorCArr ) )
          {
            util::garbageCollector( $this->garbageCollectorCArr, $lineLArr );
          }
          $returnLArr[] = $lineLArr;
        }
      }

      // Verifica se há alguma informação determinada pelo usuário para ser colocada na primeira linha da saída.
      // Isto é muito útil para exportar CSV e montar planilhas.
      if( is_array( $this->appendFirstCArr ) ){
        $returnLArr = array_merge( array( $this->appendFirstCArr ), $returnLArr );
      }

      // Verifica se há alguma informação determinada pelo usuário para ser colocada na última linha da saída.
      if( is_array( $this->appendLastCArr ) ){
        $returnLArr = array_merge( $returnLArr, array( $this->appendLastCArr ) );
      }

      return $returnLArr;
    }

    /** Determina quais chaves vão ser recuperados na query atual.
     * Esta função se sobrepõe as demais e é usada para recuperar chaves específicas em uma coleção de dados.
     * A principal funcionalidade dela é limpar os dados, principalmente para arquivos CSV.
     * @code
        // dado: { location:[-8.1313,-34.9146], key:{ subKey1:{ subKey2: { subKeyN: value } } } }
        $this->setRescueCollection( array( "location.0", "location.1", "key.subKey1.subKey2.subKeyN" ) );
     * @endcode
     * @param $collectorAArr [array|string] Com as chaves desejadas separadas por ponto.
     * @see setGarbageCollector
     */
    protected function setRescueCollection( $collectorAArr ){
      $this->rescueCollectionCArr = $collectorAArr;
    }

    /** Determina quais chaves vão ser excluídas da saída.
     * Esta função é útil para apagar dados críticos, como senhas do usuário.
     * @code
        $this->setGarbageCollector( array( "id_node", "id_changesets", "id_user", "version", "visible", , "key.subKey1.subKey2.subKeyN" ) );
     * @endcode
     * @param $collectorAArr [array|string] Com as chaves desejadas separadas por ponto.
     * @see setRescueCollection
     */
    protected function setGarbageCollector( $collectorAArr ){
      $this->garbageCollectorCArr = $collectorAArr;
    }

    /** Adiciona o valor contido no array a primeira linha da saída.
     * @code
        $this->setAppendFirst( array( "lat", "lgn", "maxSpeed" ) );
     * @endcode
     * @param $appendAArr array de string
     * @see setAppendLast
     */
    protected function setAppendFirst( $appendAArr ){
      $this->appendFirstCArr = $appendAArr;
    }

    /** Adiciona o valor contido no array a última linha da saída.
     * @param $appendAArr array de string
     * @see setAppendFirst
     */
    protected function setAppendLast( $appendAArr ){
      $this->appendLastCArr = $appendAArr;
    }
  }