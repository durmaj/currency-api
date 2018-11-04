<?php

namespace App\Service;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

class RatesFetcher
{
    
    private $entityManager;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }


    public function getRates()
    {
//        $currencies = $this->getDoctrine()
//                ->getRepository(Currency::class)
//                ->findAll();
    //getting json from NBP & decoding
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://api.nbp.pl/api/exchangerates/tables/a/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($resp);
                
        foreach ($result as $res => $value)
        {
            
         foreach ($value->rates as $entry){
          
           //create currency entities & save to DB
            $currency = new Currency();
            $currency->setName($entry->currency);
            $currency->setCode($entry->code);
            $currency->setRate($entry->mid);
            $currency->setDate(new \DateTime);
            $currency->setUpdated(new \DateTime);
            
            $this->entityManager->persist($currency);
            $this->entityManager->flush();
             
         };
         
        }

        
        return 'ok';
    }
    
}
