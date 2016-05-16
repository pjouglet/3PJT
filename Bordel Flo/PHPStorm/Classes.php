<?php

Class Station
{
    public $id;
    public $name;
    public $isCapital;
    public $zoneId;

    /**
     * Station constructor.
     * @param $id
     * @param $name
     * @param $isCapital
     * @param $zoneId
     */
    public function __construct($id, $name, $isCapital, $zoneId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->isCapital = $isCapital;
        $this->zoneId = $zoneId;
    }
}

Class Connection
{
    public $startStation;
    public $arrivalStation;
    public $startTime;
    public $arrivalTime;
    public $pathId;
    public $price;

    /**
     * Connection constructor.
     * @param $startStation
     * @param $arrivalStation
     * @param $startTime
     * @param $arrivalTime
     * @param $pathId
     * @param $price
     */
    public function __construct($startStation, $arrivalStation, $startTime, $arrivalTime, $pathId, $price)
    {
        $this->startStation = $startStation;
        $this->arrivalStation = $arrivalStation;
        $this->startTime = $startTime;
        $this->arrivalTime = $arrivalTime;
        $this->pathId = $pathId;
        $this->price = $price;
    }
}

Class Journey
{
    public $stations;
    public $startTimes;
    public $arrivalTimes;
    public $price;

    /**
     * Journey constructor.
     * @param $stations
     * @param $startTimes
     * @param $arrivalTimes
     * @param $price
     */
    public function __construct($stations, $startTimes, $arrivalTimes, $price)
    {
        $this->stations = $stations;
        $this->startTimes = $startTimes;
        $this->arrivalTimes = $arrivalTimes;
        $this->price = $price;
    }
}

Class History
{
    public $startStation;
    public $arrivalStation;
    public $startTime;
    public $arrivalTime;
    public $price;

    /**
     * History constructor.
     * @param $startStation
     * @param $arrivalStation
     * @param $startTime
     * @param $arrivalTime
     * @param $price
     */
    public function __construct($startStation, $arrivalStation, $startTime, $arrivalTime, $price)
    {
        $this->startStation = $startStation;
        $this->arrivalStation = $arrivalStation;
        $this->startTime = $startTime;
        $this->arrivalTime = $arrivalTime;
        $this->price = $price;
    }
}

Class Zone
{
    public $id;
    public $name;
    public $stations;
    public $capital;

    /**
     * Zone constructor.
     * @param $id
     * @param $name
     * @param $stations
     * @param $capital
     */
    public function __construct($id, $name, $stations, $capital)
    {
        $this->id = $id;
        $this->name = $name;
        $this->stations = $stations;
        $this->capital = $capital;
    }
}