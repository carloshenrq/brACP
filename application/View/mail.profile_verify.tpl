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

{extends file="mail.default.tpl"}
{block name="App_Mail_Body"}

    <p>
        Olá, <strong>{$verify->profile->name}</strong>! Para permitir que o perfil criado tenha os acesso devidos, é necessário que seu endereço de e-mail seja confirmado.
    </p>

    <div class="message info">
        Para confirmar seu endereço de e-mail, é necessário que você insira o código de verificação abaixo em nosso painel de controle ou clique no link.
    </div>

    <p>
        <strong>Nome:</strong> {$verify->profile->name}<br>
        <strong>E-mail:</strong> {$verify->email}<br>
        <strong>Código:</strong> {$verify->code}<br>
        <strong>Validade:</strong> {$formatter->date($verify->expireDate->format('Y-m-d H:i:s'), true)}<br>
        <strong>Link:</strong> <a href="{$urlSender}{$smarty.const.APP_URL_PATH}/profile/verify/{$verify->code}" target="_blank">{$urlSender}{$smarty.const.APP_URL_PATH}/profile/verify/{$verify->code}</a>
    </p>

    <p>
        Para confirmar sua conta, você pode clicar no link acima. Se clicar não funcionar, copie e cole o link acima em seu navegador.
    </p>

{/block}