<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Record;
use DateTime;


class RecordController extends AbstractController
{
    /**
     * @Route("/records", name="record")
     */
    public function index()
    {     
        $records = $this->generateTable();
        return $this->render('country/index.html.twig', [
            'controller_name' => 'RecordController',
            'currency_code' => '?',
            'table' => $records,
        ]);
    }
  
    public function generateTable(){
        $repository = $this->getDoctrine()
            ->getRepository(Record::class);
        $table = array();
        $records = $repository->findAll();
        foreach($records as $record){
            $table[$record->getId()]['id'] = $record->getId();
            $table[$record->getId()]['city'] = $record->getCity();
            $table[$record->getId()]['pln_value'] = $record->getPlnValue();
            $table[$record->getId()]['foreign_value'] = $record->getForeignValue();
            $table[$record->getId()]['currency_code'] = $record->getForeignCurrencyCode();
            $table[$record->getId()]['date'] = $record->getGenerationDate()->format('Y-m-d H:i:s');
        }
        return $table;
    }
}
