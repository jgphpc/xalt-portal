/*!ZZ
 * User Software Provenance JS 
 * History
 * 2015-Oct
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function usp(sysHost, startDate, endDate, userId) { // start first with User details

    // include/cscs_uspUserList.php (test with martab) = usp_cscsuid0_div
    var jsonTableData = $.ajax
        ({url: "include/cscs_uspUserList.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId,
         dataType:"json", async: false
         }).responseText;

    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'usp_cscsuid0_div';

    // Hide all tables which are not required.
    var idsToHide = [ 'cscsUid0', 'usp_cscsuid0_div', 
        'cscsPI', 'usp_cscsprojectpi_div',
        'cscsSbucheck', 'usp_cscssbucheck_div',
        'lblExec0', 'usp_exec_div', 'lblExec1',
        'lblExecDetail0', 'usp_exDetail_div', 
        'lblObj', 'obj_div', 
        'lblUspRun0', 'usp_run_div', 'lblUspRun1', 
        'lblRunObj', 'runObj_div', 'lblRunEnv', 'run_env_div', 
        'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    //console.log("usp.js: before checkJsonData");
    var count = checkJsonData(jsonTableData);             // if no data is returned do Nothing!!
    //console.log("usp.js: after checkJsonData");
    if (count!=0) {

        document.getElementById("cscsUid0").style.visibility = 'visible';
        document.getElementById("usp_cscsuid0_div").style.visibility = 'visible';

        // Create our data table out of JSON data loaded from server.
        var TableData =new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        // get userId
        userId = [TableData.getValue(0,4)]; //= row0,col4

        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            // var exec = [TableData.getValue(row,0)];

            projectId = [TableData.getValue(row,0)];
            gTu_projectpi(sysHost, startDate, endDate, userId, projectId); // project PI = usp_cscsprojectpi_div

            projectId = [TableData.getValue(row,0)];
            gTu_sbucheck(sysHost, startDate, endDate, projectId); // project usage per pid = usp_cscsuid1_div
        }

        // Add our selection handler.
        google.visualization.events.addListener(table, 'select', selectTable);

        // Display "List of Executables" of userId:
        userId = [TableData.getValue(0,4)]; //= row0,col4
        gTu_listexes(sysHost, startDate, endDate, userId); // execList = usp_exec_div

    }

}

function gTu_projectpi(sysHost, startDate, endDate, userId, projectId) {
// = project PI details

    // include/cscs_projectpi.php
    // console.log("[cscs_projectpi.php] " + userId + projectId);
    var jsonTableData = $.ajax
        ({url: "include/cscs_projectpi.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId + "&projectId=" + projectId,
         dataType:"json", async: false
         }).responseText;

    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'usp_cscsprojectpi_div';

    // Hide all tables which are not required.
    var idsToHide = [ 
        'lblExecDetail0', 'usp_exDetail_div', 
        'lblObj', 'obj_div', 
        'lblUspRun0', 'usp_run_div', 'lblUspRun1', 
        'lblRunObj', 'runObj_div', 'lblRunEnv', 'run_env_div', 
        'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);  // if no data is returned do Nothing! 
    if (count!=0) {

        document.getElementById("cscsPI").style.visibility = 'visible';
        document.getElementById("usp_cscsprojectpi_div").style.visibility = 'visible';
        //document.getElementById("lblExec1").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

/*
        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            // var exec = [TableData.getValue(row,0)];
            // userId = [TableData.getValue(row,2)];
            // gTu0(sysHost, startDate, endDate, userId, exec); // execList details
        }

        // Add our selection handler.
        google.visualization.events.addListener(table, 'select', selectTable);
*/

/* ---
        function selectHandler() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,6);
            gTu1(sysHost, startDate, endDate, userId, uuid);  // get run details
            gTu2(uuid);       // get object information 
            gTu5(uuid);       // get functions called  
        }

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);
--- */
    } else {
        // empty list = no exe found
        document.getElementById("cscsSbucheck").style.visibility = 'visible';
        document.getElementById("usp_cscssbucheck_div").style.visibility = 'visible';
    }
}


