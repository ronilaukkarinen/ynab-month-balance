<?php
// Require composer
require __DIR__ . '/vendor/autoload.php';

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
<title>YNAB Balance</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<style type="text/css">
body {
  background-color: #0d1117;
  color: #f0f6fc;
  font-family: 'Inter', -apple-system, 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', sans-serif;
}
</style>
<body>
<?php

// PHP 8 function support for PHP 7.4
if ( ! function_exists( 'str_contains' ) ) {
  function str_contains( string $haystack, string $needle ) : bool { // phpcs:ignore
    return '' === $needle || false !== strpos( $haystack, $needle );
  }
}

// Define things
$token = $_ENV['YNAB_PERSONAL_ACCESS_TOKEN'];
$budgetId = $_ENV['YNAB_BUDGET_ID'];

// Initialize YNAB via curl
$ch = curl_init();
$base = 'https://api.youneedabudget.com/v1/budgets';

curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_HTTPHEADER, [ "Authorization: Bearer $token" ] );
// TODO: Add since_date as dynamic (this month)
curl_setopt( $ch, CURLOPT_URL, $base . '/' . $budgetId . '/transactions?since_date=2022-08-01' );
// TODO: Learn how to call multiple endpoints
// TODO: Get budgeted amount for this month
// $months = curl_setopt( $ch, CURLOPT_URL, $base . '/' . $budgetId . '/months' );
$result = curl_exec( $ch );
$response = json_decode( $result, true );

$incomeItems = [
  '2411.10', // Palkka
  '679.20', // Työkkärituki
  '199.72', // Lapsilisä
  '416.91', // Lapsilisä
  '1246.44', // Veronpalautus
];
$income = array_sum( $incomeItems );

$transactionItems = 0;
foreach ( $response as $item ) {
  foreach ( $item['transactions'] as $transaction ) {

    // Sum all amounts together
    if ( ! str_contains( $transaction['category_name'], 'Inflow' ) ) {
      $transactionItems += $transaction['amount'];
    }
  }
}
$transactions = abs( $transactionItems / 1000 );
echo $transactions . '<br>';
echo $income . '<br>';
echo $income - $transactions;

curl_close( $ch );
?>
</body>
</html>
