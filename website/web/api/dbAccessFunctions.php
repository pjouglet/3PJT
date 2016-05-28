<?php

function getAllStations()
{
    global $db;
    $data = array();

    $results = $db->query("SELECT id, name FROM stations;");

    if ($results == null or !isset($results))
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

    if ($result == null or !isset($result))
        return null;
    else
        return $result;
}



function getUserIdByFbId($id)
{
    global $db;

    $results = $db->prepare("SELECT id FROM users WHERE fbid = :id;");
    $results->execute(array('id' => $id));
    $result = $results->fetch();

    if ($result == null or !isset($result))
        return array("id" => "0");
    else
        return array("id" => $result["id"]);
}



function getUserIdByGoogleId($id)
{
    global $db;

    $results = $db->prepare("SELECT id FROM users WHERE googleid = :id;");
    $results->execute(array('id' => $id));
    $result = $results->fetch();

    if ($result == null or !isset($result))
        return array("id" => "0");
    else
        return array("id" => $result["id"]);
}



function getHistoriesByUserId($id)
{
    global $db;
    $data = array();

    $results = $db->prepare("SELECT cost, start_time, end_time, start_station, end_station FROM users WHERE userid = :id;");
    $results->execute(array('id' => $id));

    if ($results == null or !isset($results))
        return null;

    foreach ($results as $result)
        $data[] = new History($result["start_station"], $result["end_station"], $result["start_time"], $result["end_time"], $result["cost"]);

    return $data;
}



function createHistory($cost, $startStationName, $arrivalStationName, $startTime, $arrivalTime, $userid)
{
    global $db;
    $timestamp = date('Y-n-j H:i:s', $startTime);
    $timestamp2 = date('Y-n-j H:i:s', $arrivalTime);

    $request = $db->prepare("INSERT INTO history (cost, start_station, end_station, start_time, end_time, userid) VALUES (:cost, :ssn, :asn, :st, :at, :uid)");
    $request->execute(array('cost' => $cost, 'ssn' => str_replace('%20', ' ', $startStationName), 'asn' => str_replace('%20', ' ', $arrivalStationName), 'st' => $timestamp, 'at' => $timestamp2, 'uid' => $userid));

    return array("id" => $db->lastInsertId());
}



function createUser($fn, $ln, $password, $email, $newsletter)
{
    global $db;

    $request = $db->prepare("INSERT INTO users (firstname, lastname, password, email, newsletter, active) VALUES (:fn, :ln, :pass, :mail, :news, 1)");
    $request->execute(array('fn' => str_replace('%20', ' ', $fn), 'ln' => str_replace('%20', ' ', $ln), 'pass' => sha1(str_replace('%20', ' ', $password)), 'mail' => str_replace('%20', ' ', $email), 'news' => $newsletter));

    return array("id" => $db->lastInsertId());
}



function createUserFb($fn, $ln, $password, $email, $newsletter)
{
    global $db;

    $request = $db->prepare("INSERT INTO users (firstname, lastname, password, email, newsletter, active) VALUES (:fn, :ln, :pass, :mail, :news, 1)");
    $request->execute(array('fn' => str_replace('%20', ' ', $fn), 'ln' => str_replace('%20', ' ', $ln), 'pass' => sha1(str_replace('%20', ' ', $password)), 'mail' => str_replace('%20', ' ', $email), 'news' => $newsletter));

    return array("id" => $db->lastInsertId());
}



function createUserGoogle($fn, $ln, $password, $email, $newsletter)
{
    global $db;

    $request = $db->prepare("INSERT INTO users (firstname, lastname, password, email, newsletter, active) VALUES (:fn, :ln, :pass, :mail, :news, 1)");
    $request->execute(array('fn' => str_replace('%20', ' ', $fn), 'ln' => str_replace('%20', ' ', $ln), 'pass' => sha1(str_replace('%20', ' ', $password)), 'mail' => str_replace('%20', ' ', $email), 'news' => $newsletter));

    return array("id" => $db->lastInsertId());
}



function isUserAllowed($email, $password)
{
    global $db;

    $results = $db->prepare("SELECT id, password FROM users WHERE email = :mail AND active = 1;");
    $results->execute(array('mail' => $email));
    $result = $results->fetch();

    if ($result == null or !isset($result))
        return array("id" => "0");
    if (sha1(str_replace('%20', ' ', $password)) == $result["password"])
        return array("id" => $result["id"]);
    else
        return array("id" => "0");
}