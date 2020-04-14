<!DOCTYPE html>
<html lang = "en">
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
include_once "estimator.php";

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
$apiType = $_POST['apiType'];

//echo var_dump($_POST);

//$estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate);
//json_encode($estimate->estimates = (array) $estimate->covid19ImpactEstimator($estimate));


//API function
function callAPI($method, $url, $data){
   $curl = curl_init();
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }
   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'APIKEY: 111111111111111111111',
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}


//encode xml
function xml_encode($mixed, $domElement=null, $DOMDocument=null) {
    if (is_null($DOMDocument)) {
        $DOMDocument =new DOMDocument;
        $DOMDocument->formatOutput = true;
        xml_encode($mixed, $DOMDocument, $DOMDocument);
        echo $DOMDocument->saveXML();
    }
    else {
        // To cope with embedded objects 
        if (is_object($mixed)) {
          $mixed = get_object_vars($mixed);
        }
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                    }
                    else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                }
                else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if (!(rtrim($index, 's') === $index)) {
                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
                        $plural->appendChild($singular);
                        $node = $singular;
                    }
                }

                xml_encode($mixedElement, $node, $DOMDocument);
            }
        }
        else {
            $mixed = is_bool($mixed) ? ($mixed ? 'true' : 'false') : $mixed;
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}



if(isset($_POST['apiCall'])){
  switch ($apiType) {
    case 'JSON':

     $data_array = $estimate->covid19ImpactEstimator($estimate);
     $make_call = callAPI('POST', 'http://35.225.33.89/covid19estimator/src/api/v1/on-covid-19/json/json.php', json_encode($data_array));

      $response = json_decode($make_call, true);
      header("Location:http://35.225.33.89/covid19estimator/src/api/v1/on-covid-19/json/json.php");
      //$errors   = $response['response']['errors'];
      //$data     = $response['response']['data'][0];

      break;
      case 'XML':

      $data_array = (array) $estimate->covid19ImpactEstimator($estimate);
      $make_call = callAPI('POST', 'http://35.225.33.89/covid19estimator/src/api/v1/on-covid-19/json/json.php',
       json_encode($data_array));
      $response = json_decode($make_call, true);
      header("Location:http://35.225.33.89/covid19estimator/src/api/v1/on-covid-19/json/json.php");
      break;
    
  }

  //echo "connection to API successful"."<br>";
  //var_dump($_POST);
}



}
else {
  //echo "could not connect to API";
}
?>


<body>
	<div class="container">
	<!--Header tag: Contains like the navigation menu -->
	<header>
  		<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
      <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="estimator_api_call.php">Covid-19 Estimator | API Call</a>
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

    <div class="form-group">
        <label for="exampleFormControlSelect1">API Call</label>
        <select class="form-control" name="apiType"id="data-period-type" required>
              <option disabled="disabled" selected="selected">-Select Format-</option>
              <option>JSON</option>
              <option>XML</option>
              
         </select>
      <div class="invalid-feedback">Required</div>
  </div>
  <button type="submit" name="apiCall" class="btn btn-success btn-lg btn-block" id="data-go-estimate">Make API Call</button>	
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