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

    ##MAIL_CREATECODE_MSG,0##<br>
    <br>
    <a href="{$href}/{$code}" target="_blank">{$href}/{$code}</a><br>
    <i>##MAIL_CREATECODE_MSG,1## <strong>{Format::date($expire)}</strong>.</i><br>
    <br>
    ##MAIL_CREATECODE_MSG,2##

{/block}
