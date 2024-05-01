<?php

namespace App;

class Patterns
{
    public static function callsign(): string
    {
        return '[A-Z]{3}[A-Z0-9]{1,4}';
    }

    public static function flightRules(): string
    {
        return '[IVYZ]';
    }

    public static function flightType(): string
    {
        return '[SNGMX]';
    }

    public static function registration(): string
    {
        return '(?:[A-Z]-[A-Z]{4}|[A-Z]{2}-[A-Z]{3}|N[0-9]{1,5}[A-Z]{0,2})';
    }

    public static function registrationWithoutHyphens(): string
    {
        return '[A-Z0-9]{2,6}';
    }

    public static function aircraftType(): string
    {
        return '[A-Z]{1}[A-Z0-9]{1,3}';
    }

    public static function wakeTurbulenceCategory(): string
    {
        return '[LMHJ]';
    }

    public static function comNavEquipment(): string
    {
        return '(?:[ABCDFGHIKLNORSTUVWXYZ]|[EM][1-3]|J[1-7]|P[1-9])+';
    }

    public static function surEquipment(): string
    {
        return '(?:[ACEHILNPSX]|[BUV][1-2]|[DG]1)+';
    }

    public static function icaoAirport(): string
    {
        return '[A-Z]{4}';
    }

    public static function ssrCode()
    {
        return '[0-7]{4}';
    }

    public static function hhmm(): string
    {
        return '(?:[01][0-9]|2[0-4])[0-5][0-9]';
    }

    public static function duration(): string
    {
        return '[0-9]{2}[0-5][0-9]';
    }

    public static function yymmdd(): string
    {
        return '[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12][0-9]|3[01])';
    }

    public static function field18Eol()
    {
        return '(?:$| [A-Z]+\/)';
    }
}
