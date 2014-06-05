Instalação do i-Educar em RHEL e CentOS
=======================================

Para fazer a instalação você vai precisar de:
* Uma instalação do CentOS 6 ou Red Hat Enterprise Linux 6 com privilégios de root
* Se usando RHEL, as licenças da distribuição
* Se usando Fedora, me avisar, porque não tentei instalar nele.

Todos os comandos de shell (precedidos por `#`) são executados como root.

Dependências
============

# SELinux e iptables

CentOS e RHEL vem com SELinux habilitado por padrão e com regras restritas do iptables. A menos que você vá rodar este sistema em produção, ao invés de criar regras para o httpd, vamos desabilitar eles. Edite o arquivo `/etc/sysconfig/selinux` e configure o parâmetro
* SELINUX=disabled
E em seguida desabilite a inicialização do iptables.

    # chkconfig iptables off 

Alterar este parâmetro requer reiniciar o servidor.

# PHP

Instale os seguintes pacotes:
* php-gd
* php-pgsql
* php-pear
* php-mbstring (se usando RHEL, devido a licença, precisa habilitar o repositório rhel-x86_64-server-optional-6)
* php
* php-devel

    # yum install php php-gd php-pgsql php-pear php-mbstring php-devel

Em seguida, instale estas dependências do PHP:
* XML_RPC2
* Mail
* Net_SMTP
* Services_ReCaptcha

    # pear channel-update pear.php.net
    # pear install -f pear
    # pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha

E configure o arquivo `/etc/php.ini` certificando-se que estes parâmetros estão com estes argumentos:
* error_reporting = E_ALL & ~E_NOTICE
* memory_limit = 64M
* display_errors = Off
* short_open_tag = On
* date.timezone = America/Sao_Paulo

# PostgreSQL 9.2

Primeiramente, baixe de http://yum.postgresql.org/repopackages.php o pacote adequado para adicionar o repositório a sua distribuição. Então instale o .rpm que contém os repositórios, e em seguida instale o PostgreSQL 9.2 e o configure para inicializar junto com o sistema e para criar seu cluster de bancos de dados inicial.

    # rpm -ivh pgdg-redhat92-9.2-7.noarch.rpm
    # yum groupinstall "PostgreSQL Database Server 9.2 PGDG"
    # service postgresql-9.2 initdb
    # service postgresql-9.2 start
    # chkconfig postgresql-9.2 on

Em seguida, vamos criar um novo banco de dados no PostgreSQL recém-instalado. Se conecte a ele pelo psql e crie o usuário e o banco de dados.

    # su - postgres
    $ psql
    psql=# CREATE ROLE ieducar;
    psql=# ALTER ROLE ieducar WITH SUPERUSER INHERIT NOCREATEROLE CREATEDB LOGIN PASSWORD 'ieducarpass';
    psql=# CREATE DATABASE ieducar WITH ENCODING = 'LATIN1' TABLESPACE = pg_default LC_COLLATE = 'pt_BR.iso88591' LC_CTYPE = 'pt_BR.iso88591' TEMPLATE = template0 OWNER = ieducar;
    psql=# \q
    $ exit

# Apache HTTPD

Aqui somente instalamos o httpd e o configuramos.

    # yum install httpd

E então edite o arquivo `/etc/httpd/conf/httpd.conf` configurando o parâmetro DocumentRoot e o bloco Directory para o conteúdo a seguir:

    DocumentRoot /var/www/html/ieducar/ieducar

    <Directory /var/www/html/ieducar/ieducar >
        Options Indexes FollowSymLinks 
        AllowOverride All 
        Allow from all 
    </Directory>

E também certifique-se que o httpd vai usar o charset do i-Educar:

    AddDefaultCharset ISO-8859-1

Instalação
==========

Para instalar o i-Educar, vamos fazer um clone via git e colocar no diretório pré-configurado do httpd.

    # yum install git
    # git clone 'https://github.com/cmsz/ieducar' /var/www/html/ieducar
    # chown -R apache:apache /var/www/html/ieducar
    # chmod -R 750 /var/www/html/ieducar

Em seguida, criamos o banco de dados. Confira o nome do banco de dados sendo modificado e do usuário recebendo grants em `ieducar/misc/database/post-install.sql` antes de executar este passo:

    # cd /var/www/html/ieducar/ieducar/misc/database
    # psql -h 127.0.0.1 -U ieducar -W -d ieducar -f ieducar.sql
    # psql -h 127.0.0.1 -U ieducar -W -d ieducar -f post-install.sql

Então vamos configurar o i-Educar a partir do arquivo de configuração fornecido. Copie o arquivo `ieducar/configuration/ieducar.ini.sample` para `ieducar/configuration/ieducar.ini` e modifique os parâmetros de acordo com sua instalação.
O serviço do httpd deve estar parado neste instante, portanto inicialize ele.

    # service httpd start

Ou caso ele esteja executando, apenas faça um `restart`.
Se tudo transcorreu bem, abra um navegador e aponte para a URL do servidor no qual você fez a instalação. O usuário administrativo é `admin` e a senha é `ieducar@serpro` .
