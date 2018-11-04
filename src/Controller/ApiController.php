<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CurrencyRepository;
use App\Repository\HistoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(CurrencyRepository $currencyRepo)
    {        
        $currencies = $currencyRepo->findAll();
        $codes = [];
        
        foreach ($currencies as $currency)
        {
           $codes[$currency->getName()]= $currency->getCode();
        }
                
        $response = new JsonResponse($codes);
        
        return $response;
    }
    
    
    /**
     * @Route("/{code}", name="rate")
     */
    public function checkRate(CurrencyRepository $currencyRepo, $code)
    {        
        
        $currency = $currencyRepo->findOneByCode($code);
        
        $response = new JsonResponse($currency->getRate());
        return $response;
    } 
    
    
    /**
     * @Route("/{code}/average", name="average")
     */
    public function average(CurrencyRepository $currencyRepo, HistoryRepository $historyRepo, $code)
    {        
        $currency = $currencyRepo->findOneByCode($code);
        $history = $historyRepo->findByCurrency($currency);
        
        $allRates = [];
        
        //calculating average - if history entries are existing - add them to array
        array_push($allRates, $currency->getRate());
        
        if (!empty($history))
        {
            foreach ($history as $entry)
            {
               array_push($allRates, $entry->getRate()); 
            }
        }
        
        $average = round(array_sum($allRates) / count($allRates), 4);
        
        $response = new JsonResponse($average);
        
        return $response;
    }   
}
