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

####BRACP_VERSION _(string)_
Variável que define a versão em que o painel de controle se encontra. É extremamente recomendado não alterar essa configuração.

####BRACP_MAINTENCE _(true|false)_
Define se o painel de controle está em modo manutenção.
_NINGUÉM PODERÁ REALIZAR AÇÕES ENQUANTO ESTIVER ATIVO. NEM MESMO OS ADMINSITRADORES._

####BRACP_DEVELOP_MODE _(true|false)_
Define se o painel de controle está em modo desenvolvedor. Algumas opções como configurações da url de requisição do pagseguro são definidas por esta variável.

####BRACP_TEMPLATE_DIR _(string)_
Diretório que se encontra os arquivos `.tpl` para montar a tela. É extremamente recomendado não alterar essa configuração.

####BRACP_ENTITY_DIR _(string)_
Diretório que se encontra os arquivos de Entity do banco de dados. É extremamente recomendado não alterar essa configuração.

####BRACP_DEFAULT_TIMEZONE _(string)_
Fuso horário para o painel de controle trabalhar corretamente com a hora.

####BRACP_URL _(string)_
URL do dominio que está instalado o painel de controle. Não é preciso o caminho da pasta aqui.

####BRACP_DIR_INSTALL_URL _(string)_
Diretório dentro do domínio que está instalado o brACP.

####BRACP_SQL_DRIVER _(string)_
Drive de conexão com o banco de dados. O Valor padrão é `pdo_mysql`. É extremamente recomendado não alterar essa configuração.

####BRACP_SQL_HOST _(string)_
Endereço do servidor de banco de dados que está instalado o brACP.

####BRACP_SQL_USER _(string)_
Usuário do banco de dados que possui a acesso aos dados do jogo.

####BRACP_SQL_PASS _(string)_
Senha do usuário do banco de dados.

####BRACP_SQL_DBNAME _(string)_
Nome do banco de dados que possui os dados do jogo.

####BRACP_MAIL_HOST _(string)_
Endereço do servidor de e-mails para o painel de controle enviar as notificações.

####BRACP_MAIL_PORT _(integer)_
Porta para conexão com o servidor de e-mails.

####BRACP_MAIL_USER _(string)_
Usuário de acesso ao servidor de e-mails.

####BRACP_MAIL_PASS _(string)_
Senha do usuário de acesso ao servidor de e-mails.

####BRACP_MAIL_FROM _(string)_
Endereço de e-mail que enviou este e-mail. (É exibido nos dados do e-mail recebido)

####BRACP_MAIL_FROM_NAME _(string)_
Nome do usuário para o endereço que enviou o e-mail. (Recomendamos colocar o nome do seu servidor)

####BRACP_MD5_PASSWORD_HASH _(true|false)_
Informa se o painel de controle irá utilizar md5 para tratar as senhas.
Verifique se seu servidor também está com essa opção ativada.

####BRACP_MAIL_REGISTER_ONCE _(true|false)_
Não permite que o endereço de e-mail seja cadastrado mais de uma vez.

####BRACP_CHANGE_MAIL_DELAY _integer_
Tempo de espera até a próxima troca de e-mail. (Em minutos)

####BRACP_ALLOW_RANKING _(true|false)_
Permitir exibição dos rankings.

####BRACP_ALLOW_RECOVER _(true|false)_
Permitir recuperação de contas. (Depende de envio de e-mails)

####BRACP_ALLOW_CREATE_ACCOUNT _(true|false)_
Permitir criação de novas contas.

####BRACP_ALLOW_RESET_APPEAR _(true|false)_
Permitir resetar a aparência dos personagens.

####BRACP_ALLOW_RESET_POSIT _(true|false)_
Permitir resetar a posição dos personagens.

####BRACP_ALLOW_RESET_EQUIP _(true|false)_
Permitir resetar o equipamento dos personagens.

####BRACP_ALLOW_LOGIN_GMLEVEL _integer_
Nivel mínimo para a conta realizar login no painel de controle.

####BRACP_ALLOW_ADMIN _(true|false)_
Permitir modo administrador. (Em desenvolvimento ainda...)

####BRACP_ALLOW_ADMIN_CHANGE_PASSWORD _(true|false)_
Permitir que administradores mudem suas senhas.

####BRACP_ALLOW_ADMIN_GMLEVEL _(true|false)_
Nível para que as contas sejam consideradas administrador.

####BRACP_ALLOW_MAIL_SEND _(true|false)_
Permitir que o painel de controle envie e-mails.

####BRACP_ALLOW_CHANGE_MAIL _(true|false)_
Permitir que os usuários alterem seus endereços de e-mail.

####BRACP_ALLOW_SHOW_CHAR_STATUS _(true|false)_
Permitir que seja visualizado se o personagem está online ou offline.

####BRACP_ALLOW_RANKING_ZENY _(true|false)_
Permitir que seja exibido o ranking de zenys por personagem.

####BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY _(true|false)_
Permitir que seja exibido a quantidade de zenys no ranking de zeny por personagens.

####BRACP_NOTIFY_CHANGE_PASSWORD _(true|false)_
Permitir notificar o usuário quando sua senha for modificada.

####BRACP_NOTIFY_CHANGE_MAIL _(true|false)_
Permitir notificar o usuário quando seu e-mail for modificado.

