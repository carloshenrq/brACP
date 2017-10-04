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

// Arquivo de carregamento do composer.
$composerFile = join(DIRECTORY_SEPARATOR, [__DIR__, 'vendor', 'autoload.php']);
 
 // Verifica se o arquivo do composer não existe para iniciar a execução
// do programa do composer.
if(!file_exists($composerFile))
{
    // Arquivo do composer para executar os dados de vendor.
    $composerPhar = join(DIRECTORY_SEPARATOR, [
        __DIR__,
        'composer.phar'
    ]);
    // Altera o timeout padrão do php.
    $oldTimeout = ini_set('max_execution_time', '1200');
    
    // Faz atualização e instalação das depências do composer...
    @exec("php {$composerPhar} self-update");
    @exec("php {$composerPhar} install", $output, $return);

    // Define o tempo de execução.
    ini_set('max_execution_time', $oldTimeout);

    // Caso a variavel não seja exatamente igual a 0, então, ocorreu algum erro de execução do composer
    // Em sua atualização, provavelmente problemas de execução.
    if($return !== 0)
        exit('Não foi possível instalar as dependencias do composer. Verifique as permissões e tente novamente.');

    // Solicita a atualização da tela para o usuário.
    exit('As depêndencias do composer foram instaladas com sucesso. Por favor, pressione F5 para atualizar.');
}

// Inclui o arquivo de composer para as dependencias de classes.
require_once $composerFile;

// Arquivos padrões para serem incluidos durante a execução do brACP.
$_filesInclude = [
    join(DIRECTORY_SEPARATOR, [__DIR__, 'config.php']),
    join(DIRECTORY_SEPARATOR, [__DIR__, 'application', 'autoload.php']),
];

foreach($_filesInclude as $_fileInclude)
{
    if(!file_exists($_fileInclude))
        exit('Arquivo de configuração do sistema não encontrado. Verifique sua instalação.');
    
    require_once $_fileInclude;
}

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set(APP_DEFAULT_TIMEZONE);

$app = new App;
$app->run();
