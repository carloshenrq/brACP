<?php
DEFINE('APP_DEVELOPER_MODE', true, false);
DEFINE('APP_DEFAULT_TIMEZONE', 'America/Sao_Paulo', false);

DEFINE('APP_URL_PATH', '/bracp', false);

DEFINE('APP_SESSION_SECURE', true, false);
DEFINE('APP_SESSION_ALGO', 'AES-256-ECB', false);
DEFINE('APP_SESSION_KEY', 'fjPY131yohICvDj5JszAFIgGajZcZ7c3p4EIECbb0ac=', false);
DEFINE('APP_SESSION_IV', '', false);

DEFINE('APP_MAILER_ALLOWED', false, false);
DEFINE('APP_MAILER_HOST', '', false);
DEFINE('APP_MAILER_PORT', 25, false);
DEFINE('APP_MAILER_ENCRYPT', '', false);
DEFINE('APP_MAILER_USER', '', false);
DEFINE('APP_MAILER_PASS', '', false);
DEFINE('APP_MAILER_FROM', '', false);
DEFINE('APP_MAILER_NAME', '', false);

DEFINE('APP_TEMPLATE_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'View' , false);
DEFINE('APP_MODEL_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'Model', false);
DEFINE('APP_CACHE_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'cache', false);
DEFINE('APP_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'plugins', false);
DEFINE('APP_SCHEMA_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'schemas', false);

DEFINE('APP_SQL_DRIVER', 'pdo_mysql', false);
DEFINE('APP_SQL_HOST', '127.0.0.1', false);
DEFINE('APP_SQL_USER', 'bracp', false);
DEFINE('APP_SQL_PASS', 'bracp', false);
DEFINE('APP_SQL_DATA', 'bracp', false);
DEFINE('APP_SQL_PERSISTENT', false, false);
DEFINE('APP_SQL_CONNECTION_STRING', 'mysql:host=' . APP_SQL_HOST . ';dbname=' . APP_SQL_DATA, false);

DEFINE('APP_CACHE_ENABLED', false, false);
DEFINE('APP_CACHE_TIMEOUT', 600, false);

DEFINE('APP_DEFAULT_LANGUAGE', 'pt-BR', false);
DEFINE('APP_DEFAULT_THEME', 'classic', false);
DEFINE('APP_PLUGIN_ALLOWED', true, false);

DEFINE('APP_RECAPTCHA_ENABLED', false, false);
DEFINE('APP_RECAPTCHA_SITE_KEY', '', false);
DEFINE('APP_RECAPTCHA_PRIV_KEY', '', false);

DEFINE('APP_FIREWALL_ALLOWED', false, false);
DEFINE('APP_FIREWALL_RULE_CONFIG', false, false);
DEFINE('APP_FIREWALL_MANAGER', true, false);

DEFINE('APP_FACEBOOK_ENABLED', false, false);
DEFINE('APP_FACEBOOK_APP_ID', '', false);
DEFINE('APP_FACEBOOK_APP_SECRET', '', false);

DEFINE('APP_GOOGLE_AUTH_MAX_ERRORS', 3, false);
DEFINE('APP_GOOGLE_AUTH_NAME', 'brACP', false);

# Configurações para o brACP. 
DEFINE('BRACP_ACCOUNT_PASSWORD_HASH', 'sha512', false);

DEFINE('BRACP_ACCOUNT_CREATE', true, false);
DEFINE('BRACP_ACCOUNT_VERIFY', true, false);
DEFINE('BRACP_ACCOUNT_VERIFY_EXPIRE', 7200 , false);
DEFINE('BRACP_ACCOUNT_WRONGPASS_BLOCKCOUNT', 5, false);
DEFINE('BRACP_ACCOUNT_WRONGPASS_BLOCKTIME', 900, false);

DEFINE('BRACP_REGEXP_NAME', '^[a-zA-ZÀ-ú0-9\s]{5,256}$', false);
DEFINE('BRACP_REGEXP_MAIL', '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$', false);
DEFINE('BRACP_REGEXP_PASS', '^((?=.*\d)(?=.*[a-zA-Z\s])(?=.*[@#$%])[a-zA-Z0-9\s@$$%]{6,})$', false);

DEFINE('BRACP_SERVER_PING', 500, false);
DEFINE('BRACP_SERVER_SQL_PERSISTENT', false, false);

# Configurações de contas para o brACP (Ragnarok)
DEFINE('BRACP_RAG_ACCOUNT_CREATE', true, false);
DEFINE('BRACP_RAG_ACCOUNT_LIMIT', 5, false);
DEFINE('BRACP_RAG_ACCOUNT_PASSWORD_HASH', true, false);
DEFINE('BRACP_RAG_ACCOUNT_PASSWORD_ALGO', 'md5', false);
