<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    public $timestamps = false;

    protected $fillable = ['id_send_currency', 'id_recive_currency', 'id_exchange_office', 'rate_send', 'rate_recive','created_at'];




}
