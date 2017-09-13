/**
 * 
 */

(function(ng) {

ng.module('angular-chartjs', [])
    .directive('chartJs', ['$timeout', function($timeout) {

        return {
            restrict : 'E',
            replace : true,
            template : '<canvas></canvas>',
            link : function(scope, element, attrs)
            {
                // console.log(attrs);


                // Obtém o contexto de canvas para gerar o grafico.
                var ctx = element[0].getContext('2d');
                var chartData = {
                    type : attrs.type,
                    data : {
                        labels : JSON.parse(atob(attrs.label)),
                        datasets : [{
                            data : JSON.parse(atob(attrs.value)),
                            backgroundColor : [
                                '#1abc9c', '#3498db', '#34495e',
                                '#f1c40f', '#e74c3c', '#95a5a6',
                                '#2ecc71', '#9b59b6', '#e67e22',
                                '#ecf0f1', '#16a085', '#2980b9',
                                '#2c3e50', '#f39c12', '#c0392b',
                                '#7f8c8d', '#27ae60', '#8e44ad',
                                '#d35400', '#bdc3c7'
                            ]
                        }]
                    },
                    options : {
                        responsive : true,
                        maintainAspectRatio : false,
                        title : {
                            display : ('title' in attrs),
                            text : attrs.title || ''
                        }
                    }
                };

                if('labelDisplay' in attrs && attrs.labelDisplay == 0)
                {
                    ng.extend(chartData.options, {
                        legend: {
                            display: false
                        }
                    });
                }


                // Faz a chamada do grafico.
                $timeout(function() {
                    var chart = new Chart(ctx, chartData);
                });
            }
        };

    }]);

}) (angular);

