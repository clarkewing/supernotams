<?php

use App\DTO\AtsMessage;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->message = <<<'ATS'
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
        ATS;
});

it('can parse an ATS message string', function () {
    expect(AtsMessage::fromString($this->message))
        ->toBeInstanceOf(AtsMessage::class)
        ->field3->toBe('FPL')
        ->field5->toBeNull()
        ->field7->toBe('CPA7878')
        ->field8->toBe('IS')
        ->field9->toBe('A35K/H')
        ->field10->toBe('SABDE2E3GHIJ1J2J3J4J5LM1OP2RVWXYZ/LB1D1G1')
        ->field13->toBe('NZAA0600')
        ->field14->toBeNull()
        ->field15->toBe('M085F400 DCT UPLAR A464 RIGMI/N0491F400 A464 SCOTT J208 BN V129 EML T13 DOLIB DCT 2003S14345E 1800S14050E 1600S13735E 1420S13441E DCT DN DCT')
        ->field16->toBe('YPDN0619 YBCS')
        ->field17->toBeNull()
        ->field18->toBe('PBN/A1B1C1D1L1O1S2T1 NAV/RNP2 COM/INTEGRATED DAT/1FANSE2PDC SUR/260B RSP180 CANMANDATE DOF/240322 REG/BLXA EET/NZZO0031 YBBB0130 SEL/ADHK CODE/789213 OPR/CPA PER/D RALT/YMML YPDN RMK/TCAS')
        ->field19->toBeNull()
        ->field20->toBeNull()
        ->field21->toBeNull()
        ->field22->toBeNull();
});

describe('getters', function () {
    beforeEach(function () {
        $this->message = AtsMessage::fromString($this->message);
    });

    it('can get the callsign', function () {
        expect($this->message->getCallsign())->toBe('CPA7878');
    });

    it('can get the aircraft type', function () {
        expect($this->message->getAircraftType())->toBe('A35K');
    });

    it('can get the departure', function () {
        expect($this->message->getDeparture())->toBe('NZAA');
    });

    it('can get the EOBT', function () {
        expect($this->message->getEobt())->toEqual(Carbon::create(
            2024,
            3,
            22,
            6,
            0,
        ));
    });

    it('can get the destination', function () {
        expect($this->message->getDestination())->toBe('YPDN');
    });

    it('can get the EET', function () {
        expect($this->message->getEet())->toEqual(CarbonInterval::create(
            hours:6,
            minutes: 19,
        ));
    });

    it('can get the destination alternates', function () {
        expect($this->message->getDestinationAlternates())->toBe(['YBCS']);

        $this->message->field16 = 'YPDN0619 YBCS YBAS';

        expect($this->message->getDestinationAlternates())->toBe(['YBCS', 'YBAS']);

        $this->message->field16 = 'YPDN0619';

        expect($this->message->getDestinationAlternates())->toBe([]);
    });

    it('can get the date of flight', function () {
        expect($this->message->getDate())->toEqual(Carbon::create(
            2024,
            3,
            22,
        ));
    });

    it('can get the registration', function () {
        expect($this->message->getRegistration())->toBe('BLXA');

        $this->message->field7 = 'FHODB';
        $this->message->field18 = Str::replace('REG/BLXA', '', $this->message->field18);

        expect($this->message->getRegistration())->toBe('FHODB');
    });

    it('can get the takeoff alternate', function () {
        expect($this->message->getTakeoffAlternate())->toBeNull();

        $this->message->field18 .= ' TALT/NZWN';

        expect($this->message->getTakeoffAlternate())->toBe('NZWN');
    });

    it('can get the enroute alternates', function () {
        expect($this->message->getEnrouteAlternates())->toBe(['YMML', 'YPDN']);

        $this->message->field18 = Str::replace('RALT/YMML YPDN', 'RALT/YMML', $this->message->field18);

        expect($this->message->getEnrouteAlternates())->toBe(['YMML']);

        $this->message->field18 = Str::replace('RALT/YMML ', '', $this->message->field18);

        expect($this->message->getEnrouteAlternates())->toBe([]);
    });

    it('can get the firs', function () {
        expect($this->message->getFirs())->toBe(['NZZO', 'YBBB']);

        $this->message->field18 = Str::replace('EET/NZZO0031 YBBB0130', 'EET/NZZO0031', $this->message->field18);

        expect($this->message->getFirs())->toBe(['NZZO']);

        $this->message->field18 = Str::replace('EET/NZZO0031', '', $this->message->field18);

        expect($this->message->getFirs())->toBe([]);
    });
});
