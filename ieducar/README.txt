// $Id$

CONTE�DO
--------

 * Requisitos
 * Instala��o
 * Documenta��o
 * Suporte t�cnico
 * Licen�a


REQUISITOS
----------

O i-Educar requer um servidor web, PHP 5.2, PostgreSQL 8.2 e a biblioteca PDFLib
(vers�o Lite ou Commercial). O servidor web Apache 2 � recomendado mas qualquer
outro com suporte a PHP pode ser utilizado.

A biblioteca PDFLib Lite tem algumas restri��es em sua utiliza��o. Consulte a
licen�a da biblioteca para ver se o seu uso n�o cair� na necessidade de adquirir
uma licen�a comercial:
http://www.pdflib.com/products/pdflib-family/pdflib-lite/pdflib-lite-licensing


INSTALA��O
----------

1. DOWNLOAD DO SOFTWARE

   Fa�a o download dos arquivos do sistema antes de prosseguir. A vers�o atual
   pode ser encontrada em:
   http://www.softwarepublico.gov.br/dotlrn/clubs/ieducar/file-storage/index?folder_id=10855442.
   Descompacte o pacote de sua prefer�ncia no diret�rio raiz do seu servidor web
   Apache.

      $ cd /var/www
      $ mkdir ieducar; cd ieducar
      $ tar -xzvf /caminho/pacotes/ieducar-X.X.X.tar.gz


2. CRIE O BANCO DE DADOS

   Crie o banco de dados ao qual o i-Educar usar� para armazenar todos os dados
   digitados atrav�s da interface web. Os passos descritos nessa se��o ir�o
   criar:

      * Um usu�rio ieducar no servidor PostgreSQL com a senha de acesso ieducar;
      * Um banco de dados ieducar.

   Observa��o: voc� pode usar o nome de usu�rio, banco de dados e senha que
   desejar. Esses s�o apenas nomes padr�es que a aplica��o usa para conectar-se
   ao banco.

   Fa�a login no servidor de banco de dados PostgreSQL com o cliente psql:

      $ su
      # su - postgres
      # psql

   Alternativamente, com o sudo:

      $ sudo -u postgres psql

   Crie o usu�rio de banco de dados que ser� utilizado pelo i-Educar:

      postgres=# CREATE ROLE ieducar;
      postgres=# ALTER ROLE ieducar WITH SUPERUSER INHERIT NOCREATEROLE \
         CREATEDB LOGIN PASSWORD 'ieducar';

   Crie o banco de dados:

      postgres=# CREATE DATABASE ieducar WITH TEMPLATE = template0 \
         OWNER = ieducar ENCODING = 'LATIN1';
      postgres=# \q

   Execute o arquivo ieducar.sql que vem no i-Educar. O diret�rio em que esse 
   arquivo reside � o misc/database.

      $ psql -d ieducar -f misc/database/ieducar.sql

   Aten��o: em algumas plataformas, o restore do banco pode acabar em um erro
   FATAL. Se isso acontecer, experimente fazer o restore no mesmo diret�rio em
   que se encontra o arquivo ieducar.sql.

   Novamente no psql, execute o seguinte comando para configurar o search_path:

      $ psql ieducar
      postgres=# ALTER DATABASE ieducar SET search_path TO "$user", public, \
        portal, cadastro, acesso, alimentos, consistenciacao, historico, \
        pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano;
      postgres=# \q;


3. EDITE O ARQUIVO DE CONFIGURA��O E CONCEDA PERMISS�ES DE ESCRITA

   O i-Educar armazena algumas configura��es necess�rias para a aplica��o em um
   arquivo chamado ieducar.ini (em configuration/), que possui uma sintaxe bem
   simples de entender. Caso tenha criado o banco de dados, nome de usu�rio ou
   senha com um valor diferente de ieducar, basta editar esse arquivo para que
   corresponda as suas escolhas:

      [production]
      ; Configura��es de banco de dados
      app.database.dbname   = ieducar
      app.database.username = ieducar
      app.database.hostname = localhost
      app.database.password = ieducar
      app.database.port     = 5432

   Exemplo: caso tenha nomeado seu banco de dados com ieducar_db, o usu�rio com
   ieducar_user e a senha com ieducar_pass, o ieducar.ini ficaria da seguinte
   forma:

      [production]
      ; Configura��es de banco de dados
      app.database.dbname   = ieducar_db
      app.database.username = ieducar_user
      app.database.hostname = localhost
      app.database.password = ieducar_pass
      app.database.port     = 5432

   Depois, conceda permiss�es de escrita nos diret�rios intranet/tmp e
   intranet/pdf. Uma forma pr�tica � dar permiss�o de escrita para o usu�rio
   dono do diret�rio e para usu�rios de um grupo. Nesse caso, mudaremos o grupo
   desses diret�rios para o grupo do usu�rio Apache.

      # chmod 775 intranet/tmp intranet/pdf
      # chgrp www-data intranet/tmp intranet/pdf

   Observa��o: www-data � o nome do grupo Apache padr�o em sistemas Debian.
   Em outros sistemas, esse nome pode ser httpd, apache ou _www. Substitua de
   acordo com o usado em seu sistema operacional.


