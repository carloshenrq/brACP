
(function(ng) {

'use strict';

ng.module('angular-window-location', [])
    .service('window-location', function() {
        return {
            current : function ()
            {
                return window.location.pathname;
            },
            main    : function ()
            {
                return document.querySelector('#APP_LOCATION').value;
            }
        }
    });


}) (angular);
