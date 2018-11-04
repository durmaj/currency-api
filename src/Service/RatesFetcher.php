<?php

namespace App\Service;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CurrencyRepository;

class RatesFetcher
{
    
    private $entityManager;
    private $currencyRepo;


    public function __construct(EntityManagerInterface $em, CurrencyRepository $cr)
    {
        $this->entityManager = $em;
        $this->currencyRepo = $cr;
    }


    public function updateFromNbp()
    {

               
    //getting json from NBP & decoding
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://api.nbp.pl/api/exchangerates/tables/a/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($resp);
        
        $nbpTable = null;
        $nbpDate = null;      
                
        foreach ($result as $res => $value)
        {
            $nbpTable = $value->table;
            $nbpDate = new \DateTime($value->effectiveDate);
            
        //checkUpdates - checking if there are newer currency tables at nbp         
            $up = $this->checkUpdates($nbpTable, $nbpDate);
            if ($up)
            {

                foreach ($value->rates as $entry)
                {

                  //create currency entities & save to DB
                   $currency = new Currency();
                   $currency->setName($entry->currency);
                   $currency->setCode($entry->code);
                   $currency->setRate($entry->mid);
                   $currency->setDate($nbpDate);
                   $currency->setUpdated(new \DateTime);
                   $currency->setNbpTable($nbpTable);

                   $this->entityManager->persist($currency);
                   $this->entityManager->flush();
                };
            }
         
        }

        
        return 'ok';
    }
    
    public function checkUpdates($table, $date)
    {
        
        $saved = $this->currencyRepo->findByTable($table);

        if ($saved[0]->getDate() != $date)
        {
            return true;
        }
        
        return false;
    }
    
    public function saveToDb()
    {
        
    }
    
}
