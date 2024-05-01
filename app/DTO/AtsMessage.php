<?php

namespace App\DTO;

use App\Exceptions\UnknownAtsMessageDesignatorException;
use App\Patterns;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

class AtsMessage extends Data
{
    public function __construct(
        /** Message type, number and reference data */
        public string $field3,
        /** Description of emergency */
        public ?string $field5,
        /** Aircraft identification and SSR mode and code */
        public ?string $field7,
        /** Flight rules and type of flight */
        public ?string $field8,
        /** Number and type of aircraft and wake turbulence category */
        public ?string $field9,
        /** Equipment */
        public ?string $field10,
        /** Departure aerodrome and time */
        public ?string $field13,
        /** Estimate data */
        public ?string $field14,
        /** Route */
        public ?string $field15,
        /** Destination aerodrome and total estimated elapsed time, alternate aerodrome(s) */
        public ?string $field16,
        /** Arrival aerodrome and time */
        public ?string $field17,
        /** Other information */
        public ?string $field18,
        /** Supplementary information */
        public ?string $field19,
        /** Alerting search and rescue information */
        public ?string $field20,
        /** Radio failure information */
        public ?string $field21,
        /** Amendment */
        public ?string $field22,
    ) {
    }

    public static function fromString(string $atsMessage): static
    {
        return static::fromArray(static::splitStringIntoFields($atsMessage));
    }

    public static function fromArray(array $fields): static
    {
        return new static(
            field3: $fields['field3'] ?? $fields[3],
            field5: $fields['field5'] ?? $fields[5] ?? null,
            field7: $fields['field7'] ?? $fields[7] ?? null,
            field8: $fields['field8'] ?? $fields[8] ?? null,
            field9: $fields['field9'] ?? $fields[9] ?? null,
            field10: $fields['field10'] ?? $fields[10] ?? null,
            field13: $fields['field13'] ?? $fields[13] ?? null,
            field14: $fields['field14'] ?? $fields[14] ?? null,
            field15: $fields['field15'] ?? $fields[15] ?? null,
            field16: $fields['field16'] ?? $fields[16] ?? null,
            field17: $fields['field17'] ?? $fields[17] ?? null,
            field18: $fields['field18'] ?? $fields[18] ?? null,
            field19: $fields['field19'] ?? $fields[19] ?? null,
            field20: $fields['field20'] ?? $fields[20] ?? null,
            field21: $fields['field21'] ?? $fields[21] ?? null,
            field22: $fields['field22'] ?? $fields[22] ?? null,
        );
    }

    protected static function splitStringIntoFields(string $atsMessage): array
    {
        // Per ICAO Doc 4444 Appendix 3 - Standard ATS Messages and their Composition
        $fields = Str::of($atsMessage)
            ->trim('()')
            ->replaceMatches('/\s+/', ' ')
            ->split('/\s*-/');

        $designator = $fields->shift();

        if ($designator === 'FPL') {
            return [
                3  => $designator,
                7  => $fields->shift(),
                8  => $fields->shift(),
                9  => $fields->shift(),
                10 => $fields->shift(),
                13 => $fields->shift(),
                15 => $fields->shift(),
                16 => $fields->shift(),
                18 => $fields->shift(),
            ];
        }

        throw new UnknownAtsMessageDesignatorException($designator);
    }

    public function getCallsign(): string
    {
        return Regex::match(
            '/^('.Patterns::callsign().'|'.Patterns::registrationWithoutHyphens().')(?:\/A'.Patterns::ssrCode().')?$/',
            $this->field7,
        )->group(1);
    }

    public function getAircraftType(): string
    {
        return Regex::match(
            '/^('.Patterns::aircraftType().')\/'.Patterns::wakeTurbulenceCategory().'$/',
            $this->field9,
        )->group(1);
    }

    public function getDeparture(): string
    {
        return Regex::match(
            '/^('.Patterns::icaoAirport().')'.Patterns::hhmm().'$/',
            $this->field13,
        )->group(1);
    }

    public function getEobt(): Carbon
    {
        return $this->getDate()->copy()->setTimeFrom(
            Carbon::createFromFormat(
                'Gi',
                Regex::match(
                    '/^'.Patterns::icaoAirport().'('.Patterns::hhmm().')$/',
                    $this->field13,
                )->group(1),
            )
        );
    }

    public function getDestination(): string
    {
        return Regex::match(
            '/^('.Patterns::icaoAirport().')'.Patterns::duration().'(?: '.Patterns::icaoAirport().'){0,2}$/',
            $this->field16,
        )->group(1);
    }

    public function getEet(): CarbonInterval
    {
        $eetString = Regex::match(
            '/^'.Patterns::icaoAirport().'('.Patterns::duration().')(?: '.Patterns::icaoAirport().'){0,2}$/',
            $this->field16,
        )->group(1);

        return CarbonInterval::create(
            hours: substr($eetString, 0, 2),
            minutes: substr($eetString, -2),
        );
    }

    public function getDestinationAlternates(): array
    {
        $result = Regex::match(
            '/^'.Patterns::icaoAirport().Patterns::duration().'((?: '.Patterns::icaoAirport().'){0,2})/',
            $this->field16,
        )->groupOr(1, '');

        return Str::of($result)
            ->explode(' ')
            ->filter()
            ->values()
            ->all();
    }

    public function getDate(): Carbon
    {
        return Carbon::createFromFormat(
            'ymd',
            Regex::match(
                '/DOF\/('.Patterns::yymmdd().')'.Patterns::field18Eol().'/',
                $this->field18,
            )->groupOr(1, now()->format('ymd')),
        )->setTime(0, 0);
    }

    public function getRegistration(): string
    {
        return Regex::match(
            '/REG\/('.Patterns::registrationWithoutHyphens().')'.Patterns::field18Eol().'/',
            $this->field18,
        )->groupOr(1, $this->getCallsign());
    }

    public function getTakeoffAlternate(): ?string
    {
        try {
            return Regex::match(
                '/TALT\/('.Patterns::icaoAirport().')'.Patterns::field18Eol().'/',
                $this->field18,
            )->group(1);
        } catch (RegexFailed) {
            return null;
        }
    }

    public function getEnrouteAlternates(): array
    {
        $result = Regex::match(
            '/RALT\/('.Patterns::icaoAirport().'(?: '.Patterns::icaoAirport().')*)'.Patterns::field18Eol().'/',
            $this->field18,
        )->groupOr(1, '');

        return Str::of($result)
            ->explode(' ')
            ->filter()
            ->values()
            ->all();
    }

    public function getFirs(): array
    {
        $result = Regex::match(
            '/EET\/('.Patterns::icaoAirport().Patterns::duration().'(?: '.Patterns::icaoAirport().Patterns::duration().')*)'.Patterns::field18Eol().'/',
            $this->field18,
        )->groupOr(1, '');

        return Str::of($result)
            ->explode(' ')
            ->filter()
            ->map(fn (string $firString) => Regex::match(
                '/^('.Patterns::icaoAirport().')'.Patterns::duration().'$/',
                $firString,
            )->group(1))
            ->values()
            ->all();
    }
}
