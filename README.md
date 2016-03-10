# brACP - brAthena Control Painel

Este é um painel de controle totalmente em português, desenvolvido por [CarlosHenrq](http://forum.brathena.org/index.php/user/60-carloshenrq/) sob licença da [GNU 3.0](http://www.gnu.org/licenses/gpl.html) para a comunidade [brAthena](http://brathena.org).

## Instalação do brACP

O brACP foi desenvolvido em linguagem [PHP](http://php.net) com o uso do servidor de páginas [Apache](http://www.apache.org) e banco de dados [MySQL](http://mysql.com)
Segue abaixo a lista das versões necessárias para execução do painel de controle de forma adequada:

* PHP 5.4 até a versão 5.6 (Isso significa que na versão PHP 7 o painel de controle ainda não foi testado.)
* Apache 2.4
  * _OBS.: **mod_rewrite** deve estar ativo!_
* MySQL 5.6

###Frameworks utilizados

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

Para realizar a instalação destes frameworks quando você baixar o brACP basta você utilizar o [Composer](https://getcomposer.org/) e executar os seguintes comandos:

`composer install`

Execute também o arquivo **sql-files/bracp-install.sql** no banco de dados do seu ragnarok para que o painel de controle funcione adequadamente.

Quando a rotina de instalação do painel de controle terminar, você pode continuar com a configuração do painel de controle.

##Configurações

@Todo:
