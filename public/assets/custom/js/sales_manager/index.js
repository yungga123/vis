
$(document).ready(function () {
  pieChart("chart_q1", 1);
  pieChart("chart_q2", 2);
  pieChart("chart_q3", 3);
  pieChart("chart_q4", 4);
});

function pieChart(chart, quarter) {
  let url = $("#tasklead_url").val();
  $.post(url, { quarter: quarter }).done(function (res) {
    //callback(res);
    //Highcharts PIE
    Highcharts.chart(chart, {
      chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: "pie",
      },
      title: {
        text: "Quarter " + quarter + " stats",
        align: "left",
      },
      tooltip: {
        pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
      },
      accessibility: {
        point: {
          valueSuffix: "%",
        },
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: "pointer",
          dataLabels: {
            enabled: true,
            format: "<b>{point.name}</b>: {point.percentage:.1f} %",
          },
        },
      },
      series: [
        {
          name: "Task Leads",
          colorByPoint: true,
          data: [
            {
              name: "Booked (100%)",
              y: res.booked,
              color: "green",
            },
            {
              name: "Negotiation (90%)",
              y: res.negotiation,
              color: "gray",
            },
            {
              name: "Evaluation (70%)",
              y: res.evaluation,
              color: "purple",
            },
            {
              name: "Developed Solution (50%)",
              y: res.dev_sol,
              color: "blue",
            },
            {
              name: "Qualified (30%)",
              y: res.qualified,
              color: "yellow",
            },
            {
              name: "Identified (10%)",
              y: res.identified,
              color: "red",
            },
          ],
        },
      ],
    });
  });
}
