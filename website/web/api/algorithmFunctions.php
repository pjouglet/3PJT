<?php

require_once("Classes.php");

$db = getDBData();


function getDBData()
{
    return new PDO("mysql:dbname=train-commander-bdd;host=127.0.0.1", "root", "");
}



function test ($startTime, $startStationId, $endStationId, $lastConnections, $excludedStations, &$connections, &$journeyConnectionArray) {

    //var_dump($journeyConnectionArray, $startTime, $startStationId, $endStationId);

    if ( $endStationId == $startStationId ) {
        $journeyConnectionArray[] = $lastConnections;
        //var_dump($journeyConnectionArray);
        return;
    }

    foreach ($connections as $connection) {
        if ($connection->startStationId == $startStationId && $connection->startTime >= $startTime && !in_array($connection->arrivalStationId, $excludedStations)) {
            $lastConnectionTmp = $lastConnections;
            $lastConnectionTmp[] = $connection;

            $excludedStationTmp = $excludedStations;
            $excludedStationTmp[] = $startStationId;

            test($connection->arrivalTime, $connection->arrivalStationId, $endStationId, $lastConnectionTmp, $excludedStationTmp, $connexions, $journeyConnectionArray);
        }
    }
}





function findCheapestJourneyInZone($startStation, $arrivalStation, $startTime) {
    global $db;
    $lastConnections = array();
    $journeyConnectionArray = array();
    $excludedStations = array();
    $timetable = array();

    //$startStation = new Station(10, "Lille", 1, 1);
    //$arrivalStation = new Station(30, "Marseille", 1, 3);


    // If national path
    if ($startStation->isCapital == 1 && $arrivalStation->isCapital == 1)
    {
        $timestamp = date('Y-n-j H:i:s', $startTime);

        $results = $db->prepare("SELECT c.stationid, c.pathid, c.start_time, s.start_stationid, s.end_stationid, s.cost, s.duree
                               FROM connections c
                               JOIN segments s ON c.segmentid = s.id
                               WHERE (c.pathid IN (SELECT id FROM paths WHERE is_national = 1))
                               AND (c.start_time >= DATE(:startTime));");
        $results->execute(array('startTime' => $timestamp));
        //var_dump($timestamp);

        foreach ($results as $r)
        {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] . 'S'))->getTimestamp(),
                $r["pathid"], $r["cost"]);
        }
    }/*
    else if ($startStation->zoneId == 2 && $arrivalStation->zoneId == 2)
    {
        $stations = array(new Station(30, "Marseille", 1, 2), new Station(31, "Cassis", 0, 2), new Station(32, "La Ciotat", 0, 2), new Station(33, "Aubagne", 0, 2));
        $timetable = array(new Connection($stations[0], $stations[1], 5100, 5200, 7, 5), new Connection($stations[1], $stations[2], 5205, 5300, 7, 3), new Connection($stations[0], $stations[3], 5150, 5350, 8, 7), new Connection($stations[3], $stations[2], 5400, 5500, 9, 4));
    }*/
    else {
        $timestamp = date('Y-n-j H:i:s', $startTime);

        $results = $db->prepare("SELECT c.stationid, c.pathid, c.start_time, s.start_stationid, s.end_stationid, s.cost, s.duree
                                 FROM connections c JOIN segments s ON c.segmentid = s.id
                                 WHERE (c.stationid = (SELECT id FROM stations WHERE c.stationid = id AND zoneid = :zoneid))
                                 AND (c.start_time >= DATE(:startTime));");
        $results->execute(array('zoneid' => $startStation->zoneId, 'startTime' => $timestamp));
        //$result = $results->fetch();
        //$startStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);


        foreach ($results->fetchAll() as $r) {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] . 'S'))->getTimestamp(),
                $r["pathid"], $r["cost"]);
        }

        //$stations = array(new Station(0, "Valenciennes", 0, 0), new Station(1, "Saint Amand les Eaux", 0, 0), new Station(2, "Lille", 1, 0), new Station(3, "Denain", 0, 0));
        //$timetable = array(new Connection($stations[3], $stations[2], 150, 900, 3, 9), new Connection($stations[0], $stations[3], 0, 100, 2, 2), new Connection($stations[0], $stations[1], 0, 100, 1, 1), new Connection($stations[1], $stations[2], 110, 250, 1, 5));
    }

    if (!isset($timetable)) {
        return null;
    }

    // Sort connections by start time. Needed for algorithm.
    usort($timetable, "compareConnectionsByStartTime");




	foreach ($timetable as $connection) {
        if ($connection->startStationId == $startStation->id) {
            $lastConnectionTmp = $lastConnections;
            $lastConnectionTmp[] = $connection;

            $excludedStationTmp = $excludedStations;
            $excludedStationTmp[] = $startStation->id;

            test($connection->arrivalTime, $connection->arrivalStationId, $arrivalStation->id, $lastConnectionTmp, $excludedStationTmp, $timetable, $journeyConnectionArray);
        }
    }

    if (!isset($journeyConnectionArray) or $journeyConnectionArray == null)
        return null;


    $cheapestJourney = $journeyConnectionArray[0];

    foreach ($journeyConnectionArray as $j)
    {
        $minPrice = 0;
        $price = 0;
        foreach ($j as $c)
            $price += $c->price;
        foreach ($cheapestJourney as $c)
            $minPrice += $c->price;
        if ($price < $minPrice)
            $cheapestJourney = $j;
    }

    var_dump($cheapestJourney);
    return ($cheapestJourney);
}



