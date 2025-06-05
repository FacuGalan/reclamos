<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    //
    protected $fillable = ['nombre','secretaria_id'];
    
    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class);
    }
}
