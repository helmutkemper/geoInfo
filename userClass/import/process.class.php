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
        "processData"
      );
    }

    public function processData(){
      $this->init( size::MByte( 5 ) );
      $this->setNewCollections();
      $this->createIndex();
      return $this->processOsmFile( "./support/brazil-latest.osm" );

      //todo: se nodes tags array = 0 inserir?
      //$parserXmlLObj->concatenateNodeData();
      //$parserXmlLObj->concatenateWayTagsAndNodes();
    }
  }