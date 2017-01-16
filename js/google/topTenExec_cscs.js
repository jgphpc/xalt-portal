/*!
 * Top Ten Executables 
 * History
 * 2015-Aug-10
 */

google.load('visualization', '1', {packages: ['corechart', 'bar', 'table']});

function topTenExec_cscs(sysHost, startDate, endDate) {

    console.log("cscs_topTenExec.php" + sysHost + startDate + endDate);
    var jsonChartData = $.ajax
        ({url: "include/cscs_topTenExec.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" +endDate,
         dataType:"json", async: false
         }).responseText;

    var count = checkJsonData(jsonChartData);
    // BubbleChart:
    if (count != 0) {

        document.getElementById("ten_exec_div").style.visibility = 'visible';
        document.getElementById("ten_exec_div0").style.visibility = 'visible';

        // Create our data table out of JSON data loaded from server.
        var chartData = new google.visualization.DataTable(jsonChartData);

        // Define Chart Options .
        var options = {title: 'Top Ten Executables',
            chartArea: {width: '80%', height:"70%", left: "auto" },
            hAxis: {title: 'Number of Jobs (log)',logScale: 'True', minValue: '0', maxValue: '1000000'},
            vAxis: {title: 'Core Hours (log)', logScale: 'True', minValue: '0' , maxValue: '10000000'},
            bubble: {textstyle: {fontSize: '3'}}
        };

        // Instantiate and draw chart.
        var chart = new google.visualization.BubbleChart(document.getElementById('ten_exec_div'));
        chart.draw(chartData, options);

    // Data table:
        document.getElementById("ten_exec_table_div").style.visibility = 'visible';
        document.getElementById("ten_exec_table_label").style.visibility = 'visible';
        var div_id = 'ten_exec_table_div';
        var table = makeTable(chartData, div_id, count);

    }

    if (count == 0){
        document.getElementById("ten_exec_div").style.visibility = 'hidden';
        document.getElementById("ten_exec_div0").style.visibility = 'hidden';
        document.getElementById("ten_exec_table_div").style.visibility = 'hidden';
        document.getElementById("ten_exec_table_label").style.visibility = 'hidden';
    }

}

function makeTable(TableData, div_id, count) {

    var tab_options;
    if (count > 10){
        tab_options = {title: 'Table View',
            showRowNumber: true,
            height: 260,
            width: '100%',
            allowHtml: true,
            alternatingRowStyle: true
        }
    } else {
        tab_options = {title: 'Table View',
            showRowNumber: true,
            height: '100%',
            width: '100%',
            allowHtml: true,
            alternatingRowStyle: true,
            page: 'enable', pageSize: '10'
        }
    }

    // Instantiate and Draw our Table
    var table = new google.visualization.Table(document.getElementById(div_id));

    table.draw(TableData, tab_options);
    return (table);
}


function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData); 
    return (o.rows.length);
}  
