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
public $currentlyInfected;
public $infectionsByRequestedTime;
public $severeCasesByRequestedTime;
public $hospitalBedsByRequestedTime;
public $casesForICUByRequestedTime;
public $casesForVentilatorsByRequestedTime;
public $dollarsInFlight;


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
	$severeImpact = (array) $data->severeImpact($data);
	
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



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name ="description" content="COVID-19 Estimator for Africa ">
	<meta name ="author" content = "Bright Onapito">
	<meta name ="viewport" content ="width=device-width, initial-scale=1.0">
	<title>SDG Challenge | COVID-19 Estimator App</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="css/styles.css">
</head>

<?php
//include_once "estimator.php";

$estimate = new Covid19Estimator();

if($_POST){
$estimate->name = $_POST['name'];
$estimate->avgAge = $_POST['avgAge'];
$estimate->avgDailyIncomeInUSD = $_POST['avgDailyIncomeInUSD'];
$estimate->avgDailyIncomePopulation = $_POST['avgDailyIncomePopulation'];
$estimate->periodType = $_POST['periodType'];
$estimate->timeToElapse = $_POST['timeToElapse'];
$estimate->reportedCases = $_POST['reportedCases'];
$estimate->population = $_POST['population'];
$estimate->totalHospitalBeds = $_POST['totalHospitalBeds'];

//echo var_dump($_POST);

//$estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate);
//header("Location:http://localhost/c19/src/estimator4.php");


	
	echo"<div class='container alert alert-success alert-dismissible'>";
	echo "<br><br>";

	//json_encode($estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate));
	  $json = json_encode($estimate->covid19ImpactEstimator($estimate));
	  echo $json;
	echo"</div>";



//json_encode($estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate));
}
?>





<body>
	<div class="container">
	<!--Header tag: Contains like the navigation menu -->
	<header>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="estimator.php">Covid-19 Estimator</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#contact">About</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <header class="bg-primary text-white">
  	<br><br><br><br><br>
    <div class="container text-center">
      <h1>#BuildforSDG Challenge 2020</h1>
      <br>
      
    </div>
  </header>

  <section id="about">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto">
         
         
          
        </div>
      </div>
    </div>
  </section>
		
	</header>

<!--The main area of the page-->
	<main>
		
		<form id="data-covid-19-estimator" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

	<div class="form-group">
	   <label for="exampleFormControlInput1">Region</label>
	   <input type="text" name="name" class="form-control" id="data-region" placeholder="Africa" value='<?php echo $estimate->name; ?>' required>
  	</div>

  <div class="form-group">
	   <label for="exampleFormControlInput1">Population</label>
	   <input type="number" class="form-control" name="population" id="data-population" placeholder="92931687" value='<?php echo $estimate->population; ?>' required>
	   <div class="invalid-feedback">Population required.</div>
  </div>

  <div class="form-group">
	   <label for="exampleFormControlInput1">Total Hospital Beds</label>
	   <input type="number" class="form-control" name="totalHospitalBeds"id="data-total-hospital-beds" placeholder="678874" value='<?php echo $estimate->totalHospitalBeds; ?>' required>
	   <div class="invalid-feedback">Required</div>
  </div>

  <div class="form-group">
    <label for="exampleFormControlSelect1">Period</label>
    <select class="form-control" name="periodType"id="data-period-type" required>
      <option disabled="disabled" selected="selected">-Select-</option>
      <option>days</option>
      <option>weeks</option>
      <option>months</option>
    </select>
    <div class="invalid-feedback">Required</div>
</div>

    <div class="form-group">
	   <label for="exampleFormControlInput1">Time Elapsed</label>
	   <input type="number" class="form-control" name="timeToElapse"id="data-time-to-elapse" placeholder="38" value='<?php echo $estimate->timeToElapse; ?>' required>
	   <div class="invalid-feedback">Required</div>
  	</div>

  	<div class="form-group">
	   <label for="exampleFormControlInput1">Reported Cases</label>
	   <input type="number" class="form-control" name="reportedCases"id="data-reported-cases" placeholder="2747" value='<?php echo $estimate->reportedCases; ?>' required>
  	</div>
  	<div>
  		<input type="hidden" id="data-avgAge" name="avgAge" value="19.7">
  		<input type="hidden" id="data-avgDailyIncomeInUSD" name="avgDailyIncomeInUSD" value="4">
  		<input type="hidden" id="data-avgDailyIncomePopulation" name="avgDailyIncomePopulation" value="0.73">
  	</div>
  

  <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block" id="data-go-estimate">Estimate</button>

			
		</form>
		<br><br>
	</main>

	
</div>

<footer class="py-3 bg-dark">
		<!--The footer section of the website-->
		 <div class="container">
      <p class="m-0 text-center text-white"> &copy; <?php echo date('Y') ?> | <a href="https://github.com/onabright" target="_blank">Bright Onapito</a></p>
    </div>
		
	</footer>



	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    

  <script>
        // Self-executing function
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('data-covid-19-estimator');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>



</body>
</html>





