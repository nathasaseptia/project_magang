<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis',
        'kode',
        'parent_up3',
    ];

    public function ulps()
    {
        return $this->hasMany(Unit::class, 'parent_up3', 'nama')->where('jenis', 'ulp');
    }
}
