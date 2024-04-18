<?php

namespace App\Models;

use App\Enum\Fns\NotamClassification;
use App\Enum\Fns\NotamMessageType;
use App\Enum\Fns\NotamStatus;
use App\Exceptions\MissingFnsNotamMessageType;
use App\Exceptions\UnknownFnsNotamMessageType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use SimpleXMLElement;
use Spatie\Regex\Regex;

class FnsNotam extends Model
{
    protected $connection = 'aim_fns';

    protected $table = 'notams';

    protected $primaryKey = 'fnsid';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'correlationid'      => 'integer',
            'issuedtimestamp'    => 'datetime:Y-m-d H:i:s',
            'storedtimestamp'    => 'datetime:Y-m-d H:i:s.v',
            'updatedtimestamp'   => 'datetime:Y-m-d H:i:s',
            'validfromtimestamp' => 'datetime:Y-m-d H:i:s',
            'validtotimestamp'   => 'datetime:Y-m-d H:i:s',
            'classification'     => NotamClassification::class,
            'status'             => NotamStatus::class,
        ];
    }

    protected $appends = [
        'message',
        'message_type',
    ];

    protected function aixmnotammessage(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): SimpleXMLElement => tap(new SimpleXMLElement($value))
                ->registerXPathNamespace('e', 'http://www.aixm.aero/schema/5.1/event'),
        );
    }

    protected function message(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->getRawNotamMessage(),
        );
    }

    protected function messageType(): Attribute
    {
        return Attribute::make(
            get: function (): NotamMessageType {
                $availableTypes = array_map(
                    'strval',
                    $this->aixmnotammessage->xpath('//e:NOTAMTranslation/e:type'),
                );

                if (count($availableTypes) === 0) {
                    throw new MissingFnsNotamMessageType;
                }

                // Prefer ICAO message type when multiple types are available.
                if (in_array('OTHER:ICAO', $availableTypes, true)) {
                    return NotamMessageType::ICAO;
                }

                if (in_array('LOCAL_FORMAT', $availableTypes, true)) {
                    return NotamMessageType::Domestic;
                }

                throw new UnknownFnsNotamMessageType($availableTypes[0]);
            },
        );
    }

    protected function number(): Attribute
    {
        return Attribute::make(
            get: fn (): string => match ($this->message_type) {
                NotamMessageType::Domestic => Regex::match(
                    '/^![A-Z0-9]{3} (\d{1,2}\/\d{3,4})/',
                    $this->message,
                )->group(1),
                NotamMessageType::ICAO => Regex::match(
                    '/^([A-Z]{1,2}\d{3,4}\/\d{2}) NOTAM[NRC]/',
                    $this->message,
                )->group(1),
                // TODO: Handle domestic NOTAM numbering with ICAO format messages (e.g. fnsid 58994930)
            },
        );
    }

    protected function getRawNotamMessage(): string
    {
        if ($this->message_type === NotamMessageType::Domestic) {
            return trim(
                $this->aixmnotammessage->xpath('//e:NOTAMTranslation/e:simpleText')[0]
            );
        }

        // We have to use SimpleXMLElement::asXML() here because the provided XML isn’t always valid.
        return trim(strip_tags(html_entity_decode(
            $this->aixmnotammessage->xpath('//e:NOTAMTranslation/e:formattedText')[0]->asXML()
        )));
    }
}
