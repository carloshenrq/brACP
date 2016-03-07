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

DEFINE('BRACP_MAINTENCE', false, false);
DEFINE('BRACP_DEVELOP_MODE', true, false);

DEFINE('BRACP_TEMPLATE_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR, false);
DEFINE('BRACP_ENTITY_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'Entity', false);
DEFINE('BRACP_DEFAULT_TIMEZONE', 'America/Sao_Paulo', false);

DEFINE('BRACP_URL', 'http://127.0.0.1:8080', false);
DEFINE('BRACP_DIR_INSTALL_URL', '/brACP/', false);

DEFINE('BRACP_SQL_DRIVER', 'pdo_mysql', false);
DEFINE('BRACP_SQL_HOST', '127.0.0.1', false);
DEFINE('BRACP_SQL_USER', 'ragnarok', false);
DEFINE('BRACP_SQL_PASS', 'ragnarok', false);
DEFINE('BRACP_SQL_DBNAME', 'ragnarok', false);

DEFINE('BRACP_MAIL_HOST', '127.0.0.1', false);
DEFINE('BRACP_MAIL_PORT',  25, false);
DEFINE('BRACP_MAIL_USER', 'ragnarok', false);
DEFINE('BRACP_MAIL_PASS', 'ragnarok', false);
DEFINE('BRACP_MAIL_FROM', 'noreply@127.0.0.1', false);
DEFINE('BRACP_MAIL_FROM_NAME', 'noreply', false);

DEFINE('BRACP_MD5_PASSWORD_HASH', true, false);
DEFINE('BRACP_MAIL_REGISTER_ONCE', true, false);
DEFINE('BRACP_MAIL_SHOW_LOG', true, false);
DEFINE('BRACP_CHANGE_MAIL_DELAY', 60, false);

DEFINE('BRACP_ALLOW_RANKING', true, false);
DEFINE('BRACP_ALLOW_RECOVER', true, false);
DEFINE('BRACP_ALLOW_CREATE_ACCOUNT', true, false);
DEFINE('BRACP_ALLOW_RESET_APPEAR', true, false);
DEFINE('BRACP_ALLOW_RESET_POSIT', true, false);
DEFINE('BRACP_ALLOW_RESET_EQUIP', true, false);
DEFINE('BRACP_ALLOW_LOGIN_GMLEVEL', 0, false);
DEFINE('BRACP_ALLOW_ADMIN', true, false);
DEFINE('BRACP_ALLOW_ADMIN_CHANGE_PASSWORD', true, false);
DEFINE('BRACP_ALLOW_ADMIN_GMLEVEL', 99, false);
DEFINE('BRACP_ALLOW_MAIL_SEND', true, false);
DEFINE('BRACP_ALLOW_CHANGE_MAIL', true, false);
DEFINE('BRACP_ALLOW_SHOW_CHAR_STATUS', true, false);

DEFINE('BRACP_ALLOW_RANKING_ZENY', true, false);
DEFINE('BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY', true, false);

DEFINE('BRACP_NOTIFY_CHANGE_PASSWORD', true, false);
DEFINE('BRACP_NOTIFY_CHANGE_MAIL', true, false);

// Teste desconsiderado quando: BRACP_MD5_PASSWORD_HASH = true
DEFINE('BRACP_RECOVER_BY_CODE', false, false);
DEFINE('BRACP_RECOVER_CODE_EXPIRE', 120, false);
DEFINE('BRACP_RECOVER_RANDOM_STRING', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
DEFINE('BRACP_RECOVER_STRING_LENGTH', 8, false);

DEFINE('BRACP_RECAPTCHA_ENABLED', false, false);
DEFINE('BRACP_RECAPTCHA_PUBLIC_KEY', '', false);
DEFINE('BRACP_RECAPTCHA_PRIVATE_KEY', '', false);
DEFINE('BRACP_RECAPTCHA_PRIVATE_URL' , 'https://www.google.com/recaptcha/api/siteverify', false);

DEFINE('BRACP_REGEXP_USERNAME', '[a-zA-Z0-9]{4,24}', false);
DEFINE('BRACP_REGEXP_PASSWORD', '[a-zA-Z0-9]{4,20}', false);
DEFINE('BRACP_REGEXP_EMAIL', '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}', false);

// PAGSEGURO- CONFIGURAÇÕES DO PAGSEGURO
DEFINE('PAG_INSTALL', true, false);
if(BRACP_DEVELOP_MODE)
{
    DEFINE('PAG_URL', 'https://sandbox.pagseguro.uol.com.br', false);
    DEFINE('PAG_WS_URL', 'https://ws.sandbox.pagseguro.uol.com.br', false);
    DEFINE('PAG_STC_URL', 'https://stc.sandbox.pagseguro.uol.com.br', false);
}
else
{
    DEFINE('PAG_URL', 'https://pagseguro.uol.com.br', false);
    DEFINE('PAG_WS_URL', 'https://ws.pagseguro.uol.com.br', false);
    DEFINE('PAG_STC_URL', 'https://stc.pagseguro.uol.com.br', false);
}

DEFINE('PAG_EMAIL', '', false);
DEFINE('PAG_TOKEN', '', false);

DEFINE('DONATION_AMOUNT_MULTIPLY', 100, false);
DEFINE('DONATION_AMOUNT_USE_RATE', true, false);
DEFINE('DONATION_AMOUT_SHOW_RATE_CALC', true, false);

DEFINE('DONATION_SHOW_NEXT_PROMO', true, false);
DEFINE('DONATION_INTERVAL_DAYS', 3, false);
