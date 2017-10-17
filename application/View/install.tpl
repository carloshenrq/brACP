{**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2015  brAthena, CHLFZ
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *}
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1.0">

		<link href="https://fonts.googleapis.com/css?family=Anton" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>

        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/install.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.button.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.message.scss"/>
    </head>
    <body>
    	<div class="install-container">

    		<div class="install-header">
    			<div class="install-logo"></div>
    			<h1>Instalação do brACP</h1>
    			<p>Para realizar a configuração correta do brACP, por favor, preencha os campos corretamente.</p>
    		</div>

    		<div class="install-body">

    			<div class="install-div-container">

    				<!-- Configurações para conexão com a base de dados -->
    				<div class="install-data">
    					<h1>Banco de Dados</h1>

    					<div class="install-data-body">
    						<p>
    							As configurações a seguir são para a conexão entre o brACP e o banco de dados, por favor, verifique com cuidado.
    						</p>

    						<div class="install-data-items">
    							<div class="install-data-item">
    								<label>
    									<span>SQL Drive</span>
    									<select ng-model="config.APP_SQL_DRIVER">
    										<option>pdo_mysql</option>
    										<option>drizzle_pdo_mysql</option>
    										<option>mysqli</option>
    										<option>pdo_sqlite</option>
    										<option>pdo_pgsql</option>
    										<option>pdo_oci</option>
    										<option>pdo_sqlsrv</option>
    										<option>sqlsrv</option>
    										<option>oci8</option>
    										<option>sqlanywhere</option>
    									</select>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Servidor</span>
    									<input type="text" ng-model="config.APP_SQL_HOST"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Usuário</span>
    									<input type="text" ng-model="config.APP_SQL_USER"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Senha</span>
    									<input type="password" ng-model="config.APP_SQL_PASS"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Banco de Dados</span>
    									<input type="password" ng-model="config.APP_SQL_DATA"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Persistente?</span>
    									<select ng-model="config.APP_SQL_PERSISTENT">
    										<option value="0">Desativado</option>
    										<option value="1">Ativado</option>
    									</select>
    								</label>
    							</div>

    							<button class="button fill">
    								<i class="fa fa-refresh"></i>
    								Testar Conexão
    							</button>
    						</div>
    					</div>
    				</div>

    				<!-- Configurações para conexão com servidor de e-mails -->
    				<div class="install-data">
    					<h1>Servidor de E-mails</h1>

    					<div class="install-data-body">
    						<p>
    							As configurações a seguir são para a conexão entre o brACP e o servidor de e-mails, por favor, verifique com cuidado.
    						</p>

    						<div class="install-data-items">
    							<div class="install-data-item">
    								<label>
    									<span>Permitir?</span>
    									<select ng-model="config.APP_MAILER_ALLOWED">
    										<option value="0">Não</option>
    										<option value="1">Sim</option>
    									</select>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Servidor</span>
    									<input type="text" ng-model="config.APP_MAILER_HOST"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Porta</span>
    									<input type="text" ng-model="config.APP_MAILER_PORT"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Transporte</span>
    									<select ng-model="config.APP_MAILER_ENCRYPT">
    										<option value="">Nenhum</option>
    										{foreach from=stream_get_transports() item=$transport}
    											<option value="{$transport}">{$transport}</option>
    										{/foreach}
    									</select>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Usuário</span>
    									<input type="text" ng-model="config.APP_MAILER_USER"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Senha</span>
    									<input type="password" ng-model="config.APP_MAILER_PASS"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Remetente</span>
    									<input type="text" ng-model="config.APP_MAILER_FROM"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>Nome</span>
    									<input type="text" ng-model="config.APP_MAILER_NAME"/>
    								</label>
    							</div>
    							<button class="button fill">
    								<i class="fa fa-refresh"></i>
    								Testar Envio
    							</button>

    						</div>
    					</div>
    				</div>

    				<!-- Configurações para conexão com servidor de e-mails -->
    				<div class="install-data">
    					<h1>Sessão Criptografada</h1>

    					<div class="install-data-body">
    						<p>
    							As configurações a seguir são para a segurança na sessão de usuário e brACP.
    						</p>
    						<div class="install-data-items">
								<div class="install-data-item">
									<label>
										<span>Permitir?</span>
										<select ng-model="config.APP_SESSION_SECURE">
											<option value="1">Sim</option>
											<option value="0">Não</option>
										</select>
									</label>
								</div>
								<div class="install-data-item">
									<label>
										<span>Algoritmo</span>
										<select ng-model="config.APP_SESSION_ALGO">
											<option value="">- Escolher</option>
											{foreach from=openssl_get_cipher_methods() item=$algo}
												<option value="{$algo}">{$algo}</option>
											{/foreach}
										</select>
									</label>
								</div>
    							<div class="install-data-item">
    								<label>
    									<span>Chave</span>
    									<input type="text" ng-model="config.APP_SESSION_KEY"/>
    								</label>
    							</div>
    							<div class="install-data-item">
    								<label>
    									<span>IV</span>
    									<input type="text" ng-model="config.APP_SESSION_IV"/>
    								</label>
    							</div>
							</div>
    					</div>
    				</div>

    			</div>

    		</div>

    	</div>

    </body>
</html>