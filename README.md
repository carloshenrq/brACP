# brACP - brAthena Control Painel

Este é um painel de controle totalmente em português, desenvolvido por [CarlosHenrq](http://forum.brathena.org/index.php/user/60-carloshenrq/) sob licença da [GNU 3.0](http://www.gnu.org/licenses/gpl.html) para a comunidade [brAthena](http://brathena.org).

## Instalação do brACP

O brACP foi desenvolvido em linguagem [PHP](http://php.net) com o uso do servidor de páginas [Apache](http://www.apache.org) e banco de dados [MySQL](http://mysql.com)
Segue abaixo a lista das versões necessárias para execução do painel de controle de forma adequada:

* PHP 5.5+
* Apache 2.4
  * _OBS.: **mod_rewrite** deve estar ativo!_
* MySQL 5.6

### Extensões necessárias

* [cURL](http://php.net/manual/en/book.curl.php)
* [Hash](http://php.net/manual/en/book.hash.php)
* [Json](http://php.net/manual/en/book.json.php)
* [Xml](http://php.net/manual/en/book.xml.php)
* [LibXml](http://php.net/manual/en/book.libxml.php)
* [OpenSSL](http://php.net/manual/en/book.openssl.php)
* [PCRE](http://php.net/manual/en/book.pcre.php)
* [PDO](http://php.net/manual/en/book.pdo.php)
* [PDO_MySQL](http://php.net/manual/en/ref.pdo-mysql.php)
* [Sockets](http://php.net/manual/en/book.sockets.php)
* [Zip](http://php.net/manual/en/book.zip.php)

### Frameworks utilizados

* [SlimFramwork](http://slimframework.com)
  * Versão: 3.1.0
* [Smarty](http://www.smarty.net/)
  * Versão: 3.1.29
* [Guzzle](http://guzzlephp.org/)
  * Versão: 6.1.1
* [Doctrine](http://www.doctrine-project.org/)
  * Versão: 2.4.2
* [SwiftMailer](http://swiftmailer.org/)
  * Versão: 5.4.1
* [scssphp](http://leafo.net/scssphp/)
  * Versão: 0.6.6
* [Minify](http://www.minifier.org/)
  * Versão: 1.3.39

Para realizar a instalação destes frameworks quando você baixar o brACP basta você utilizar o [Composer](https://getcomposer.org/) e executar os seguintes comandos:

`composer install`

## Recomendações

Se você desejar uma outra opção de painel de controle, nós recomendamos o [FluxCP](https://github.com/HerculesWS/FluxCP).

## Instalação
A Instalação deve seguir normalmente quando você abrir no seu navegador o caminho de uso do brACP. Leia atentamente todos os campos e como ele irá agir no brACP.
**_Antes de completar a instalação o brACP irá realizar alguns testes de conexão com o banco de dados e irá lhe retornar os erros encontrados._**

    @TODO: Continuar explicação de instalação.

### 1- Configurar o Apache
Nas configurações do Apache, você tem que habilitar o **mod_rewrite** e também a leitura de arquivos .htaccess

#### 1.1- Habilitando a leitura de arquivos .htaccess
Primeiro você precisa localizar o arquivo **httpd.conf** exitente dentro da pasta **conf** da sua instalação do Apache.

Procure por este trecho:

    #
    # DocumentRoot: The directory out of which you will serve your
    # documents. By default, all requests are taken from this directory, but
    # symbolic links and aliases may be used to point to other locations.
    #
    DocumentRoot "<DIRETORIO DE INSTALAÇÃO>"
    <Directory "<DIRETORIO DE INSTALAÇÃO>">
        #
        # Possible values for the Options directive are "None", "All",
        # or any combination of:
        #   Indexes Includes FollowSymLinks SymLinksifOwnerMatch ExecCGI MultiViews
        #
        # Note that "MultiViews" must be named *explicitly* --- "Options All"
        # doesn't give it to you.
        #
        # The Options directive is both complicated and important.  Please see
        # http://httpd.apache.org/docs/2.4/mod/core.html#options
        # for more information.
        #
        Options Indexes FollowSymLinks

        #
        # AllowOverride controls what directives may be placed in .htaccess files.
        # It can be "All", "None", or any combination of the keywords:
        #   AllowOverride FileInfo AuthConfig Limit
        #
        AllowOverride None

        #
        # Controls who can get stuff from this server.
        #
        Require all granted
    </Directory>

Troque o `AllowOverride None` para `AllowOverride All` e reinicie o serviço do Apache e prontinho, a leitura dos .htaccess estão habilitadas para você.

#### 1.2- Habilitando o MOD\_REWRITE

Ainda no mesmo arquivo de configuração do apache **httpd.conf**, procure este trecho:

    #LoadModule reflector_module modules/mod_reflector.so
    #LoadModule remoteip_module modules/mod_remoteip.so
    #LoadModule request_module modules/mod_request.so
    #LoadModule reqtimeout_module modules/mod_reqtimeout.so
    #LoadModule rewrite_module modules/mod_rewrite.so
    #LoadModule sed_module modules/mod_sed.so
    #LoadModule session_module modules/mod_session.so

Remova o \# onde está o `#LoadModule rewrite_module modules/mod_rewrite.so` ficando:

    #LoadModule reflector_module modules/mod_reflector.so
    #LoadModule remoteip_module modules/mod_remoteip.so
    #LoadModule request_module modules/mod_request.so
    #LoadModule reqtimeout_module modules/mod_reqtimeout.so
    LoadModule rewrite_module modules/mod_rewrite.so
    #LoadModule sed_module modules/mod_sed.so
    #LoadModule session_module modules/mod_session.so

Agora salve e reinicie o apache.

### 2- Instalando o Composer
Para instalar o composer, você deve possuir o PHP já instalado. Se tiver dúvidas de como instalar o PHP no windows, [clique aqui](http://php.net/manual/pt_BR/install.windows.php).

#### 2.1 - Se você usa Linux...

Para realizar a instalação de acordo, recomendo instalar o composer da seguinte forma:

    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/bin --filename=composer
    php -r "unlink('composer-setup.php');"

#### 2.2 - Se você usa Windows...

Visite [este link](https://getcomposer.org/download/) e siga as instruções que o próprio site da...

### 3- Instalando as dependências do composer
Após instalado o composer, navegue até a pasta do brACP e:

#### 3.1 - Se você usa Linux...
Digite:
    composer install

#### 3.2 - Se você usa Windows...
Ao entrar na pasta, clique no botão direito do mouse no arquivo 'composer.json' e depois clique em 'Composer Install' no menu que irá abrir, aguarde a finalização e pronto. :3

## Agradecimentos

[Protimus](http://forum.brathena.org/index.php/user/1-protimus/) : Por fornecer dicas de segurança.

[Tidus](http://forum.brathena.org/index.php/user/6106-tidus/) : Por fornecer o banco de dados em que o todos os testes foram realizados.

[Aly](http://forum.brathena.org/index.php/user/294-aly/) : Incentivar o desenvolvimento do painel de controle e permitir que isso acontecesse.

[Kaoozie](http://forum.brathena.org/index.php/user/3605-kaoozie/) : Realizar os teste do painel de controle.

[Sir Will](http://forum.brathena.org/index.php/user/18776-sir-will/) : Realizar os teste do painel de controle. (issue #7)

[Twitter Bootstrap](http://getbootstrap.com/) : Estilização dos botões no tema padrão do painel de controle, foram obtidos em [boostrap 2.3.2](http://getbootstrap.com/2.3.2/)

[Megasantos](http://forum.brathena.org/index.php/user/947-jonatas/) : Traduzir as classes no FluxCP e deixar disponivel.

[JulioCF](http://forum.brathena.org/index.php/user/45-juliocf/) : Sugestões de incremento ao painel de controle. (issue #6)

[Shiraz](http://forum.brathena.org/index.php/user/321-shiraz/) : Ajuda com implementações relacionadas ao [memcached](https://memcached.org/)

## Desenvolvedores

[CarlosHenrq](http://forum.brathena.org/index.php/user/60-carloshenrq/)
