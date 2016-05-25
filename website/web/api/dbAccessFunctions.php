<?php

function getAllStations()
{
    global $db;
    $data = array();

    $results = $db->query("SELECT id, name FROM stations;");

    if ($results == null)
        return null;

    foreach ($results as $result)
        $data[] = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);

    return $data;
}



function getUserById($id)
{
    global $db;

    $results = $db->prepare("SELECT * FROM users WHERE id = :id;");
    $results->execute(array('id' => $id));
    $result = $results->fetch();

    if ($result == null)
        return null;
    else
        return $result;
}



function getHistoriesByUserId($id)
{
    global $db;
    $data = array();

    $results = $db->prepare("SELECT cost, start_time, end_time, start_station, end_station FROM users WHERE userid = :id;");
    $results->execute(array('id' => $id));

    if ($results == null)
        return null;

    foreach ($results as $result)
        $data[] = new History($result["start_station"], $result["end_station"], $result["start_time"], $result["end_time"], $result["cost"]);

    return $data;
}



function saveHistory($history)
{

}



function isUserAllowed($email, $password)
{
    global $db;

    $results = $db->prepare("SELECT id, password FROM users WHERE email = :mail AND active = 1;");
    $results->execute(array('mail' => $email));
    $result = $results->fetch();

    if ($result == null or !isset($result))
        return 0;
    if (sha1($password) == $result["password"])
        return $result["id"];
    else
        return 0;
}