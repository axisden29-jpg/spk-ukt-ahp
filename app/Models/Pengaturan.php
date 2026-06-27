<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    public $timestamps = false;

    protected $fillable = ['kunci', 'nilai', 'keterangan'];

    const UPDATED_AT = 'updated_at';

    public static function get(string $kunci, $default = null)
    {
        return Cache::rememberForever("pengaturan_{$kunci}", function () use ($kunci, $default) {
            $row = static::where('kunci', $kunci)->first();
            return $row ? $row->nilai : $default;
        });
    }

    public static function set(string $kunci, $nilai, string $keterangan = null): void
    {
        static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai, 'keterangan' => $keterangan, 'updated_at' => now()]
        );
        Cache::forget("pengaturan_{$kunci}");
    }

    public static function nominalUkt(int $golongan): array
    {
        $data = json_decode(static::get('nominal_ukt', '{}'), true);
        return $data[$golongan] ?? ['label' => 'UKT ' . $golongan, 'nominal' => 0];
    }

    public static function formatRupiah(int $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
}