/*
function findCheapestJourneyInZone($startStationId, $arrivalStationId, $startTime)
{
    // First test
    if ($startStationId == $arrivalStationId) {
        return null;
    }

    global $db;


    //set the distance array
    $_distArr = array();
    $_distArr[1][2] = 7;
    $_distArr[1][3] = 9;
    $_distArr[1][6] = 14;
    $_distArr[2][1] = 7;
    $_distArr[2][3] = 10;
    $_distArr[2][4] = 15;
    $_distArr[3][1] = 9;
    $_distArr[3][2] = 10;
    $_distArr[3][4] = 11;
    $_distArr[3][6] = 2;
    $_distArr[4][2] = 15;
    $_distArr[4][3] = 11;
    $_distArr[4][5] = 6;
    $_distArr[5][4] = 6;
    $_distArr[5][6] = 9;
    $_distArr[6][1] = 14;
    $_distArr[6][3] = 2;
    $_distArr[6][5] = 9;

//the start and the end
    $a = 1;
    $b = 5;

//initialize the array for storing
    $S = array();//the nearest path with its parent and weight
    $Q = array();//the left nodes without the nearest path
    foreach (array_keys($_distArr) as $val) $Q[$val] = PHP_INT_MAX;
    $Q[$a] = 0;

//start calculating
    while (!empty($Q)) {
        $min = array_search(min($Q), $Q);//the most min weight
        if ($min == $b) break;
        foreach ($_distArr[$min] as $key => $val) if (!empty($Q[$key]) && $Q[$min] + $val < $Q[$key]) {
            $Q[$key] = $Q[$min] + $val;
            $S[$key] = array($min, $Q[$key]);
        }
        unset($Q[$min]);
    }

//list the path
    $path = array();
    $pos = $b;
    while ($pos != $a) {
        $path[] = $pos;
        $pos = $S[$pos][0];
    }
    $path[] = $a;
    $path = array_reverse($path);

//print result
    echo "<br />From $a to $b";
    echo "<br />The length is " . $S[$b][1];
    echo "<br />Path is " . implode('->', $path);
}*/


// Useless
function findQuickestJourneysInRange($startStationId, $arrivalStationId, $minTime, $maxTime)
{
    $journeys = getQuickestJourneysInRange($startStationId, $arrivalStationId, $minTime, $maxTime);

    var_dump($journeys);
    return ($journeys);
}





