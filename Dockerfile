FROM ubuntu:18.04
LABEL maintainer Caroline Salib <caroline@portabilis.com.br>

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update \
	&& apt-get install -y \
	curl \
	php-curl \
	git-core \
	apache2 \
	libapache2-mod-php \
	php-pgsql \
	php-pear \
	php-mbstring \
	rpl \
	wget \
	libreadline7 \
	# virou pacote virtual
	# libreadline7-dev \
	make \
	gcc \
	zlib1g-dev \
	software-properties-common \
	# não existe mais
	# python-software-properties \
	--no-install-recommends \	
	&& echo "America/Sao_Paulo" > /etc/timezone \
	&& dpkg-reconfigure -f noninteractive tzdata \
	&& add-apt-repository -y ppa:openjdk-r/ppa \
	&& apt-get -y update \
	&& apt-get -y install openjdk-8-jdk \
	&& a2enmod rewrite \
	# Instala pacotes pear
	&& pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha \
	&& apt-get clean \
	&& apt-get purge --auto-remove -y \
	&& rm -rf /var/lib/apt/lists/*
COPY ieducar.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html/i-educar
RUN a2ensite 000-default.conf \
	&& update-alternatives --config java
EXPOSE 80
CMD /usr/sbin/apache2ctl -D FOREGROUND

