<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $fillable = [
        'nb_id',
        'last_name',
        'first_name',
        'formation',
        'county_short',
        'status',
        'org',
        'home_address',
        'active_address',
        'phone',
        'email',
        'social_media',
        'serie_ci',
        'nr_ci',
        'cnp',
        'gender',
        'birthdate',
        'profession',
        'studies',
        'political_experience',
        'areas_of_interest',
        'citizenship',
        'started_on',
        'member_fee_paid_until',
        'r_community',
        'r_subsidiary',
        'r_genplus',
        'r_region',
        'last_document_nr',
        'date_last_document_nr',
        'sanctions',
        'signature'
    ];
}