// Can general
function findQuickestJourneys($searchMode, $startStationId, $arrivalStationId, $startTime)
{
    $minTime = $startTime - 3600;
    $maxTime = $startTime + 7200;

    $journeys = getQuickestJourneysInRange($startStationId, $arrivalStationId, $minTime, $maxTime);

    $earlyJourneys = array();

    foreach ($journeys as $j) {
        if ($j->startTimes[0] < $startTime) {
            $earlyJourneys[] = $j;
        }
    }

    if (count($earlyJourneys) >= 2) {
        for ($i = 0; $i < count($earlyJourneys) - 2; $i++) {
            array_shift($journeys);
        }
    }

    var_dump($journeys);
    return ($journeys);
}





function findJourney($searchMode, $startStationId, $arrivalStationId, $startTime)
{
    // First test
    if ($startStationId == $arrivalStationId)
        return null;

    global $db;

    $journey = new Journey(array(), array(), array(), 0);

    // Get departure station
    $results = $db->prepare("SELECT * FROM stations WHERE id = :station;");
    $results->execute(array('station' => $startStationId));
    $result = $results->fetch();
    if ($result == null) {
        return null;
    } else {
        $startStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);
    }


    // Get arrival station
    $results->execute(array('station' => $arrivalStationId));
    $result = $results->fetch();
    if ($result == null) {
        return null;
    } else {
        $arrivalStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);
    }


    //$startStation = new Station(0, "Valenciennes", 0, 0);
    //$arrivalStation = new Station(2, "Lille", 1, 0);
    //$arrivalStation = new Station(32, "La Ciotat", 0, 2);

    // If we stay within the same zone
    if ($startStation->zoneId == $arrivalStation->zoneId)
    {
        if ($searchMode == "time")
            $connections = findQuickestJourneyInZone($startStation, $arrivalStation, $startTime);
        else
            $connections = findCheapestJourneyInZone($startStation, $arrivalStation, $startTime);

        if ($connections == null)
            return null;

        putConnectionsInJourney($journey, $connections);
    }
    else if ($startStation->zoneId != $arrivalStation->zoneId)
    {
        // Get national departure station
        $results = $db->prepare("SELECT * FROM stations WHERE is_national = 1 AND zoneid = :zoneid;");
        $results->execute(array('zoneid' => $startStation->zoneId));
        $result = $results->fetch();
        $startNationalStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);

        // Get national arrival station
        $results->execute(array('zoneid' => $arrivalStation->zoneId));
        $result = $results->fetch();
        $arrivalNationalStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);

        //$startNationalStation = new Station(2, "Lille", 1, 0);
        //$arrivalNationalStation = new Station(30, "Marseille", 1, 2);

        if ($startStation != $startNationalStation)
        {
            if ($searchMode == "time")
                $connections = findQuickestJourneyInZone($startStation, $startNationalStation, $startTime);
            else
                $connections = findCheapestJourneyInZone($startStation, $startNationalStation, $startTime);

            if ($connections == null)
                return null;

            putConnectionsInJourney($journey, $connections);
        }


        if (end($journey->arrivalTimes) != false)
            if ($searchMode == "time")
                $connections = findQuickestJourneyInZone($startNationalStation, $arrivalNationalStation, end($journey->arrivalTimes));
            else
                $connections = findCheapestJourneyInZone($startNationalStation, $arrivalNationalStation, end($journey->arrivalTimes));
        else
            if ($searchMode == "time")
                $connections = findQuickestJourneyInZone($startNationalStation, $arrivalNationalStation, $startTime);
            else
                $connections = findCheapestJourneyInZone($startNationalStation, $arrivalNationalStation, $startTime);

        if ($connections == null)
            return null;

        putConnectionsInJourney($journey, $connections);


        if ($arrivalStation != $arrivalNationalStation)
        {
            if ($searchMode == "time")
                $connections = findQuickestJourneyInZone($arrivalNationalStation, $arrivalStation, end($journey->arrivalTimes));
            else
                $connections = findCheapestJourneyInZone($arrivalNationalStation, $arrivalStation, end($journey->arrivalTimes));

            if ($connections == null)
                return null;

            putConnectionsInJourney($journey, $connections);
        }
    }
    else
        return null;

    var_dump($journey);
    return ($journey);
}


