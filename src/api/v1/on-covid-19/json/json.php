<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
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
Functions
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

	//$this->infectionsByRequestedTime = $currentlyInfected*(pow(2, intval($timeToElapse/3)));
	
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

	$impact = (array) $data->impact($data);
	//echo json_encode($impact);
	echo str_replace('\\u0000', "", json_encode($impact));
	$severeImpact = (array) $data->severeImpact($data);
	echo str_replace('\\u0000', "", json_encode($severeImpact));
	//echo json_encode($severeImpact);

return $data;

	}

}


//$data = new Covid19Estimator();

$estimate = new Covid19Estimator();

if($_GET){
$estimate->name = $_GET['name'];
$estimate->avgAge = $_GET['avgAge'];
$estimate->avgDailyIncomeInUSD = $_GET['avgDailyIncomeInUSD'];
$estimate->avgDailyIncomePopulation = $_GET['avgDailyIncomePopulation'];
$estimate->periodType = $_GET['periodType'];
$estimate->timeToElapse = $_GET['timeToElapse'];
$estimate->reportedCases = $_GET['reportedCases'];
$estimate->population = $_GET['population'];
$estimate->totalHospitalBeds = $_GET['totalHospitalBeds'];

//echo var_dump($_GET);

// set response code - 200 OK
 http_response_code(200);

//$estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate);
//echo json_encode($estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate));
 $estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate);



//$jsonData = json_encode($data);
//echo $jsonData;
}

else{

	// set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no estimation available
    echo json_encode(
        array("whoopsie!" => "Could not get covid-19 estimate in JSON")
    );

}

/*

if($data->covid19ImpactEstimator($data)){

 // set response code - 200 OK
 http_response_code(200);

$jsonData = json_encode($data);
echo $jsonData;
}


else{

	// set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no estimation available
    echo json_encode(
        array("message" => "Could not get estimate")
    );
}
*/

?>



