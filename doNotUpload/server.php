<?php
  
  $_SERVER[ "GEOINFO_DISPLAY_ERROS" ] = 1;

  // on/off para definir o formato da saída como json ou string de teste
  $_SERVER[ "json_output" ] = "on";
  $_SERVER[ "html_tag_pre" ] = "off";
  $_SERVER[ "disable_login_needed" ] = "off";

  $_SERVER[ "GEOINFO_PAGINATION_OFFSET" ] = 20;


  $_SERVER[ "database_user" ] = null;
  $_SERVER[ "database_password" ] = null;
  $_SERVER[ "database_host" ] = "";
  $_SERVER[ "server_in_test" ] = true;

  $_SERVER[ "timezone" ] = "Europe/London";
  $_SERVER[ "datetime_format" ] = "Y-m-d H:i:s";
  $_SERVER[ "date_format" ] = "Y-m-d";
  $_SERVER[ "default_date" ] = "2222-22-22 22:22:22";
  $_SERVER[ "error_header" ] = "HTTP/1.0 503 Not Implemented";
  $_SERVER[ "server_address" ] = "";

  $_SERVER[ "place_average_label" ] = "General";

  // lista de todos os módulos abertos, ou seja, os sem necessidade de login
  $_SERVER[ "login_module_unsafe_list" ] = "home,login,log";
  $_SERVER[ "integer_greater_32bits" ] = "facebookid,twitterid,idfacebookowner,idtwitterowner";

  $_SERVER[ "NextPrevPageExpirationInterval" ] = 7200;

  $_SERVER[ "GEOINFO_CRYPT_KEY" ] = "qazxswedcvfrtgbnhyujmikolp123456";

  $_SERVER[ "GEOINFO_MONGO_CONNECTION_STRING" ]   = null;
  $_SERVER[ "GEOINFO_MONGO_OPTIONS_ARRAY" ]       = array("socketTimeoutMS" => "90000");
  $_SERVER[ "GEOINFO_MONGO_DRIVE_OPTIONS_ARRAY" ] = array();
  $_SERVER[ "GEOINFO_MONGO_DATABASE_STRING" ]            = "curriculo_2";