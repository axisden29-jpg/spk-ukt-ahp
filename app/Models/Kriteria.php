<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $fillable = [
    'nama', 'kode', 'deskripsi', 'skala_min', 'skala_max',
    'keterangan_skala', 'urutan', 'is_aktif'
];

protected $casts = [
    'is_aktif'         => 'boolean',
    'keterangan_skala' => 'array',
];

    public function nilaiMahasiswas()
    {
        return $this->hasMany(NilaiMahasiswa::class);
    }

    public function matriksBaris()
    {
        return $this->hasMany(MatriksAhp::class, 'kriteria_baris_id');
    }

    public function matriksKolom()
    {
        return $this->hasMany(MatriksAhp::class, 'kriteria_kolom_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->orderBy('urutan');
    }
}