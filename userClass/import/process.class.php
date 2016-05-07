<?php

  class process extends osmXml{
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
        "importData",
        "processNodeData"
      );
    }

    public function getUserData(){
      $collectionLObj = $this->collectionTmpNodesCObj->find();
      $tmpNodesCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionTmpNodeTagCObj->find();
      $tmpNodesTagsCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionTmpWaysCObj->find();
      $tmpWaysCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionTmpWayTagCObj->find();
      $tmpWaysTagsCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionTmpWayNodeCObj->find();
      $tmpWayNodeCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionSetupFill->find();
      $tmpFillSetupCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionSetupMap->find();
      $tmpSetupMapCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionNodesCObj->find();
      $nodesCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionWaysCObj->find();
      $waysCountLUInt = $collectionLObj->count();

      return array(
        "tmpNodes" => $tmpNodesCountLUInt,
        "tmpNodesTags" => $tmpNodesTagsCountLUInt,
        "tmpWays" => $tmpWaysCountLUInt,
        "tmpWaysTags" => $tmpWaysTagsCountLUInt,
        "tmpWayNode" => $tmpWayNodeCountLUInt,
        "tmpFillSetup" => $tmpFillSetupCountLUInt,
        "tmpSetupMap" => $tmpSetupMapCountLUInt,
        "nodes" => $nodesCountLUInt,
        "ways" => $waysCountLUInt
      );
    }

    public function importData(){
      global $pageLimitGUInt;
      global $pageOffsetGUInt;
      global $pageTotalGObj;
      global $pageNextLinkGStr;

      if ( !isset( $_REQUEST[ "osmXmlToDataBaseCompressIdFUInt" ] ) )
      {
        $this->previousFileTextCStr = "";
        $this->compressDataCStr = "";
        $this->fileLastByteReadCUInt = 0;
        $this->makeFileToExportIndexCUInt = 0;
      }
      else
      {
        $this->makeFileToExportIndexCUInt = $_REQUEST[ "osmXmlToDataBaseCompressIdFUInt" ];

        $this->fileLastByteReadCUInt = $_SESSION[ "osmXmlToDataBase" ][ "fileLastByteRead" ];
        $this->previousFileTextCStr = $_SESSION[ "osmXmlToDataBase" ][ "previousFileText" ];
        $this->compressDataCStr = $_SESSION[ "osmXmlToDataBase" ][ "compressData" ];
        $this->fileLastByteReadCUInt = $_SESSION[ "osmXmlToDataBase" ][ "fileLastByteRead" ];
      }

      $this->init( size::MByte( 5 ) );
      $this->setNewCollections();
      $this->createIndex();

      $returnLArr = $this->processOsmFile( "./support/brazil-latest.osm" );

      $_SESSION[ "osmXmlToDataBase" ][ "fileLastByteRead" ] = $this->fileLastByteReadCUInt;
      $_SESSION[ "osmXmlToDataBase" ][ "previousFileText" ] = $this->previousFileTextCStr;
      $_SESSION[ "osmXmlToDataBase" ][ "compressData" ] = $this->compressDataCStr;
      $_SESSION[ "osmXmlToDataBase" ][ "fileLastByteRead" ] = $this->fileLastByteReadCUInt;

      $pageLimitGUInt = 1;
      $pageOffsetGUInt = $this->makeFileToExportIndexCUInt;
      $pageTotalGObj = ceil( filesize( $this->osmFileNameCStr ) / $this->parserXmlBytesPerPageCUInt );

      $_SERVER[ "REQUEST_URI" ] = preg_replace( "%(.*?)(\?.*)%", "$1", $_SERVER[ "REQUEST_URI" ] );
      $pageNextLinkGStr = $_SERVER[ "REQUEST_SCHEME" ] . "://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?osmXmlToDataBaseCompressIdFUInt={$this->makeFileToExportIndexCUInt}";

      if( $this->getProcessEnd() == true ){
        $_SESSION[ "osmXmlToDataBase" ] = array();
      }

      return array_merge( $returnLArr, $this->getUserData() );

      //$parserXmlLObj->concatenateWayTagsAndNodes();
    }

    public function processNodeData(){
      if( !isset( $_SESSION[ "osmXmlToDataBase" ][ "limit" ] ) ){
        $_SESSION[ "osmXmlToDataBase" ][ "limit" ] = 500;
        $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] = 0;
      }
      else{
        $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] += $_SESSION[ "osmXmlToDataBase" ][ "limit" ];
      }

      $this->concatenateNodeData( $_SESSION[ "osmXmlToDataBase" ][ "limit" ], $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] );
    }
  }