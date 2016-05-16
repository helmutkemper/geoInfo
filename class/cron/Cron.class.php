<?php
  
  class Cron extends osmXml{
    
    function __construct()
    {
      parent::__construct();
      self::connect();

      $this->init( size::MByte( 5 ) );
      $this->setNewCollections();
      $this->createIndex();

      $dirLArr = scandir( "./map" );
      foreach( $dirLArr as $dirValueLStr ){
        if( ( $dirValueLStr == "." ) || ( $dirValueLStr == ".." ) ){
          continue;
        }

        if( is_file( "./map/" . $dirValueLStr ) ){
          $md5LStr = md5( "./map/" . $dirValueLStr );
          if( $this->itIsToProcessThisFile( "./map/" . $dirValueLStr, $md5LStr ) ){
            $this->processOsmFile( "./map/brazil-latest.osm", true, false );
            $this->concatenateNodeData();
            $this->concatenateWayTagsAndNodes();
            $this->setFileProcessEnd( "./map/" . $dirValueLStr, $md5LStr );
          }
        }
      }
    }
  }