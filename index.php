<?php
// List this month's income
if ( '2022-08' === date( 'Y-m' ) ) {
  $incomeItems = [
    '2866.40', // Palkka
    '679.20', // Työkkärituki
    '199.72', // Lapsilisä
    '446.81', // Vammaistuki
    '150.00', // Vuokravakuus
    '1246.44', // Veronpalautukset
  ];
} else {
  $incomeItems = [
    '2866.40', // Palkka
    '679.20', // Työkkärituki
    '199.72', // Lapsilisä
    '446.81', // Vammaistuki
  ];
}

// Require composer
require __DIR__ . '/vendor/autoload.php';

// Locale
date_default_timezone_set( 'Europe/Helsinki' );
setlocale( LC_ALL, 'fi_FI.UTF-8' );
setlocale( LC_TIME, 'fi_FI.UTF-8' );

// Set errors
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );

// Set up phpdotenv
$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
$dotenv->load();
?>
<!doctype html>
<html>
<head>
<title>Rahatilanne</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<style type="text/css">
:root {
  --color-neutral: #96cde4;
  --color-green: #1ccc5c;
  --color-red: #ec1b4b;
}

body {
  background-color: #0d1117;
  color: #f0f6fc;
  font-family: 'Inter', -apple-system, 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', sans-serif;
}

.unit {
  font-size: 32px;
  opacity: .7;
  font-weight: 400;
}

.neutral {
  color: var(--color-neutral);
}

.red {
  color: var(--color-red);
}

.green {
  color: var(--color-green);
}

.background-red {
  background-color: var(--color-red);
}

.background-green {
  background-color: var(--color-green);
}

.value {
  display: inline-block;
  font-size: 80px;
  font-weight: 700;
  margin-bottom: 10px;
  margin-right: 10px;
}

.pre {
  display: none;
  font-size: 14px;
  line-height: 1.77;
}

.label {
  display: inline-block;
  font-size: 16px;
  line-height: 1.77;
}

.item {
  max-width: 700px;
  padding: 20px 50px;
}


.powered {
  color: #fff;
  display: inline-block;
  text-decoration: none;
  margin-top: 60px;
  opacity: .2;
  transition: all 180ms;
}

.explanation {
  color: #898989;
  font-size: 13px;
  line-height: 1.77;
  margin: 15px 0 0 0;
}

.powered:hover {
  opacity: .7;
}

.powered span {
  font-size: 11px;
  margin-bottom: 10px;
  display: block;
}

.progress-bar,
.progress-bar-expenses {
  border-radius: 4px;
  height: 4px;
  white-space: nowrap;
}

.progress-bar {
  background-color: rgba(255 255 255 / .2);
  margin-top: 20px;
  max-width: 275px;
  width: 100%;
  display: none;
}

.progress-bar-expenses {
  padding: 0 5px;
  display: flex;
  align-items: center;
}

.progress-bar-value {
  color: #fff;
  font-weight: 600;
  display: inline-block;
  display: none;
  font-size: 13px;
  margin-right: 10px;
}

.progress-bar-label {
  color: #fff;
  display: inline-block;
  display: none;
  font-size: 10px;
  opacity: .5;
}

@media (max-width: 600px) {
  .item {
    padding: 20px 15px;
  }

  .value {
    font-size: 62px;
  }

  .label {
    font-size: 14px;
  }
}
</style>
<body>
<div class="wrapper">
<?php

// PHP 8 function support for PHP 7.4
if ( ! function_exists( 'str_contains' ) ) {
  function str_contains( string $haystack, string $needle ) : bool { // phpcs:ignore
    return '' === $needle || false !== strpos( $haystack, $needle );
  }
}

/**
 * Fucntion: array_contains
 * Check if string contains word from array.
 *
 * @since 1.0.0
 * @version 1.0.0
**/
function array_contains( $str, array $arr ) {
  foreach ( $arr as $a ) {
      if ( stripos( $str, $a ) !== false ) return true;
  }
  return false;
}

// Define things
$budgetId = $_ENV['YNAB_BUDGET_ID'];

// Function to call the API
function callAPI( $method, $url, $data ) {
  $token = $_ENV['YNAB_PERSONAL_ACCESS_TOKEN'];
  $curl = curl_init();

  switch ( $method ) {
    case 'POST':
      curl_setopt( $curl, CURLOPT_POST, 1 );

      if ( $data )
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
        break;
    case 'PUT':
      curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT' );

      if ($data)
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
        break;
    default:
      if ($data)
        $url = sprintf( '%s?%s', $url, http_build_query( $data ) );
    }

  // Options
  curl_setopt( $curl, CURLOPT_URL, $url );
  curl_setopt( $curl, CURLOPT_HTTPHEADER, [ "Authorization: Bearer $token" ] );
  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );

  // Disable caching
  // curl_setopt( $curl, CURLOPT_FRESH_CONNECT, true );

  // Execute
  $result = curl_exec( $curl );
  if ( ! $result ) { die( 'Connection Failure' ); }
  curl_close( $curl );
  return $result;
}

// Curl base URL
$base = 'https://api.youneedabudget.com/v1/';

// $months = curl_setopt( $ch, CURLOPT_URL, $base . '/' . $budgetId . '/months' );

// Get budgets
$get_budgets = callAPI( 'GET', $base . '/budgets/' . $budgetId . '/transactions?since_date=' . date( 'Y-m' ) . '-01', false );
$response_budgets = json_decode( $get_budgets, true );

