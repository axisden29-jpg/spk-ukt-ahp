<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $fillable = ['nim', 'nama', 'program_studi', 'angkatan'];

    public function nilaiMahasiswas()
    {
        return $this->hasMany(NilaiMahasiswa::class);
    }

    public function hasilUkts()
    {
        return $this->hasMany(HasilUkt::class);
    }

    public function nilaiPeriode($periode)
    {
        return $this->nilaiMahasiswas()->where('periode', $periode)->with('kriteria')->get();
    }
    public function user()
    {
        return $this->hasOne(\App\Models\User::class);
    }

    protected static function booted()
    {
        static::created(function ($mahasiswa) {
            \App\Models\User::firstOrCreate(
                ['email' => $mahasiswa->nim],
                [
                    'name'         => $mahasiswa->nama,
                    'password'     => $mahasiswa->nim, // Automagically hashed by User model
                    'role'         => 'mahasiswa',
                    'mahasiswa_id' => $mahasiswa->id,
                ]
            );
        });

        static::updated(function ($mahasiswa) {
            if ($mahasiswa->isDirty('nim') || $mahasiswa->isDirty('nama')) {
                $user = \App\Models\User::where('mahasiswa_id', $mahasiswa->id)->first();
                if ($user) {
                    $user->update([
                        'name'  => $mahasiswa->nama,
                        'email' => $mahasiswa->nim,
                    ]);
                }
            }
        });

        static::deleted(function ($mahasiswa) {
            \App\Models\User::where('mahasiswa_id', $mahasiswa->id)->delete();
        });
    }
}