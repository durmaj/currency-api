<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CurrencyRepository;


use App\Service\RatesFetcher;


class ApiController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(RatesFetcher $rates, CurrencyRepository $currencyRepo)
    {
//        $curr = $rates->updateFromNbp();
        
        
        $currencies = $currencyRepo->findAll();
        $codes = [];
        
        foreach ($currencies as $currency)
        {
            array_push($codes, $currency->getCode());

        }
        dump($codes);
        
        return $this->json($codes);
    }
    
    
        /**
     * @Route("/{code}", name="list")
     */
    public function checkRate(CurrencyRepository $currencyRepo, $code)
    {        
        
        $currency = $currencyRepo->findOneByCode($code);

        dump($currency->getRate());
        
        return $this->json($currency);
    }
    
    
}
