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
	
    Para confirmar a criação da sua conta, é necessário que você confirme sua identidade de e-mail clicando no link abaixo.<br>
    <br>
    <a href="{$href}/{$code}" target="_blank">{$href}/{$code}</a><br>
    <i>Link válido até <strong>{Format::date($expire)}</strong>.</i><br>
    <br>
    Após acessar o link, você receberá um segundo e-mail informando que sua conta foi confirmada com sucesso.
{/block}
