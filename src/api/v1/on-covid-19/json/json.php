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
public $currentlyInfected;
public $infectionsByRequestedTime;
public $severeCasesByRequestedTime;
public $hospitalBedsByRequestedTime;
public $casesForICUByRequestedTime;
public $casesForVentilatorsByRequestedTime;
public $dollarsInFlight;



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


//$data = new Covid19Estimator();

$estimate = new Covid19Estimator();

if($_GET){

//start counting http response time
usleep(mt_rand(100, 10000));

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



//if data is received set response code
 http_response_code(200);

//$estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate);
//echo json_encode($estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate));
 //$estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate);
 $json =json_encode($estimate->covid19ImpactEstimator($estimate));
 echo $json;


 /*
 $apiCallInfo = array(
 "a" => $_SERVER['PHP_SELF'],
 "b" => $_SERVER['REQUEST_METHOD'],
 "c" => $_SERVER['REDIRECT_STATUS'],
 "d" => $_SERVER['REMOTE_ADDRESS'],
 "e" => $_SERVER['REQUEST_TIME'],
 "f" => $_SERVER['USER_AGENT'],
 "g" => $_SERVER['REQUEST_TIME_FLOAT'],
 "h" => $_SERVER['CONTENT_LENGTH']
);
 /*End Log requests*/

 //set the log date
 $logDate = date('d-m-Y H:i:s');

 //get the http response code
 $HTTPStatus = http_response_code();

 //calculate the http request/response time
 $getMicroTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
 $round = round($getMicroTime, 3);
 $getMinute = (int) $round;
 $responseTime = $round - $getMinute . " ms";

 //store the log as an array
 $apiCallDetails = array(
		 "Time Stamp" => $logDate,
		 "HTTP Method" => $_SERVER['REQUEST_METHOD'],
		 "Request URI" => dirname($_SERVER['SCRIPT_NAME']), //hide the file name json.php
		 //"HTTP Status" => $_SERVER['REDIRECT_STATUS'],
		 "HTTP Status" => $HTTPStatus,
		 "Remote Address" => $_SERVER['REMOTE_ADDR'],
		 "Response Time" => $responseTime
 
);

//convert the array into a string and format each entry per line
$apiCallToString = implode("\t", $apiCallDetails) ."\n";
$logAPICall = print_r($apiCallToString, true);

//write the request log to the log file
$logPath =  $_SERVER['DOCUMENT_ROOT'] . '/covid19estimator/src/api/v1/on-covid-19/logs/requests.txt';
//$writeToLog = file_put_contents('log.txt', $logAPICall, FILE_APPEND);
$writeToLog = file_put_contents($logPath, $logAPICall, FILE_APPEND);


}

else{

	// set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no estimation available
    echo json_encode(
        array("whoospie!" => "Could not get covid-19 estimate in JSON")
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



