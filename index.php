<?php
// Cache settings
$cachefile = 'cache.html';
$cachetime = 1 * 60;
if ( file_exists( $cachefile ) && time() - $cachetime < filemtime( $cachefile ) ) {
	$comment = '<!-- Amazing hand crafted super cache by rolle, generated ' . date( 'H:i', filemtime( $cachefile ) ) . ' -->';
	include $cachefile;
  echo $comment;
	exit;
}
ob_start();

// Require composer
require __DIR__ . '/vendor/autoload.php';

// Locale
date_default_timezone_set( 'Europe/Helsinki' );
setlocale( LC_ALL, 'fi_FI.UTF-8' );
setlocale( LC_TIME, 'fi_FI.UTF-8' );

// Set errors
ini_set( 'display_errors', 0 );
ini_set( 'display_startup_errors', 0 );

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
  --color-red: #df345b;
  --color-text: #f0f6fc;
}

body {
  background-color: #0d1117;
  color: var(--color-text);
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
  padding: 30px;
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

.last-modified {
  color: #666;
  font-size: 13px;
  margin-top: -10px;
  margin-bottom: 0;
  position: relative;
}

.powered:hover {
  opacity: .7;
}

.powered span {
  font-size: 11px;
  margin-bottom: 10px;
  display: block;
  margin-top: 10px;
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

.item-wrapper p {
  margin: 0;
}

.item-wrapper-alt {
  border-radius: 10px;
  padding: 0;
  margin: 15px 0;
  width: auto;
  display: inline-flex;
}

.item-wrapper-alt:first-of-type {
  margin-top: 0;
}

.item-wrapper-alt:last-of-type {
  margin-bottom: 20px;
}

.item-wrapper-alt .value {
  font-size: 62px;
  margin: 0;
}

.item-wrapper-alt .unit {
  font-size: 22px;
}

.sub-label {
  font-size: 12px;
  margin-top: 6px;
  display: block;
  opacity: .8;
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

#chart {
  margin-top: 2rem;
}

.apexcharts-tooltip {
  background: rgb(0 0 0 / .95) !important;
  color: var(--color-text) !important;
  border: 0 !important;
  box-shadow: none !important;
  font-size: 12px !important;
  border-radius: 4px !important;
  font-family: 'Inter', -apple-system, 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', sans-serif !important;
}

.apexcharts-bar-area {
  transition: none !important;
}

.apexcharts-backgroundBar {
  background-color: transparent !important;
  box-shadow: none !important;
  /* Hack to hide the background bar */
  width: 1000% !important;
  transform: translateX(-21px) !important;
}

.apexcharts-bar-area:hover {
  fill: var(--color-red) !important;
  box-shadow: none !important;
}

.apexcharts-tooltip-series-group {
  padding: 0 10px 0 5px !important;
}

.apexcharts-tooltip-text-y-value {
  font-size: 20px !important;
  font-weight: 600 !important;
  color: var(--color-red) !important;
}

.apexcharts-tooltip-text-y-label,
.apexcharts-tooltip-marker {
  display: none !important;
}

.apexcharts-tooltip-title {
  background: #000 !important;
  border: 0 !important;
  margin-bottom: 0 !important;
  padding: 8px 10px 2px 10px !important;
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

// Get budgets
$get_budgets = callAPI( 'GET', $base . '/budgets/?include_accounts=true', false );
$response_budget = json_decode( $get_budgets, true );

// Get accounts
$get_accounts = callAPI( 'GET', $base . '/budgets/' . $budgetId . '/accounts', false );
$response_accounts = json_decode( $get_accounts, true );

// If rate limited
if ( str_contains( $get_budgets, 'Too many requests' ) ) {
  echo '<div class="item"><div class="item-wrapper item-wrapper-alt"><p class="explanation" style="font-size: 16px;">Rajapinnan rajat tulivat vastaan. Yritä myöhemmin uudelleen.</p></div></div>';
} else {

// Get budget transactions
$get_budget_transactions = callAPI( 'GET', $base . '/budgets/' . $budgetId . '/transactions?since_date=' . date( 'Y-m' ) . '-01', false );
$response_budget_transactions = json_decode( $get_budget_transactions, true );

// Get budget transactions for week graph
$get_budget_transactions_for_week_graph = callAPI( 'GET', $base . '/budgets/' . $budgetId . '/transactions?since_date=' . date( 'Y-m-d', strtotime( '-7 day' ) ), false );
$response_budget_transactions_for_week_graph = json_decode( $get_budget_transactions_for_week_graph, true );

// Get scheduled transactions
$get_scheduled = callAPI( 'GET', $base . '/budgets/' . $budgetId . '/scheduled_transactions?since_date=' . date( 'Y-m' ) . '-01', false );
$response_scheduled = json_decode( $get_scheduled, true );

// Get budgeted amount this month
$get_months = callAPI( 'GET', $base . '/budgets/' . $budgetId . '/months/' . date( 'Y-m' ) . '-01', false );
$response_months = json_decode( $get_months, true );

// Get this month's amounts
$underfunded = 0;
$food_money_available = 0;
$budgeted_income = 0;
$currently_available = 0;
$goal_target = 0;
$goal_overall_funded = 0;

// Time since function
function get_time_ago( $time ) {
  $time_difference = time() - $time;

  if ( $time_difference < 100 ) { return 'Budjettia päivitetty viimeksi hetki sitten'; }
  $condition = array(
    12 * 30 * 24 * 60 * 60  => 'vuosi',
    30 * 24 * 60 * 60       => 'kuukausi',
    24 * 60 * 60            => 'päivä',
    60 * 60                 => 'tunti',
    60                      => 'minuutti',
    1                       => 'sekuntti',
  );

  foreach ( $condition as $secs => $str ) {
		$d = $time_difference / $secs;
		if ( $d >= 1 ) {
		  $t = round( $d );
		  if ( 'tunti' === $str && $t > 1 ) {
				$str = 'tuntia';
		  } elseif ( 'päivä' === $str && $t > 1 ) {
				$str = 'päivää';
		  } elseif ( 'minuutti' === $str && $t > 1 ) {
				$str = 'minuuttia';
		  }

		  return 'Budjettia päivitetty viimeksi ' . $t . ' ' . $str . ' sitten';
		}
  }
}

// Get budget info
$budget_last_updated = 0;
foreach ( $response_budget as $budgets ) {
  foreach ( $budgets as $budgetlist ) {
		foreach ( $budgetlist as $budget ) {
		  if ( '24d3a66a-0a98-4677-8875-c6d12986480a' === $budget['id'] ) {
				$last_modified_time = strtotime( $budget['last_modified_on'] );
				$budget_last_updated = get_time_ago( $last_modified_time );
		  }
		}
  }
}

// Get account info
$account_balance_without_savings = 0;
foreach ( $response_accounts as $accounts ) {
  foreach ( $accounts as $accountlist ) {
    foreach ( $accountlist as $account ) {
      // All accounts that are not savings
		  if (
        ( '6da3bf99-84c8-4db4-bae1-667e8db42976' === $account['id'] ||
        '6b31b842-9bf0-45ce-b8e6-dedda4aa2b37' === $account['id'] ||
        '1753dd01-eeae-48de-b44d-efafe3acf465' === $account['id'] ||
        'f456839b-704a-4546-a439-06d5c610579f' === $account['id'] ||
        'a9489031-a9d1-428a-b024-609cee3b8f02' === $account['id'] ||
        '14c8f018-7249-4121-b15e-38dda043774a' === $account['id'] ||
        false === $budget['closed'] ) &&
        'c2b180e2-fd7f-49a5-9087-5e938dd11f02' !== $account['id']
        ) {
		    $account_balance_without_savings += $account['balance'] / 1000;
		  }
		}
  }
}

// Get months
foreach ( $response_months as $month ) {

  // Get income that has been already received this month so far
  $budgeted_income = abs( $month['month']['income'] / 1000 );

  foreach ( $month['month']['categories'] as $category ) {

		// If is not ASP, income deleted or hidden
		if ( ! str_contains( $category['name'], 'Inflow' ) && '85ee6c02-bcdc-471e-886a-9b9fcd7f4df7' !== $category['id'] && false === $category['hidden'] && false === $category['deleted'] ) {

		  // All money currently available
      if ( 'NEED' === $category['goal_type'] ) {
		    $currently_available += $category['balance'] / 1000;
      }
		}

		// Food category
		if ( 'f6824431-03d1-4230-80de-126b66bac5d2' === $category['id'] ) {
		  $food_money_available += $category['balance'] / 1000;
		}

		// Get underfunded amount
    if ( 'NEED' === $category['goal_type'] ) {
      $underfunded += $category['goal_under_funded'];
    }
  }
}

// Get scheduled transactions
$income_items = 0;
foreach ( $response_scheduled as $scheduled ) {
  foreach ( $scheduled['scheduled_transactions'] as $scheduled_transaction ) {
		if ( str_contains( $scheduled_transaction['category_name'], 'Inflow' ) && str_contains( $scheduled_transaction['date_next'], date( 'Y-m' ) ) ) {
		  $income_items += $scheduled_transaction['amount'];
		}
  }
}

// Get this month's transactions
$transaction_items = 0;
foreach ( $response_budget_transactions as $budget_transaction ) {

  // Get transactions
  foreach ( $budget_transaction['transactions'] as $transaction ) {

		// Ignore investments
		$ignored_accounts = [
		'e902b887-bc20-4eed-ae82-c36a8f8505d6',
		'e2662f83-0ddf-4275-be3c-6e90cf4006f8',
		'8e0bcb4d-d623-4ba7-a29f-c949b4a16282',
		'c54bf70e-62b7-4507-acdf-e07e1cab60bb',
		'66c28b83-f66d-40c0-a771-aa06f28c633e',
		'df28aa6c-e99c-40a3-b070-a61d2d978943',
		];

		// Sum all amounts together
		if ( ! array_contains( $transaction['account_id'], $ignored_accounts ) && ! str_contains( $transaction['category_name'], 'Inflow' ) && null === $transaction['transfer_account_id'] ) {
		  $transaction_items += $transaction['amount'];
		}
  }
}

foreach ( $response_budget_transactions_for_week_graph as $budget_transaction_for_week_graph ) {

  // Get transactions
  foreach ( $budget_transaction_for_week_graph['transactions'] as $transaction_for_week_graph ) {

		// Ignore investments
		$ignored_accounts = [
		'e902b887-bc20-4eed-ae82-c36a8f8505d6',
		'e2662f83-0ddf-4275-be3c-6e90cf4006f8',
		'8e0bcb4d-d623-4ba7-a29f-c949b4a16282',
		'c54bf70e-62b7-4507-acdf-e07e1cab60bb',
		'66c28b83-f66d-40c0-a771-aa06f28c633e',
		'df28aa6c-e99c-40a3-b070-a61d2d978943',
		];

		// Sum all amounts together
		if ( ! array_contains( $transaction_for_week_graph['account_id'], $ignored_accounts ) && ! str_contains( $transaction_for_week_graph['category_name'], 'Inflow' ) && null === $transaction_for_week_graph['transfer_account_id'] ) {

      // Show only this week's transactions
      if ( $transaction_for_week_graph['date'] >= date( 'Y-m-d', strtotime( '-7 day' ) ) ) {

				// Create initial array
				$week_transactions[] = array(
				  'date' => $transaction_for_week_graph['date'],
				  'amount' => number_format( (float) abs( $transaction_for_week_graph['amount'] / 1000 ), 2, '.', '' ),
				);
      }
		}
  }
}

// Get right amounts
$underfunded = $underfunded / 1000;
$transactions = abs( $transaction_items / 1000 );
$income = abs( $income_items / 1000 ) + $budgeted_income;
$expenses = $transactions + $underfunded;

// Calculate
$substraction = $income - ( $expenses + $currently_available );
?>

<div class="item">
  <div class="items">

    <div class="item-wrapper item-wrapper-alt">
      <p>
        <?php
          if ( $substraction > 0 ) {
            $class = 'green';
            $substraction = '+' . abs( $substraction );
          } else {
            $class = 'red';
            $substraction = $substraction;
          }

          // Print balance
          $balance = number_format( (float) $substraction, 2, ',', '' );
          echo '<span class="value ' . $class . '">' . $balance . ' <span class="unit">&euro;</span></span>';

          // Calculate days remaining this month
          $timestamp = strtotime( 'now' );
          $days_remaining_this_month = (int) date( 't', $timestamp ) - (int) date( 'j', $timestamp );

          if ( $days_remaining_this_month === 0 ) {
            $days_remaining_this_month = 1;
          }
        ?>
        <span class="sub-label <?php echo $class; ?>">Kuukauden tulot miinus menot</span></span>
      </p>
    </div><br>

    <div class="item-wrapper item-wrapper-alt">
      <p>
        <span class="value green"><?php echo number_format( (float) $food_money_available / $days_remaining_this_month, 2, ',', '' ); ?> <span class="unit">&euro;</span></span><br />
        <span class="sub-label green">Reaaliaikainen päiväbudjetti ruokaan</span></span>
      </p>
    </div>

  </div>

  <p class="explanation">
    <span>Rahaa tilillä nyt <b style="font-weight: 500;" class="green"><?php echo number_format( (float) $account_balance_without_savings, 2, ',', '' ); ?> &euro;</b><br></span>
    <span>Tämän kuun tulot on <b style="font-weight: 500;" class="green"><?php echo number_format( (float) $income, 2, ',', '' ); ?> &euro;</b><br></span>
    <!-- <span>Tämän kuun menot tähän mennessä <b style="font-weight: 500;" class="neutral"><?php echo number_format( (float) $expenses, 2, ',', '' ); ?> &euro;</b><br></span> -->
    <span>Koko kuun arvioidut menot <b style="font-weight: 500;" class="neutral"><?php echo number_format( (float) $expenses + $currently_available, 2, ',', '' ); ?> &euro;</b><br></span>
    <span>Rahaa käytetty tässä kuussa <b style="font-weight: 500;" class="neutral"><?php echo number_format( (float) $transactions, 2, ',', '' ); ?> &euro;</b><br></span>
    <span>Tämän kuun budjetoidut menot <b style="font-weight: 500;" class="neutral"><?php echo number_format( (float) $currently_available, 2, ',', '' ); ?> &euro;</b><br></span>
    <span>Budjetoimatta (tulevaa rahaa) <b style="font-weight: 500;" class="green"><?php echo number_format( (float) $income - $currently_available, 2, ',', '' ); ?> &euro;</b><br></span>
    <span>Ruokabudjetti loppukuulle <?php echo $days_remaining_this_month; ?> päivälle <b style="font-weight: 500;" class="green"><?php echo number_format( (float) $food_money_available, 2, ',', '' ); ?> &euro;</b><br></span>
    <span>Tuloista kulujen jälkeen jää vielä <b style="font-weight: 500;" class="green"><?php echo number_format( (float) $income - $transactions, 2, ',', '' ); ?> &euro;</b><br></span>
    <span>Tässä kuussa tarvitaan vielä <b style="font-weight: 500;" class="neutral"><?php echo number_format( (float) $underfunded, 2, ',', '' ); ?> &euro;</b><br></span>
  </p>

  <div id="chart"></div>

  <p class="last-modified">
    <?php echo $budget_last_updated; ?>.
  </p>

  <a class="powered" href="https://app.youneedabudget.com/"><span>Rajapinnan tarjoaa</span><svg aria-label="YNAB" fill="none" height="29" viewBox="0 0 115 29" width="115" xmlns="http://www.w3.org/2000/svg"><path d="M18.249 17.795v10.091H9.766v-10.09L0 0h9.719l4.447 8.527C15.66 5.763 17.143 2.682 18.543 0h9.307zm26.544 10.091l-7.954-13.02v13.02h-8.036V0h7.248l7.953 12.808V0h7.99v27.886zm27.391-4.951h-8.201l-1.647 4.94h-9.06L63.489 0h9.766l9.965 27.886h-9.436zm-4.071-12.761L65.97 16.56h4.283zm27.732 17.712H84.48V0h10.79c6.388 0 10.095 2.717 10.095 7.657 0 2.47-1.283 4.493-3.377 6.01 2.188 1.235 3.541 3.705 3.541 6.222-.011 5.151-3.635 7.997-9.683 7.997zm-1.2-21.382h-2.06v4.528h2.19c1.07 0 2.023-.87 2.023-2.387-.012-1.188-.788-2.14-2.153-2.14zM94.48 16.76h-1.894v4.658h1.777c1.776 0 2.718-.87 2.718-2.27 0-1.482-.742-2.388-2.6-2.388z" fill="#fff"/><path d="M105.776 23.723c0-2.552 2.059-4.61 4.612-4.61S115 21.17 115 23.722s-2.059 4.61-4.612 4.61-4.612-2.058-4.612-4.61z" fill="#fff"/></svg></a>
</div>
</div>


<?php
// Create finished array for this week's transactions grouped by day
$week_transactions_combined_by_day = array();

foreach ( $week_transactions as $element ) {
  $amount = $element['amount'];
  $date_key = $element['date'];

  if ( array_key_exists( $date_key, $week_transactions_combined_by_day ) ) {
    $week_transactions_combined_by_day[ $date_key ]['y'] += $amount;
  } else {
    // Otherwise create a new element with datetimeobject as key
    $week_transactions_combined_by_day[ $date_key ]['x'] = $date_key;
    $week_transactions_combined_by_day[ $date_key ]['y'] = $amount;
  }
}

$data_for_apexchart = array_values( $week_transactions_combined_by_day );
$json_for_apexchart = trim( json_encode( $data_for_apexchart ), '[]""' );
?>
<script src="https://momentjs.com/downloads/moment-with-locales.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
window.onload = function(){
  moment.locale('fi');
}

var options = {
  series: [
    {
      data: [
        <?php echo $json_for_apexchart; ?>
      ]
    }
  ],
  states: {
    active: {
      filter: {
        type: 'none'
      }
    }
  },
  grid: {
    padding: {
      left: -17,
    },
    show: false,
    xaxis: {
      lines: {
      show: false
    }
   },
  },
  chart: {
    background: '#0d1117',
    height: 200,
    width: 280,
    type: "bar",
    toolbar: {
      show: false,
      tools: {
        download: false
      }
    },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      borderRadius: 2,
      columnWidth: '40%',
      barHeight: '70%',
      distributed: false,
      colors: {
        backgroundBarColors: ["#0d1117"]
      }
    },
  },
  fill: {
    colors: "#666",
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    width: 0
  },
  tooltip: {
    enabled: true,
    followCursor: false,
    onDatasetHover: {
      highlightDataSeries: false,
    },
  },
  floating: true,
  xaxis: {
    labels: {
      show: false,
      formatter: function (val) {
        return moment(new Date(val)).format("dddd l");
      }
    },
    axisTicks: {
      show: false
    },
    axisBorder: {
      show: false
    },
    legend: {
      show: false
    }
  },
  yaxis: {
    labels: {
      show: false,
      formatter: function(val) {
        return "-" + parseFloat(val).toFixed(2) + " €";
      }
    },
    axisTicks: {
    show: false
    },
    axisBorder: {
      show: false
    },
    legend: {
      show: false
    }
  },
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>
<?php }
// Cache end
$fp = fopen( $cachefile, 'w' );
fwrite( $fp, ob_get_contents() );
fclose( $fp );
ob_end_flush();
?>
</body>
</html>
