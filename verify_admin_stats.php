<?php

use App\Models\User;
use App\Models\Flight;
use App\Models\Booking;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Bind request to avoid error in response helper
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);
\Illuminate\Support\Facades\Facade::clearResolvedInstance('request');

echo "--- Current Stats ---\n";
echo "Users: " . User::count() . "\n";
echo "Flights: " . Flight::count() . "\n";
echo "Bookings: " . Booking::count() . "\n";

// Instantiate controller
$controller = new AdminDashboardController();

// We need to assume the Request is okay or not needed. stats() doesn't take arguments.
try {
    $response = $controller->stats();
    echo "\n--- Controller Response ---\n";
    $data = $response->getData(true); // Get array

    // Check if chart_data exists
    if (isset($data['data']['chart_data'])) {
        echo "Chart Data: Present (" . count($data['data']['chart_data']) . " days)\n";
        // Show last day
        $lastDay = end($data['data']['chart_data']);
        echo "Last Day: " . transform($lastDay) . "\n";
    } else {
        echo "Chart Data: MISSING\n";
    }

    echo "Total Revenue: " . $data['data']['revenue'] . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

function transform($arr)
{
    return json_encode($arr);
}
