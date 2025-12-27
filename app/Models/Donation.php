<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $casts = ["datas" => "array"];

    public function orphanage(){
        return $this->belongsTo(Orphanage::class);
    }
}
