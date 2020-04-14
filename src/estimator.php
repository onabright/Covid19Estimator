<?php

class Covid19Estimator{

//data
public $name;
public $avgAge;
public $avgDailyIncomeInUSD;
public $avgDailyIncomePopulation;
public $periodType;
public $timeToElapse;
public $reportedCases;
public $population;
public $totalHospitalBeds;

//estimators
protected $currentlyInfected;
protected $infectionsByRequestedTime;
protected $severeCasesByRequestedTime;
protected $hospitalBedsByRequestedTime;
protected $casesForICUByRequestedTime;
protected $casesForVentilatorsByRequestedTime;
protected $dollarsInFlight;


/************
Methods to handle estimates
************/

//currentlyInfected
function currentlyInfected($reportedCases){
	$this->currentlyInfected = $reportedCases * 10;
	//$this->severeImpact = $this->currentlyInfected * 50;
}


//infectionsByRequestedTime
function infectionsByRequestedTime($currentlyInfected, $timeToElapse, $periodType){
	//currentlyInfected x (2 to the power of factor of 3 of $timeToElapse)

	switch ($periodType) {
		case 'days':
		$this->infectionsByRequestedTime = $currentlyInfected*(pow(2, intval($timeToElapse/3)));
			
			break;
		case 'weeks':
		$this->infectionsByRequestedTime = $currentlyInfected*(pow(2, intval(($timeToElapse*7)/3)));

			break;
		case 'months':
		$this->infectionsByRequestedTime = $currentlyInfected*(pow(2, intval(($timeToElapse*30)/3)));
			break;
		
		default:
		$this->infectionsByRequestedTime = $currentlyInfected*(pow(2, intval($timeToElapse/3)));
		  break;
	}
	
}


//severeCasesByRequestedTime
function severeCasesByRequestedTime($infectionsByRequestedTime){
	$this->severeCasesByRequestedTime = $infectionsByRequestedTime*0.15;

}


//hospitalBedsByRequestedTime
function hospitalBedsByRequestedTime($totalHospitalBeds, $severeCasesByRequestedTime){
	//total hospital beds (35% availability) - severe cases
	$this->hospitalBedsByRequestedTime = intval(($totalHospitalBeds*0.35) - $severeCasesByRequestedTime);

}


//casesForICUByRequestedTime
function casesForICUByRequestedTime($infectionsByRequestedTime){
	$this->casesForICUByRequestedTime = $infectionsByRequestedTime*0.05;

}


//casesForVentilatorsByRequestedTime
function casesForVentilatorsByRequestedTime($infectionsByRequestedTime){
	$this->casesForVentilatorsByRequestedTime = intval($infectionsByRequestedTime*0.02);


}

//dollarsInFlight

function dollarsInFlight($infectionsByRequestedTime, $avgDailyIncomeInUSD, $timeToElapse){
	$this->dollarsInFlight = intval((($infectionsByRequestedTime*0.65*$avgDailyIncomeInUSD)/$timeToElapse));

}

//impact estimator
function impact($data){
	
	$data->currentlyInfected($data->reportedCases);
	$data->infectionsByRequestedTime($data->currentlyInfected, $data->timeToElapse, $data->periodType);
	$data->severeCasesByRequestedTime($data->infectionsByRequestedTime);
	$data->hospitalBedsByRequestedTime($data->totalHospitalBeds, $data->severeCasesByRequestedTime);
	$data->casesForICUByRequestedTime($data->infectionsByRequestedTime);
	$data->casesForVentilatorsByRequestedTime($data->infectionsByRequestedTime);
	$data->dollarsInFlight($data->infectionsByRequestedTime, $data->avgDailyIncomeInUSD, $data->timeToElapse);
return $data;
}

//severe impact estimator
 function severeImpact($data){

	$data->currentlyInfected($data->reportedCases*50);
	$data->infectionsByRequestedTime($data->currentlyInfected, $data->timeToElapse, $data->periodType);
	$data->severeCasesByRequestedTime($data->infectionsByRequestedTime);
	$data->hospitalBedsByRequestedTime($data->totalHospitalBeds, $data->severeCasesByRequestedTime);
	$data->casesForICUByRequestedTime($data->infectionsByRequestedTime);
	$data->casesForVentilatorsByRequestedTime($data->infectionsByRequestedTime);
	$data->dollarsInFlight($data->infectionsByRequestedTime, $data->avgDailyIncomeInUSD, $data->timeToElapse);
return $data;
}


//impact estimation
function covid19ImpactEstimator($data){
	$impact = (array) $this->impact($data);
	$severeImpact = (array) $this->severeImpact($data);
	
/*
data: input,
impact: {},
severeImpact {}
*/

return $data = array(
	"data" => array(
		"input" => $data,
	),

	"estimate" => array(
		"impact" => $impact,
		"severeImpact" => $severeImpact
	),
  
 );
		
 }

}

?>



