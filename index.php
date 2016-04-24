<?php

  session_start ();

  define( "CLASS_URL_UINT", 3 );
  define( "MODULE_URL_UINT", 4 );
  define( "CONTROLLER_URL_UINT", 5 );

  if ( is_file ( "./doNotUpload/server.php" ) )
  {
    include_once "./doNotUpload/server.php";
  }

  ignore_user_abort( true );

  if ( ob_get_level () == 0 )
  {
    ob_start ();
  }

  ini_set('display_errors', ( String ) $_SERVER[ "DISPLAY_ERROS" ] );
  error_reporting( E_ALL ^ E_NOTICE );

  if ( !trim ( $_SERVER[ "REQUEST_SCHEME" ] ) )
  {
    $_SERVER[ "REQUEST_SCHEME" ] =  "http";
  }

  define ( "timezone", "Europe/London" );
  date_default_timezone_set ( $_SERVER[ "timezone" ] );


  include_once( "./class/mongodb/db.class.php" );
  include_once( "./class/mongodb/normalize.class.php" );
  include_once( "./class/Crypt/Crypt.class.php" );


  //->    include_once ( "./RulesData/GoogleplacesphotosData.class.php" );

  //->    use Aws\S3\S3Client;

  //instantiate the class
  //->    $s3 = new S3 ( $_SERVER[ "AWS_ACCESS_KEY_ID" ] , $_SERVER[ "AWS_SECRET_KEY" ] );

  new Crypt();
  
  $MongoDbVgObj                  =  new db ();
  $pageTotalVgObj                =  0;
  $pageLimitVgUInt                =  null;
  $pageOffsetVgUInt               =  null;
  $orderByVgStr                  =  null;
  $sortVgStr                     =  null;
  $pageNextVgUInt                 =  null;
  $pagePreviousVgUInt             =  null;
  $pageNextVgStr                 =  null;
  $actionVgStr                   =  null;
  $idByUrlVgArr                  =  array ();
  $moduleVgStr                   =  null;
  $controllerVgStr               =  null;
  $headerActionVgArr             =  array ();
  $sessionVgArr          =  array ();
  $pagePreviousQueryVgStr = null;

  global $MongoDbVgObj;
  global $pageTotalVgObj;
  global $pageLimitVgUInt;
  global $pageOffsetVgUInt;
  global $orderByVgStr;
  global $sortVgStr;
  global $pageNextVgUInt;
  global $pagePreviousVgUInt;
  global $pageNextVgStr;
  global $actionVgStr;
  global $idByUrlVgArr;
  global $moduleVgStr;
  global $controllerVgStr;
  global $headerActionVgArr;
  global $sessionVgArr;
  global $pagePreviousQueryVgStr;

  $MongoDbVgObj->connect ();
  $MongoDbVgObj->setDataBase();

  try
  {
    if ( isset ( $_REQUEST[ "orderBy" ] ) )
    {
      $orderByVgStr    =  $_REQUEST[ "orderBy" ];
      unset ( $_REQUEST[ "orderBy" ] );
    }

    if ( isset ( $_REQUEST[ "sort" ] ) )
    {
      $sortVgStr    =  $_REQUEST[ "sort" ];
      unset ( $_REQUEST[ "sort" ] );
    }

    if ( !isset ( $_REQUEST[ "limit" ] ) )
    {
      $pageLimitVgUInt    =  $_SERVER[ "paginationOffset" ];
    }
    else
    {
      $pageLimitVgUInt    =  (INT) $_REQUEST[ "limit" ];
      unset ( $_REQUEST[ "limit" ] );
    }

    if ( !isset ( $_REQUEST[ "offset" ] ) )
    {
      $pageOffsetVgUInt   =  0;
    }
    else
    {
      $pageOffsetVgUInt   =  $_SERVER[ "offset" ];
      unset ( $_REQUEST[ "offset" ] );
    }

    if ( !is_null ( $_REQUEST[ "cy" ] ) )
    {
      $pagePreviousQueryVgStr =  $_REQUEST[ "cy" ];
    }

    // Divide as vari�veis passadas por URL
    $dataByUrlVgArr        =  explode ( "/", @$_SERVER[ "REDIRECT_URL" ] );

    foreach ( $dataByUrlVgArr as $dataUrlVgStr )
    {
      if ( preg_match ( "/^[0-9a-fA-F]{24}$/si", $dataUrlVgStr ) )
      {
        $idByUrlVgArr[]  =  $dataUrlVgStr;
      }
    }

    if ( !is_array ( $sessionVgArr[ "Configuration" ] ) )
    {
      $sessionVgArr[ "Configuration" ] = array ();
    }

    if ( ( is_numeric ( $dataByUrlVgArr[ CONTROLLER_URL_UINT ] ) ) || ( preg_match ( "/^[0-9a-fA-F]{24}$/si", $dataByUrlVgArr[ CONTROLLER_URL_UINT ] ) ) )
    {
      $vgaArguments    =  array_slice ( $dataByUrlVgArr, CONTROLLER_URL_UINT );
      unset ( $dataByUrlVgArr[ CONTROLLER_URL_UINT ] );
    }

    else if ( isset ( $dataByUrlVgArr[ CONTROLLER_URL_UINT ] ) )
    {
      $vgaArguments    =  array_slice ( $dataByUrlVgArr, CONTROLLER_URL_UINT + 1 );
    }

    else
    {
      $vgaArguments    =  array ();
    }

    if ( strtolower ( $dataByUrlVgArr[ MODULE_URL_UINT ] ) == "favicon.ico" )
    {
      header( "Content-Type: image/png" );
      readfile ( "./Favicon.png" );
      die ();
    }

    if ( !$dataByUrlVgArr[ MODULE_URL_UINT ] )
    {
      throw new Exception ( "You don't have permission to access this server" );
    }

    if ( !is_file ( "./userClass/{$dataByUrlVgArr[ CLASS_URL_UINT ]}/{$dataByUrlVgArr[ MODULE_URL_UINT ]}.class.php" ) )
    {
      throw new Exception ( "The class file '{$dataByUrlVgArr[ CLASS_URL_UINT ]}/{$dataByUrlVgArr[ MODULE_URL_UINT ]}.class.php' was not found in module directory." );
    }

    include_once ( "./userClass/{$dataByUrlVgArr[ CLASS_URL_UINT ]}/{$dataByUrlVgArr[ MODULE_URL_UINT ]}.class.php" );

    $instanceClassVgObj = new $dataByUrlVgArr[ MODULE_URL_UINT ]();

    $parameterVgArr    =  $dataByUrlVgArr;
    unset ( $parameterVgArr[ 0 ], $parameterVgArr[ 1 ], $parameterVgArr[ 2 ], $parameterVgArr[ CLASS_URL_UINT ], $parameterVgArr[ MODULE_URL_UINT ], $parameterVgArr[ CONTROLLER_URL_UINT ] );
    $parameterVgArr = array_merge( $parameterVgArr, array() );

    if ( ( !$dataByUrlVgArr[ CONTROLLER_URL_UINT ] ) || ( is_numeric ( $dataByUrlVgArr[ CONTROLLER_URL_UINT ] ) ) )
    {
      $vgaRawData = getRawData();

      if ( isset ( $vgaRawData[ "id" ] ) )
      {
        $idByUrlVgArr[] = $vgaRawData[ "id" ];
      }

      else if ( isset ( $_REQUEST[ "id" ] ) )
      {
        $idByUrlVgArr[] = $_REQUEST[ "id" ];
      }

      if ( isset ( $vgaRawData[ "controller" ] ) )
      {
        $controllerVgStr =  $vgaRawData[ "controller" ];
      }

      else if ( isset ( $_REQUEST[ "controller" ] ) )
      {
        $controllerVgStr =  $_REQUEST[ "controller" ];
      }

      else
      {
        $controllerVgStr = null;
      }

      if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "delete" )
      {
        $actionVgStr = "delete";
        $_REQUEST[ 'REQUEST_METHOD' ] = "delete";
        $dataByUrlVgArr[ CONTROLLER_URL_UINT ] = "Delete";

        if ( $controllerVgStr == null )
        {
          $controllerVgStr = "Delete";
        }

        translateMethod();
      }

      else if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "put" )
      {
        $fileGetInputVgStr = file_get_contents( "php://input" );
        $_REQUEST = array_merge( $_REQUEST, ( array ) json_decode( $fileGetInputVgStr ) );
        $actionVgStr = "put";
        $_REQUEST[ 'REQUEST_METHOD' ] = "put";

        if ( $controllerVgStr == null )
        {
          $controllerVgStr = "Update";
        }

        translateMethod();

        $dataByUrlVgArr[ CONTROLLER_URL_UINT ]    =  $controllerVgStr;
      }

      else if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "post" ) //create
      {
        $actionVgStr = "post";
        $_REQUEST[ 'REQUEST_METHOD' ] = "post";

        if ( $controllerVgStr == null )
        {
          $controllerVgStr = "Create";
        }

        translateMethod();

        $dataByUrlVgArr[ CONTROLLER_URL_UINT ] = $controllerVgStr;
      }

      else if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "get" ) //show
      {
        $actionVgStr = "get";
        $_REQUEST[ 'REQUEST_METHOD' ] = "get";

        if ( $controllerVgStr == null )
        {
          $controllerVgStr = "Show";
        }

        translateMethod();

        $dataByUrlVgArr[ CONTROLLER_URL_UINT ] = $controllerVgStr;
      }

      else
      {
        $actionVgStr = "";

        if ( $controllerVgStr == null )
        {
          $controllerVgStr = "Show";
        }

        translateMethod();

        $dataByUrlVgArr[ CONTROLLER_URL_UINT ] = $controllerVgStr;
      }
    }

    else
    {
      $_REQUEST[ "REQUEST_METHOD" ] = $dataByUrlVgArr[ CONTROLLER_URL_UINT ];
    }

    if( !method_exists( $instanceClassVgObj, getNamesOfPublicMethods ) ){
      throw new Exception( "You must create one function named 'getNamesOfPublicMethods' and this function must return one array of string with the name of public functions into you class" );
    }

    if( !in_array( $dataByUrlVgArr[ CONTROLLER_URL_UINT ], $instanceClassVgObj->getNamesOfPublicMethods() ) )
    {
      throw new Exception( "The module '{$dataByUrlVgArr[ MODULE_URL_UINT ]}' was registered, but the method '{$dataByUrlVgArr[ CONTROLLER_URL_UINT ]}' was not found in class file." );
    }

    $moduleVgStr =  $dataByUrlVgArr[ MODULE_URL_UINT ];
    $controllerVgStr =  $dataByUrlVgArr[ CONTROLLER_URL_UINT ];

    if ( !method_exists ( $instanceClassVgObj, ucfirst( $dataByUrlVgArr[ CONTROLLER_URL_UINT ] ) ) )
    {
      throw new Exception( "The module '{$dataByUrlVgArr[ MODULE_URL_UINT ]}' was registered, but the method '{$dataByUrlVgArr[ CONTROLLER_URL_UINT ]}' was not found in class file." );
    }

    else
    {
      if ( isset ( $HTTP_RAW_POST_DATA ) )
      {
        $_REQUEST = array_merge ( $_REQUEST, ( array ) json_decode ( $HTTP_RAW_POST_DATA ) );
      }
  
      //Isto é devido a um bug
      foreach ( $_REQUEST as $vlsKey => $vlxValue )
      {
        if ( $vlxValue === true )
        {
          $_REQUEST[ $vlsKey ] = 1;
        }
    
        else if ( $vlxValue === false )
        {
          $_REQUEST[ $vlsKey ] = 0;
        }
      }

      /*$vlbSecurity = $instanceClassVgObj->testMethodSecurity ( $_REQUEST, $vgaArguments );

      if ( !$vlbSecurity )
      {
        throw new Exception ();
      }*/

      $returnVgArr = $instanceClassVgObj->$dataByUrlVgArr[ CONTROLLER_URL_UINT ]( $_REQUEST, $vgaArguments );

      if ( $pageOffsetVgUInt < 0 )
      {
        $pageOffsetVgUInt = 0;
      }

      $_REQUEST[ "limit" ]  =  $pageLimitVgUInt;
      $_REQUEST[ "offset" ] =  $pageOffsetVgUInt + $pageLimitVgUInt;
      $_REQUEST[ "cy" ]     =  $pagePreviousQueryVgStr;

      $cryptQueryGStr     =  Crypt::encrypt ( $_REQUEST );
      //$cryptQueryGStr     =  Query::make( $pagePreviousQueryVgStr );

      $_REQUEST[ "offset" ] =  $pageOffsetVgUInt - $pageLimitVgUInt;

      $returnVgArr   =  array   (
        "meta"       => array (
          "limit"       =>  ( INT ) $pageLimitVgUInt,
          "next"        =>  ( $pageLimitVgUInt + $pageOffsetVgUInt >= $pageTotalVgObj ) ? null : $_SERVER["REDIRECT_URL"] . "?query={$cryptQueryGStr}",
          "offset"      =>  ( INT ) $pageOffsetVgUInt,
          "previous"    =>  ( !$pageOffsetVgUInt ) ? null : "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REDIRECT_URL"] . "?query={$vlsQueryPrevious}",
          "total_count" =>  ( INT ) $pageTotalVgObj,
          "success"     =>  true,
          "action"      =>  $headerActionVgArr,
          "error"       =>  array (Crypt::decrypt($cryptQueryGStr))
        ),
        "objects"    =>  $returnVgArr
      );
      $outputVgStr   =  json_encode ( $returnVgArr );
      $outputVgStr   =  str_replace ( "\\/", "/", $outputVgStr );

      header("Content-Type: application/json");
      header("Access-Control-Allow-Origin: *");
      header('Access-Control-Allow-Methods: GET');

      header('Expires: Mon, 20 Dec 1998 01:00:00 GMT');
      header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
      header('Cache-Control: no-cache, must-revalidate');
      header('Pragma: no-cache');

      header("Content-Length: " . strlen( $outputVgStr ), true);
      print $outputVgStr;

      ob_flush();
      flush();
      ob_end_flush();
    }
  }

  catch ( Exception $eventVaObj )
  {
    header("Content-Type: application/json");
  
    header('Expires: Mon, 20 Dec 1998 01:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    if ( is_array ( $errorVgArr ) )
    {
      global $serverResponseVgX;
      array_push ( $errorVgArr, $serverResponseVgX );
      array_push ( $errorVgArr, $eventVaObj->getMessage() );
    }

    else
    {
      global $serverResponseVgX;

      $errorVgArr = json_decode ( $eventVaObj->getMessage() );
      if ( !is_array ( $errorVgArr ) )
      {
        $errorVgArr = array ( $eventVaObj->getMessage() );
      }

      if ( !is_null ( $serverResponseVgX ) )
      {
        array_push ( $errorVgArr, $serverResponseVgX );
      }
    }

    foreach ( $errorVgArr as $errorKeyVgUInt => $errorValueVgX )
    {
      $tmpVgStr = json_decode ( $errorValueVgX, true );

      if ( !is_null ( $tmpVgStr ) )
      {
        $errorVgArr[ $errorKeyVgUInt ] = $tmpVgStr;
      }
    }

    $messageVlX = $eventVaObj->getMessage();
    if ( preg_match ( "@message.*?data.*?global@", $messageVlX ) )
    {
      $errorVgArr[] = json_decode ( $messageVlX, true );
    }

    $returnVgArr = array (
      "meta" => array (
        "limit"       =>  ( INT ) $pageLimitVgUInt,
        "next"        =>  null,
        "offset"      =>  ( INT ) $pageOffsetVgUInt,
        "previous"    =>  null,
        "total_count" =>  0,
        "success"     =>  false,
        "error"       =>  $errorVgArr
      ),
      "objects"    =>  array ()
    );

    print json_encode ( $returnVgArr );
  }

  function getRawData ()
  {
    global $HTTP_RAW_POST_DATA;

    if ( isset ( $HTTP_RAW_POST_DATA ) )
    {
      return array_merge ( $_REQUEST, ( array ) json_decode ( $HTTP_RAW_POST_DATA ) );
    }

    return array ();
  }

  function translateMethod ()
  {
    global $controllerVgStr;

    switch ( ucfirst ( $controllerVgStr ) )
    {
      case "Update": $_REQUEST[ 'REQUEST_METHOD' ] = "put";
        break;

      case "Create": $_REQUEST[ 'REQUEST_METHOD' ] = "post";
        break;

      case "Show":   $_REQUEST[ 'REQUEST_METHOD' ] = "get";
        break;

      case "Delete": $_REQUEST[ 'REQUEST_METHOD' ] = "delete";
        break;
    }
  }