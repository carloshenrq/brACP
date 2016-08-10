²{**
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

<input type="checkbox" class="modal-trigger-check" id="modal-donation"/>
<div class="modal" ng-controller="donation">

    <div class="modal-title">
        @@DONATIONS(TITLE)
        <label for="modal-donation" class="modal-close">&times;</label>
    </div>

    <div class="modal-body" style="max-width: 580px;">
        <p>
            @@DONATIONS,MESSAGE,HEADER(0)
        </p>
        
        <div class="message warning icon">
            @@DONATIONS,MESSAGE,HEADER(1)
        </div>

        <label class="input-checkbox">
            <input type="checkbox" ng-model="accept_terms">
            @@DONATIONS,MESSAGE(ACCEPT_TERMS)
        </label>

        <div ng-if="accept_terms">
            <p class="message info">Arraste a barrinha para indicar o valor de sua doação!.</p>

            <form class="modal-form" ng-submit="submitRegister()">

                <span class="display-money">{literal}{{donationValue}}{/literal}</span>
                <input type="range" ng-model="donationValue" required min="{$smarty.const.BRACP_DONATION_MIN_VALUE}" max="{$smarty.const.BRACP_DONATION_MAX_VALUE}"/>

                <input type="text" ng-model="login" placeholder="Nome de usuário (opcional)"/>
                <span class="display-notify">Ao não preencher o nome de usuário, nós entendemos que você não deseja receber o CASH em sua conta.</span>


                <input id="_submitDonation" type="submit"/>

            </form>
        </div>

    </div>

    <div class="modal-footer">
        <label class="button success icon" for="_submitDonation" ng-if="accept_terms">Doar</label>
        <label class="button warning icon" for="modal-donation">Cancelar</label>
    </div>
</div>
