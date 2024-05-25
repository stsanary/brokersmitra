<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Properties extends Model
{
    use HasFactory;

    const PROPERTY_FOR = [
        'sale' => 1,
        'rent' => 2,
        'lease' => 3,
    ];

    const PROPERTY_TYPE = [
        'agricultural land' => 1,
        'builder floor apartment' => 2,
        'commercial land' => 3,
        'commercial office space' => 4,
        'commercial shop' => 5,
        'commercial showroom' => 6,
        'farm house' => 7,
        'industrial building' => 8,
        'industrial land' => 9,
        'industrial shed' => 10,
        'multistorey apartment' => 11,
        'penthouse' => 12,
        'residential house' => 13,
        'residential plot' => 14,
        'service apartment' => 15,
        'studio apartment' => 16,
        'villa' => 17,
        'warehouse/ godown' => 18,
        'co-working space' => 19,
    ];

    const TRANSACTION_TYPE = [
        'new property' => 1,
        'resale' => 2,
        'under construction' => 3,
        'ready to move' => 4,
        'preleased property' => 5,
        'foreclosure' => 6,
        'others' => 7,
    ];

    const POSSESSION_STATUS = [
        'under construction' => 1,
        'ready to move' => 2,
    ];

    const TYPE_OF_COWORKING_SPACE = [
        'private office' => 1,
        'dedicated desk' => 2,
        'hot desk' => 3,
        'meeting room' => 4,
        'conference room' => 5,
        'training room' => 6,
        'event space' => 7,
        'virtual office' => 8,
        'coworking cafe' => 9,
        'others' => 10,
    ];

    const CA_UNIT = [
        'sq-ft' => 1,
        'sq-yrd' => 2,
        'sq-m' => 3,
        'acres' => 4,
        'hectares' => 5,
    ];

    const PA_UNIT = [
        'sq-ft' => 1,
        'sq-yrd' => 2,
        'sq-m' => 3,
        'acres' => 4,
        'hectares' => 5,
    ];

    const FURNISHED = [
        'furnished' => 1,
        'semi-furnished' => 2,
        'unfurnished' => 3,
    ];

    const AMENITIES = [
        'carparking' => 1,
        'carparking number' => 2,
        'lift' => 3,
        'powerbackup' => 4,
        'security' => 5,
        'gym' => 6,
        'swimming pool' => 7,
        'community hall' => 8,
        'garden' => 9,
        'wifi' => 10,
        'food' => 11,
        'ac' => 12,
        'pantry' => 13,
        'meeting room' => 14,
        'conference room' => 15,
    ];


    protected $fillable = ['*'];

}
