<?php

/*
 * File ini bagian dari:
 *
 * OpenDK
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2017 - 2023 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package    OpenDK
 * @author     Tim Pengembang OpenDesa
 * @copyright  Hak Cipta 2017 - 2023 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license    http://www.gnu.org/licenses/gpl.html    GPL V3
 * @link       https://github.com/OpenSID/opendk
 */

namespace App\Imports;

use App\Models\DataDesa;
use App\Models\Penduduk;
use App\Models\TingkatPendidikan;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SinkronPenduduk implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{
    use Importable;

    /**
     * {@inheritdoc}
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * {@inheritdoc}
     */
    public function collection(Collection $collection)
    {
        $kode_desa = Arr::flatten(DataDesa::pluck('desa_id'));

        foreach ($collection as $value) {
            if (! in_array($value['desa_id'], $kode_desa)) {
                Log::debug('Desa tidak terdaftar');

                DB::rollBack(); // rollback data yang sudah masuk karena ada data yang bermasalah
                Storage::deleteDirectory('temp'); // Hapus folder temp ketika gagal
                throw  new Exception('kode Desa tidak terdaftar . kode desa yang bermasalah : '. $value['desa_id']);
            }

            $insert = [
                'nik'                   => $value['nomor_nik'],
                'nama'                  => $value['nama'],
                'no_kk'                 => $value['nomor_kk'],
                'sex'                   => $value['jenis_kelamin'],
                'tempat_lahir'          => $value['tempat_lahir'],
                'tanggal_lahir'         => $value['tanggal_lahir'],
                'agama_id'              => $value['agama'],
                'pendidikan_kk_id'      => $value['pendidikan_dlm_kk'],
                'pendidikan_sedang_id'  => $value['pendidikan_sdg_ditempuh'],
                'pekerjaan_id'          => $value['pekerjaan'],
                'status_kawin'          => $value['kawin'],
                'kk_level'              => $value['hubungan_keluarga'],
                'warga_negara_id'       => $value['kewarganegaraan'],
                'nama_ibu'              => $value['nama_ibu'],
                'nama_ayah'             => $value['nama_ayah'],
                'golongan_darah_id'     => $value['gol_darah'],
                'akta_lahir'            => $value['akta_lahir'],
                'dokumen_pasport'       => $value['nomor_dokumen_pasport'],
                'tanggal_akhir_pasport' => $value['tanggal_akhir_pasport'],
                'dokumen_kitas'         => $value['nomor_dokumen_kitas'],
                'ayah_nik'              => $value['nik_ayah'],
                'ibu_nik'               => $value['nik_ibu'],
                'akta_perkawinan'       => $value['nomor_akta_perkawinan'],
                'tanggal_perkawinan'    => $value['tanggal_perkawinan'],
                'akta_perceraian'       => $value['nomor_akta_perceraian'],
                'tanggal_perceraian'    => $value['tanggal_perceraian'],
                'cacat_id'              => $value['cacat'],
                'cara_kb_id'            => $value['cara_kb'],
                'hamil'                 => $value['hamil'],

                // Tambahan
                'foto'            => $value['foto'],
                'alamat_sekarang' => $value['alamat_sekarang'],
                'alamat'          => $value['alamat'],
                'dusun'           => $value['dusun'],
                'rw'              => $value['rw'],
                'rt'              => $value['rt'],
                'desa_id'         => $value['desa_id'],
                'id_pend_desa'    => $value['id'],
                'status_dasar'    => $value['status_dasar'],
                'status_rekam'    => $value['status_rekam'],
                'created_at'      => $value['created_at'],
                'updated_at'      => $value['updated_at'],
                'imported_at'     => now(),
            ];

            Penduduk::updateOrInsert([
                'desa_id' => $insert['desa_id'],
                'nik'     => $insert['nik']
            ], $insert);
        }

        // update rekap tingkat pendidikan
        $dt = \Carbon\Carbon::now();
        TingkatPendidikan::updateOrCreate(
            [
                'desa_id' => $insert['desa_id'],
                'semester' => ($dt->format('n') <= 6) ? 1 : 2,
                'tahun' => $dt->format('Y'),
            ],
            [
                'desa_id' => $insert['desa_id'],
                'semester' => ($dt->format('n') <= 6) ? 1 : 2,
                'tahun' => $dt->format('Y'),
                'tidak_tamat_sekolah'=> $collection->filter(fn ($value, $key) => $value['pendidikan_dlm_kk'] <= 2)->count(),
                'tamat_sd'=> $collection->filter(fn ($value, $key) => $value['pendidikan_dlm_kk'] == 3)->count(),
                'tamat_smp'=> $collection->filter(fn ($value, $key) => $value['pendidikan_dlm_kk'] == 4)->count(),
                'tamat_sma'=> $collection->filter(fn ($value, $key) => $value['pendidikan_dlm_kk'] == 5)->count(),
                'tamat_diploma_sederajat'=> $collection->filter(fn ($value, $key) => $value['pendidikan_dlm_kk'] >= 6)->count(),
            ]
        );
    }
}
