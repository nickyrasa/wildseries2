<?php

namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController
{
    #[Route('/logout', name: 'app_logout')]
    public function logOut(Security $security): Response
    {
        // logout the user in on the current firewall
        $response = $security->logOut();

        // you can also disable the csrf logout
        $response = $security->logOut(false);

        // ... return $response (if set) or e.g. redirect to the homepage
        return new Response('test');
    }
}
