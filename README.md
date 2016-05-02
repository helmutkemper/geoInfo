geoInfo
---------

![geoInfo logo](./logo_1000.png)

Este projeto tem a finalidade de criar um servidor **PHP/MongoDB** completo, e de forma
automática, com informações geográficas coletadas do
[OpenStreetMaps](http://www.openstreetmap.org/).

Basicamente, são dois repositórios separados, o repositório de nome
[**aws**](https://github.com/helmutkemper/aws) contém as instruções de criação dos
servidores para os serviços da [Amazon Web Service](aws.amazon.com) de forma
automática, onde o script de criação vai criar uma máquina
[Elastic Beanstalk](http://docs.aws.amazon.com/elasticbeanstalk/latest/dg/Welcome.html),
instalar o [MongoDB](https://www.mongodb.org/), o driver Linux, fazer o download da
última versão do [mapa do Brasil](http://download.geofabrik.de/south-america/brazil.html)
e instalar a última versão [desse repositório](https://github.com/helmutkemper/geoInfo)
no servidor.

Quando o processo terminar, basta executar a página principal na raiz do servidor e
esperar algumas horas até que o processo de importação do XML do mapa, no formato
**OSM**, esteja completo, dependendo da máquina, de 3h a 4h.

Após o fim do processo, haverá uma **API** pronta para recuperar informações geográficas
especialmente pensada para aplicações mobile.

Para mais informações, veja o meu site pessoal [**http://www.kemper.com.br**](http://www.kemper.com.br)

Exemplo de como capturar todas as escolas dentro de uma determinada região:

```
http://www.dominio.com/nodes/nodes/get/tipo_1:valor_1;tipo_2:valor_2/coor. inferior/coor. esquerda/coor. superior/coor. direita
http://www.dominio.com/nodes/nodes/get/amenity:school/-8.1313/-34.9146/-8.1047/-34.8710
```

Após instalar o arquivo, acesse a página de importação em:
```
http://www.dominio.com/open/html/import/index.html
```