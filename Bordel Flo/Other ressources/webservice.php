<?php
/**
 * Created by PhpStorm.
 * User: Luciole
 * Date: 02/02/2016
 * Time: 15:21
 */

class WebService
{
	/*
	Zone A!

	  A B C D E
	A 0 7 2 8 6
	B 7 0 * 6 *
	C 2 * 0 5 3
	D 8 6 5 0 *
	E 6 * 3 * 0
	*/
	$ZoneA = array(array(0,7,2,8,6), array(7,0,-1,6,-1), array(2,-1,0,5,3), array(8,6,5,0,-1), array(6,-1,3,-1,0));


    function Hello(){
        return "hello world";
    }

    function findTheoricalNationalTravel(start, arrival)
	{
		//select * from travels where start_city = $start and arrival_city = $arrival;
		//select city1, city2, time from segments where city1 = ANY ($nationalCities) and city2 = ANY($nationalCities);

		if ($start == $arrival)
			return NULL;

		if (!array_key_exists($start, $ZoneA) || !array_key_exists($arrival, $ZoneA))
			return NULL;

		// Choper les trajets contenant la ville de départ à l'horaire indiqué (+ 4h par exemple) + trier par horaire
		// On vérifie que la gare de départ n'est pas le terminus du trajet
		$trajets_valides = array();
		foreach ($trajets as $trajet)
		{
			if (!end(trajets_valides) == $start)
			{
				array_push($trajets_valides, trajet);
			}
		}

		$trajetsDirects = array()
		foreach ($trajets_valides as $trajet)
		{
			$trajetIsValide = FALSE;
			foreach ($trajet.gares as $gare)
			{
				if ($gare == $arrival)
				{
					$trajetIsValide = TRUE;
				}
			}

			if (trajetIsValide == TRUE)
			{
				array_push($trajetsDirects, $trajet);
			}
		}


		if (!empty($trajetsDirects))
		{
			return $trajetsDirects;
		}
		else
		{
			
		}


	}
}

$options = array('uri' => "http://train-commander.dev");

$server = new SoapServer(NULL, $options);
$server->setClass("WebService");
$server->handle();

/*
  /*test web-API

        $options = array('location' => "http://train-commander.dev/webservice.php", 'uri' => "http://train-commander.dev");
        $api = new \SoapClient(NULL, $options);

        echo $api->Hello();
*/
?>


