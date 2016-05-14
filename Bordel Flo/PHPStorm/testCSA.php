<?php
/**
 * Created by PhpStorm.
 * User: Stafolk
 * Date: 14/05/2016
 * Time: 15:26
 */

Class Station
{
    public $id;
    public $name;
    public $isCapital;
    public $zone;

    /**
     * Station constructor.
     * @param $id
     * @param $name
     * @param $isCapital
     * @param $zone
     */
    public function __construct($id, $name, $isCapital, $zone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->isCapital = $isCapital;
        $this->zone = $zone;
    }
}

Class Connection
{
    public $startStation;
    public $arrivalStation;
    public $startTimestamp;
    public $arrivalTimestamp;

    /**
     * Connection constructor.
     * @param $startStation
     * @param $arrivalStation
     * @param $startTimestamp
     * @param $arrivalTimestamp
     */
    public function __construct($startStation, $arrivalStation, $startTimestamp, $arrivalTimestamp)
    {
        $this->startStation = $startStation;
        $this->arrivalStation = $arrivalStation;
        $this->startTimestamp = $startTimestamp;
        $this->arrivalTimestamp = $arrivalTimestamp;
    }
}



function findJourney($start, $arrival, $startTime)
{
    // Get all stations
    $stations = array(new Station(0, "Valenciennes", 0, 0), new Station(1, "Saint Amand les Eaux", 0, 0), new Station(2, "Lille", 1, 0), new Station(3, "Denain", 0, 0));

    // Initialisation
    // Timetable MUST be sorted by start time
    $timetable = array(new Connection($stations[0], $stations[1], 0, 100), new Connection($stations[1], $stations[2], 110, 250), new Connection($stations[0], $stations[3], 0, 100), new Connection($stations[3], $stations[2], 150, 900));
    $arrivalTimestamp = array_fill(0, count($stations), PHP_INT_MAX);
    $inConnection = array_fill(0, count($stations), PHP_INT_MAX);
    $arrivalTimestamp[$start->id] = $startTime;

    foreach($timetable as $cID => $c)
    {
        if ($arrivalTimestamp[$c->startStation->id] <= $c->startTimestamp && $arrivalTimestamp[$c->arrivalStation->id] > $c->arrivalTimestamp) {
            $arrivalTimestamp[$c->arrivalStation->id] = $c->arrivalTimestamp;
            $inConnection[$c->arrivalStation->id] = $cID;
        }
    }

    if ($inConnection[$arrival->id] === PHP_INT_MAX)
    {
        echo "NO SOLUTION\n";
        return;
    }


    // On dépile pour afficher les résultats
    $route = [];
    $lastConnectionIndex = $inConnection[$arrival->id];
    while ($lastConnectionIndex !== PHP_INT_MAX) {
        $connection = $timetable[$lastConnectionIndex];
        $route[] = $connection;
        $lastConnectionIndex = $inConnection[$connection->startStation->id];
    } ;
    foreach (array_reverse($route) as $row) {
        printf("%s to %s : %d %d<br />", $row->startStation->name, $row->arrivalStation->name, $row->startTimestamp, $row->arrivalTimestamp);
    }
    echo "\n";
}

findJourney(new Station(0, "Valenciennes", 0, 0), new Station(2, "Lille", 1, 0), 0);

?>