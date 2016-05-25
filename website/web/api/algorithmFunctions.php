<?php

require_once("Classes.php");

$db = getDBData();


function getDBData()
{
    return new PDO("mysql:dbname=train-commander-bdd;host=127.0.0.1", "root", "");
}




function findJourneys($searchMode, $startStationId, $arrivalStationId, $startTime)
{
    $minTime = $startTime - 3600;
    $maxTime = $startTime + 7200;

    $journeys = getJourneysInRange($searchMode, $startStationId, $arrivalStationId, $minTime, $maxTime);

    $earlyJourneys = array();

    foreach ($journeys as $j)
    {
        if ($j->startTimes[0] < $startTime)
            $earlyJourneys[] = $j;
    }

    if (count($earlyJourneys) >= 2)
    {
        for ($i = 0; $i < count($earlyJourneys) - 2; $i++)
            array_shift($journeys);
    }

    return ($journeys);
}




function findJourneysInRange($searchMode, $startStationId, $arrivalStationId, $minTime, $maxTime)
{
    $journeys = getJourneysInRange($searchMode, $startStationId, $arrivalStationId, $minTime, $maxTime);

    return ($journeys);
}




function getJourneysInRange($searchMode, $startStationId, $arrivalStationId, $minTime, $maxTime)
{
    $journeys = null;

    while ($minTime <= $maxTime)
    {
        $journey = findJourney($searchMode, $startStationId, $arrivalStationId, $minTime);

        if (isset($journey) and $journey != null)
        {
            if ($journey->startTimes[0] > $maxTime)
                break;
            if (isset($journeys))
            {
                if (!in_array($journey, $journeys))
                {
                    $journeys[] = $journey;
                    $minTime = $journey->startTimes[0];
                }
            }
            else
            {
                $journeys[] = $journey;
                $minTime = $journey->startTimes[0];
            }
        }

        $minTime += 600;
    }

    return ($journeys);
}




function findJourney($searchMode, $startStationId, $arrivalStationId, $startTime)
{
    if ($startStationId == $arrivalStationId)
        return null;

    global $db;

    $journey = new Journey(array(), array(), array(), 0);


    // Get departure station
    $results = $db->prepare("SELECT * FROM stations WHERE id = :station;");
    $results->execute(array('station' => $startStationId));
    $result = $results->fetch();
    if ($result == null)
        return null;
    else
        $startStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);

    // Get arrival station
    $results->execute(array('station' => $arrivalStationId));
    $result = $results->fetch();
    if ($result == null)
        return null;
    else
        $arrivalStation = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);


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

    return ($journey);
}




function findCheapestJourneyInZone($startStation, $arrivalStation, $startTime)
{
    // Initialisation
    $lastConnections = array();
    $excludedStations = array();
    $journeyConnectionArray = array();

    $timetable = populateTimetable($startStation, $arrivalStation, $startTime);

    if (!isset($timetable) or $timetable == null)
        return null;


    // Find all possible valid paths
    foreach ($timetable as $connection)
    {
        if ($connection->startStationId == $startStation->id)
        {
            $lastConnectionTmp = $lastConnections;
            $lastConnectionTmp[] = $connection;

            $excludedStationTmp = $excludedStations;
            $excludedStationTmp[] = $startStation->id;

            getRecursiveJourneys($connection->arrivalStationId, $arrivalStation->id, $connection->arrivalTime, $lastConnectionTmp, $excludedStationTmp, $timetable, $journeyConnectionArray);
        }
    }

    if (!isset($journeyConnectionArray) or $journeyConnectionArray == null)
        return null;


    // Find cheapest path among all possible paths
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

    return ($cheapestJourney);
}




function getRecursiveJourneys($startStationId, $endStationId, $startTime, $lastConnections, $excludedStations, &$connections, &$journeyConnectionArray)
{
    if ( $endStationId == $startStationId )
    {
        $journeyConnectionArray[] = $lastConnections;
        return;
    }

    foreach ($connections as $connection)
    {
        if ($connection->startStationId == $startStationId && $connection->startTime >= $startTime && !in_array($connection->arrivalStationId, $excludedStations))
        {
            $lastConnectionTmp = $lastConnections;
            $lastConnectionTmp[] = $connection;

            $excludedStationTmp = $excludedStations;
            $excludedStationTmp[] = $startStationId;

            getRecursiveJourneys($connection->arrivalStationId, $endStationId, $connection->arrivalTime, $lastConnectionTmp, $excludedStationTmp, $connexions, $journeyConnectionArray);
        }
    }
}




function findQuickestJourneyInZone($startStation, $arrivalStation, $startTime)
{
    // Initialisation
    $inConnection = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp[$startStation->id] = $startTime;

    $timetable = populateTimetable($startStation, $arrivalStation, $startTime);

    if (!isset($timetable) or $timetable == null)
        return null;

    // Sort connections by start time. Needed for algorithm.
    usort($timetable, "compareConnectionsByStartTime");


    // Main algorithm
    foreach ($timetable as $cID => $c)
    {
        if ($arrivalTimestamp[$c->startStationId] <= $c->startTime && $arrivalTimestamp[$c->arrivalStationId] > $c->arrivalTime)
        {
            $arrivalTimestamp[$c->arrivalStationId] = $c->arrivalTime;
            $inConnection[$c->arrivalStationId] = $cID;
        }
    }

    // On dépile pour avoir le trajet dans l'ordre
    $route = [];
    $lastConnectionIndex = $inConnection[$arrivalStation->id];
    while ($lastConnectionIndex !== PHP_INT_MAX)
    {
        $connection = $timetable[$lastConnectionIndex];
        $route[] = $connection;
        $lastConnectionIndex = $inConnection[$connection->startStationId];
    };

    return (array_reverse($route));
}




function populateTimetable($startStation, $arrivalStation, $startTime)
{
    global $db;
    $timetable = null;

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

        foreach ($results as $r)
        {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] . 'S'))->getTimestamp(),
                $r["pathid"], $r["cost"]);
        }
    }
    else
    {
        $timestamp = date('Y-n-j H:i:s', $startTime);

        $results = $db->prepare("SELECT c.stationid, c.pathid, c.start_time, s.start_stationid, s.end_stationid, s.cost, s.duree
                                 FROM connections c JOIN segments s ON c.segmentid = s.id
                                 WHERE (c.stationid = (SELECT id FROM stations WHERE c.stationid = id AND zoneid = :zoneid))
                                 AND (c.start_time >= DATE(:startTime));");
        $results->execute(array('zoneid' => $startStation->zoneId, 'startTime' => $timestamp));

        foreach ($results->fetchAll() as $r)
        {
            $date = new DateTime($r["start_time"]);
            $timetable[] = new Connection($r["stationid"],
                ($r["stationid"] == $r["start_stationid"]) ? $r["end_stationid"] : $r["start_stationid"],
                $date->getTimestamp(),
                $date->add(new DateInterval('PT' . $r["duree"] . 'S'))->getTimestamp(),
                $r["pathid"], $r["cost"]);
        }
    }

    return $timetable;
}




function compareConnectionsByStartTime($c1, $c2)
{
    if ($c1->startTime == $c2->startTime)
        return 0;
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