<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RatesFetcher;

class ApiController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(RatesFetcher $rates)
    {
        $curr = $rates->getRates();
        
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiController.php',
        ]);
    }
}
