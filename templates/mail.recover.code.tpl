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
 *}

{extends file="mail.default.tpl"}

{block name="mail_body"}
	
    Uma tentativa de recuperação de senha foi realizada na sua conta.<br>
    Para confirmar essa tentativa de recuperação, por favor, clique no link abaixo ou copie e cole o endereço em seu navegador.<br>
    <br>
    <a href="{$href}/{$code}" target="_blank">{$href}/{$code}</a><br>
    <i>Link válido até <strong>{$expire}</strong>.</i><br>
    <br>
    Após acessar o link, você receberá um segundo e-mail com a nova senha gerada aleatóriamente pelo sistema.
{/block}
