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

    public function importData(){
      global $pageLimitGUInt;
      global $pageOffsetGUInt;
      global $pageTotalGObj;
      global $pageNextLinkGStr;

      if ( !isset( $_REQUEST[ "osmXmlToDataBaseCompressIdFUInt" ] ) )
      {
        $this->previousFileTextCStr = "";
        $this->compressIndexCUInt = 0;
        $this->compressDataCStr = "";
        $this->fileLastByteReadCUInt = 0;
        $this->makeFileToExportIndexCUInt = 0;
      }
      else
      {
        $this->compressIndexCUInt = $_REQUEST[ "compressIndex" ];
        $this->makeFileToExportIndexCUInt = $_REQUEST[ "osmXmlToDataBaseCompressIdFUInt" ];
      }

      $this->init( size::MByte( 5 ) );
      $this->setNewCollections();
      $this->createIndex();

      $returnLArr = $this->processOsmFile( "./support/brazil-latest.osm" );

      $pageLimitGUInt = 1;
      $pageOffsetGUInt = $this->makeFileToExportIndexCUInt;
      $pageTotalGObj = ceil( filesize( $this->osmFileNameCStr ) / $this->parserXmlBytesPerPageCUInt );
      $_SERVER[ "REQUEST_URI" ] = preg_replace( "%(.*?)(\?.*)%", "$1", $_SERVER[ "REQUEST_URI" ] );
      $pageNextLinkGStr = $_SERVER[ "REQUEST_SCHEME" ] . "://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?osmXmlToDataBaseCompressIdFUInt={$this->makeFileToExportIndexCUInt}&compressIndex={$this->compressIndexCUInt}";

      return $returnLArr;

      //$parserXmlLObj->concatenateWayTagsAndNodes();
    }

    public function processNodeData(){
      $this->concatenateNodeData();
    }
  }