4. CONFIGURE O APACHE OU CRIE UM VIRTUAL HOST

   A partir da vers�o 1.1.X, o i-Educar inclui, por padr�o, um arquivo chamado
   .htaccess no diret�rio raiz da aplica��o. Esse arquivo cont�m diretivas de
   configura��o do servidor Apache que tornam o i-Educar mais seguro.
   Al�m disso, esse arquivo configura o PHP corretamente para as necessidades
   da aplica��o.

   Para que esse arquivo seja executado a cada requisi��o, � necess�rio
   configurar o Apache para que este execute os arquivos .htaccess ou criar um
   Virtual Host. A primeira op��o requer a edi��o do arquivo
   /etc/apache2/site-available/default. A �nica diretiva a ser alterada �
   AllowOverride (linha 11) para All:

        9         <Directory /var/www/>
       10                 Options Indexes FollowSymLinks MultiViews
       11                 AllowOverride All
       12                 Order allow,deny
       13                 allow from all
       14         </Directory>

   Reinicie o servidor Apache:

      $ /etc/init.d/apache2 restart

   A segunda op��o requer a cria��o de um novo arquivo em
   /etc/apache2/sites-available/. Crie um arquivo chamado ieducar.local com o
   seguinte conte�do:

      <VirtualHost *:80>
        ServerName ieducar.local
        DocumentRoot /var/www/ieducar

        <Directory /var/www/ieducar>
          AllowOverride all
          Order deny,allow
          Allow from all
        </Directory>
      </VirtualHost>

   Edite o arquivo /etc/hosts (no Windows esse arquivo fica em
   C:\WINDOWS\system32\drivers\etc\hosts) e adicione a seguinte linha:

      127.0.0.1      ieducar.local

   Reinicie o servidor Apache:

      $ /etc/init.d/apache2 restart

   Pronto. Agora, acesse o endere�o http://ieducar.local em seu navegador.

   Aten��o: configurar o seu servidor Apache (seguindo uma das op��es
   apresentadas) � importante para a seguran�a da aplica��o. Assim, evita-se que
   arquivos importantes como o configuration/ieducar.ini e os relat�rios gerados
   pela aplica��o fiquem publicamente expostos para leitura atrav�s da Internet.


5. ACESSE A APLICA��O

   Abra o navegador de sua prefer�ncia e acesse o endere�o
   http://localhost/ieducar ou http://ieducar.local (caso tenha configurado um
   Virtual Host). Fa�a o login na aplica��o utilizando o usu�rio administrador.
   O login e senha para acesso s�o admin e admin, respectivamente.


6. CONFIGURE O PHP

   Esse passo � opcional caso tenha configurado o Apache (via AllowOverride ou
   VirtualHost). Edite o arquivo php.ini da seguinte forma:

   * memory_limit: altere para, no m�nimo, 32M (devido a gera��o de relat�rios
   consumir bastante mem�ria, pode ser necess�rio aumentar para uma quantidade
   maior em plataformas 64 bits);
      memory_limit = 32M

   * error_reporting: altere para E_ALL & ~E_NOTICE para evitar que avisos do
   n�vel E_NOTICE (comuns na vers�o atual), apare�am nas telas quebrando o
   layout do sistema. E_ERROR � o recomendado para ambientes de produ��o.
      error_reporting = E_ALL & ~E_NOTICE

   * display_errors: altere para Off em produ��o:
      display_errors = Off

   * short_open_tag: altere para On.
      short_open_tag = On

   Observa��o: a localiza��o do arquivo php.ini � diferente entre os sistemas
   operacionais. No Debian/Ubuntu, o padr�o � /etc/php5/apache2/php.ini. Para
   descobrir onde o arquivo fica em seu sistema operacional, acesse o endere�o
   http://localhost/ieducar/info.php e procure por Loaded Configuration File.

   Ap�s qualquer altera��o no arquivo php.ini, reinicie seu servidor web:

      # /etc/init.d/apache2 restart


7. FONTE

   * https://svn.softwarepublico.gov.br/trac/ieducar/wiki/Documentacao/1.1.X/Instalacao


DOCUMENTA��O
------------

A documenta��o oficial do i-Educar est� dispon�vel em wiki:
http://svn.softwarepublico.gov.br/trac/ieducar/wiki

Problemas comuns de instala��o podem ser encontrados no FAQ (perguntas
frequentes):
http://svn.softwarepublico.gov.br/trac/ieducar/wiki/Documentacao/FAQ/1.X


SUPORTE T�CNICO
---------------

Suporte t�cnico pode ser encontrado nos f�runs da comunidade i-Educar no Portal
do Software P�blico Brasileiro (requer cadastro):
http://www.softwarepublico.gov.br/dotlrn/clubs/ieducar


LICEN�A
-------

O i-Educar � um Software P�blico Brasileiro (SPB), livre e licenciado pela
Creative Commons Licen�a P�blica Geral vers�o 2 traduzida (CC GNU/GPL 2). Uma
c�pia da licen�a est� incluida nesta distribui��o no arquivo LICENSE-pt_BR.txt.