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

<h1>Administração &raquo; Backup</h1>

<div class="bracp-message success">
    O Arquivo <strong>{$bkp_response.fileName}</strong> foi criado com 
    <strong>{$bkp_response.fileCount}</strong> arquivos possuindo tamanho de <strong>{Format::bytes($bkp_response.fileSize)}</strong>.
    <br>
    <br>
    <div class="bracp-message info">
        <h1>Soma de verificação</h1>
        <strong>md5:</strong> {$bkp_response.fileHashMD5} <br>
        <strong>sha1:</strong> {$bkp_response.fileHashSHA1} <br>
        <strong>sha512:</strong> {$bkp_response.fileHashSHA512}
    </div>
</div>
