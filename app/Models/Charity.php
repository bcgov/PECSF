<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charity extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'charity_name',
        'charity_status',
        'type_od_qualified_donee',
        'effective_date_of_status',
        'sanction',
        'designation_code',
        'charity_type',
        'category_code',
        'address',
        'city',
        'province',
        'country',
        'postal_code',
        'ongoing_program',
        'url',

        'use_alt_address', 'alt_address1', 'alt_address2', 'alt_city', 'alt_province', 'alt_country',
        'alt_postal_code',
        'financial_contact_name', 'financial_contact_title', 'financial_contact_email',
        'comments',
        'created_by_id', 'updated_by_id',
        
    ];

    protected $casts = [
        'effective_date_of_status' => 'date'
    ];

    protected $appends = [
        'designation_name',  
        'category_name',

    ];


    public const DESIGNATION_LIST = 
    [
        "0001" => "Charitable organization",
        "0003" => "Private foundation",
        "0002" => "Public foundation",
    ];

    public const PROVINCE_LIST = [
        "AB" => "ALBERTA",
        "BC" => "BRITISH COLUMBIA",
        "MB" => "MANITOBA",
        "NB" => "NEW BRUNSWICK",
        "NL" => "NEWFOUNDLAND AND LABRADOR",
        "NT" => "NORTHWEST TERRITORIES",
        "NS" => "NOVA SCOTIA",
        "NU" => "NUNAVUT",
        "ON" => "ONTARIO",
        "OC" => "Outside of Canada",
        "PE" => "PEI",
        "QC" => "QUEBEC",
        "SK" => "SASKATCHEWAN",
        "YT" => "YUKON",
    ];

    public const CATEGORY_LIST = [
        "0175"	=>	"Agriculture",
        "0180"	=>	"Animal Welfare",
        "0190"	=>	"Arts",
        "0214"	=>	"CAAA",
        "0030"	=>	"Christianity",
        "0160"	=>	"Community Resource",
        "0140"	=>	"Complementary or Alternative Health Care",
        "0100"	=>	"Core Health Care",
        "0080"	=>	"Ecumenical and Inter-faith Organizations",
        "0012"	=>	"Education in the arts",
        "0013"	=>	"Educational organizations not elsewhere categorized",
        "0170"	=>	"Environment",
        "0015"	=>	"Foundations Advancing Education",
        "0090"	=>	"Foundations Advancing Religions",
        "0002"	=>	"Foundations Relieving Poverty",
        "0210"	=>	"Foundations",
        "0130"	=>	"Health Care Products",
        "0040"	=>	"Islam",
        "0050"	=>	"Judaism",
        "0215"	=>	"NASO",
        "0001"	=>	"Organizations Relieving Poverty",
        "0060"	=>	"Other Religions",
        "0120"	=>	"Protective Health Care",
        "0200"	=>	"Public Amenities",
        "0150"	=>	"Relief of the Aged",
        "0014"	=>	"Research",
        "0070"	=>	"Support of Religion",
        "0011"	=>	"Support of schools and education",
        "0110"	=>	"Supportive Health Care",
        "0010"	=>	"Teaching Institutions",
        "0155"	=>	"Upholding Human Rights",
    ];

    public function pledges() {
        return $this->belongsToMany('App\Models\Pledge', 'pledge_charities', 'charity_id', 'pledge_id')
        ->using('App\Models\PledgeCharity')->withTimestamps();
    }


    public function capitalized_name() {

        return preg_replace_callback('/\s(\w+)|(\w+)\s/', function ($word) { return ucwords(strtolower($word[0])) ; }, $this->charity_name);
        
    }
 
    public function getDesignationNameAttribute()
    {
        //return $this->designation_name();
        return array_key_exists($this->designation_code, self::DESIGNATION_LIST) ? self::DESIGNATION_LIST[$this->designation_code] : '';
    }

    public function getCategoryNameAttribute()
    {
        // return $this->category_name();
        return array_key_exists($this->category_code, self::CATEGORY_LIST) ? self::CATEGORY_LIST[$this->category_code] : '';
    }


    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id')->withDefault();
    }
   
}
