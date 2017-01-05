<?php
/*
 * Get usage (CNH) for given projectID (ignore sysHost and date range). 
 */

$sysHost   = $_GET["sysHost"];
$startDate = $_GET["startDate"];
$endDate   = $_GET["endDate"];
$projectId = $_GET["projectId"]; // $projectId="s715|u1";

try {
    include (__DIR__ ."/connCSCSdb.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ----------------------------------------------------- 
    $sql="SELECT us.FK_fcl_id, us.allocation_start, us.project_id,
          qu.id, qu.type, 
          ROUND(us.sum_cpu_hours, 0) as usedCNH,
          3*qu.quota as 3mQuota, 
          ROUND(100.0*us.sum_cpu_hours/(3*qu.quota) ,1) as UsageP
          FROM cpu_usage_count us, quotas qu 
          WHERE                                                                                        
          us.project_id = qu.project_id AND   
          us.project_id = '$projectId' AND
          us.FK_fcl_id = 'DAINT' AND 
          us.FK_fcl_id = qu.FK_fcl_id AND
          us.allocation_start >= '2017-01-01';
    ";

    //print_r($sql);
    //   +-----------+------------------+------------+------+------+---------+---------+--------+
    //   | FK_fcl_id | allocation_start | project_id | id   | type | usedCNH | 3mQuota | Usage% |
    //   +-----------+------------------+------------+------+------+---------+---------+--------+
    //   | DAINT     | 2017-01-01       | s715       | 2174 | prod |     373 |   75000 |    0.5 |
    //   +-----------+------------------+------------+------+------+---------+---------+--------+

    // ---
    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    // ---
    echo "{ \"cols\": [
        {\"id\":\"\",\"label\":\" System\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" PeriodStart\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" pid\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" id\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Type\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Used\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" 3mQuota\",\"pattern\":\"\",\"type\":\"string\"},
        {\"id\":\"\",\"label\":\" Used%\",\"pattern\":\"\",\"type\":\"string\"}
        ], 
    \"rows\": [ 
    ";

$total_rows = $query->rowCount();
$row_num = 0;

foreach($result as $row){
    $row_num++;

    echo "{\"c\":[
    {\"v\":\"" . $row['FK_fcl_id'       ] . "\",\"f\":null},
    {\"v\":\"" . $row['allocation_start'] . "\",\"f\":null},
    {\"v\":\"" . $row['project_id'      ] . "\",\"f\":null},
    {\"v\":\"" . $row['id'              ] . "\",\"f\":null},
    {\"v\":\"" . $row['type'            ] . "\",\"f\":null},
    {\"v\":\"" . $row['usedCNH'         ] . "\",\"f\":null},
    {\"v\":\"" . $row['3mQuota'         ] . "\",\"f\":null},
    {\"v\":\"" . $row['UsageP'          ] . "\",\"f\":null}
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