function gTu_sbucheck(sysHost, startDate, endDate, projectId) {
// = sbucheck = project usage per pid

    // include/cscs_sbucheck.php
    // console.log("[cscs_sbucheck.php] " + projectId);
    var jsonTableData = $.ajax
        ({url: "include/cscs_sbucheck.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&projectId=" + projectId,
         dataType:"json", async: false
         }).responseText;

    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'usp_cscssbucheck_div';

    // Hide all tables which are not required.
    var idsToHide = [ 
        'lblExecDetail0', 'usp_exDetail_div', 
        'lblObj', 'obj_div', 
        'lblUspRun0', 'usp_run_div', 'lblUspRun1', 
        'lblRunObj', 'runObj_div', 'lblRunEnv', 'run_env_div', 
        'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);  // if no data is returned do Nothing! 
    if (count!=0) {

        document.getElementById("cscsSbucheck").style.visibility = 'visible';
        document.getElementById("usp_cscssbucheck_div").style.visibility = 'visible';
        //document.getElementById("lblExec1").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

/*
        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            // var exec = [TableData.getValue(row,0)];
            // userId = [TableData.getValue(row,2)];
            // gTu0(sysHost, startDate, endDate, userId, exec); // execList details
        }

        // Add our selection handler.
        google.visualization.events.addListener(table, 'select', selectTable);
*/

/* ---
        function selectHandler() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,6);
            gTu1(sysHost, startDate, endDate, userId, uuid);  // get run details
            gTu2(uuid);       // get object information 
            gTu5(uuid);       // get functions called  
        }

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);
--- */
    } else {
        // empty list = no exe found
        document.getElementById("cscsSbucheck").style.visibility = 'visible';
        document.getElementById("usp_cscssbucheck_div").style.visibility = 'visible';
    }
}


function gTu_listexes(sysHost, startDate, endDate, userId) { // get exec list

    // include/uspExecList.php
    // console.log("[uspExecList.php] " + userId );
    var jsonTableData = $.ajax
        ({url: "include/uspExecList.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId,
         dataType:"json", async: false
         }).responseText;

    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'usp_exec_div';

    // Hide all tables which are not required.
    var idsToHide = [ 
        'lblExecDetail0', 'usp_exDetail_div', 
        'lblObj', 'obj_div', 
        'lblUspRun0', 'usp_run_div', 'lblUspRun1', 
        'lblRunObj', 'runObj_div', 'lblRunEnv', 'run_env_div', 
        'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);  // if no data is returned do Nothing! 
    if (count!=0) {

        document.getElementById("lblExec0").style.visibility = 'visible';
        document.getElementById("usp_exec_div").style.visibility = 'visible';
        document.getElementById("lblExec1").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var exec = [TableData.getValue(row,0)];
            // userId = [TableData.getValue(row,2)];
            // gTu0(sysHost, startDate, endDate, userId, exec); // execList details
        }

        // Add our selection handler.
        google.visualization.events.addListener(table, 'select', selectTable);


/* ---
        function selectHandler() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,6);
            gTu1(sysHost, startDate, endDate, userId, uuid);  // get run details
            gTu2(uuid);       // get object information 
            gTu5(uuid);       // get functions called  
        }

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);
--- */
    } else {
        // empty list = no exe found
        document.getElementById("lblExec0").style.visibility = 'visible';
        document.getElementById("usp_exec_div").style.visibility = 'visible';
        document.getElementById("lblExec1").style.visibility = 'visible';
    }
}
 
function gTu0(sysHost, startDate, endDate, userId, exec) {         // get exec detail list

    console.log("[uspExecDetail] " + userId + exec);
    var jsonTableData = $.ajax
        ({url: "include/uspExecDetail.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId + "&exec=" + exec,
         dataType:"json", async: false
         }).responseText;

    var div_id = 'usp_exDetail_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblExecDetail0', 'usp_exDetail_div', 'lblObj', 'obj_div',
        'lblUspRun0', 'usp_run_div', 'lblUspRun1','lblRunObj', 'runObj_div', 'lblRunEnv', 
        'run_env_div','lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count!=0) {

        document.getElementById("lblExecDetail0").style.visibility = 'visible';
        document.getElementById("usp_exDetail_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        function selectHandler() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,6);
            gTu1(sysHost, startDate, endDate, userId, uuid);       /* get run details */      
            gTu2(uuid);       /* get object information */
            gTu5(uuid);       /* get functions called  */
        }

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

    }
}   

