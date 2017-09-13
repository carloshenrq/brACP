/**
 * Plugin para usar com upload de arquivos em base64.
 * Esse plugin, irá utilizar um input[file] com eventos para convert o resultado em base64 
 * e talvez ser enviado para o servidor.
 * 
 * Como usar: ng.module('app', ['angular-input-file'])
 * 
 * No html colocar:
 * 
 * <file-base64 ng-model=""></file-base64>
 */

(function(ng) {

'use strict';

ng.module('angular-input-file', []).directive('fileBase64', ['$timeout', function($timeout) {
    return {
        restrict : 'E',
        require : "ngModel",
        template : '<input type="file"/>',
        replace : true,
        link : function(scope, element, attrs, ngModel)
        {
            $timeout(function() {
                if('id' in attrs)
                    element.prop('id', attrs.id);

                element.bind('input', function(e) {
                    if(e.target.files && e.target.files.length > 0 && e.target.files[0])
                    {
                        $timeout(function() {
                            var file = e.target.files[0];
                            var fileReader = new FileReader();

                            fileReader.addEventListener('load', function(evt) {
                                scope.$apply(function() {
                                    ngModel.$setViewValue(evt.target.result);
                                });
                            });

                            fileReader.readAsDataURL(file);
                        });
                    }
                });
            });
        }
    };
}]);

}) (angular);

