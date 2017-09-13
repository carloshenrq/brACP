
(function(ng) {


'use strict';


ng.module('angular-quill', []).directive('quillEditor', ['$timeout', function($timeout) {
    return {
        restrict : 'E',
        require : "ngModel",
        template : '<div/>',
        replace : true,
        link : function(scope, element, attrs, ngModel) {
            
            var options = {
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }, { 'font': [] }, { size : ['small', false, 'large', 'huge']}, { 'color': [] }, { 'background': [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{'list' : 'ordered'}, {'list' : 'bullet'}, { 'script': 'sub'}, { 'script': 'super' }],
                        ['link', 'image', 'code-block', 'blockquote'],
                        [{ 'align': [] }],
                        ['clean']
                    ]
                },
                theme: 'snow'
            }, extra = attrs.extra ? scope.$eval(attrs.extra) : {}, editor;

            angular.extend(options, extra);

            $timeout(function() {
                editor = new Quill(element[0], options);

                ngModel.$render();

                editor.on('text-change', function(delta, source)
                {
                    $timeout(function() {
                        scope.$apply(function() {
                            ngModel.$setViewValue(editor.root.innerHTML);
                        });
                    });
                });
            });

            ngModel.$render = function()
            {
                if(angular.isDefined(editor))
                {
                    $timeout(function() {
                        editor.root.innerHTML = ngModel.$viewValue || '';
                    });
                }
            };

        }
    }
}]);


}) (angular);

