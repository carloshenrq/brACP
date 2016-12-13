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
<!DOCTYPE html>
<html>

	<head>
		<style>
			*
			{
				padding: 0px; margin: 0px;
				font-family: Tahoma, Verdana, Arial;
				font-size: 12px;
			}
		</style>
	</head>

	<body>
		{translate p0="{$userid}"}@MAIL_TITLE@{/translate}
		--------------------------------------------------------------------------------<br>
		<br>
        {block name="mail_body"}
        {/block}
		<br>
		<br>
		--------------------------------------------------------------------------------<br>
		{translate p0="{$smarty.const.BRACP_MAIL_FROM}" p1="{$ipAddress}" p2="{date('Y-m-d H:i:s')}"}@MAIL_MESSAGE_FOOTER@{/translate}
	</body>

</html>