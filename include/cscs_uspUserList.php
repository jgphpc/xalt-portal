<?php
/*
 * Get details (Institution and Name) for given userID (ignore sysHost and date range). 
 */

/* -----------------------------------------------------------------------
mysql> select pers_org_id,pers_title,pers_fname,pers_lname,pers_country,pers_status from person where pers_id="adeli";
+-------------+------------+------------+------------+--------------+-------------+
| pers_org_id | pers_title | pers_fname | pers_lname | pers_country | pers_status |
+-------------+------------+------------+------------+--------------+-------------+
| ETHZ        | Mr.        | Adel       | Imamovic   | CH           | Opened      |
+-------------+------------+------------+------------+--------------+-------------+
*/

/* warning: ko if \" is missing ! the following is ok:
{ "cols": [
    {"id":"","label":"UID","pattern":"","type":"string"},
    {"id":"","label":"Institution","pattern":"","type":"string"},
    {"id":"","label":"Title","pattern":"","type":"string"},
    {"id":"","label":"Fname","pattern":"","type":"string"},
    {"id":"","label":"Lname","pattern":"","type":"string"},
    {"id":"","label":"Country","pattern":"","type":"string"},
    {"id":"","label":"Status","pattern":"","type":"string"}
], 
"rows": [ 
{"c":[
    {"v":"adeli","f":null},
    {"v":"ETHZ","f":null},
    {"v":"Mr.","f":null},
    {"v":"Adel","f":null},
    {"v":"Imamovic","f":null},
    {"v":"CH","f":null},
    {"v":"Opened","f":null}
    ]} 
        ] 
}
----------------------------------------------------------------------- 
KO:
{ "cols": [
        {"id":"","label":" pid","pattern":"","type":"string"},
        {"id":"","label":" Partition","pattern":"","type":"string"},
        {"id":"","label":" Status","pattern":"","type":"string"},
        {"id":"","label":" End","pattern":"","type":"string"},
        {"id":"","label":" uid","pattern":"","type":"string"},
        {"id":"","label":" gid","pattern":"","type":"string"}
        ], 
    "rows": [ {"c":[
    {"v":"s715","f":null},
    {"v":"Hybrid","f":null},
    {"v":"Opened","f":null},
    {"v":"2017-09-30","f":null},
    {"v":"martab","f":null},
     {"v":"s715","f":null}, <----------- , pas OK
        ]}, {"c":[
    {"v":"u1","f":null},
    {"v":"Multicore","f":null},
    {"v":"Opened","f":null},
    {"v":"2018-03-31","f":null},
    {"v":"martab","f":null},
     {"v":"u1","f":null} <----------- OK
        ]}
] }

OK:
{ "cols": [
    {"id":"","label":"Build User","pattern":"","type":"string"}, 
    {"id":"","label":"Earliest LinkDate","pattern":"","type":"string"}, 
    {"id":"","label":"Latest LinkDate","pattern":"","type":"string"}, 
    {"id":"","label":"Count","pattern":"","type":"number"}
    ], 
    "rows": [ {"c":[
        {"v":"giome","f":null},
        {"v":"2016-12-22 01:52:57","f":null},
        {"v":"2016-12-22 03:31:56","f":null},
        {"v":17,"f":null}
        ]}, {"c":[
        {"v":"jfang","f":null},
        {"v":"2016-12-12 21:27:20","f":null},
        {"v":"2016-12-15 13:10:30","f":null},
        {"v":11,"f":null}
        ]}, {"c":[
        {"v":"vsharma","f":null},
        {"v":"2016-12-19 18:29:08","f":null},
        {"v":"2016-12-19 19:13:34","f":null},
        {"v":7,"f":null}
        ]} ] }

*/

$sysHost    = $_GET["sysHost"];
$startDate  = $_GET["startDate"];
$endDate    = $_GET["endDate"];
$userId     = $_GET["userId"]; // $userId="martab";