/*
function gTu1(sysHost, startDate, endDate, userId, uuid) {         // get run details 

    console.log("[uspJobDetail:]" + sysHost + startDate + endDate + userId + uuid);
    var jsonTableData = $.ajax
        ({url:"include/uspJobDetail.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + 
         "&userId=" + userId  + "&uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    console.log(jsonTableData);

    var div_id = 'usp_run_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblUspRun0', 'usp_run_div', 'lblUspRun1',
        'lblObj', 'obj_div','lblRunObj', 'runObj_div', 
        'lblRunEnv', 'run_env_div', 'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             // if no data is returned do Nothing!! 
    if (count != 0) {

        document.getElementById("lblUspRun0").style.visibility = 'visible';
        document.getElementById("usp_run_div").style.visibility = 'visible';
        document.getElementById("lblUspRun1").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        function selectHandler() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var runId = TableData.getValue(row,0);
            // get run details irrespective of who built the code
            gTu3(runId);            // get runtime env detail 
            gTu4(runId);            // get objects at runtime 
        }

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

    }
}
*/

/*
function gTu2(uuid) {               // get object information 

    console.log("&uuid=" + uuid);

    var jsonTableData = $.ajax
        ({url:"include/getExecObj.php",
         data: "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'obj_div';

    // List ids to hide
    var idsToHide = [ 'lblObj', 'obj_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         // if no data is returned do Nothing!! 
    if (count != 0) {
        document.getElementById("lblObj").style.visibility = 'visible';
        document.getElementById("obj_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}
*/

/*
function gTu3(runId) {               // get runtime env information 

    console.log("&runId=" + runId);
    var jsonTableData = $.ajax
        ({url:"include/getRunEnv.php",
         data: "runId=" + runId,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'run_env_div';

    // List ids to hide
    var idsToHide = ['lblRunEnv', 'run_env_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         // if no data is returned do Nothing!! 
    if (count != 0) {
        document.getElementById("lblRunEnv").style.visibility = 'visible';
        document.getElementById("run_env_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}
*/

/*
function gTu4(runId) {               // get objects at runtime 

    console.log("&runId=" + runId);
    var jsonTableData = $.ajax
        ({url:"include/getRunObj.php",
         data: "runId=" + runId,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'runObj_div';

    // List ids to hide
    var idsToHide = ['lblRunObj', 'runObj_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         // if no data is returned do Nothing!! 
    if (count != 0) {
        document.getElementById("lblRunObj").style.visibility = 'visible';
        document.getElementById("runObj_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}
*/

/*
function gTu5(uuid) {               // get functions called  

    console.log("&uuid=" + uuid);

    var jsonTableData = $.ajax
        ({url:"include/getExecFunc.php",
         data: "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'func_div';

    // List ids to hide
    var idsToHide = [ 'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         // if no data is returned do Nothing!! 
    if (count != 0) {
        document.getElementById("lblFunc").style.visibility = 'visible';
        document.getElementById("func_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}
*/


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
    console.log("usp.js: inside1 checkJsonData: " + jsonTableData);
    var o = JSON.parse(jsonTableData);
    //console.log("usp.js: inside2 checkJsonData");
    return (o.rows.length);
}

function hideAllDivs (idsToHide) {

    var attrToHide = document.querySelectorAll("*[style]");

    for(var i=0; i< attrToHide.length; i++) {
        if ($.inArray(attrToHide[i].id, idsToHide) != -1){     // if ID is present in the list Hide it
            attrToHide[i].style.visibility = "hidden";
        }
    }
}

