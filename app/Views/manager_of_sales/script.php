<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>


<script>
    //Pie (OVerall stats)
    Highcharts.chart('overall-stats', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Over-All Stats Chart',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        accessibility: {
            point: {
                valueSuffix: ''
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y} '
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Quantity',
            colorByPoint: true,
            data: [{
                name: 'Booked',
                y: <?= count($bookedNumber) ?>,
                color: 'green'
            }, {
                name: 'Negotiation',
                y: <?= count($negotiationNumber) ?>
            }, {
                name: 'Evaluation',
                y: <?= count($evalNumber) ?>
            }, {
                name: 'Developed Solution',
                y: <?= count($devsolNumber) ?>
            }, {
                name: 'Qualified',
                y: <?= count($qualifiedNumber) ?>
            }, {
                name: 'Identified',
                y: <?= count($identifiedNumber) ?>
            }]
        }]
    });

    //HalfDonut (PointerQ1)
    Highcharts.chart('pointer-q1', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: 'Q1',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%'],
                size: '110%'
            }
        },
        series: [{
            type: 'pie',
            name: 'Browser share',
            innerSize: '50%',
            data: [
                ['Chrome', 73.86],
                ['Edge', 11.97],
                ['Firefox', 5.52],
                ['Safari', 2.98],
                ['Internet Explorer', 1.90],
                {
                    name: 'Other',
                    y: 3.77,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });

    Highcharts.chart('pointer-q2', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: 'Q2',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%'],
                size: '110%'
            }
        },
        series: [{
            type: 'pie',
            name: 'Browser share',
            innerSize: '50%',
            data: [
                ['Chrome', 73.86],
                ['Edge', 11.97],
                ['Firefox', 5.52],
                ['Safari', 2.98],
                ['Internet Explorer', 1.90],
                {
                    name: 'Other',
                    y: 3.77,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });

    Highcharts.chart('pointer-q3', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: 'Q3',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%'],
                size: '110%'
            }
        },
        series: [{
            type: 'pie',
            name: 'Browser share',
            innerSize: '50%',
            data: [
                ['Chrome', 73.86],
                ['Edge', 11.97],
                ['Firefox', 5.52],
                ['Safari', 2.98],
                ['Internet Explorer', 1.90],
                {
                    name: 'Other',
                    y: 3.77,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });

    Highcharts.chart('pointer-q4', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: 'Q4',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%'],
                size: '110%'
            }
        },
        series: [{
            type: 'pie',
            name: 'Browser share',
            innerSize: '50%',
            data: [
                ['Chrome', 73.86],
                ['Edge', 11.97],
                ['Firefox', 5.52],
                ['Safari', 2.98],
                ['Internet Explorer', 1.90],
                {
                    name: 'Other',
                    y: 3.77,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });
</script>