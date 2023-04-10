//Highcharts PIE
Highcharts.chart('chart_q1', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Quarter 1 Stats',
        align: 'left'
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
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            }
        }
    },
    series: [{
        name: 'Task Leads',
        colorByPoint: true,
        data: [{
            name: 'Booked (100%)',
            y: 70.67,
            color: 'green'
        }, {
            name: 'Negotiation (90%)',
            y: 14.77,
            color: 'gray'
        },  {
            name: 'Evaluation (70%)',
            y: 4.86,
            color: 'purple'
        }, {
            name: 'Developed Solution (50%)',
            y: 2.63,
            color: 'blue'
        }, {
            name: 'Qualified (30%)',
            y: 1.53,
            color: 'yellow'
        },  {
            name: 'Identified (10%)',
            y: 1.40,
            color: 'red'
        }]
    }]
});