try {
    include (__DIR__ ."/connCSCSdb.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ----------------------------------------------------- STEP1:

    // prm.FK_project as 2d_gid_from_project 
#     $sql="SELECT 
#         pr.proj_id, pr.proj_nodetype, pr.proj_status, pr.proj_end,
#         prm.user_name, prm.FK_project
#         FROM project pr, project_member prm
#         WHERE prm.user_name   = '$userId'
#             AND prm.FK_project  = pr.proj_id
#             AND pr.proj_status = 'Opened'
#             AND pr.proj_end >= '2017-01-01';
#         ";

    // acct gives primary groupid:
    $sql="SELECT 
        pr.proj_id, pr.proj_nodetype, pr.proj_status, pr.proj_end, 
        act.acct_id, act.acct_proj_id
        FROM project pr, account act 
        WHERE act.acct_id='$userId' 
        AND act.acct_fcl_id='DAINT' 
        AND pr.proj_status='Opened' 
        AND pr.proj_end >= '2017-01-01' 
        AND act.acct_proj_id=pr.proj_id ;
    ";

    //print_r($sql);
    // +---------+---------------+-------------+------------+-----------+---------------------+
    // | proj_id | proj_nodetype | proj_status | proj_end   | user_name | 2d_gid_from_project |
    // +---------+---------------+-------------+------------+-----------+---------------------+
    // | s715    | Hybrid        | Opened      | 2017-09-30 | martab    | s715                |
    // | u1      | Multicore     | Opened      | 2018-03-31 | martab    | u1                  |
    // +---------+---------------+-------------+------------+-----------+---------------------+

    // ---
    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    // ---
    echo "{ \"cols\": [
        {\"id\":\"\",\"label\":\" pid\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Partition\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Status\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" End\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" uid\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" gid\",\"pattern\":\"\",\"type\":\"string\"}
        ], 
    \"rows\": [ 
    ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    echo "{\"c\":[
    {\"v\":\"" . $row['proj_id'         ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_nodetype'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_status'     ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_end'        ] . "\",\"f\":null},
    {\"v\":\"" . $row['acct_id'         ] . "\",\"f\":null},
    {\"v\":\"" . $row['acct_proj_id'    ] . "\",\"f\":null}
    ";

//    {\"v\":\"" . $row['user_name'       ] . "\",\"f\":null},
//    {\"v\":\"" . $row['FK_project'      ] . "\",\"f\":null}

    if ($row_num == $total_rows){
//        echo " {\"v\":\"" . $row['FK_project'  ] . "\",\"f\":null}
        echo "]}
        ";
    } else {
//        echo " {\"v\":\"" . $row['FK_project'  ] . "\",\"f\":null},
    
        echo "]},
        ";
    } // else

}
echo " ] }";
} // the end


// -----------------------------------------------------
/* STEP2:
    $projectId="s715"; // TODO... + test with: userId=martab
    // pro.proj_pers_id as PI / per.pers_id as UID
    $sql="SELECT 
      pro.proj_id, pro.proj_pers_id , pro.proj_org_id, pro.proj_status, 
      pro.proj_domain, pro.proj_end, pro.proj_nodetype,
      per.pers_id, per.pers_title, per.pers_fname, per.pers_lname, per.pers_country
      FROM project pro, person per
      WHERE
          pro.proj_id like concat('%', '$projectId','%') AND
          per.pers_id like concat('%', '$userId','%') ;
      ";
*/

/*
// AND xr.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
//cscs: xr.syshost = '$sysHost' AND
    //print_r($sql);

    $query = $conn->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

// | s715    | polinod | USI | Opened | Astronomy   | 2017-09-30 | Hybrid |   martab | Ms. | Marta | Bon | CH 

    echo "{ \"cols\": [
        {\"id\":\"\",\"label\":\"gid\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\"PI\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\"Institution\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Status \",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Domain \",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" End \",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Partition \",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" uid \",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\"Title\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\"Fname\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\"Lname\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\"Country\",\"pattern\":\"\",\"type\":\"string\"}
        ], 
    \"rows\": [ ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    echo "{\"c\":[
    {\"v\":\"" . $row['proj_id'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_pers_id' ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_org_id'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_status'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_domain'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_end'     ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_nodetype'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_id'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_title'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_fname'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_lname'   ] . "\",\"f\":null},
    ";

    if ($row_num == $total_rows){
        echo "
        {\"v\":\"" . $row['pers_country' ] . "\",\"f\":null}
        ]}
        ";
    } else {
        echo "
        {\"v\":\"" . $row['pers_country' ] . "\",\"f\":null},
        ]},
        ";
    } // else

}
echo " ] }";
} // the end

*/

/* OLD OK:
    $sql="SELECT 
        pers_id,
        pers_org_id,
        pers_title,pers_fname,pers_lname,
        pers_country,pers_status 
        FROM person 
        WHERE person.pers_id like concat('%', '$userId','%');
    "; 

    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['proj_id'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_pers_id' ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_org_id'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_status'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_domain'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_end'     ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_nodetype'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_id'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_title'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_fname'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_lname'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_country' ] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['proj_id'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_pers_id' ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_org_id'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_status'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_domain'  ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_end'     ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_nodetype'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_id'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_title'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_fname'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_lname'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_country' ] . "\",\"f\":null},
    ]}, ";
    } // else
}
echo " ] }";
}
*/


// TODO: restruct if/else

/* OK:
    echo "{ \"cols\": [
{\"id\":\"\",\"label\":\"GID\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Institution\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Title\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Fname\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Lname\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Country\",\"pattern\":\"\",\"type\":\"string\"},
{\"id\":\"\",\"label\":\"Status\",\"pattern\":\"\",\"type\":\"string\"}
], 
\"rows\": [ ";
*/

/*  OK:
    if ($row_num == $total_rows){
        echo "{\"c\":[
    {\"v\":\"" . $row['pers_id'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_org_id'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_title'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_fname'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_lname'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_country'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_status'] . "\",\"f\":null}
    ]}";
    } else {
        echo "{\"c\":[
    {\"v\":\"" . $row['pers_id'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_org_id'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_title'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_fname'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_lname'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_country'] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_status'] . "\",\"f\":null}
    ]}, ";
    } 
*/



catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
