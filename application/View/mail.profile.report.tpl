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
        Olá, <strong>{$report->informer->name}</strong>! Gostariamos de informar que recebemos sua denuncia!
    </p>

    <p class="message warning">
        Denuncias sem fundamento, serão recusadas e o informante poderá perder o direito de realizar denuncias.
    </p>

    <p>
        Sua denúncia realizada em <strong>{strtolower($formatter->date($report->date->format('Y-m-d H:i:s'), true))}</strong> contra o perfil <strong>{$report->profile->name}</strong> foi recebida pelo nosso sistema e logo será analisada.
    </p>

    <p>
        Também gostariamos de deixar claro, que as denuncias são resolvidas por ordem de chegada, então, pedimos que aguarde ;)
    </p>

    <div class="mail-text-info">{$report->text}</div>

    <p>
        Também gostariamos de lembrar, que você ficará impossibilitado de realizar novas denuncias contra o perfil dele, por pelo menos 2 horas, ok?
    </p>

{/block}