<?php

namespace Database\Factories;

use App\DTO\Location;
use App\Enum\LocationType;
use App\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class FlightFactory extends Factory
{
    protected $model = Flight::class;

    public function definition(): array
    {
        return [
            'number'        => $this->faker->regexify('([A-Z][\d]|[\d][A-Z]|[A-Z]{2})(\d{1,})'),
            'callsign'      => $this->faker->regexify('[A-Z]{3}[A-Z0-9]{1,}'),
            'date'          => now(),
            'std'           => now(),
            'sta'           => now()->addHours(2.5),
            'aircraft_type' => $this->faker->regexify('[A-Z]{1}[A-Z0-9]{1,3}'),
            'registration'  => $this->faker->regexify('[A-Z]-[A-Z]{4}|[A-Z]{2}-[A-Z]{3}|N[0-9]{1,5}[A-Z]{0,2}'),
            'remarks'       => 'OFP #'.$this->faker->numberBetween(1, 20),
            'locations'     => $this->generateLocations(),
        ];
    }

    protected function generateLocations(): Collection
    {
        return collect([
            $this->fakeLocation(LocationType::Departure),
            $this->fakeLocation(LocationType::TakeoffAlternate, $this->faker->numberBetween(0, 1)),
            $this->fakeLocation(LocationType::Destination),
            $this->fakeLocation(LocationType::DestinationAlternate, $this->faker->numberBetween(0, 2)),
            $this->fakeLocation(LocationType::Enroute, $this->faker->numberBetween(0, 4)),
            $this->fakeLocation(LocationType::Fir, $this->faker->numberBetween(1, 4)),
        ])->flatten()->filter()->values();
    }

    protected function fakeLocation(LocationType $type, int $count = 1): Collection|Location|null
    {
        $locations = collect();

        for ($i = 0; $i < $count; $i++) {
            $locations->add(
                new Location($type, $this->faker->regexify('[A-Z]{4}'))
            );
        }

        return match ($count) {
            0       => null,
            1       => $locations->first(),
            default => $locations,
        };
    }
}
