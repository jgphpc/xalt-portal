<?php
/* 
 * Get totatSUs, though this query may not given the correct picture.
 */ 

$sysHost   = $_GET["sysHost"];
$startDate = $_GET["startDate"];
$endDate   = $_GET["endDate"];
 
try {
    date_default_timezone_set('Europe/Zurich');
    include (__DIR__ ."/conn.php");

    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT DISTINCT xl.link_program as linkProgram from xalt_link xl 
        where xl.build_syshost = '$sysHost' AND 
        xl.link_program IS NOT NULL AND 
        xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        ";
    $query = $conn->prepare($sql);
    $query->execute();
    $total_rows = $query->rowCount();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    $datetime1 = strtotime($startDate);
    $datetime2 = strtotime($endDate);

    $days = ($datetime2-$datetime1)/(3600*24);
    $monFlag = False; $weekFlag = False; $dayFlag = False; 

    switch(true) {
    case ($days > 30) :           # group by month 
        $dateFormat = " DATE_FORMAT(xl.date, '%b') AS Month ";
        $groupBy    = " GROUP BY Month, Year ";
        $monFlag    = True;
        break;
    case ($days < 30 && $days > 7):    # group by week
        $dateFormat = " DATE_FORMAT(xl.date, '%u') AS Week ";
        $groupBy    = " GROUP BY Week, Year ";
        $weekFlag    = True;
        break;
    case ($days < 7) :            # group by day
        $dateFormat = " DATE_FORMAT(xl.date, '%d-%b') AS Day ";
        $groupBy    = " GROUP BY Day, Year ";
        $dayFlag    = True;
        break;
    }


    /* Get Case statement for SQL query */
    $sum_col = array();
    foreach($result as $row){
        $sum_col[] = "SUM(CASE WHEN xl.link_program = '".$row['linkProgram']."' THEN 1 ELSE 0 END) AS '".$row['linkProgram']."'";
    }

    /* Get all compiler as object in json output */ 
    $col = array();
    foreach($result as $row){
        $col[] = $row[linkProgram];
    }                

    if(count($sum_col) > 0) {
        $sql = "
            SELECT $dateFormat,
            date(min(xl.date)) as DateTimeRange,
            YEAR(xl.date) AS Year,  
            ".implode(",", $sum_col)."
            FROM xalt_link xl         
            WHERE xl.build_syshost='$sysHost' AND          
            xl.link_program IS NOT NULL AND 
            xl.link_program NOT LIKE ' ' AND
            xl.date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'         
            $groupBy         
            ORDER BY Year desc, DateTimeRange ASC; 
        ";
    }

    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll(PDO:: FETCH_ASSOC);

    /* Get xAxis Category */
    $timeLine    = array();
    $strTimeLine = "";

    if ($monFlag){
        foreach($result as $row){
            $timeLine[] = $row['Month'];
        }
    } elseif ($weekFlag){
        foreach($result as $row){
            $timeLine[] = $row['DateTimeRange'];
        }
    } elseif ($dayFlag){
        foreach($result as $row){
            $timeLine[] = $row['Day'];
        }
    }
    $strTimeLine = implode(",", $timeLine);

    /* make dataseries in JSON format */
    $dataseries = array();
    for($i=0; $i < sizeof($col); $i++) {
        $data= array();
        foreach($result as $row){
            $data[] = $row[$col[$i]];
        }
        $strdata = implode(",", $data);
        $dataseries[] = "{\"name\" : " . "\"" . $col[$i] . "\", " . "\"data\" :[" . $strdata . "]}"; 
    }
    $strdataseries = "[".implode(", ",$dataseries)."]";

    # header and body of the json data chart
    echo ($strTimeLine. "#");
    echo ($strdataseries); 
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

?>
