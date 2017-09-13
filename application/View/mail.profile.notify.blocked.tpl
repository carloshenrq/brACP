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
        Olá, <strong>{$profile->name}</strong>!
    </p>

    {if $blocked}
        <div class="message warning">
            Todas as permissões de acesso para este perfil, foram revogadas permanentemente.
        </div>
        <p>
            Você ainda poderá realizar login, porém, não possuirá acesso a várias do painel de controle.<br>
            <br>
            <em><strong>Pode ser que esta punição, não se reflita diretamente ao jogo.</strong></em><br>
            Então, dependendo do caso, você pode ter perdido o acesso em algumas informações do seu perfil.
        </p>
    {else}
        <div class="message info">
            Suas permissões de acesso foram devolvidas, e você já poderá voltar a utilizar normalmente todas as funcionalidades.
        </div>
        <p>
            <em>* Se você possui bloqueios relacionados a criação de novos acessos para jogo e para denuncias, estes não são tratados neste mesmo e-mail.</em>
        </p>
    {/if}

{/block}