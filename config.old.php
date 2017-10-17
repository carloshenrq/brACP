<?php
/**
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
 */

return [

	// Configurações de localização (pasta) e timezone	
	'APP_DEVELOPER_MODE'		=> true,
	'APP_DEFAULT_TIMEZONE'		=> 'America/Sao_Paulo',
	'APP_URL_PATH'				=> '/bracp',

	// Configuração de diretórios
	'APP_TEMPLATE_DIR'			=> join(DIRECTORY_SEPARATOR, [
		__DIR__, 'application', 'View',
	]),
	'APP_MODEL_DIR'				=> join(DIRECTORY_SEPARATOR, [
		__DIR__, 'application', 'Model',
	]),
	'APP_CACHE_DIR'				=> join(DIRECTORY_SEPARATOR, [
		__DIR__, 'cache'
	]),
	'APP_PLUGIN_DIR'			=> join(DIRECTORY_SEPARATOR, [
		__DIR__, 'plugins'
	]),
	'APP_SCHEMA_DIR'			=> join(DIRECTORY_SEPARATOR, [
		__DIR__, 'schemas'
	]),

	// Configurações de cache local
	'APP_CACHE_ENABLED'			=> false,
	'APP_CACHE_TIMEOUT'			=> 600,

	// Configurações de linguagem, temas e plugins
	'APP_DEFAULT_LANGUAGE'		=> 'pt-BR',
	'APP_DEFAULT_THEME'			=> 'classic',
	'APP_PLUGIN_ALLOWED'		=> true,

	// Configurações de RECAPTCHA
	'APP_RECAPTCHA_ENABLED'		=> false,
	'APP_RECAPTCHA_SITE_KEY'	=> '',
	'APP_RECAPTCHA_PRIV_KEY'	=> '',

	// Configurações de firewall
	'APP_FIREWALL_ALLOWED'		=> false,
	'APP_FIREWALL_RULE_CONFIG'	=> false,
	'APP_FIREWALL_MANAGER'		=> false,

	// Configurações de API para o facebook
	'APP_FACEBOOK_ENABLED'		=> false,
	'APP_FACEBOOK_APP_ID'		=> '',
	'APP_FACEBOOK_APP_SECRET'	=> '',

	// Configurações para o google authenticator
	'APP_GOOGLE_AUTH_MAX_ERRORS'	=> 3,
	'APP_GOOGLE_AUTH_NAME'			=> 'brACP',

	// ---------- CONFIGURAÇÕES PARA O RAGNAROK ---------- //

	'BRACP_ACCOUNT_CREATE'					=> true,
	'BRACP_ACCOUNT_PASSWORD_HASH'			=> 'sha512',
	'BRACP_ACCOUNT_VERIFY'					=> true,
	'BRACP_ACCOUNT_VERIFY_EXPIRE'			=> 7200,
	'BRACP_ACCOUNT_WRONGPASS_BLOCKCOUNT'	=> 5,
	'BRACP_ACCOUNT_WRONGPASS_BLOCKTIME'		=> 900,

	'BRACP_REGEXP_NAME'						=> '^[a-zA-ZÀ-ú0-9\s]{5,256}$',
	'BRACP_REGEXP_MAIL'						=> '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
	'BRACP_REGEXP_PASS'						=> '^((?=.*\d)(?=.*[a-zA-Z\s])(?=.*[@#$%])[a-zA-Z0-9\s@$$%]{6,})$',

	'BRACP_SERVER_PING'						=> 500,
	'BRACP_SERVER_SQL_PERSISTENT'			=> false,

	'BRACP_RAG_ACCOUNT_CREATE'				=> true,
	'BRACP_RAG_ACCOUNT_LIMIT'				=> 5,
	'BRACP_RAG_ACCOUNT_PASSWORD_HASH'		=> true,
	'BRACP_RAG_ACCOUNT_PASSWORD_ALGO'		=> 'md5',

];
