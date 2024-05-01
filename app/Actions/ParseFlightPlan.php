<?php

namespace App\Actions;

use App\DTO\AtcFlightPlan;
use App\DTO\AtsMessage;
use App\Enum\Airports;
use App\Models\Flight;
use Exception;

class ParseFlightPlan
{
    public static function process(string $flightPlanText): AtcFlightPlan
    {
        return (new self())->parse($flightPlanText);
    }

    public function parse($flightPlanText): AtcFlightPlan
    {
        $flight = Flight::fromFpl(
            AtsMessage::from($flightPlanText)
        );

        $locations = collect($flight->locations);
        $requestedLocations = $locations->pluck('location');

        // TODO This is for the demo only. Check to make sure only airports in the UK, Ireland, Australia and New Zealand.
        $allowedLocations = str(Airports::ALL)->upper()->explode(',');

        if ($requestedLocations->diff($allowedLocations)->count() > 0) {
            throw new Exception('Sorry, for this demo, you can strictly only submit ATC flight plan messages that contain major (i.e. the main/international) airports anywhere in Ireland, the United Kingdom, Australia, or New Zealand. '.$requested->diff($allowedLocations)->implode(',').' is not allowed. Currently accepted airports are: '.str(Airports::ALL)->explode(',')->sort()->implode(','));
        }

        return new AtcFlightPlan(
            departureAirport: $locations->where('type', 'departure')->pluck('location'),
            destinationAirport: $locations->where('type', 'destination')->pluck('location'),
            destinationAlternate: $locations->where('type', 'destination_alternate')->pluck('location'),
            firs: $locations->where('type', 'fir')->pluck('location'),
            enrouteAlternates: $locations->where('type', 'enroute')->pluck('location'),
            takeoffAlternate: $locations->where('type', 'departure_alternate')->pluck('location'),
        );
    }
}
