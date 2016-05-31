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

<input type="checkbox" class="modal-trigger-check" id="modal-login"/>
<div class="modal" ng-app="account" ng-controller="login">
    <div class="modal-title">
        @@LOGIN(TITLE)
        <label for="modal-login" class="modal-close">&times;</label>
    </div>
    <div class="modal-body">
        
        @@LOGIN,MESSAGE(HEADER)

        <form class="modal-form">
            <input type="text" ng-modal="userid" placeholder="@@LOGIN,HOLDER(USERID)" size="32"/>
            <input type="password" ng-modal="password" placeholder="@@LOGIN,HOLDER(PASSWD)" size="32"/>
        </form>

        <p class="link">@@LOGIN,MESSAGE(CREATE)</p>
        <p class="link">@@LOGIN,MESSAGE(RECOVER)</p>

    </div>
    <div class="modal-footer">
        <div class="button success icon">@@LOGIN,BUTTONS(SUBMIT)</div>
        <label class="button error icon" for="modal-login">@@LOGIN,BUTTONS(CLOSE)</label>
    </div>
</div>
