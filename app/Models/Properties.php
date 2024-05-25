<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;

    const  PROPERTY_FOR = [
        'Sale' => 1,
        'Rent' => 2,
        'Lease' => 3,
    ];

    const PROPERTY_TYPE = [
        'Agricultural Land' => 1,
        'Builder Floor Apartment' => 2,
        'Commercial Land'   => 3,
        'Commercial Office Space'   => 4,
        'Commercial Shop'  => 5,
        'Commercial Showroom' => 6,
        'Farm House'    => 7,
        'Industrial Building' => 8,
        'Industrial Land' => 9,
        'Industrial Shed' => 10,
        'Multistorey Apartment' => 11,
        'Penthouse' => 12,
        'Residential House' => 13,
        'Residential Plot' => 14,
        'Service Apartment'  => 15,
        'Studio Apartment' => 16,
        'Villa' => 17,
        'Warehouse/ Godown' => 18,
        'CO-WORKING SPACE' => 19,
    ];

    const TRANSACTION_TYPE = [
        'New Property'  => 1,
        'Resale'   => 2,
        'Under Construction' => 3,
        'Ready to Move' => 4,
        'Preleased Property'    => 5,
        'Foreclosure'  => 6,
        'Others'  => 7,
    ];

    const POSSESSION_STATUS = [
        'Under Construction' => 1,
        'Ready to Move' => 2,
        ]
    ;

    const TYPE_OF_COWORKING_SPACE = [
        'Private Office' => 1,
        'Dedicated Desk'  => 2  ,
        'Hot Desk' => 3,

        'Meeting Room' => 4,
        'Conference Room' => 5,
        'Training Room' => 6,
        'Event Space' => 7,
        'Virtual Office' => 8,
        'Coworking Cafe' => 9,
        'Others' => 10,
    ];

    const CA_UNIT = [
        'Sq-ft' => 1,
        'Sq-yrd' => 2,
        'Sq-m' => 3,
        'Acres' => 4,
        'Hectares' => 5,
    ];

    const PA_UNIT = [
        'Sq-ft' => 1,
        'Sq-yrd' => 2,
        'Sq-m' => 3,
        'Acres' => 4,
        'Hectares' => 5,
    ];

    const Furnished = [
        'Furnished' => 1,
        'Semi-Furnished' => 2,
        'Unfurnished' => 3,
    ];

    const Amenities = [
        'CARPARKING',
        'CARPARKING NUMBER',
        'LIFT',
        'POWEBACKUP',
        'SECURITY',
        'GYM',
        'SWIMMING POLL',
        'COMMUNITY HALL',
        'GARDEN',
        'WIFI',
        'FOOD',
        'AC',
        'PANTRY',
        'MEETING ROOM',
        'CONFRENS ROOM',
    ];

    protected $fillable = ['*'];

}