####BRACP_RECOVER_BY_CODE _(true|false)_
Gerar código de recuperação da conta. (Depende de habilitar recuperação de conta)

####BRACP_RECOVER_CODE_EXPIRE _integer_
Tempo para o código de recuperação expirar. (Em minutos)

####BRACP_RECOVER_RANDOM_STRING _(string)_
Cadeia de caracteres para gerar a nova senha aleatória do jogador.

####BRACP_RECOVER_STRING_LENGTH _integer_
Tamanho para a nova senha do jogador.

####BRACP_RECAPTCHA_ENABLED _(true|false)_
Permitir que o reCAPTCHA da google seja utilizado.

####BRACP_RECAPTCHA_PUBLIC_KEY _(string)_
Chave publica de comunicação com o reCAPTCHA. (Exibido no campo da tela)

####BRACP_RECAPTCHA_PRIVATE_KEY _(string)_
Chave privada de comunicação com o reCAPTCHA. (Nunca é exibido.)

####BRACP_RECAPTCHA_PRIVATE_URL _(string)_
Caminho para realizar a verificação do reCAPTCHA.

####BRACP_REGEXP_USERNAME _(string)_
Expressão regular para os nomes de usuários que forem digitados no brACP.

####BRACP_REGEXP_PASSWORD _(string)_
Expressão regular para as senhas que forem digitadas no brACP.

####BRACP_REGEXP_EMAIL _(string)_
Expressão regular para os endereços de e-mail que forem digitados no sistema.

####PAG_INSTALL _(true|false)_
Permitir uso da biblioteca do PagSeguro

####PAG_URL _(string)_
Caminho para requisições normais do pagseguro. É extremamente recomendado não alterar essa configuração.

####PAG_WS_URL _(string)_
Caminho para requisições ao webservice do pagseguro. É extremamente recomendado não alterar essa configuração.

####PAG_STC_URL _(string)_
Caminho para requisições estaticas do pagseguro. É extremamente recomendado não alterar essa configuração.

####PAG_EMAIL _(string)_
Endereço de e-mail para realizar as transações no PagSeguro.

####PAG_TOKEN _(string)_
Token para realizar as requisições de transações no PagSeguro.

####DONATION_AMOUNT_MULTIPLY _(integer)_
Define a quantidade de bônus que será atribuida a cada R$ 1,00

####DONATION_AMOUNT_USE_RATE _(true|false)_
Permitir usar o calculo para o doador cobrir as taxas do pagamento.

####DONATION_AMOUT_SHOW_RATE_CALC _(true|false)_
Permitir exibir o calculo para o doador ficar ciente do valor extra.

####DONATION_SHOW_NEXT_PROMO _(true|false)_
Permitir as próximas doações.

####DONATION_INTERVAL_DAYS _(integer)_
Tempo, em dias, para exibir as próximas promoções.

####BRACP_ALLOW_CHOOSE_THEME _(true|false)_
Permitir usuário escolher o tema.

####BRACP_DEFAULT_THEME _(string)_
Pasta para o tema que o usuário ou o sistema escolheu.

####BRACP_CONFIRM_ACCOUNT _(true|false)_
Permitir que o painel de controle crie um código de ativação para testar se o endereço de e-mail cadastrado é real.

####BRACP_DEFAULT_LANGUAGE _(string)_
Idioma padrão do painel de controle.

####BRACP_MEMCACHE
Permitir que o painel de controle utilize um servidor [memcached](https://memcached.org/)

####BRACP_MEMCACHE_SERVER
Endereço do servidor para ser utilizado o cache.

####BRACP_MEMCACHE_PORT
Porta que o serviço está em execução no endereço indicado.

####BRACP_MEMCACHE_EXPIRE
Tempo que o cache gerado demorará para expirar no servidor.

##Recomendações

Se você desejar uma outra opção de painel de controle, nós recomendamos o [FluxCP](https://github.com/HerculesWS/FluxCP).

##Agradecimentos

[Protimus](http://forum.brathena.org/index.php/user/1-protimus/) : Por fornecer dicas de segurança.

[Tidus](http://forum.brathena.org/index.php/user/6106-tidus/) : Por fornecer o banco de dados em que o todos os testes foram realizados.

[Aly](http://forum.brathena.org/index.php/user/294-aly/) : Incentivar o desenvolvimento do painel de controle e permitir que isso acontecesse.

[Kaoozie](http://forum.brathena.org/index.php/user/3605-kaoozie/) : Realizar os teste do painel de controle.

[Sir Will](http://forum.brathena.org/index.php/user/18776-sir-will/) : Realizar os teste do painel de controle. (issue #7)

[Twitter Bootstrap](http://getbootstrap.com/) : Estilização dos botões no tema padrão do painel de controle, foram obtidos em [boostrap 2.3.2](http://getbootstrap.com/2.3.2/)

[Megasantos](http://forum.brathena.org/index.php/user/947-jonatas/) : Traduzir as classes no FluxCP e deixar disponivel.

[JulioCF](http://forum.brathena.org/index.php/user/45-juliocf/) : Sugestões de incremento ao painel de controle. (issue #6)

[Shiraz](http://forum.brathena.org/index.php/user/321-shiraz/) : Ajuda com implementações relacionadas ao [memcached](https://memcached.org/)

##Desenvolvedores

[CarlosHenrq](http://forum.brathena.org/index.php/user/60-carloshenrq/)
