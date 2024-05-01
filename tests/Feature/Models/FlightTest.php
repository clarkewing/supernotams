<?php

use App\DTO\AtsMessage;
use App\DTO\Location;
use App\Enum\LocationType;
use App\Models\Flight;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

it('prunes flights untouched for over a month', function () {
    $flight = Flight::factory()->create();

    Artisan::call('model:prune');

    $this->assertDatabaseHas(Flight::class, ['id' => $flight->id]);

    $this->travel(32)->days();

    Artisan::call('model:prune');

    $this->assertDatabaseMissing(Flight::class, ['id' => $flight->id]);
});

it('can be instantiated from a FPL ATS message', function () {
    $fplMessage = AtsMessage::fromString(<<<'ATS'
        (FPL-CPA7878-IS
         -A35K/H-SABDE2E3GHIJ1J2J3J4J5LM1OP2RVWXYZ/LB1D1G1
         -NZAA0600
         -M085F400 DCT UPLAR A464 RIGMI/N0491F400 A464 SCOTT J208 BN V129
         EML T13 DOLIB DCT 2003S14345E 1800S14050E 1600S13735E 1420S13441E
         DCT DN DCT
         -YPDN0619 YBCS
         -PBN/A1B1C1D1L1O1S2T1 NAV/RNP2 COM/INTEGRATED DAT/1FANSE2PDC
         SUR/260B RSP180 CANMANDATE DOF/240322 REG/BLXA EET/NZZO0031
         YBBB0130 SEL/ADHK CODE/789213 OPR/CPA PER/D RALT/YMML YPDN
         RMK/TCAS)
        ATS);

    expect(Flight::fromFpl($fplMessage))
        ->toBeInstanceOf(Flight::class)
        ->exists->toBeFalse()
        ->callsign->toBe('CPA7878')
        ->date->toEqual(Carbon::create(2024, 3, 22))
        ->std->toEqual(Carbon::create(2024, 3, 22, 6, 0))
        ->sta->toEqual(Carbon::create(2024, 3, 22, 12, 19))
        ->aircraft_type->toBe('A35K')
        ->registration->toBe('BLXA')
        ->locations->toBeInstanceOf(Collection::class)
        ->locations->each->toBeInstanceOf(Location::class)
        ->locations->each->toHaveKeys(['type', 'location'])
        ->locations->toMatchArray([
            ['type' => 'departure', 'location' => 'NZAA'],
            ['type' => 'destination', 'location' => 'YPDN'],
            ['type' => 'destination_alternate', 'location' => 'YBCS'],
            ['type' => 'enroute', 'location' => 'YMML'],
            ['type' => 'enroute', 'location' => 'YPDN'],
            ['type' => 'fir', 'location' => 'NZZO'],
            ['type' => 'fir', 'location' => 'YBBB'],
        ]);
});

it('can be created from a FPL ATS message', function () {
    $fplMessage = AtsMessage::fromString(<<<'ATS'
        (FPL-CPA7878-IS
         -A35K/H-SABDE2E3GHIJ1J2J3J4J5LM1OP2RVWXYZ/LB1D1G1
         -NZAA0600
         -M085F400 DCT UPLAR A464 RIGMI/N0491F400 A464 SCOTT J208 BN V129
         EML T13 DOLIB DCT 2003S14345E 1800S14050E 1600S13735E 1420S13441E
         DCT DN DCT
         -YPDN0619 YBCS
         -PBN/A1B1C1D1L1O1S2T1 NAV/RNP2 COM/INTEGRATED DAT/1FANSE2PDC
         SUR/260B RSP180 CANMANDATE DOF/240322 REG/BLXA EET/NZZO0031
         YBBB0130 SEL/ADHK CODE/789213 OPR/CPA PER/D RALT/YMML YPDN
         RMK/TCAS)
        ATS);

    expect(Flight::createFromFpl($fplMessage))
        ->toBeInstanceOf(Flight::class)
        ->exists->toBeTrue();
});

it('can return a collection of all locations', function () {
    $flight = Flight::factory()->create([
        'locations' => collect([
            new Location(LocationType::Departure, 'LFPO'),
            new Location(LocationType::Destination, 'GMMW'),
            new Location(LocationType::DestinationAlternate, 'GMFO'),
            new Location(LocationType::Fir, 'LFFF'),
            new Location(LocationType::Fir, 'LFBB'),
            new Location(LocationType::Fir, 'LECM'),
            new Location(LocationType::Fir, 'GMMM'),
        ]),
    ]);

    expect($flight->allLocations())
        ->toMatchArray([
            'LFPO',
            'GMMW',
            'GMFO',
            'LFFF',
            'LFBB',
            'LECM',
            'GMMM',
        ]);
});
