<?php

namespace App\Http\Controllers;

use App\Jobs\NotamProcessJob;
use App\Rules\IsAtcFlightPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotamController extends Controller
{
    public function index()
    {
        return inertia('Notams/NotamsIndex', [
            'session_id' => Session::getId(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'flight_plan' => ['required', 'string', new IsAtcFlightPlan]
        ]);

        dispatch(new NotamProcessJob(trim($validated['flight_plan']), Session::getId()));

        return redirect()->route('notam.index');
    }
}
