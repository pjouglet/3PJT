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
    $arrivalStation = new Station(2, "Lille", 1, 0);

    if ($startStation->zoneId == $arrivalStation->zoneId)
    {
        $connections = findQuickestJourneyInZone($startStation, $arrivalStation, $startTime);
        putConnectionsInJourney($journey, $connections);
        var_dump($journey);
        return ($journey);
    }
    else if ($startStation->zoneId != $arrivalStation->zoneId)
    {
        var_dump($journey);
        return ($journey);
    }
    else
    {
        return null;
    }
}



function findQuickestJourneyInZone($startStation, $arrivalStation, $startTime)
{
    // Initialisation
    $timetable = array();
    $inConnection = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp = array_fill(0, 10000, PHP_INT_MAX);
    $arrivalTimestamp[$startStation->id] = $startTime;


    // Get all zone paths, connections and segments
    // Foreach path -> foreach connection -> get segment, $timetable=new Connection($startStation, $segmentArrivalStation, $startTime, $startTime + segmentTime, $pathId, $segmentPrice)

    $stations = array(new Station(0, "Valenciennes", 0, 0), new Station(1, "Saint Amand les Eaux", 0, 0), new Station(2, "Lille", 1, 0), new Station(3, "Denain", 0, 0));
    $timetable = array(new Connection($stations[3], $stations[2], 150, 900, 3, 9), new Connection($stations[0], $stations[3], 0, 100, 2, 2), new Connection($stations[0], $stations[1], 0, 100, 1, 1), new Connection($stations[1], $stations[2], 110, 250, 1, 5));

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
        else if ($i == count($connections) - 1)
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