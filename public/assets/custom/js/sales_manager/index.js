
$(document).ready(function () {
  pieChart("chart_q1", 1);
  pieChart("chart_q2", 2);
  pieChart("chart_q3", 3);
  pieChart("chart_q4", 4);

  tasklead_stats();
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

function tasklead_stats() {
  let url = $('#tasklead_stats_url').val();

  $.post(url,{}).done(function(res){
    //console.log(res);

    $('.booked_count').html(res.booked);
    $('.negotiation_count').html(res.negotiation);
    $('.evaluation_count').html(res.evaluation);
    $('.dev_sol_count').html(res.dev_sol);
    $('.qualified_count').html(res.qualified);
    $('.identified_count').html(res.identified);

    let booked_amt = Number(res.booked_amt[0].project_amount);
    let negotiation_amt = Number(res.negotiation_amt[0].project_amount);
    let evaluation_amt = Number(res.evaluation_amt[0].project_amount);
    let dev_sol_amt = Number(res.dev_sol_amt[0].project_amount);
    let qualified_amt = Number(res.qualified_amt[0].project_amount);
    let identified_amt = Number(res.identified_amt[0].project_amount);
    $('.booked_amt').html(booked_amt.toLocaleString("en",{minimumFractionDigits: 2}));
    $('.negotiation_amt').html(negotiation_amt.toLocaleString("en",{minimumFractionDigits: 2}));
    $('.evaluation_amt').html(evaluation_amt.toLocaleString("en",{minimumFractionDigits: 2}));
    $('.dev_sol_amt').html(dev_sol_amt.toLocaleString("en",{minimumFractionDigits: 2}));
    $('.qualified_amt').html(qualified_amt.toLocaleString("en",{minimumFractionDigits: 2}));
    $('.identified_amt').html(identified_amt.toLocaleString("en",{minimumFractionDigits: 2}));


    //console.log(res.booked_amt[0].project_amount);

  });
}
