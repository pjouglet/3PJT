<?php

namespace Supinfo\CommanderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CookieLawController extends Controller
{
    public function acceptLawCookieAction(Request $request){

        if($request->get("accept_cookies")){//On accepte les cookies
            $cookie = new Cookie('cookie_law', "true", time() + 3600 * 24 * 365);
            $response = new Response();
            $response->headers->setCookie($cookie);
            $response->send();

            return $this->redirect($this->generateUrl($request->get('currrent_location')));
        }
    }
}