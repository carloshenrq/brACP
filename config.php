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

DEFINE('BRACP_DIR_INSTALL_URL', '/brACP/', false);

DEFINE('BRACP_SQL_DRIVER', 'pdo_mysql', false);
DEFINE('BRACP_SQL_HOST', '127.0.0.1', false);
DEFINE('BRACP_SQL_USER', 'ragnarok', false);
DEFINE('BRACP_SQL_PASS', 'ragnarok', false);
DEFINE('BRACP_SQL_DBNAME', 'ragnarok', false);

DEFINE('BRACP_MD5_PASSWORD_HASH', true, false);
DEFINE('BRACP_MAIL_REGISTER_ONCE', true, false);

DEFINE('BRACP_ALLOW_RANKING', true, false);
DEFINE('BRACP_ALLOW_RECOVER', true, false);
DEFINE('BRACP_ALLOW_CREATE_ACCOUNT', true, false);
DEFINE('BRACP_ALLOW_RESET_APPEAR', true, false);
DEFINE('BRACP_ALLOW_RESET_POSIT', true, false);
DEFINE('BRACP_ALLOW_RESET_EQUIP', true, false);
DEFINE('BRACP_ALLOW_LOGIN_GMLEVEL', 0, false);
DEFINE('BRACP_ALLOW_ADMIN', true, false);
DEFINE('BRACP_ALLOW_ADMIN_CHANGE_PASSWORD', true, false);
DEFINE('BRACP_ALLOW_ADMIN_GMLEVEL',99, false);