function findQuickestJourneyInZone($startStation, $arrivalStation, $startTime)
{
    // Initialisation
    global $db;
    $stations = null;
    $timetable = null;
    $inConnection = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp[$startStation->id] = $startTime;


    // If national path
    if ($startStation->isCapital == 1 && $arrivalStation->isCapital == 1) {
        $timestamp = date('Y-n-j H:i:s', $startTime);

        $results = $db->prepare("SELECT c.stationid, c.pathid, c.start_time, s.start_stationid, s.end_stationid, s.cost, s.duree
                               FROM connections c
                               JOIN segments s ON c.segmentid = s.id
                               WHERE (c.pathid IN (SELECT id FROM paths WHERE is_national = 1))
                               AND (c.start_time >= DATE(:startTime));");
        $results->execute(array('startTime' => $timestamp));
        //var_dump($timestamp);

        foreach ($results as $r) {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] . 'S'))->getTimestamp(),
                $r["pathid"], $r["cost"]);
        }

        //$stations = array(new Station(20, "Paris", 1, 1), new Station(30, "Marseille", 1, 2), new Station(2, "Lille", 1, 0));
        //$timetable = array(new Connection($stations[2], $stations[0], 1100, 2000, 4, 20), new Connection($stations[0], $stations[1], 2100, 4000, 5, 40), new Connection($stations[2], $stations[1], 2000, 5000, 6, 65));
    }/*
    else if ($startStation->zoneId == 2 && $arrivalStation->zoneId == 2)
    {
        $stations = array(new Station(30, "Marseille", 1, 2), new Station(31, "Cassis", 0, 2), new Station(32, "La Ciotat", 0, 2), new Station(33, "Aubagne", 0, 2));
        $timetable = array(new Connection($stations[0], $stations[1], 5100, 5200, 7, 5), new Connection($stations[1], $stations[2], 5205, 5300, 7, 3), new Connection($stations[0], $stations[3], 5150, 5350, 8, 7), new Connection($stations[3], $stations[2], 5400, 5500, 9, 4));
    }*/
    else {
        $timestamp = date('Y-n-j H:i:s', $startTime);

        $results = $db->prepare("SELECT c.stationid, c.pathid, c.start_time, s.start_stationid, s.end_stationid, s.cost, s.duree
                                 FROM connections c JOIN segments s ON c.segmentid = s.id
                                 WHERE (c.stationid = (SELECT id FROM stations WHERE c.stationid = id AND zoneid = :zoneid))
                                 AND (c.start_time >= DATE(:startTime));");
        $results->execute(array('zoneid' => $startStation->zoneId, 'startTime' => $timestamp));
        //$result = $results->fetch();
        //$startStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);


        foreach ($results->fetchAll() as $r) {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] . 'S'))->getTimestamp(),
                $r["pathid"], $r["cost"]);
        }

        //$stations = array(new Station(0, "Valenciennes", 0, 0), new Station(1, "Saint Amand les Eaux", 0, 0), new Station(2, "Lille", 1, 0), new Station(3, "Denain", 0, 0));
        //$timetable = array(new Connection($stations[3], $stations[2], 150, 900, 3, 9), new Connection($stations[0], $stations[3], 0, 100, 2, 2), new Connection($stations[0], $stations[1], 0, 100, 1, 1), new Connection($stations[1], $stations[2], 110, 250, 1, 5));
    }

    if (!isset($timetable)) {
        return null;
    }

    // Sort connections by start time. Needed for algorithm.
    usort($timetable, "compareConnectionsByStartTime");

    //var_dump($timetable);

    // Main algorithm
    foreach ($timetable as $cID => $c) {
        if ($arrivalTimestamp[$c->startStationId] <= $c->startTime && $arrivalTimestamp[$c->arrivalStationId] > $c->arrivalTime) {
            //var_dump($inConnection[$c->arrivalStationId]);
            $arrivalTimestamp[$c->arrivalStationId] = $c->arrivalTime;
            $inConnection[$c->arrivalStationId] = $cID;
            //var_dump($inConnection[$c->arrivalStationId]);
            //var_dump($cID);
        }
    }
    //var_dump($inConnection[$arrivalStation->id]);
    /*
    if ($inConnection[$arrivalStation->id] === PHP_INT_MAX)
    {
        echo "NO SOLUTION\n";
        return null;
    }*/


    // On dépile pour afficher les résultats
    $route = [];
    $lastConnectionIndex = $inConnection[$arrivalStation->id];
    while ($lastConnectionIndex !== PHP_INT_MAX) {
        $connection = $timetable[$lastConnectionIndex];
        $route[] = $connection;
        $lastConnectionIndex = $inConnection[$connection->startStationId];
    };
    foreach (array_reverse($route) as $row) {
        //printf("%s to %s : %d %d<br />", $row->startStationId, $row->arrivalStationId, $row->startTime, $row->arrivalTime);
    }
    //echo "\n";

    return (array_reverse($route));
}


