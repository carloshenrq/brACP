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

<h1>@@ADMIN,BACKUP(TITLE)</h1>

<div class="bracp-message success">
    @@ADMIN,BACKUP,MESSAGE(SUCCESS, {$bkp_response.fileName}, {$bkp_response.fileCount}, {Format::bytes($bkp_response.fileSize)})
    <br>
    <div class="bracp-message info">
        <h1>@@ADMIN,BACKUP,MESSAGE(CHEKCSUM)</h1>
        <strong>@@ADMIN,BACKUP,MESSAGE(MD5):</strong> {$bkp_response.fileHashMD5} <br>
        <strong>@@ADMIN,BACKUP,MESSAGE(SHA1):</strong> {$bkp_response.fileHashSHA1} <br>
        <strong>@@ADMIN,BACKUP,MESSAGE(SHA512):</strong> {$bkp_response.fileHashSHA512}
    </div>
</div>
