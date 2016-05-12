<?php

  class process extends osmXml{
    const PAGE_LIMIT = 1000;

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
        "importFillSetup",
        "importMapSetup",
        "importData",
        "processNodeData",
        "processWaysData",
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
        "setupFill" => $tmpFillSetupCountLUInt,
        "setupMap" => $tmpSetupMapCountLUInt,
        "nodes" => $nodesCountLUInt,
        "ways" => $waysCountLUInt
      );
    }

    public function importMapSetup(){
      $this->setNewCollections();
      $mapSetupCollectionLObj = $this->getSetupMapPointer();
      $mapSetupCollectionLObj->insert( json_decode('{"mapVersion": 1,"layerMin": -5,"layerDefault": 0,"layerMax": 5,"layerMultiplier": 5,"zoomFactorMin": 1,"zoomFactorMax": 30,"zoomFactorScale": [0,0.01,0.009,0.008,0.007,0.006,0.005,0.004,0.003,0.002,0.005,0.0009,0.0008,0.0007,0.0006,0.015,0.0004,0.0003,0.0002,0.0001,0.005,0.00008,0.00007,0.00006,0.00005,0.00004,0.00003,0.00002,0.00001,0.000009,0.000008,0.000008],"dbLimit": [5000,5000,5000,5000,5000,5000,5000,5000,5000,5000,1000,5000,5000,5000,5000,5000,5000,5000,5000,5000,5000,5000,5000,5000,5000,100,5000,5000,5000,5000,5000,5000]}', true ) );
      $this->setPageTotal( 1 );
      $this->setPageOffset( 1 );

      $this->setPageNextLink( null );

      $returnLArr = array(
        "block" => $_SESSION[ "osmXmlToDataBase" ][ "blockIndex" ],
        "total" => 1
      );

      return array_merge( $returnLArr, $this->getUserData() );
    }

    public function importFillSetup(){
      $fileNameLStr = "./fillSetup.json";
      $this->setNewCollections();
      $fillSetupCollectionLObj = $this->getSetupFillPointer();
      $fileSizeLUInt = size::MByte( 5 );

      if( !isset( $_REQUEST[ "block" ] ) ){
        $_SESSION[ "osmXmlToDataBase" ][ "blockIndex" ] = 0;
        $_SESSION[ "osmXmlToDataBase" ][ "fileSeek" ] = 0;
        $_SESSION[ "osmXmlToDataBase" ][ "lastBuffer" ] = "";
      }

      $resourceFileLObj = fopen( $fileNameLStr, "r" );
      if ( !$resourceFileLObj )
      {
        if ( !is_file ( $fileNameLStr ) )
        {
          throw new Exception ( "File not found / Arquivo não encontrado: {$fileNameLStr}." );
        }
        if ( !is_readable( $fileNameLStr ) )
        {
          throw new Exception ( "I found, but I can`t read file / O arquivo existe, porém, eu não posso ler o arquivo: {$fileNameLStr}." );
        }
      }

      fseek( $resourceFileLObj, $_SESSION[ "osmXmlToDataBase" ][ "fileSeek" ] );

      $fileDataLX = fread( $resourceFileLObj, $fileSizeLUInt );

      if ( $fileDataLX != false )
      {
        $fileDataLX = $_SESSION[ "osmXmlToDataBase" ][ "lastBuffer" ] . $fileDataLX;

        $_SESSION[ "osmXmlToDataBase" ][ "fileSeek" ] = ftell( $resourceFileLObj );

        preg_match_all( "%^(.*)(\r\n.*?)$%si", $fileDataLX, $matchesLArr );
      }
      $_SESSION[ "osmXmlToDataBase" ][ "lastBuffer" ] = $matchesLArr[ 2 ][ 0 ];
  
      $fileDataLX = $matchesLArr[ 1 ][ 0 ];
      $fileDataLX = explode( "\r\n", $fileDataLX );
      foreach( $fileDataLX as $lineLX ){
        $lineLX = str_replace( "\\r", "\r", $lineLX );
        $lineLX = str_replace( "\\n", "\n", $lineLX );
        $lineLX = json_decode( $lineLX, 1 );
        unset( $lineLX[ "_id" ] );
        if( is_null( $lineLX ) ){
          continue;
        }
        $fillSetupCollectionLObj->insert( $lineLX );
      }

      $_SESSION[ "osmXmlToDataBase" ][ "blockIndex" ] += 1;

      $this->setPageTotal( ceil( filesize( $fileNameLStr ) / $fileSizeLUInt ) );
      $this->setPageOffset( $_SESSION[ "osmXmlToDataBase" ][ "blockIndex" ] );

      $_SERVER[ "REQUEST_URI" ] = preg_replace( "%(.*?)(\?.*)%", "$1", $_SERVER[ "REQUEST_URI" ] );
      $this->setPageNextLink( $_SERVER[ "REQUEST_SCHEME" ] . "://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?block=" . $_SESSION[ "osmXmlToDataBase" ][ "blockIndex" ] );

      $returnLArr = array(
        "block" => $_SESSION[ "osmXmlToDataBase" ][ "blockIndex" ],
        "total" => ceil( filesize( $fileNameLStr ) / $fileSizeLUInt )
      );

      return array_merge( $returnLArr, $this->getUserData() );
    }

    //http://localhost/open/html/import/index.html
    public function importData(){
      if( isset( $_REQUEST[ "block" ] ) )
      {
        $this->setDataBlock( $_SESSION[ "osmXmlToDataBase" ][ "blockId" ] );
        $this->setFilePointer( $_SESSION[ "osmXmlToDataBase" ][ "fileLastByteRead" ] );
        $this->setPreviousBuffer( $_SESSION[ "osmXmlToDataBase" ][ "previousFileText" ] );
      }

      $this->init( size::MByte( 5 ) );
      $this->setNewCollections();
      $this->createIndex();

      $returnLArr = $this->processOsmFile( "./support/brazil-latest.osm" );

      $_SESSION[ "osmXmlToDataBase" ][ "blockId" ] = $this->getDataBlock();
      $_SESSION[ "osmXmlToDataBase" ][ "fileLastByteRead" ] = $this->getFilePointer();
      $_SESSION[ "osmXmlToDataBase" ][ "previousFileText" ] = $this->getPreviousBuffer();

      $this->setPageLimit( 1 );
      $this->setPageOffset( $this->getDataBlock() );
      $this->setPageTotal( ceil( filesize( $this->osmFileNameCStr ) / $this->parserXmlBytesPerPageCUInt ) );

      $_SERVER[ "REQUEST_URI" ] = preg_replace( "%(.*?)(\?.*)%", "$1", $_SERVER[ "REQUEST_URI" ] );
      $this->setPageNextLink( $_SERVER[ "REQUEST_SCHEME" ] . "://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?block=" . $this->getDataBlock() );

      return array_merge( $returnLArr, $this->getUserData() );
    }

    public function processNodeData(){
      if ( !isset( $_REQUEST[ "block" ] ) ){
        $_SESSION[ "osmXmlToDataBase" ] = array();
        $_SESSION[ "osmXmlToDataBase" ][ "limit" ] = process::PAGE_LIMIT;
        $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] = 0;
      }
      else{
        $this->setDataBlock( $_SESSION[ "osmXmlToDataBase" ][ "blockId" ] );
        $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] += $_SESSION[ "osmXmlToDataBase" ][ "limit" ];
      }

      $this->setNewCollections();
      $returnLArr = $this->concatenateNodeData( $_SESSION[ "osmXmlToDataBase" ][ "limit" ], $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] );

      $this->setPageLimit( 1 );
      $this->setPageOffset( $this->getDataBlock() );
      $this->setPageTotal( $this->getNodeDataTotal() / $_SESSION[ "osmXmlToDataBase" ][ "limit" ] );
      $_SESSION[ "osmXmlToDataBase" ][ "blockId" ] = $this->getDataBlock();

      $_SERVER[ "REQUEST_URI" ] = preg_replace( "%(.*?)(\?.*)%", "$1", $_SERVER[ "REQUEST_URI" ] );
      $this->setPageNextLink( $_SERVER[ "REQUEST_SCHEME" ] . "://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?block=" . $this->getDataBlock() );

      if( $_REQUEST[ "block" ] == $this->getDataBlock() ){
        $_SESSION[ "osmXmlToDataBase" ] = array();
      }

      return array_merge( $returnLArr, $this->getUserData() );
    }

    public function processWaysData(){
      if ( !isset( $_REQUEST[ "block" ] ) ){
        $_SESSION[ "osmXmlToDataBase" ] = array();
        $_SESSION[ "osmXmlToDataBase" ][ "limit" ] = process::PAGE_LIMIT;
        $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] = 0;
      }
      else{
        $this->setDataBlock( $_SESSION[ "osmXmlToDataBase" ][ "blockId" ] );
        $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] += $_SESSION[ "osmXmlToDataBase" ][ "limit" ];
      }

      $this->setNewCollections();
      $returnLArr = $this->concatenateWayTagsAndNodes( $_SESSION[ "osmXmlToDataBase" ][ "limit" ], $_SESSION[ "osmXmlToDataBase" ][ "offSet" ] );

      $this->setPageLimit( 1 );
      $this->setPageOffset( $this->getDataBlock() );
      $this->setPageTotal( $this->getNodeDataTotal() / $_SESSION[ "osmXmlToDataBase" ][ "limit" ] );
      $_SESSION[ "osmXmlToDataBase" ][ "blockId" ] = $this->getDataBlock();

      $_SERVER[ "REQUEST_URI" ] = preg_replace( "%(.*?)(\?.*)%", "$1", $_SERVER[ "REQUEST_URI" ] );
      $this->setPageNextLink( $_SERVER[ "REQUEST_SCHEME" ] . "://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?block=" . $this->getDataBlock() );

      return array_merge( $returnLArr, $this->getUserData() );
    }
  }