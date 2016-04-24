<?php

  class normalize extends db{
    protected $garbageCollectorCArr;
    protected $rescueCollectionCArr;

    protected $appendFirstCArr;
    protected $appendLastCArr;

    function __construct()
    {
      $this->garbageCollectorCArr = null;
    }

    function setOutputJSonMobile(){
      global $outputTypeGStr;

      $outputTypeGStr = "json_mobile";
    }

    function setOutputJSon(){
      global $outputTypeGStr;

      $outputTypeGStr = "json";
    }

    function setOutputCsv(){
      global $outputTypeGStr;

      $outputTypeGStr = "csv";
    }

    function MongoFindToOutput( $toFindAArr ){
      global $pageLimitGUInt;
      global $pageOffsetGUInt;
      global $pageTotalGObj;
      global $pagePreviousQueryGStr;

      $pagePreviousQueryGStr = $toFindAArr;

      $passLBoo = false;

      if( isset( $_REQUEST[ "query" ] ) ){
        $dataQueryLArr = Crypt::decrypt( $_REQUEST[ "query" ] );

        if( isset( $dataQueryLArr[ "cy" ] ) ){
          $cursorLObj = $this->collectionCObj->find( $dataQueryLArr[ "cy" ] );
          $pageTotalGObj = $cursorLObj->count();
          $cursorLObj->skip( $dataQueryLArr[ "offset" ] );
          $cursorLObj->limit( $dataQueryLArr[ "limit" ] );

          $passLBoo = true;
        }
      }

      if( $passLBoo == false ) {
        $cursorLObj = $this->collectionCObj->find( $toFindAArr );
        $pageTotalGObj = $cursorLObj->count();
        $cursorLObj->skip( $pageOffsetGUInt );
        $cursorLObj->limit( $pageLimitGUInt );
      }

      $returnLArr = array();
      foreach( $cursorLObj as $lineLArr ){

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
        else
        {
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

      if( is_array( $this->appendFirstCArr ) ){
        $returnLArr = array_merge( array( $this->appendFirstCArr ), $returnLArr );
      }

      if( is_array( $this->appendLastCArr ) ){
        $returnLArr = array_merge( $returnLArr, array( $this->appendLastCArr ) );
      }

      return $returnLArr;
    }

    public function setRescueCollection( $collectorAArr ){
      $this->rescueCollectionCArr = $collectorAArr;
    }

    public function setGarbageCollector( $collectorAArr ){
      $this->garbageCollectorCArr = $collectorAArr;
    }

    public function setAppendFirst( $appendAArr ){
      $this->appendFirstCArr = $appendAArr;
    }

    public function setAppendLast( $appendAArr ){
      $this->appendLastCArr = $appendAArr;
    }
  }