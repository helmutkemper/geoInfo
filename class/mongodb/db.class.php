<?php

  /**
   * @autor Helmut Kemper
   * @email helmut.kemper@gmail.com
   * @site http://www.kemper.com.br
   *
   * Class db
   *
   * Descrição:
   *
   * Classe de conexão ao banco de dados MongoDB.
   *
   * Perceba que as variáveis de conexão são capturadas do servidor.
   * $_SERVER[ "mongo_connection_string" ], $_SERVER[ "mongo_options_array" ],$_SERVER[ "mongo_drive_options_array" ]
   * $_SERVER[ "mongo_database" ]
   *
   * Licença:
   *
   * Este código é aberto e feito com o intuito de ajudar, porém, sem garantias de funcionar ou obrigação de suporte de
   * qualquer forma da minha parte.
   *
   * Você é livre para copiar e usar o código desse repositório conforme as suas necessidades e livre para lucrar com
   * ele, sem ter que me pagar royalties, desde que siga as regras abaixo:
   *
   * - Para usar este código, você se compromete a divulgar meu nome e trabalho como sendo o criador original da
   *   ferramenta de integração com o seu sistema de mapas no site da sua aplicação;
   * - Você se compromete a me enviar de forma documentada qualquer correção e/ou melhorias feitas no código para que eu
   *   decida quais das mesmas sejam adicionas ao projeto original de forma aberta a toda a comunidade, sem custos;
   * - Você se compromete a contribuir tecnicamente com a comunidade de desenvolvedores de forma gratuita;
   * - Você se compromete a me manter informado onde o código é usado e me enviar material de divulgação, para seja
   *   feita propaganda desse código e casos de sucesso;
   * - Você se compromete a não usar o código em aplicações que possam colocar a vida de pessoas em risco desnecessários
   *   e/ou aplicações militares sem autorização prévia da minha parte.
   */
  class db
  {
    /**
     * @var $connectionCObj Object de conexão da classe MongoClient.
     */
    public $connectionCObj;

    /**
     * @var $dataBaseCObj Object de conexão ao banco de dados.
     */
    public $dataBaseCObj;

    public $collectionCObj;

    /**
     * osmXmlToMongoDb constructor.
     */
    public function __construct()
    {
      
    }

    /**
     * Construtor da classe.
     */
    public function connect() {
      
      if( !is_array( $_SERVER[ "GEOINFO_MONGO_OPTIONS_ARRAY" ] ) ){
        $_SERVER[ "GEOINFO_MONGO_OPTIONS_ARRAY" ] = json_decode( $_SERVER[ "GEOINFO_MONGO_OPTIONS_ARRAY" ], true );
      }
      if( !is_array( $_SERVER[ "GEOINFO_MONGO_OPTIONS_ARRAY" ] ) ){
        $_SERVER[ "GEOINFO_MONGO_OPTIONS_ARRAY" ] = array();
      }
      
      if( !is_array( $_SERVER[ "GEOINFO_MONGO_DRIVE_OPTIONS_ARRAY" ] ) ){
        $_SERVER[ "GEOINFO_MONGO_DRIVE_OPTIONS_ARRAY" ] = json_decode( $_SERVER[ "GEOINFO_MONGO_DRIVE_OPTIONS_ARRAY" ], true );
      }
      if( !is_array( $_SERVER[ "GEOINFO_MONGO_DRIVE_OPTIONS_ARRAY" ] ) ){
        $_SERVER[ "GEOINFO_MONGO_DRIVE_OPTIONS_ARRAY" ] = array();
      }

      try{
        $this->connectionCObj = new MongoClient( $_SERVER[ "GEOINFO_MONGO_CONNECTION_STRING" ], $_SERVER[ "GEOINFO_MONGO_OPTIONS_ARRAY" ], $_SERVER[ "GEOINFO_MONGO_DRIVE_OPTIONS_ARRAY" ] );
        $this->setDataBase();
      }
      catch( MongoConnectionException $e ){
        throw new Exception( $e->getMessage() );
      }
      catch( Exception $e ){
        throw new Exception( $e->getMessage() );
      }
    }

    public function setDataBase( $dataBaseAStr = null ) {
      if( is_null( $dataBaseAStr ) ){
        $this->dataBaseCObj        = $this->connectionCObj->$_SERVER[ "GEOINFO_MONGO_DATABASE_STRING" ];
      }
      else{
        $this->dataBaseCObj        = $this->connectionCObj->$dataBaseAStr;
      }
    }

    public function setCollection( $collectionAStr ){
      $this->collectionCObj = $this->dataBaseCObj->$collectionAStr;
    }
  }