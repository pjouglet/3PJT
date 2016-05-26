<?php

require_once "algorithmFunctions.php";
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
    $data = findJourney($uri[1], $uri[2], $uri[3], $uri[4]);

else if ($uri[0] == "journeys")
{
    if (isset($uri[5]))
    {
        if (is_numeric($uri[5]))
            $data = findJourneysInRange($uri[1], $uri[2], $uri[3], $uri[4], $uri[5]);
    }
    else
        $data = findJourneys($uri[1], $uri[2], $uri[3], $uri[4]);
}
else if ($uri[0] == "stations")
    $data = getAllStations();

else if ($uri[0] == "user")
    $data = getUserById($uri[1]);

else if ($uri[0] == "connect")
    $data = isUserAllowed($uri[1], $uri[2]);

else if ($uri[0] == "create")
{
    if ($uri[1] == "user")
        $data = createUser($uri[2], $uri[3], $uri[4], $uri[5], $uri[6]);
    else if ($uri[1] == "history")
        $data = createHistory($uri[2], $uri[3], $uri[4], $uri[5], $uri[6], $uri[7]);
}
else
{
    echo "INVALID REQUEST";
    header("HTTP/1.1 " . 405 . " " . "Invalid request.");
    return json_encode($data);
}




if ($data == null)
    header("HTTP/1.1 " . 404 . " " . "No results found.");
else
    header("HTTP/1.1 " . 200 . " " . "OK.");


echo json_encode($data);

return json_encode($data);