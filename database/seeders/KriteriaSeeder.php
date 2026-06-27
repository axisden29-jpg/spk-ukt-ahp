<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Kriteria::truncate();
\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kriterias = [
            [
                'nama'      => 'Penghasilan Orang Tua/Wali',
                'kode'      => 'penghasilan_ortu',
                'deskripsi' => 'Total penghasilan bulanan orang tua atau wali mahasiswa',
                'urutan'    => 1,
                'keterangan_skala' => [
                    1 => '< Rp500.000 / bulan',
                    2 => 'Rp500.000 – Rp1.500.000 / bulan',
                    3 => 'Rp1.500.000 – Rp3.000.000 / bulan',
                    4 => 'Rp3.000.000 – Rp5.000.000 / bulan',
                    5 => '> Rp5.000.000 / bulan',
                ],
            ],
            [
                'nama'      => 'Pekerjaan Orang Tua/Wali',
                'kode'      => 'pekerjaan_ortu',
                'deskripsi' => 'Jenis dan stabilitas pekerjaan orang tua atau wali',
                'urutan'    => 2,
                'keterangan_skala' => [
                    1 => 'Tidak bekerja / buruh harian lepas',
                    2 => 'Petani / nelayan / pedagang kaki lima',
                    3 => 'Wiraswasta kecil / karyawan swasta',
                    4 => 'PNS / TNI / Polri / BUMN golongan rendah',
                    5 => 'PNS senior / profesional / pengusaha / direktur',
                ],
            ],
            [
                'nama'      => 'Jumlah Tanggungan Keluarga',
                'kode'      => 'tanggungan',
                'deskripsi' => 'Jumlah anggota keluarga yang menjadi tanggungan',
                'urutan'    => 3,
                'keterangan_skala' => [
                    1 => '> 6 orang tanggungan',
                    2 => '5 – 6 orang tanggungan',
                    3 => '3 – 4 orang tanggungan',
                    4 => '2 orang tanggungan',
                    5 => '1 orang tanggungan',
                ],
            ],
            [
                'nama'      => 'Kondisi Tempat Tinggal',
                'kode'      => 'tempat_tinggal',
                'deskripsi' => 'Status kepemilikan, kondisi bangunan, dan fasilitas rumah',
                'urutan'    => 4,
                'keterangan_skala' => [
                    1 => 'Menumpang / tidak layak huni / gubuk',
                    2 => 'Sewa / kontrakan bangunan sederhana',
                    3 => 'Rumah sendiri sederhana (semi permanen)',
                    4 => 'Rumah sendiri layak huni (permanen)',
                    5 => 'Rumah sendiri mewah / lebih dari satu rumah',
                ],
            ],
            [
                'nama'      => 'Kepemilikan Aset/Kekayaan',
                'kode'      => 'aset_kekayaan',
                'deskripsi' => 'Kepemilikan tanah, kendaraan, atau usaha keluarga',
                'urutan'    => 5,
                'keterangan_skala' => [
                    1 => 'Tidak memiliki aset apapun',
                    2 => 'Memiliki 1 aset kecil (sepeda / perabot rumah)',
                    3 => 'Memiliki kendaraan roda dua / tanah kecil',
                    4 => 'Memiliki rumah + kendaraan roda dua/empat',
                    5 => 'Memiliki banyak aset / properti / usaha produktif',
                ],
            ],
            [
                'nama'      => 'Biaya Listrik & Utilitas',
                'kode'      => 'biaya_listrik',
                'deskripsi' => 'Rata-rata tagihan listrik dan utilitas per bulan',
                'urutan'    => 6,
                'keterangan_skala' => [
                    1 => '< Rp100.000 / bulan',
                    2 => 'Rp100.000 – Rp300.000 / bulan',
                    3 => 'Rp300.000 – Rp600.000 / bulan',
                    4 => 'Rp600.000 – Rp1.000.000 / bulan',
                    5 => '> Rp1.000.000 / bulan',
                ],
            ],
            [
                'nama'      => 'Data Pendukung Lainnya',
                'kode'      => 'data_pendukung',
                'deskripsi' => 'Dokumen pendukung seperti slip gaji, kartu keluarga, dll',
                'urutan'    => 7,
                'keterangan_skala' => [
                    1 => 'Tidak ada dokumen pendukung sama sekali',
                    2 => 'Dokumen ada tapi tidak lengkap / tidak valid',
                    3 => 'Dokumen cukup lengkap (KK + 1 dokumen lain)',
                    4 => 'Dokumen lengkap (KK + slip gaji + surat keterangan)',
                    5 => 'Dokumen lengkap, terverifikasi, dan sangat mendukung',
                ],
            ],
        ];

        foreach ($kriterias as $k) {
            Kriteria::create(array_merge($k, [
                'skala_min' => 1,
                'skala_max' => 5,
                'is_aktif'  => true,
            ]));
        }
    }
}