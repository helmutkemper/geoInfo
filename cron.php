<?php

  error_reporting( E_ALL ^ E_NOTICE );
  
  //chdir( "/var/app/current" );
  chdir( "/home/kemper/Dropbox/Sites/sites_atual/api" );

  if ( is_file ( "./doNotUpload/server.php" ) )
  {
    include_once "./doNotUpload/server.php";
  }

  ignore_user_abort( true );

  include_once( "./class/mongodb/db.class.php" );
  include_once( "./class/mongodb/normalize.class.php" );
  include_once( "./class/crypt/crypt.class.php" );
  include_once( "./class/token/Token.class.php" );
  include_once( "./class/util/util.class.php" );
  include_once( "./class/util/utilGeo.class.php" );
  
  include_once "./class/import/osmXmlToMongoDbSupport.class.php";
  include_once "./class/import/osmXml.class.php";
  include_once( "./class/cron/Cron.class.php" );

  include_once "./class/util/size.class.php";

  try{
    $cronLObj = new Cron();
  }
  catch( Exception $e ){
    die( $e->getMessage() );
  }