<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Database\Seeders\DemoTaskSeeder;

class DemoResetController extends Controller
{
    public function reset()
    {
        $seeder = new DemoTaskSeeder();
        $seeder->run();

        return response()->json([
            'success' => true,
            'message' => 'Demo data reset successfully with 6 fresh tasks',
        ]);
    }
}