function compareConnectionsByStartTime($c1, $c2)
{
    if ($c1->startTime == $c2->startTime) {
        return 0;
    }
    return ($c1->startTime < $c2->startTime) ? -1 : 1;
}


// Can general
function getQuickestJourneysInRange($startStationId, $arrivalStationId, $minTime, $maxTime)
{
    $journeys = null;

    while ($minTime <= $maxTime) {
        $journey = findQuickestJourney($startStationId, $arrivalStationId, $minTime);
        if (isset($journey) and $journey != null) {
            if ($journey->startTimes[0] > $maxTime) {
                break;
            }
            if (isset($journeys)) {
                if (!in_array($journey, $journeys)) {
                    $journeys[] = $journey;
                    $minTime = $journey->startTimes[0];
                }
            } else {
                $journeys[] = $journey;
                $minTime = $journey->startTimes[0];
            }
        }

        $minTime += 600;
    }

    return ($journeys);
}


function putConnectionsInJourney($journey, $connections)
{
    $startPathConnection = $connections[0];
    $lastPathConnection = $connections[0];
    $pathId = $connections[0]->pathId;
    $price = 0;

    global $db;
    $results = $db->prepare("SELECT name FROM stations WHERE id = :station;");

    foreach ($connections as $i => $c) {
        if ($c->pathId != $pathId) {
            $results->execute(array('station' => $startPathConnection->startStationId));
            $result = $results->fetch();
            $journey->stations[] = $result["name"];

            $results->execute(array('station' => $lastPathConnection->arrivalStationId));
            $result = $results->fetch();
            $journey->stations[] = $result["name"];

            $journey->startTimes[] = $startPathConnection->startTime;
            $journey->arrivalTimes[] = $lastPathConnection->arrivalTime;
            $journey->price += $price;

            $startPathConnection = $c;
            $pathId = $c->pathId;
            $price = 0;
        }

        // If it is the last connection
        if ($i == count($connections) - 1) {
            $lastPathConnection = $c;
            $price += $c->price;

            $results->execute(array('station' => $startPathConnection->startStationId));
            $result = $results->fetch();
            $journey->stations[] = $result["name"];

            $results->execute(array('station' => $lastPathConnection->arrivalStationId));
            $result = $results->fetch();
            $journey->stations[] = $result["name"];

            $journey->startTimes[] = $startPathConnection->startTime;
            $journey->arrivalTimes[] = $lastPathConnection->arrivalTime;
            $journey->price += $price;
        }

        $price += $c->price;
        $lastPathConnection = $c;
    }

    return $journey;
}

?>