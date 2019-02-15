<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Exception;
use App\Entity\Record;
use DateTime;

class CountryController extends AbstractController
{
    /**
     * @Route("/", name="country")
     */
    public function index()
    {
        $code = "?";
        $msg='';
        $pln_amount = $capital_name = $foreign_amount = "";
        if(!empty($_POST['capitalCity']) && !empty($_POST['plnAmount'])){          
            $capital_name = $_POST['capitalCity'];
            $pln_amount = $_POST['plnAmount'];
            try{
                $currencyData = $this->byCapitalCity($capital_name);
                $code = $currencyData[0]['currencies'][0]['code'];
                $foreign_amount = $this->calculateCurrency($code, $pln_amount);
            } catch (Exception $ex) {
                $msg = "Wystąpił problem ze znalezieniem pożądanego miasta";
                return $this->render('country/index.html.twig', [
                'currency_code' => $code,
                'capital_name' => $capital_name,
                'pln_amount' => $pln_amount,
                'message' => $msg, 
                ]);
            }
            $this->saveRecord($pln_amount, $foreign_amount, $code, $capital_name);
            
            
        }
        return $this->render('country/index.html.twig', [
            'currency_code' => $code,
            'capital_name' => $capital_name,
            'pln_amount' => $pln_amount,
            'foreign_amount' => $foreign_amount,
            'controller_name' => 'CountryController',
        ]);
    }
    
    private $guzzleClient;
    private $fields;
    
    public function __construct()
    {
        $this->guzzleClient = new Client([
            "base_uri" => "https://restcountries.eu/rest/v2/",
        ]);
        $this->fields = [];
    }
    
     public function fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
    
    public function byCapitalCity($capitalCity)
    {
        $url = sprintf("capital/%s?fields=currencies", $capitalCity);        
        return $this->execute($url);
    }
    
    
    private function execute($url, $requestParams = [])
    {
        if (count($this->fields)) {
            $requestParams = array_merge($requestParams, [
                "fields" => implode(";", $this->fields),
            ]);
            $this->fields = [];
        }
        try {
            $response = $this->guzzleClient->get($url, [
                "query" => $requestParams,
            ])->getBody()->getContents();
            return json_decode($response, true);
        } catch (Exception $exception) {
            if($exception->getCode() == '404'){
                return 'ERROR';
            }else{
            throw new Exception($exception->getMessage());
            }
        }
    }
    private function saveRecord($pln_value,$foreign_value, $foreign_currency_code, $city){
        try{
            $entityManager = $this->getDoctrine()->getManager();
            $record = new Record();
            $record->setPlnValue($pln_value);
            $record->setCity($city);
            $record->setForeignCurrencyCode($foreign_currency_code);
            $record->setForeignValue($foreign_value);
            $datetime1 = new DateTime(date('Y-m-d h:m:s'));
            $record->setGenerationDate($datetime1);
            echo "<pre>";
            print_r($record);
            echo "</pre>";
            $entityManager->persist($record);
            $entityManager->flush();
            return true;
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
    
    
    private function calculateCurrency($currencyCode , $amountToConvert){
        // set API Endpoint, access key, required parameters
        $endpoint = 'live';
        $access_key = 'e0be2dcc7789103833a32aa33c777ed6';

        $from = 'PLN';
        $to = $currencyCode;
        $amount = $amountToConvert;

        // initialize CURL:
        $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&currencies='.$from.','.$to.'&format=1');   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // get the (still encoded) JSON data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $conversionResult = json_decode($json, true);
        $keys = array_keys($conversionResult['quotes']);
        
        //Wartość 1 USD w PLN
        $valueOfPLN = $conversionResult['quotes'][$keys[0]];
        //Wartość 1 USD w jednostkach wybranej waluty
        $valueOfForeign = $value = $conversionResult['quotes'][$keys[1]];
        //Suma posiadanych USD razy wartość wybranej waluty
        $result = ($amount / $valueOfPLN) * $valueOfForeign;
        
        return number_format($result, 2, '.','');
    }
}
