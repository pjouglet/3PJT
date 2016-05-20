<?php

function getAllStations()
{
    global $db;
    $data = array();

    $results = $db->query("SELECT * FROM stations;");

    if ($results == null)
        return null;

    foreach ($results as $result)
    {
        $data[] = new Station($result["id"], $result["name"], $result["is_national"], $result["zoneid"]);
    }

    return $data;
}



function getUserById($id)
{
    global $db;

    $results = $db->prepare("SELECT * FROM users WHERE id = :id;");
    $results->execute(array('id' => $id));
    $result = $results->fetch();

    if ($result == null)
    {
        return null;
    }
    else
    {
        return $result;
    }
}

?>