<?php

  class osmXml extends osmXmlToMongoDbSupport
  {
    protected $microTimeCObj;

    /**
     * @var string nome do arquivo xml a ser processado.
     */
    protected $osmFileNameCStr = "";

    /**
     * @var string arquiva as sobras do xml que não foram processadas na operação anterior.
     */
    protected $flagLastTagProcessedIsOpenCStr = "";

    /**
     * @var int positivo com o tabanho em bytes do arquivo xml a ser processado.
     */
    protected $parserXmlBytesPerPageCUInt;

    protected $blockIndexCUInt;
    protected $previousFileTextCStr;
    //protected $compressDataCStr;
    protected $fileLastByteReadCUInt;

    public function __construct()
    {
      if( is_null( $this->fileLastByteReadCUInt ) ){
        $this->fileLastByteReadCUInt = 0;
      }
      if( is_null( $this->blockIndexCUInt ) ){
        $this->blockIndexCUInt = 0;
      }
      if( is_null( $this->previousFileTextCStr ) ){
        $this->previousFileTextCStr = "";
      }

      parent::__construct();
    }

    protected function init( $parserXmlBytesPerPageAUInt = 1048576 )
    {
      $this->microTimeCObj = microtime( true );

      $this->parserXmlBytesPerPageCUInt = $parserXmlBytesPerPageAUInt;
    }

    protected function getPreviousBuffer(){
      return $this->previousFileTextCStr;
    }

    protected function setPreviousBuffer( $valueAUInt ){
      $this->previousFileTextCStr = $valueAUInt;
    }

    protected function getDataBlock(){
      return $this->blockIndexCUInt;
    }

    protected function setDataBlock( $valueAUInt ){
      $this->blockIndexCUInt = $valueAUInt;
    }

    protected function getFilePointer(){
      return $this->fileLastByteReadCUInt;
    }

    protected function setFilePointer( $valueAUInt ){
      $this->fileLastByteReadCUInt = $valueAUInt;
    }

    public function processOsmFile ( $osmFileNameAStr )
    {
      $this->osmFileNameCStr = $osmFileNameAStr;

      $parserXmlLObj = xml_parser_create( "UTF-8" );
      xml_set_object( $parserXmlLObj, $this );
      xml_set_element_handler( $parserXmlLObj, "osmXmlOnOpenTag", "osmXmlOnCloseTag" );

      $resourceOsmFileLObj = fopen( $this->osmFileNameCStr, "r" );
      if ( !$resourceOsmFileLObj )
      {
        if ( !is_file ( $this->osmFileNameCStr ) )
        {
          throw new Exception ( "File not found / Arquivo não encontrado: {$this->osmFileNameCStr}." );
        }
        if ( !is_readable( $this->osmFileNameCStr ) )
        {
          throw new Exception ( "I found, but I can`t read file / O arquivo existe, porém, eu não posso ler o arquivo: {$this->osmFileNameCStr}." );
        }
      }

      fseek( $resourceOsmFileLObj, $this->fileLastByteReadCUInt );

      $osmXmlFileDataLStr    = fread( $resourceOsmFileLObj, $this->parserXmlBytesPerPageCUInt );

      $osmXmlDataToParserLStr    = "";
      $osmXmlTmpDataToParserLStr = "";

      if ( $osmXmlFileDataLStr != false )
      {
        $osmXmlFileDataLStr = $this->previousFileTextCStr . $osmXmlFileDataLStr;

        $this->fileLastByteReadCUInt = ftell( $resourceOsmFileLObj );

        preg_match_all( "%^(.*?)(<.*>)(.*)$%si", $osmXmlFileDataLStr, $matchesLArr );

        $flagLastTagProcessedIsOpenLBol = false;
        if ( preg_match_all( "%^(.*?)(<.*>)(.*)$%si", $osmXmlFileDataLStr, $matchesLArr ) == 0 )
        {
          $this->previousFileTextCStr = $osmXmlFileDataLStr;
        }
        else
        {
          if ( preg_match_all( "%(<.*?>)%si", $matchesLArr[ 2 ][ 0 ], $matchesTagsLArr ) == 0 )
          {
            $this->previousFileTextCStr = $matchesLArr[ 2 ][ 0 ] . $matchesLArr[ 3 ][ 0 ];
          }
          else
          {
            foreach( $matchesTagsLArr[ 1 ] as $matchesTagsKeyLUInt => $matchesTagsValueLStr )
            {
              /**
               * <?xml version="1.0" encoding="UTF-8"?>
               */
              if ( substr( $matchesTagsValueLStr, 0, strlen( "<?xml" ) ) == "<?xml" )
              {
                //$osmXmlDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
              }

              /**
               * <osm version="0.6" generator="CGImap 0.0.2">
               */
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "<osm" ) ) == "<osm" )
              {
                //$osmXmlDataToParserLStr .= $matchesTagsValueLStr;
                //$osmXmlDataToParserLStr .= "</osm>" . "\r\n";
              }

              /**
               * <bounds minlat="54.0889580" minlon="12.2487570" maxlat="54.0913900" maxlon="12.2524800"/>
               */
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "<bounds" ) ) == "<bounds" )
              {
                $osmXmlDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
              }

              /**
               * <node id="298884269" lat="54.0901746" lon="12.2482632" user="SvenHRO" uid="46882" visible="true" version="1" changeset="676636" timestamp="2008-09-21T21:37:45Z"/>
               */
              else if ( ( substr( $matchesTagsValueLStr, 0, strlen( "<node" ) ) == "<node" ) && ( substr( $matchesTagsValueLStr, -2, strlen( "/>" ) ) == "/>" ) )
              {
                $osmXmlDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
              }

              /**
               * <node id="1831881213" version="1" changeset="12370172" lat="54.0900666" lon="12.2539381" user="lafkor" uid="75625" visible="true" timestamp="2012-07-20T09:43:19Z">
               * ...
               * </node>
               */
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "<node" ) ) == "<node" )
              {
                $flagLastTagProcessedIsOpenLBol = true;
                $osmXmlTmpDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
              }
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "</node>" ) ) == "</node>" )
              {
                $osmXmlDataToParserLStr    .= $osmXmlTmpDataToParserLStr . $matchesTagsValueLStr . "\r\n";
                $osmXmlTmpDataToParserLStr  = "";
              }

              /**
               * <tag k="traffic_sign" v="city_limit"/>
               */
              else if ( ( substr( $matchesTagsValueLStr, 0, strlen( "<tag" ) ) == "<tag" ) && ( substr( $matchesTagsValueLStr, -2, strlen( "/>" ) ) == "/>" ) )
              {
                if( $flagLastTagProcessedIsOpenLBol == true )
                {
                  $osmXmlTmpDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
                }
                else
                {
                  $osmXmlDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
                }
              }

              /**
               * <way id="26659127" user="Masch" uid="55988" visible="true" version="5" changeset="4142606" timestamp="2010-03-16T11:47:08Z">
               * ...
               * </way>
               */
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "<way" ) ) == "<way" )
              {
                $flagLastTagProcessedIsOpenLBol = true;
                $osmXmlTmpDataToParserLStr .= $matchesTagsValueLStr;
              }
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "</way>" ) ) == "</way>" )
              {
                $flagLastTagProcessedIsOpenLBol = false;
                $osmXmlDataToParserLStr    .= $osmXmlTmpDataToParserLStr . $matchesTagsValueLStr . "\r\n";
                $osmXmlTmpDataToParserLStr  = "";
              }

              /**
               * <nd ref="261728686"/>
               */
              else if ( ( substr( $matchesTagsValueLStr, 0, strlen( "<nd" ) ) == "<nd" ) && ( substr( $matchesTagsValueLStr, -2, strlen( "/>" ) ) == "/>" ) )
              {
                if( $flagLastTagProcessedIsOpenLBol == true )
                {
                  $osmXmlTmpDataToParserLStr .= $matchesTagsValueLStr;
                }
                else
                {
                  $osmXmlDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
                }
              }

              /**
               * <relation id="56688" user="kmvar" uid="56190" visible="true" version="28" changeset="6947637" timestamp="2011-01-12T14:23:49Z">
               * ...
               * </relation>
               */
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "<relation" ) ) == "<relation" )
              {
                $flagLastTagProcessedIsOpenLBol = true;
                $osmXmlTmpDataToParserLStr .= $matchesTagsValueLStr;
              }
              else if ( substr( $matchesTagsValueLStr, 0, strlen( "</relation>" ) ) == "</relation>" )
              {
                $flagLastTagProcessedIsOpenLBol = false;
                $osmXmlDataToParserLStr    .= $osmXmlTmpDataToParserLStr . $matchesTagsValueLStr . "\r\n";
                $osmXmlTmpDataToParserLStr  = "";
              }

              /**
               * <member type="node" ref="294942404" role=""/>
               */
              else if ( ( substr( $matchesTagsValueLStr, 0, strlen( "<member" ) ) == "<member" ) && ( substr( $matchesTagsValueLStr, -2, strlen( "/>" ) ) == "/>" ) )
              {
                if( $flagLastTagProcessedIsOpenLBol == true )
                {
                  $osmXmlTmpDataToParserLStr .= $matchesTagsValueLStr;
                }
                else
                {
                  $osmXmlDataToParserLStr .= $matchesTagsValueLStr . "\r\n";
                }
              }
            }

            $this->previousFileTextCStr = $osmXmlTmpDataToParserLStr . $matchesLArr[ 3 ][ 0 ];
          }
        }

        $osmXmlDataToParserLStr = "<?xml version='1.0' encoding='UTF-8'?><bitOfData>" . $osmXmlDataToParserLStr . "<bitOfData>";

        xml_parse( $parserXmlLObj, $osmXmlDataToParserLStr, feof( $resourceOsmFileLObj ) );

        return $this->runNextPage();
      }
      else
      {
        $this->setProcessEnd();
        return;
      }
    }

    /*
    protected function addToCompressedFile ( $textAStr, $idOfDataAStr )
    {
      switch ( $idOfDataAStr )
      {
        case "createDataBaseAndSelect":
        case "createTables":
          if( $this->blockIndexCUInt == 0 )
          {
            $this->compressDataCStr .= $textAStr;
          }
          break;

        case "createNode":
        case "createNodeTag":
        case "createWay":
        case "createWayNode":
        case "createWayTag":
        $this->compressDataCStr .= $textAStr;
          break;
      }
    }
    */

    private function runNextPage()
    {
      $this->blockIndexCUInt += 1;

      return array(
        "block" => $this->blockIndexCUInt,
        "total" => ceil( filesize( $this->osmFileNameCStr ) / $this->parserXmlBytesPerPageCUInt )
      );
    }

    private function osmXmlOnOpenTag( $parserXmlAObj, $nodeNameAStr, $nodeAttributesAArr )
    {
      foreach( $nodeAttributesAArr as $nodeAttributesKeyLUInt => $nodeAttributesValueLX )
      {
        if( preg_match( "%([0-9]{4}-[0-9]{2}-[0-9]{2})T([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})Z%", $nodeAttributesValueLX, $matchesLArr ) )
        {
          $nodeAttributesValueLX = "{$matchesLArr[ 1 ]} {$matchesLArr[ 2 ]}";
        }

        $nodeAttributesAArr[ $nodeAttributesKeyLUInt ] = addslashes( /*utf8_decode*/ ( $nodeAttributesValueLX ) );
      }

      if ( ( !isset( $nodeAttributesAArr["VISIBLE"] ) ) || ( $nodeAttributesAArr["VISIBLE"] == true ) )
      {
        $nodeAttributesAArr["VISIBLE"] = "TRUE";
      }
      else
      {
        $nodeAttributesAArr["VISIBLE"] = "FALSE";
      }

      switch( $nodeNameAStr )
      {
        case "NODE":
          $this->flagLastTagProcessedIsOpenCStr = $nodeNameAStr;
          $this->xmlReferenceId                 = $nodeAttributesAArr["ID"];
          $this->xmlReferenceVersion            = $nodeAttributesAArr["VERSION"];

          $this->createOsmUser( $nodeAttributesAArr["UID"], $nodeAttributesAArr["USER"], $this->idUserCUInt );
          $this->createNode( $nodeAttributesAArr["ID"], $nodeAttributesAArr["CHANGESET"], $nodeAttributesAArr["UID"], $nodeAttributesAArr["VERSION"], $nodeAttributesAArr["VISIBLE"], $nodeAttributesAArr["LAT"], $nodeAttributesAArr["LON"], $nodeAttributesAArr["TIMESTAMP"], $this->idUserCUInt, $this->idLoaderCUInt );
          break;

        case "WAY":
          $this->flagLastTagProcessedIsOpenCStr = $nodeNameAStr;
          $this->xmlReferenceId                 = $nodeAttributesAArr["ID"];
          $this->xmlReferenceVersion            = $nodeAttributesAArr["VERSION"];

          $this->createOsmUser( $nodeAttributesAArr["UID"], $nodeAttributesAArr["USER"], $this->idUserCUInt );
          $this->createWay( $nodeAttributesAArr["ID"], $nodeAttributesAArr["CHANGESET"], $nodeAttributesAArr["UID"], $nodeAttributesAArr["VERSION"], $nodeAttributesAArr["VISIBLE"], $nodeAttributesAArr["TIMESTAMP"], $this->idUserCUInt, $this->idLoaderCUInt );
          break;

        case "ND":
          switch( $this->flagLastTagProcessedIsOpenCStr )
          {
            case "WAY":
              $this->createWayNode( $this->xmlReferenceId, $nodeAttributesAArr["REF"] );
              break;
          }
          break;

        case "TAG":

          if( $nodeAttributesAArr["K"] == "addr:postcode" )
          {
            $nodeAttributesAArr["V"] = preg_replace( "%[^0-9A-Za-z]%", "", $nodeAttributesAArr["V"] );
          }

          switch( $this->flagLastTagProcessedIsOpenCStr )
          {
            case "NODE":
              if( !isset( $nodeAttributesAArr["VERSION"] ) )
              {
                $nodeAttributesAArr["VERSION"] = $this->xmlReferenceVersion;
              }
              $this->createNodeTag( $this->xmlReferenceId, $nodeAttributesAArr["VERSION"], $nodeAttributesAArr["K"], $nodeAttributesAArr["V"] );
              break;

            case "WAY":
              if( !isset( $nodeAttributesAArr["VERSION"] ) )
              {
                $nodeAttributesAArr["VERSION"] = $this->xmlReferenceVersion;
              }
              $this->createWayTag( $this->xmlReferenceId, $nodeAttributesAArr["VERSION"], $nodeAttributesAArr["K"], $nodeAttributesAArr["V"] );
              break;
          }
          break;
      }
    }

    private function osmXmlOnCloseTag( $parserXmlAObj, $nodeNameAStr )
    {
      switch( $nodeNameAStr )
      {
        case "NODE":
          $this->flagLastTagProcessedIsOpenCStr = "";
          break;
      }
    }
  }
