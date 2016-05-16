<?php

  class view extends osmXml{
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
        "viewNow"
      );
    }

    public function viewNow(){
      $this->setNewCollections();

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

      //$collectionLObj = $this->collectionSetupFill->find();
      //$tmpFillSetupCountLUInt = $collectionLObj->count();

      //$collectionLObj = $this->collectionSetupMap->find();
      //$tmpSetupMapCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionNodesCObj->find();
      $nodesCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionWaysCObj->find();
      $waysCountLUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionKeyValueDistinctCObj->find();
      $KeyValueDistinctUInt = $collectionLObj->count();

      $collectionLObj = $this->collectionImportFileCObj->find(
        array(
          "isProcessing" => true
        )
      );
      $KeyValueProcessingUInt = $collectionLObj->count();

      return array(
        "tmpNodes" => $tmpNodesCountLUInt,
        "tmpNodesTags" => $tmpNodesTagsCountLUInt,
        "tmpWays" => $tmpWaysCountLUInt,
        "tmpWaysTags" => $tmpWaysTagsCountLUInt,
        "tmpWayNode" => $tmpWayNodeCountLUInt,
        //"setupFill" => $tmpFillSetupCountLUInt,
        //"setupMap" => $tmpSetupMapCountLUInt,
        "nodes" => $nodesCountLUInt,
        "ways" => $waysCountLUInt,
        "KeyValDist" => $KeyValueDistinctUInt,
        "processing" => $KeyValueProcessingUInt
      );
    }
  }