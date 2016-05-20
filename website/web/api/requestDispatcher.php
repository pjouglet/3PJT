<?php

require_once "testCSA2.php";
require_once "dbAccessFunctions.php";

header("Content-Type: application/json");

//echo $_SERVER['REQUEST_URI'];
$uri = explode("/", $_SERVER['REQUEST_URI']);
foreach ($uri as $i => $item)
{
    if($i == 0 || $i == 1) // || $i == count($uri)-1
        continue;
    //var_dump($item);
}

array_shift($uri);
array_shift($uri);

foreach ($uri as $i => $item)
{
    //var_dump($item);
}

$data = null;

if ($uri[0] == "journey")
{
    if ($uri[1] == "time")
    {
        $data = findQuickestJourney($uri[2], $uri[3], $uri[4]);
    }
}
else if ($uri[0] == "journeys")
{

}
else if ($uri[0] == "station")
{

}
else if ($uri[0] == "stations")
{
    $data = getAllStations();
}
else if ($uri[0] == "user")
{

}
else if ($uri[0] == "users")
{

}
else
{
    echo "INVALID REQUEST";
    header("HTTP/1.1 " . 405 . " " . "Invalid request.");
    return json_encode($data);
}

if ($data == null)
{
    header("HTTP/1.1 " . 404 . " " . "No results found.");
}
else
{
    header("HTTP/1.1 " . 200 . " " . "OK.");
}

echo json_encode($data);

return json_encode($data);

?>