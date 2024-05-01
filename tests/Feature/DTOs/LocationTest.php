<?php

use App\DTO\Location;
use App\Enum\LocationType;

it('knows if its NOTAMs were set', function () {
    $location = new Location(LocationType::Departure, 'LFPO');
    expect($location->notamsSet())->toBeFalse();

    $location = new Location(LocationType::Departure, 'LFPO', ['A2399/23']);
    expect($location->notamsSet())->toBeTrue();

    $location = new Location(LocationType::Departure, 'LFPO', []);
    expect($location->notamsSet())->toBeTrue();
});

it('can fetch active NOTAMs for itself', function () {
    $location = new Location(LocationType::Departure, 'LFPO');

    expect($location->notamsSet())->toBeFalse();

    $location->fetchNotams();

    expect($location->notamsSet())->toBeTrue();

    expect($location->notams)
        ->not->toBeEmpty()
        ->each->toHaveKeys(['id', 'fullText', 'source']);
});
