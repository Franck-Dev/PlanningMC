<?php

namespace App\Services;

use App\Repository\ChargeRepository;
use App\Repository\PlanningRepository;
use App\Repository\PolymRealRepository;

class FunctIndic
{
    // private $foo;
    // private $bar;
    // private $baz;
    // private $other;
    
    public function __construct(
        // Foo $foo,
        // Bar $bar,
        // Baz $baz,
        // Other $other
    )
    {
        // $this->foo = $foo;
        // $this->bar = $bar;
        // $this->baz = $baz;
        // $this->other = $other;
    }

    public function chargTot(PlanningRepository $repo, $FinSem, $DebSem) {
        //Création de la variable charge totale sur la semaine
       $Polyms=$repo -> findCharge($FinSem,$DebSem);
       foreach($Polyms as $polym){
           $CharTot=intval($polym['DureTheoPolym']/10000);
       }
       return $CharTot;
    }

    public function chargMachsem(PlanningRepository $repo, $FinSem, $DebSem, $TpsOuvParMach, $CharTot) {
        //Création de la variable charge de chaque machine sur la semaine encours
       $Polyms=$repo -> findChargeMach($FinSem,$DebSem);
       $datu = [];
       $datrix=[];
       $i = 0;
       foreach($Polyms as $polym){
           $y=intval($polym['DureTheoPolym']/10000);
           $RatioC=round(($y/$TpsOuvParMach)*100,1);
           $RatioR=round(($y/$CharTot)*100,1);
           $datu[$i] = ['y'=> $y,'indexLabel'=> $RatioC.'%',  'label' => $polym['Moyen']];
           $datrix[$i] = ['y'=> $y,'indexLabel'=> $RatioR.'%',  'label' => $polym['Moyen']];
           $i = $i + 1;
       }
       return $result=[$datu,$datrix];
    }

    public function totalPcs(PolymRealRepository $repo, $fin, $debut) {
        // Création de la variable pour le nombre total de pcs sur une durée
        $Polyms=$repo -> findAllPcs ($debut, $fin);
        foreach($Polyms as $polym){
            $TotPcs=intval($polym[1]);
        }
        return $TotPcs;
    }

    public function ordoUnMois(ChargeRepository $repo, $fin, $debut) {
        //Récupération de la charge SAP sur 1 mois
        $ChargeTot=$repo -> findChargeSem($debut,$fin); 
        $i=0;
        $j=0;
        foreach($ChargeTot as $charge){
            $y=intval($charge['NbrRef']);
            $lundi = new \DateTime();
            $lundi->setISOdate($charge['Annee'], $charge['Semaine']);
            $jour=strtotime($lundi->format("Y-m-d"))*1000;
            //dump($lundi->format("WY"));
            $TboData[$j]=['x'=> $jour,'y'=>$y];
            $Tablo[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
                $i = $i + 1;
            //$Tablo[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"false",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            
            //$CharTot=intval($polym['DureTheoPolym']/10000);
        }
        return $Tablo;
        //dump($RepartP);
    }

    public function ordoRetardUnMois(ChargeRepository $repo, $fin, $debut) {

        $ChargeTard=$repo -> findChargeSem($fin,$debut);
        //dump($ChargeTard);
        $i=0;
        $j=0;
        foreach($ChargeTard as $charge){
            $y=intval($charge['NbrRef']);
            $DebSem = new \DateTime();
            $DebSem->setISOdate($charge['Annee'], $charge['Semaine']);
            $JDebSem=strtotime($DebSem->format("Y-m-d"))*1000;
            //dump($lundi->format("WY"));
            $TboDatas[$j]=['x'=> $JDebSem,'y'=>$y];
            $Tablos[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"true",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboDatas];
                $i = $i + 1;
            //$Tablo[$i]=['type'=>"stackedColumn",'name'=>$charge['Cycles'],'showInLegend'=>"false",'xValueType'=>"dateTime",'yValueFormatString'=>"###",'dataPoints'=>$TboData];
            
            //$CharTot=intval($polym['DureTheoPolym']/10000);
        }
        return $Tablos;
    }
}