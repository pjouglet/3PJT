<?php

require_once("Classes.php");



function findQuickestJourney($startStationId, $arrivalStationId, $startTime)
{
    // First test
    if ($startStationId == $arrivalStationId)
    {
        return null;
    }

    $journey = new Journey(array(), array(), array(), 0);

    // Select * from stations where id = $startStationId or $arrivalStationId
    $startStation = new Station(0, "Valenciennes", 0, 0);
    //$arrivalStation = new Station(2, "Lille", 1, 0);
    $arrivalStation = new Station(32, "La Ciotat", 0, 2);

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

        $startNationalStation = new Station(2, "Lille", 1, 0);
        $arrivalNationalStation = new Station(30, "Marseille", 1, 2);

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
    //$timetable = array();
    $inConnection = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp[$startStation->id] = $startTime;
    $stations = null;
    $timetable = null;


    // Get all zone paths, connections and segments
    // Foreach path -> foreach connection -> get segment, $timetable=new Connection($startStation, $segmentArrivalStation, $startTime, $startTime + segmentTime, $pathId, $segmentPrice)

    // If national path
    if ($startStation->isCapital == 1 && $arrivalStation->isCapital == 1)
    {
        $stations = array(new Station(20, "Paris", 1, 1), new Station(30, "Marseille", 1, 2), new Station(2, "Lille", 1, 0));
        $timetable = array(new Connection($stations[2], $stations[0], 1100, 2000, 4, 20), new Connection($stations[0], $stations[1], 2100, 4000, 5, 40), new Connection($stations[2], $stations[1], 2000, 5000, 6, 65));
    }
    else if ($startStation->zoneId == 2 && $arrivalStation->zoneId == 2)
    {
        $stations = array(new Station(30, "Marseille", 1, 2), new Station(31, "Cassis", 0, 2), new Station(32, "La Ciotat", 0, 2), new Station(33, "Aubagne", 0, 2));
        $timetable = array(new Connection($stations[0], $stations[1], 5100, 5200, 7, 5), new Connection($stations[1], $stations[2], 5205, 5300, 7, 3), new Connection($stations[0], $stations[3], 5150, 5350, 8, 7), new Connection($stations[3], $stations[2], 5400, 5500, 9, 4));
    }
    else
    {
        $stations = array(new Station(0, "Valenciennes", 0, 0), new Station(1, "Saint Amand les Eaux", 0, 0), new Station(2, "Lille", 1, 0), new Station(3, "Denain", 0, 0));
        $timetable = array(new Connection($stations[3], $stations[2], 150, 900, 3, 9), new Connection($stations[0], $stations[3], 0, 100, 2, 2), new Connection($stations[0], $stations[1], 0, 100, 1, 1), new Connection($stations[1], $stations[2], 110, 250, 1, 5));
    }

    // Sort connections by start time. Needed for algorithm.
    usort($timetable, "compareConnectionsByStartTime");


    // Main algorithm
    foreach($timetable as $cID => $c)
    {
        if ($arrivalTimestamp[$c->startStation->id] <= $c->startTime && $arrivalTimestamp[$c->arrivalStation->id] > $c->arrivalTime)
        {
            $arrivalTimestamp[$c->arrivalStation->id] = $c->arrivalTime;
            $inConnection[$c->arrivalStation->id] = $cID;
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
        $lastConnectionIndex = $inConnection[$connection->startStation->id];
    } ;
    foreach (array_reverse($route) as $row)
    {
        printf("%s to %s : %d %d<br />", $row->startStation->name, $row->arrivalStation->name, $row->startTime, $row->arrivalTime);
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

    foreach ($connections as $i => $c)
    {
        if ($c->pathId != $pathId)
        {
            $journey->stations[] = $startPathConnection->startStation;
            $journey->stations[] = $lastPathConnection->arrivalStation;
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

            $journey->stations[] = $startPathConnection->startStation;
            $journey->stations[] = $lastPathConnection->arrivalStation;
            $journey->startTimes[] = $startPathConnection->startTime;
            $journey->arrivalTimes[] = $lastPathConnection->arrivalTime;
            $journey->price += $price;
        }

        $price += $c->price;
        $lastPathConnection = $c;
    }

    return $journey;
}


findQuickestJourney(0, 2, 0);

?>