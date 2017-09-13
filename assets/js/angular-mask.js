(function(ng) {

'use strict';

var appMask = ng.module('angular-mask', []);

/**
 * Diretiva para formatador de campo com mascara.
 */
appMask.directive('formatMask', ['$timeout', function($timeout) {
    return {
        restrict : 'A',
        require : "ngModel",
        link : function(scope, element, attrs, ngModel) {

            (function($) {

                var jele = $(element[0]);

                $timeout(function() {
                    ngModel.$render();
                    jele.on('input', function(e) {
                        var value = $(this).val();
                        scope.$apply(function() {
                            ngModel.$setViewValue(value);
                        });
                    }).mask(attrs.formatMask);
                });

                ngModel.$render = function() {
                    $timeout(function() {
                        jele.val(ngModel.$viewValue).trigger('input');
                        ngModel.$setViewValue(jele.val());
                    });
                };

            }) (window.jQuery);
        }
    };
}]);

}) (angular);
