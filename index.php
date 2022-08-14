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

// Define things
$token = $_ENV['YNAB_PERSONAL_ACCESS_TOKEN'];
$budgetId = $_ENV['YNAB_BUDGET_ID'];

// Initialize YNAB via curl
$ch = curl_init();
$base = 'https://api.youneedabudget.com/v1/budgets';

curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_HTTPHEADER, [ "Authorization: Bearer $token" ] );
curl_setopt( $ch, CURLOPT_URL, $base . '/' . $budgetId . '/transactions?since_date=2022-08-01' );
$response = curl_exec( $ch );

// Get api response code
var_dump( $response );

?>
</body>
</html>