// Get budgeted amount this month
$get_months = callAPI( 'GET', $base . '/budgets/' . $budgetId . '/months/' . date( 'Y-m' ) . '-01', false );
$response_months = json_decode( $get_months, true );

$income = array_sum( $incomeItems );

// Get this month's underfunded amount
$underfunded = 0;
$budgeted = 0;
foreach ( $response_months as $month ) {

  $budgeted = $month['month']['budgeted'] / 1000;
  $activity = abs( $month['month']['activity'] / 1000 );

  foreach ( $month['month']['categories'] as $category ) {
    $underfunded += $category['goal_under_funded'];
  }
}

// Get this month's transactions
$transactionItems = 0;
foreach ( $response_budgets as $budget ) {
  foreach ( $budget['transactions'] as $transaction ) {

    $ignored_accounts = [
      'e902b887-bc20-4eed-ae82-c36a8f8505d6',
      'e2662f83-0ddf-4275-be3c-6e90cf4006f8',
      '8e0bcb4d-d623-4ba7-a29f-c949b4a16282',
      'c54bf70e-62b7-4507-acdf-e07e1cab60bb',
      '66c28b83-f66d-40c0-a771-aa06f28c633e',
      'df28aa6c-e99c-40a3-b070-a61d2d978943',
    ];

    // Sum all amounts together
    if ( ! array_contains( $transaction['account_id'], $ignored_accounts ) && ! str_contains( $transaction['category_name'], 'Inflow' ) ) {
      $transactionItems += $transaction['amount'];
    }
  }
}

// Get right amounts
$underfunded = $underfunded / 1000;
$transactions = abs( $transactionItems / 1000 );

// Calculate
$substraction = ( $income - $transactions ) - $underfunded;
?>

<div class="item">
  <?php
    $msg = 'jää säästöön';
    if ( $substraction > 0 ) {
      $class = 'green';
      $substraction = '+' . abs( $substraction );
    } else {
      $class = 'red';
      $substraction = $substraction;
    }

    $balance = number_format( (float) $substraction, 2, ',', '' );
    echo '<span class="value ' . $class . '">' . $balance . ' <span class="unit">&euro;</span></span>';
  ?>
  <span class="label <?php echo $class; ?>" style="display: none;"><?php echo $msg; ?></span>

  <div class="progress-bar">
    <div class="progress-bar-expenses background-<?php echo $class; ?>" style="width: <?php echo round( ( $budgeted / $income ) * 100, 0 ); ?>%">
      <span class="progress-bar-value"><?php echo round( $budgeted, 0 ); ?> &euro; / <?php echo round( $income, 0 ); ?> &euro;</span>
      <span class="progress-bar-label">Menot / Tulot</span>
    </div>
  </div>

  <p class="explanation">
    <span>Tämän kuun tulot on <b style="font-weight: 500;" class="neutral"><?php echo round( $income, 2 ); ?> &euro;</b><br></span>
    <span style="display: none;">Tälle kuulle budjetoitu <b style="font-weight: 500;" class="neutral"><?php echo round( $budgeted, 2 ); ?> &euro;</b><br></span>
    <span>Rahaa käytetty <b style="font-weight: 500;" class="neutral"><?php echo round( $transactions, 2 ); ?> &euro;</b><br></span>
    <span>Tuloista kulujen jälkeen jää vielä <b style="font-weight: 500;" class="green"><?php echo round( $income - $transactions, 2 ); ?> &euro;</b><br></span>
    <span>Tässä kuussa tarvitaan vielä <b style="font-weight: 500;" class="green"><?php echo round( $underfunded, 2 ); ?> &euro;</b><br></span>
  </p>

  <a class="powered" href="https://app.youneedabudget.com/"><span>Rajapinnan tarjoaa</span><svg aria-label="YNAB" fill="none" height="29" viewBox="0 0 115 29" width="115" xmlns="http://www.w3.org/2000/svg"><path d="M18.249 17.795v10.091H9.766v-10.09L0 0h9.719l4.447 8.527C15.66 5.763 17.143 2.682 18.543 0h9.307zm26.544 10.091l-7.954-13.02v13.02h-8.036V0h7.248l7.953 12.808V0h7.99v27.886zm27.391-4.951h-8.201l-1.647 4.94h-9.06L63.489 0h9.766l9.965 27.886h-9.436zm-4.071-12.761L65.97 16.56h4.283zm27.732 17.712H84.48V0h10.79c6.388 0 10.095 2.717 10.095 7.657 0 2.47-1.283 4.493-3.377 6.01 2.188 1.235 3.541 3.705 3.541 6.222-.011 5.151-3.635 7.997-9.683 7.997zm-1.2-21.382h-2.06v4.528h2.19c1.07 0 2.023-.87 2.023-2.387-.012-1.188-.788-2.14-2.153-2.14zM94.48 16.76h-1.894v4.658h1.777c1.776 0 2.718-.87 2.718-2.27 0-1.482-.742-2.388-2.6-2.388z" fill="#fff"/><path d="M105.776 23.723c0-2.552 2.059-4.61 4.612-4.61S115 21.17 115 23.722s-2.059 4.61-4.612 4.61-4.612-2.058-4.612-4.61z" fill="#fff"/></svg></a>
</div>
</div>
</body>
</html>
