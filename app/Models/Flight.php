<?php

namespace App\Models;

use App\DTO\AtsMessage;
use App\DTO\Location;
use App\Enum\LocationType;
use App\Events\FetchedNotams;
use App\Events\FetchingNotams;
use App\Exceptions\InvalidAtsMessageException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Collection;

class Flight extends Model
{
    use HasFactory;
    use HasUuids;
    use Prunable;

    protected $fillable = [
        'number',
        'callsign',
        'date',
        'std',
        'sta',
        'aircraft_type',
        'registration',
        'remarks',
        'locations',
    ];

    protected function casts()
    {
        return [
            'date' => 'date',
            'std'  => 'datetime',
            'sta'  => 'datetime',
        ];
    }

    protected function locations(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): Collection => collect(Json::decode($value))
                ->map(fn (array $data): Location => Location::from($data)),
            set: fn (Collection|array $value) => ['locations' => Json::encode($value)],
        );
    }

    public static function createFromFpl(AtsMessage $fpl): static
    {
        return tap(static::fromFpl($fpl))->save();
    }

    public static function fromFpl(AtsMessage $fpl): static
    {
        if ($fpl->field3 !== 'FPL') {
            throw new InvalidAtsMessageException;
        }

        return static::make([
            'callsign'      => $fpl->getCallsign(),
            'date'          => $fpl->getDate(),
            'std'           => $fpl->getEobt(),
            'sta'           => $fpl->getEobt()->add($fpl->getEet()),
            'aircraft_type' => $fpl->getAircraftType(),
            'registration'  => $fpl->getRegistration(),
            'locations'     => collect([
                new Location(
                    LocationType::Departure,
                    $fpl->getDeparture(),
                ),
                $fpl->getTakeoffAlternate() ? new Location(
                    LocationType::TakeoffAlternate,
                    $fpl->getTakeoffAlternate(),
                ) : null,
                new Location(
                    LocationType::Destination,
                    $fpl->getDestination(),
                ),
                ...array_map(fn (string $location) => new Location(
                    LocationType::DestinationAlternate,
                    $location,
                ), $fpl->getDestinationAlternates()),
                ...array_map(fn (string $location) => new Location(
                    LocationType::Enroute,
                    $location,
                ), $fpl->getEnrouteAlternates()),
                ...array_map(fn (string $location) => new Location(
                    LocationType::Fir,
                    $location,
                ), $fpl->getFirs()),
            ])->filter()->values(),
        ]);
    }

    public function prunable(): Builder
    {
        return static::query()
            ->where('updated_at', '<=', now()->subMonth());
    }

    public function allLocations(): Collection
    {
        return $this->locations
            ->pluck('location');
    }

    public function fetchNotams(): void
    {
        FetchingNotams::dispatch($this);

        /** @var Location $location */
        foreach ($this->locations as $location) {
            if ($location->notamsSet()) {
                continue;
            }

            $location->fetchNotams();
        }

        FetchedNotams::dispatch($this, $this->locations->sum(fn (Location $location) => count($location->notams)));
    }
}
