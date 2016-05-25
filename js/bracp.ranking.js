/**
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
 */


var rankings = angular.module('ranking', []);

rankings.controller('economy', ['$scope', '$http', function($scope, $http) {

    $scope.chars = [];


    $scope._init    = function() {

        $scope.reload();

    };

    $scope.reload   = function() {

        $http
            .get(document.querySelector('#_BRACP_URL').value + 'rankings/chars/economy/json')
            .then(function(response) {
                $scope.chars = response.data;
            });

    };
}]);

rankings.controller('chars', ['$scope', '$http', function($scope, $http) {

    $scope.chars = [];


    $scope._init    = function() {

        $scope.reload();

    };

    $scope.reload   = function() {

        $http
            .get(document.querySelector('#_BRACP_URL').value + 'rankings/chars/json')
            .then(function(response) {
                $scope.chars = response.data;
            });

    };


}]);

