<?php

namespace App\DTO;

use App\Contracts\NotamFetcher;
use App\Enum\LocationType;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class Location extends Data
{
    public Collection $notams;

    public function __construct(
        public LocationType $type,
        public string $location,
        Collection|array|null $notams = null,
    ) {
        if (! is_null($notams)) {
            $this->notams = collect($notams);
        }
    }

    public function fetchNotams(): void
    {
        $this->notams = app(NotamFetcher::class)->get(collect($this->location));
    }

    public function notamsSet(): bool
    {
        return isset($this->notams);
    }
}
