
(function(ng) {

'use strict';

ng.module('angular-uri-parser', [])
    .service('uri-parser', function() {
        var parseArray = function(array2parse)
        {
            var _tmp = [];

            ng.forEach(array2parse, function(value, key) {
                _tmp.push(encodeURIComponent(key) + '=' + encodeURIComponent(value));
            });
            return _tmp.join('&');
        };

        return {
            'parse' : parseArray
        };
    });

}) (angular);
