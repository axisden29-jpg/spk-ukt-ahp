<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiMahasiswa extends Model
{
    protected $fillable = ['mahasiswa_id', 'kriteria_id', 'nilai', 'periode'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}