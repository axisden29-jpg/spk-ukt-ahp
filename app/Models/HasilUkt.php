<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilUkt extends Model
{
    protected $fillable = ['mahasiswa_id', 'periode', 'skor_total', 'peringkat', 'golongan_ukt', 'metode_pembagian'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function getLabelGolonganAttribute(): string
    {
        return match ($this->golongan_ukt) {
            1 => 'UKT 1 - Sangat Tidak Mampu',
            2 => 'UKT 2 - Tidak Mampu',
            3 => 'UKT 3 - Menengah ke Bawah',
            4 => 'UKT 4 - Menengah',
            5 => 'UKT 5 - Mampu',
            default => 'Tidak Diketahui',
        };
    }
}