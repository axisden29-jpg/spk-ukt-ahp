<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatriksAhp extends Model
{
    protected $fillable = ['kriteria_baris_id', 'kriteria_kolom_id', 'nilai', 'periode'];

    public function kriteriaBaris()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_baris_id');
    }

    public function kriteriaKolom()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_kolom_id');
    }
}