<?php
/*
 * Get PI for given projectID (ignore sysHost and date range). 
 */

$sysHost   = $_GET["sysHost"];
$startDate = $_GET["startDate"];
$endDate   = $_GET["endDate"];
$userId    = $_GET["userId"];    // $userId="martab";
$projectId = $_GET["projectId"]; // $projectId="s715|u1";

try {
    include (__DIR__ ."/connCSCSdb.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ----------------------------------------------------- 
    $sql="SELECT pro.proj_id, pro.proj_pers_id as PI, pro.proj_org_id, pro.proj_status, 
      pro.proj_domain, pro.proj_end, pro.proj_nodetype,
      per.pers_id as UID, per.pers_title, per.pers_fname, per.pers_lname, 
      per.pers_country
      FROM project pro, person per
      WHERE
      pro.proj_id='$projectId' AND
      per.pers_id='$userId' ;
    ";


    //print_r($sql);
// | proj_id | PI      | proj_org_id | proj_status | proj_domain | proj_end   | proj_nodetype | UID    | pers_title | pers_fname | pers_lname | pers_country |
// +---------+---------+-------------+-------------+-------------+------------+---------------+--------+------------+------------+------------+-------
// | s715    | polinod | USI         | Opened      | Astronomy   | 2017-09-30 | Hybrid        | martab | Ms.        | Marta      | Bon        | CH

    // ---
    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    // ---
    echo "{ \"cols\": [
        {\"id\":\"\",\"label\":\" pid\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" PI\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Org\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Status\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Domain\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" End\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Partition\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" uid\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Title\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Fname\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Lname\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Country\",\"pattern\":\"\",\"type\":\"string\"}
        ], 
    \"rows\": [ 
    ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    echo "{\"c\":[
    {\"v\":\"" . $row['proj_id'         ] . "\",\"f\":null},
    {\"v\":\"" . $row['PI'              ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_org_id'     ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_status'     ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_domain'     ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_end'        ] . "\",\"f\":null},
    {\"v\":\"" . $row['proj_nodetype'   ] . "\",\"f\":null},
    {\"v\":\"" . $row['UID'             ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_title'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_fname'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_lname'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['pers_country'    ] . "\",\"f\":null}
    ";

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

catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
