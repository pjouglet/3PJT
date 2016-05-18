<?php

require_once("Classes.php");

$db = getDBData();



function getDBData()
{
    return new PDO("mysql:dbname=train-commander-bdd;host=127.0.0.1", "root", "");
}



function findQuickestJourney($startStationId, $arrivalStationId, $startTime)
{
    // First test
    if ($startStationId == $arrivalStationId)
    {
        return null;
    }

    global $db;

    $journey = new Journey(array(), array(), array(), 0);

    // Get departure station
    $results = $db->prepare("SELECT * FROM stations WHERE id = :station;");
    $results->execute(array('station' => $startStationId));
    $result = $results->fetch();
    $startStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);

    // Get arrival station
    $results->execute(array('station' => $arrivalStationId));
    $result = $results->fetch();
    $arrivalStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);

    //$startStation = new Station(0, "Valenciennes", 0, 0);
    //$arrivalStation = new Station(2, "Lille", 1, 0);
    //$arrivalStation = new Station(32, "La Ciotat", 0, 2);

    // If we stay within the same zone
    if ($startStation->zoneId == $arrivalStation->zoneId)
    {
        $connections = findQuickestJourneyInZone($startStation, $arrivalStation, $startTime);
        if ($connections == null)
        {
            return null;
        }
        putConnectionsInJourney($journey, $connections);
    }
    else if ($startStation->zoneId != $arrivalStation->zoneId)
    {
        // Get stations where capitale = 1 && (zoneid = $startStation->id or zoneid = $arrivalStation->id)

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

        $connections = findQuickestJourneyInZone($startStation, $startNationalStation, $startTime);
        if ($connections == null)
        {
            return null;
        }
        putConnectionsInJourney($journey, $connections);

        $connections = findQuickestJourneyInZone($startNationalStation, $arrivalNationalStation, end($journey->arrivalTimes));
        if ($connections == null)
        {
            return null;
        }
        putConnectionsInJourney($journey, $connections);

        if ($arrivalStation != $arrivalNationalStation)
        {
            $connections = findQuickestJourneyInZone($arrivalNationalStation, $arrivalStation, end($journey->arrivalTimes));
            if ($connections == null)
            {
                return null;
            }
            putConnectionsInJourney($journey, $connections);
        }
    }
    else
    {
        return null;
    }

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
    if ($startStation->isCapital == 1 && $arrivalStation->isCapital == 1)
    {
        $results = $db->query("SELECT c.stationid, c.pathid, c.start_time, s.start_stationid, s.end_stationid, s.cost, s.duree
                               FROM connections c
                               JOIN segments s ON c.segmentid = s.id
                               WHERE c.pathid IN (SELECT id FROM paths WHERE is_national = 1);");

        foreach ($results as $r)
        {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] .'S'))->getTimestamp(),
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
    else
    {
        $results = $db->prepare("SELECT c.stationid, c.pathid, c.start_time, s.start_stationid, s.end_stationid, s.cost, s.duree
                                 FROM connections c JOIN segments s ON c.segmentid = s.id
                                 WHERE c.stationid = (SELECT id FROM stations WHERE c.stationid = id AND zoneid = :zoneid);");
        $results->execute(array('zoneid' => $startStation->zoneId));
        //$result = $results->fetch();
        //$startStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);

        foreach ($results->fetchAll() as $r)
        {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] .'S'))->getTimestamp(),
                $r["pathid"], $r["cost"]);
        }

        //$stations = array(new Station(0, "Valenciennes", 0, 0), new Station(1, "Saint Amand les Eaux", 0, 0), new Station(2, "Lille", 1, 0), new Station(3, "Denain", 0, 0));
        //$timetable = array(new Connection($stations[3], $stations[2], 150, 900, 3, 9), new Connection($stations[0], $stations[3], 0, 100, 2, 2), new Connection($stations[0], $stations[1], 0, 100, 1, 1), new Connection($stations[1], $stations[2], 110, 250, 1, 5));
    }

    // Sort connections by start time. Needed for algorithm.
    usort($timetable, "compareConnectionsByStartTime");

    var_dump($timetable);

    // Main algorithm
    foreach($timetable as $cID => $c)
    {
        if ($arrivalTimestamp[$c->startStationId] <= $c->startTime && $arrivalTimestamp[$c->arrivalStationId] > $c->arrivalTime)
        {
            $arrivalTimestamp[$c->arrivalStationId] = $c->arrivalTime;
            $inConnection[$c->arrivalStationId] = $cID;
        }
    }

    if ($inConnection[$arrivalStation->id] === PHP_INT_MAX)
    {
        echo "NO SOLUTION\n";
        return;
    }


    // On dépile pour afficher les résultats
    $route = [];
    $lastConnectionIndex = $inConnection[$arrivalStation->id];
    while ($lastConnectionIndex !== PHP_INT_MAX)
    {
        $connection = $timetable[$lastConnectionIndex];
        $route[] = $connection;
        $lastConnectionIndex = $inConnection[$connection->startStationId];
    } ;
    foreach (array_reverse($route) as $row)
    {
        printf("%s to %s : %d %d<br />", $row->startStationId, $row->arrivalStationId, $row->startTime, $row->arrivalTime);
    }
    echo "\n";

    return (array_reverse($route));
}



function compareConnectionsByStartTime($c1, $c2)
{
    if ($c1->startTime == $c2->startTime)
    {
        return 0;
    }
    return ($c1->startTime < $c2->startTime) ? -1 : 1;
}


function putConnectionsInJourney($journey, $connections)
{
    $startPathConnection = $connections[0];
    $lastPathConnection = $connections[0];
    $pathId = $connections[0]->pathId;
    $price = 0;

    global $db;
    $results = $db->prepare("SELECT name FROM stations WHERE id = :station;");

    foreach ($connections as $i => $c)
    {
        if ($c->pathId != $pathId)
        {
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
        if ($i == count($connections) - 1)
        {
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


findQuickestJourney(1, 34, 0);

?>