<?php


  $_SERVER[ "DISPLAY_ERROS" ] = 0;

  // on/off para definir o formato da saída como json ou string de teste
  $_SERVER[ "json_output" ] = "on";
  $_SERVER[ "html_tag_pre" ] = "off";
  $_SERVER[ "disable_login_needed" ] = "off";

  $_SERVER[ "paginationOffset" ] = 20;


  $_SERVER[ "database" ] = "qconsp";

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

  $_SERVER[ "crypt_key" ] = "qazxswedcvfrtgbnhyujmikolp123456";
























  $_SERVER[ "mongo_connection_string" ]   = null;
  $_SERVER[ "mongo_options_array" ]       = array("socketTimeoutMS" => "90000");
  $_SERVER[ "mongo_drive_options_array" ] = array();
  $_SERVER[ "mongo_database" ]            = "qconsp";