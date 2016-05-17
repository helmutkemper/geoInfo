<?php

  //t2.medium
/*
  $c = new MongoClient();
  $db = $c->qconsp;
  $fs = $db->setupFill;

  $r = fopen("./fillSetup.json","w");

  $cl = $fs->find();
  foreach( $cl as $l ){
    $l = json_encode($l);
    $l = str_replace("\n","\\n",$l);
    $l = str_replace("\r","\\r",$l);
    fwrite($r,"{$l}\r\n");
    //print "\$collectionFillSetup->insert(\"json_decode({$l})\");\r\n<br>";
  }
  fclose($r);
*/


  //?start_debug=1&debug_host=127.0.0.1&debug_port=10137

  session_start ();

  define( "CLASS_URL_UINT", 0 );
  define( "MODULE_URL_UINT", 1 );
  define( "CONTROLLER_URL_UINT", 2 );

  define ( "TIMEZONE_DEFAULT", "Europe/London" );

  /*
   * $_SERVER[ "GEOINFO_TIMEZONE_DEFAULT" ] determina a time_zone. "Europe/London" facilita o cálculo de tempo.
   * $_SERVER[ "GEOINFO_PAGINATION_OFFSET" ] limite da itens por página. 20 por padrão.
   *
   */

  if ( is_file ( "./doNotUpload/server.php" ) )
  {
    include_once "./doNotUpload/server.php";
  }

  ignore_user_abort( true );

  if ( ob_get_level () == 0 )
  {
    ob_start ();
  }

  ini_set('display_errors', ( String ) $_SERVER[ "GEOINFO_DISPLAY_ERROS" ] );
  error_reporting( E_ALL ^ E_NOTICE );

  if ( !trim ( $_SERVER[ "REQUEST_SCHEME" ] ) )
  {
    $_SERVER[ "REQUEST_SCHEME" ] =  "http";
  }

  if( isset( $_SERVER[ "GEOINFO_TIMEZONE_DEFAULT" ] ) ){
    date_default_timezone_set ( $_SERVER[ "GEOINFO_TIMEZONE_DEFAULT" ] );
  }
  else{
    date_default_timezone_set ( TIMEZONE_DEFAULT );
  }

  include_once( "./class/mongodb/db.class.php" );
  include_once( "./class/mongodb/normalize.class.php" );
  include_once( "./class/crypt/crypt.class.php" );
  include_once( "./class/token/Token.class.php" );
  include_once( "./class/util/util.class.php" );
  include_once( "./class/util/utilGeo.class.php" );

  include_once "./class/import/osmXmlToMongoDbSupport.class.php";
  include_once "./class/import/osmXml.class.php";

  include_once "./class/util/size.class.php";

  //->    use Aws\S3\S3Client;

  //instantiate the class
  //->    $s3 = new S3 ( $_SERVER[ "AWS_ACCESS_KEY_ID" ] , $_SERVER[ "AWS_SECRET_KEY" ] );

  new Crypt();

  $outputTypeGStr = "json_mobile";

  $pageTotalGObj         =  0;
  $pageLimitGUInt        =  null;
  $pageOffsetGUInt       =  null;
  $orderByGStr           =  null;
  $sortGStr              =  null;
  $pageNextGUInt         =  null;
  $pagePreviousGUInt     =  null;
  $pageNextGStr          =  null;
  $actionGStr            =  null;
  $idByUrlGArr           =  array ();
  $moduleGStr            =  null;
  $controllerGStr        =  null;
  $headerActionGArr      =  array ();
  $sessionGArr           =  array ();
  $pagePreviousQueryGStr =  null;
  $debugEnableGBoo       =  false;
  $debugDataGArr         =  array();
  $errorGArr             =  array();
  $processEndGBoo      =  false;

  global $outputTypeGStr;
  global $pageTotalGObj;
  global $pageLimitGUInt;
  global $pageOffsetGUInt;
  global $pageNextLinkGStr;
  global $pagePreviousLinkGStr;
  global $orderByGStr;
  global $sortGStr;
  global $pageNextGUInt;
  global $pagePreviousGUInt;
  global $pageNextGStr;
  global $actionGStr;
  global $idByUrlGArr;
  global $moduleGStr;
  global $controllerGStr;
  global $headerActionGArr;
  global $sessionGArr;
  global $pagePreviousQueryGStr;
  global $debugEnableGBoo;
  global $debugDataGArr;
  global $errorGArr;
  global $processEndGBoo;

  try
  {
    if ( isset ( $_REQUEST[ "orderBy" ] ) )
    {
      $orderByGStr    =  $_REQUEST[ "orderBy" ];
      unset ( $_REQUEST[ "orderBy" ] );
    }

    if ( isset ( $_REQUEST[ "sort" ] ) )
    {
      $sortGStr    =  $_REQUEST[ "sort" ];
      unset ( $_REQUEST[ "sort" ] );
    }

    if ( !isset ( $_REQUEST[ "limit" ] ) )
    {
      $pageLimitGUInt    =  $_SERVER[ "GEOINFO_PAGINATION_OFFSET" ];
    }
    else
    {
      $pageLimitGUInt    =  (INT) $_REQUEST[ "limit" ];
      unset ( $_REQUEST[ "limit" ] );
    }

    if ( !isset ( $_REQUEST[ "offset" ] ) )
    {
      $pageOffsetGUInt   =  0;
    }
    else
    {
      $pageOffsetGUInt   =  $_REQUEST[ "offset" ];
      unset ( $_REQUEST[ "offset" ] );
    }

    if ( !is_null ( $_REQUEST[ "cy" ] ) )
    {
      $pagePreviousQueryGStr =  $_REQUEST[ "cy" ];
    }

    // Divide as variáveis passadas por URL
    $dataByUrlGArr        =  explode ( "/", @$_SERVER[ "REDIRECT_URL" ] );

    foreach ( $dataByUrlGArr as $dataUrlKeyGStr => $dataUrlGStr ){
      unset( $dataByUrlGArr[ $dataUrlKeyGStr ] );
      if( $dataUrlGStr === "" ){
        break;
      }
    }
    $dataByUrlGArr = array_merge( $dataByUrlGArr, array() );

    foreach ( $dataByUrlGArr as $dataUrlGStr )
    {
      if ( preg_match ( "/^[0-9a-fA-F]{24}$/si", $dataUrlGStr ) )
      {
        $idByUrlGArr[]  =  $dataUrlGStr;
      }
    }

    if ( !is_array ( $sessionGArr[ "Configuration" ] ) )
    {
      $sessionGArr[ "Configuration" ] = array ();
    }

    if ( ( is_numeric ( $dataByUrlGArr[ CONTROLLER_URL_UINT ] ) ) || ( preg_match ( "/^[0-9a-fA-F]{24}$/si", $dataByUrlGArr[ CONTROLLER_URL_UINT ] ) ) )
    {
      $vgaArguments    =  array_slice ( $dataByUrlGArr, CONTROLLER_URL_UINT );
      unset ( $dataByUrlGArr[ CONTROLLER_URL_UINT ] );
    }

    else if ( isset ( $dataByUrlGArr[ CONTROLLER_URL_UINT ] ) )
    {
      $vgaArguments    =  array_slice ( $dataByUrlGArr, CONTROLLER_URL_UINT + 1 );
    }

    else
    {
      $vgaArguments    =  array ();
    }

    //if ( strtolower ( $dataByUrlGArr[ MODULE_URL_UINT ] ) == "favicon.ico" )
    if ( strtolower ( $dataByUrlGArr[ CLASS_URL_UINT ] ) == "favicon.ico" )
    {
      header( "Content-Type: image/png" );
      readfile ( "./Favicon.png" );
      die ();
    }

    if ( strtolower ( $dataByUrlGArr[ CLASS_URL_UINT ] ) == "open" )
    {
      if( !is_file( "./userClass" . $_SERVER["REQUEST_URI"] ) ){
        if( is_file( "./userClass" . $_SERVER["REQUEST_URI"] . "index.htm" ) ){
          readfile( "./userClass" . $_SERVER["REQUEST_URI"] . "index.htm" );
          die();
        }
        else if( is_file( "./userClass" . $_SERVER["REQUEST_URI"] . "/index.htm" ) ){
          header("Location: " . $_SERVER["REQUEST_URI"] . "/");
          die();
        }
        else if( is_file( "./userClass" . $_SERVER["REQUEST_URI"] . "index.html" ) ){
          readfile( "./userClass" . $_SERVER["REQUEST_URI"] . "index.html" );
          die();
        }
        else if( is_file( "./userClass" . $_SERVER["REQUEST_URI"] . "/index.html" ) ){
          header("Location: " . $_SERVER["REQUEST_URI"] . "/");
          die();
        }
      }

      readfile( "./userClass" . $_SERVER["REQUEST_URI"] );
      die();
    }

    if ( !$dataByUrlGArr[ MODULE_URL_UINT ] )
    {
      throw new Exception ( "You don't have permission to access this server" );
    }

    if ( !is_file ( "./userClass/{$dataByUrlGArr[ CLASS_URL_UINT ]}/{$dataByUrlGArr[ MODULE_URL_UINT ]}.class.php" ) )
    {
      throw new Exception ( "The class file '{$dataByUrlGArr[ CLASS_URL_UINT ]}/{$dataByUrlGArr[ MODULE_URL_UINT ]}.class.php' was not found in module directory." );
    }

    include_once ( "./userClass/{$dataByUrlGArr[ CLASS_URL_UINT ]}/{$dataByUrlGArr[ MODULE_URL_UINT ]}.class.php" );

    $instanceClassGObj = new $dataByUrlGArr[ MODULE_URL_UINT ]();

    $parameterGArr    =  $dataByUrlGArr;
    unset ( $parameterGArr[ 0 ], $parameterGArr[ 1 ], $parameterGArr[ 2 ], $parameterGArr[ CLASS_URL_UINT ], $parameterGArr[ MODULE_URL_UINT ], $parameterGArr[ CONTROLLER_URL_UINT ] );
    $parameterGArr = array_merge( $parameterGArr, array() );
    $parameterGArr[ ( count( $parameterGArr ) - 1 ) ] = preg_replace( "/(.*?)(\?[a-zA-Z_][a-zA-Z0-9_]*=.*)/si", "$1", $parameterGArr[ ( count( $parameterGArr ) - 1 ) ] );

    if ( ( !$dataByUrlGArr[ CONTROLLER_URL_UINT ] ) || ( is_numeric ( $dataByUrlGArr[ CONTROLLER_URL_UINT ] ) ) )
    {
      $vgaRawData = getRawData();

      if ( isset ( $vgaRawData[ "id" ] ) )
      {
        $idByUrlGArr[] = $vgaRawData[ "id" ];
      }

      else if ( isset ( $_REQUEST[ "id" ] ) )
      {
        $idByUrlGArr[] = $_REQUEST[ "id" ];
      }

      if ( isset ( $vgaRawData[ "controller" ] ) )
      {
        $controllerGStr =  $vgaRawData[ "controller" ];
      }

      else if ( isset ( $_REQUEST[ "controller" ] ) )
      {
        $controllerGStr =  $_REQUEST[ "controller" ];
      }

      else
      {
        $controllerGStr = null;
      }

      if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "delete" )
      {
        $actionGStr = "delete";
        $_REQUEST[ 'REQUEST_METHOD' ] = "delete";
        $dataByUrlGArr[ CONTROLLER_URL_UINT ] = "Delete";

        if ( $controllerGStr == null )
        {
          $controllerGStr = "Delete";
        }

        translateMethod();
      }

      else if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "put" )
      {
        $fileGetInputGStr = file_get_contents( "php://input" );
        $_REQUEST = array_merge( $_REQUEST, ( array ) json_decode( $fileGetInputGStr ) );
        $actionGStr = "put";
        $_REQUEST[ 'REQUEST_METHOD' ] = "put";

        if ( $controllerGStr == null )
        {
          $controllerGStr = "Update";
        }

        translateMethod();

        $dataByUrlGArr[ CONTROLLER_URL_UINT ]    =  $controllerGStr;
      }

      else if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "post" ) //create
      {
        $actionGStr = "post";
        $_REQUEST[ 'REQUEST_METHOD' ] = "post";

        if ( $controllerGStr == null )
        {
          $controllerGStr = "Create";
        }

        translateMethod();

        $dataByUrlGArr[ CONTROLLER_URL_UINT ] = $controllerGStr;
      }

      else if ( strtolower ( $_SERVER['REQUEST_METHOD'] ) == "get" ) //show
      {
        $actionGStr = "get";
        $_REQUEST[ 'REQUEST_METHOD' ] = "get";

        if ( $controllerGStr == null )
        {
          $controllerGStr = "Show";
        }

        translateMethod();

        $dataByUrlGArr[ CONTROLLER_URL_UINT ] = $controllerGStr;
      }

      else
      {
        $actionGStr = "";

        if ( $controllerGStr == null )
        {
          $controllerGStr = "Show";
        }

        translateMethod();

        $dataByUrlGArr[ CONTROLLER_URL_UINT ] = $controllerGStr;
      }
    }

    else
    {
      $_REQUEST[ "REQUEST_METHOD" ] = $dataByUrlGArr[ CONTROLLER_URL_UINT ];
    }

    if( !method_exists( $instanceClassGObj, "getNamesOfPublicMethods" ) ){
      throw new Exception( "You must create one function named 'getNamesOfPublicMethods' and this function must return one array of string with the name of public functions into you class" );
    }

    if( !in_array( $dataByUrlGArr[ CONTROLLER_URL_UINT ], $instanceClassGObj->getNamesOfPublicMethods() ) )
    {
      throw new Exception( "The module '{$dataByUrlGArr[ MODULE_URL_UINT ]}' was registered, but the method '{$dataByUrlGArr[ CONTROLLER_URL_UINT ]}' was not found in class file." );
    }

    $moduleGStr =  $dataByUrlGArr[ MODULE_URL_UINT ];
    $controllerGStr =  $dataByUrlGArr[ CONTROLLER_URL_UINT ];

    if ( !method_exists ( $instanceClassGObj, ucfirst( $dataByUrlGArr[ CONTROLLER_URL_UINT ] ) ) )
    {
      throw new Exception( "The module '{$dataByUrlGArr[ MODULE_URL_UINT ]}' was registered, but the method '{$dataByUrlGArr[ CONTROLLER_URL_UINT ]}' was not found in class file." );
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

      $returnGArr = $instanceClassGObj->$dataByUrlGArr[ CONTROLLER_URL_UINT ]( $_REQUEST, $vgaArguments );

      if ( $pageOffsetGUInt < 0 )
      {
        $pageOffsetGUInt = 0;
      }

      $_REQUEST[ "limit" ]  =  $pageLimitGUInt;
      $_REQUEST[ "offset" ] =  $pageOffsetGUInt + $pageLimitGUInt;
      $_REQUEST[ "cy" ]     =  $pagePreviousQueryGStr;

      //$cryptQueryGStr     =  Crypt::encrypt ( $_REQUEST );
      $cryptQueryGStr     =  Crypt::encrypt ( array( "cy" => $pagePreviousQueryGStr, "limit" => $_REQUEST[ "limit" ], "offset" => $_REQUEST[ "offset" ] ) );

      $_REQUEST[ "offset" ] =  $pageOffsetGUInt - $pageLimitGUInt;

      $_SERVER[ "REQUEST_URI" ] = preg_replace( "/(.*?)\?.*/", "$1", $_SERVER[ "REQUEST_URI" ] );

      if( is_null( $pageNextLinkGStr ) ){
        if( $pageLimitGUInt + $pageOffsetGUInt >= $pageTotalGObj ){
          $pageNextLinkGStr = null;
        }
        else
        {
          $pageNextLinkGStr = $_SERVER[ "REQUEST_SCHEME" ] . "://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?query={$cryptQueryGStr}";
        }
      }

      //( !$pageOffsetGUInt ) ? null : $_REQUEST[ "REQUEST_URI" ] . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . "?query={$vlsQueryPrevious}"

      if( $outputTypeGStr == "json_mobile" )
      {
        $returnGArr = array(
          "meta" => array(
            "limit" => ( INT )$pageLimitGUInt,
            "next" => $pageNextLinkGStr,
            "offset" => ( INT )$pageOffsetGUInt,
            "previous" => $pagePreviousLinkGStr,
            "total_count" => ( INT )$pageTotalGObj,
            "success" => true,
            "action" => $headerActionGArr,
            "error" => null,
            "processes_end" => $processEndGBoo
          ),
          "objects" => $returnGArr
        );

        if( $debugEnableGBoo == true ){
          $returnGArr[ "meta" ][ "debug" ] = $debugDataGArr;
        }

        $outputGStr = json_encode( $returnGArr );
        $outputGStr = str_replace( "\\/", "/", $outputGStr );

        header( "Content-Type: application/json" );
        header( "Access-Control-Allow-Origin: *" );
        header( 'Access-Control-Allow-Methods: GET' );

        header( 'Expires: Mon, 20 Dec 1998 01:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . 'GMT' );
        header( 'Cache-Control: no-cache, must-revalidate' );
        header( 'Pragma: no-cache' );

        header( "Content-Length: " . strlen( $outputGStr ), true );
      }
      else if( $outputTypeGStr == "json" )
      {
        //$returnGArr = array( "meta" => array( "limit" => ( INT )$pageLimitGUInt, "next" => ( $pageLimitGUInt + $pageOffsetGUInt >= $pageTotalGObj ) ? null : $_SERVER[ "REDIRECT_URL" ] . "?query={$cryptQueryGStr}", "offset" => ( INT )$pageOffsetGUInt, "previous" => ( !$pageOffsetGUInt ) ? null : "http://" . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REDIRECT_URL" ] . "?query={$vlsQueryPrevious}", "total_count" => ( INT )$pageTotalGObj, "success" => true, "action" => $headerActionGArr, "error" => array( Crypt::decrypt( $cryptQueryGStr ) ) ), "objects" => $returnGArr );
        $outputGStr = json_encode( $returnGArr );
        $outputGStr = str_replace( "\\/", "/", $outputGStr );

        header( "Content-Type: application/json" );
        header( "Access-Control-Allow-Origin: *" );
        header( 'Access-Control-Allow-Methods: GET' );

        header( 'Expires: Mon, 20 Dec 1998 01:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . 'GMT' );
        header( 'Cache-Control: no-cache, must-revalidate' );
        header( 'Pragma: no-cache' );

        header( "Content-Length: " . strlen( $outputGStr ), true );
      }
      else if( $outputTypeGStr == "csv" )
      {
        $outputGStr = "";
        foreach( $returnGArr as $returnValueLArr )
        {
          $outputGStr .= implode( ",", $returnValueLArr ) . "\r\n";
        }

        header( "Content-Type: application/csv" );
        header( "Access-Control-Allow-Origin: *" );
        header( 'Access-Control-Allow-Methods: GET' );

        header( 'Expires: Mon, 20 Dec 1998 01:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . 'GMT' );
        header( 'Cache-Control: no-cache, must-revalidate' );
        header( 'Pragma: no-cache' );

        header( "Content-Length: " . strlen( $outputGStr ), true );
      }

      print $outputGStr;

      ob_flush();
      flush();
      ob_end_flush();
    }
  }

  catch ( Exception $eventAObj )
  {
    header("Content-Type: application/json");
  
    header('Expires: Mon, 20 Dec 1998 01:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    if ( is_array ( $errorGArr ) )
    {
      array_push ( $errorGArr, $eventAObj->getMessage() );
    }

    else
    {
      $errorGArr = json_decode ( $eventAObj->getMessage() );
      if ( !is_array ( $errorGArr ) )
      {
        $errorGArr = array ( $eventAObj->getMessage() );
      }
    }

    foreach ( $errorGArr as $errorKeyGUInt => $errorValueGX )
    {
      $tmpGStr = json_decode ( $errorValueGX, true );

      if ( !is_null ( $tmpGStr ) )
      {
        $errorGArr[ $errorKeyGUInt ] = $tmpGStr;
      }
    }

    $messageLX = $eventAObj->getMessage();
    if ( preg_match ( "@message.*?data.*?global@", $messageLX ) )
    {
      $errorGArr[] = json_decode ( $messageLX, true );
    }

    $returnGArr = array (
      "meta"            => array (
        "limit"         =>  ( INT ) $pageLimitGUInt,
        "next"          =>  null,
        "offset"        =>  ( INT ) $pageOffsetGUInt,
        "previous"      =>  null,
        "total_count"   =>  0,
        "success"       =>  false,
        "error"         =>  $errorGArr,
        "action"        => $headerActionGArr,
        "processes_end" => $processEndGBoo
      ),
      "objects"    =>  array ()
    );

    print json_encode ( $returnGArr );
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
    global $controllerGStr;

    switch ( ucfirst ( $controllerGStr ) )
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