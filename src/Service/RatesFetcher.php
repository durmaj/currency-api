<?php

namespace App\Service;

use App\Entity\Currency;
use App\Entity\History;
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
        
        $tables = ['a', 'b'];
        
        foreach($tables as $table)
        { 
        //getting json from NBP & decoding
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'http://api.nbp.pl/api/exchangerates/tables/'.$table.'/');
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
                //if there are tables to update, proceed with updating
                    $currencies = $value->rates;
                    $this->saveToDb($currencies, $nbpDate, $nbpTable);
                }

            }
            
        };
 
        
        return true;
    }
    
    public function checkUpdates($table, $date)
    {
        
        $saved = $this->currencyRepo->findByTable($table);
        
        if (!empty($saved))
        {
            $tmp = $saved[0]->getDate();
            
            if ($tmp == $date)
            {
                return false; //no need to update
            }
        }
        
        return true; //updates can be made
    }
    
    public function saveToDb($currencies, $nbpDate, $nbpTable)
    {
        foreach ($currencies as $entry)
        {
            
           if ($this->currencyRepo->findOneByCode($entry->code))
           {
        
                $currency = $this->currencyRepo->findOneByCode($entry->code);
            //log current entry to history    
                $history = new History();
                $history->setCurrency($currency);
                $history->setRate($currency->getRate());
                $history->setDate($currency->getDate());
                
                $this->entityManager->persist($history);

            // update existing currency   
                $currency->setRate($entry->mid);
                $currency->setDate($nbpDate);
                $currency->setUpdated(new \DateTime); 
                
                $this->entityManager->persist($currency);
                $this->entityManager->flush();
                
           } else {
        //create new currency & save to DB
           $currency = new Currency();
           $currency->setName($entry->currency);
           $currency->setCode($entry->code);
           $currency->setRate($entry->mid);
           $currency->setDate($nbpDate);
           $currency->setUpdated(new \DateTime);
           $currency->setNbpTable($nbpTable);

           $this->entityManager->persist($currency);
           $this->entityManager->flush();
           }

        };
        return true;
    }
    
}
