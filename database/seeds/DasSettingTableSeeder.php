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

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DasSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('das_setting')->truncate();

        DB::table('das_setting')->insert([
            0 => [
                'id' => 1,
                'key' => 'judul_aplikasi',
                'value' => 'Kecamatan',
                'type' => 'input',
                'description' => 'Judul halaman aplikasi.',
                'kategori' => 'sistem',
                'option' => '{}',
            ],
            1 => [
                'id' => 2,
                'key' => 'artikel_kecamatan_perhalaman',
                'value' => '10',
                'type' => 'number',
                'description' => 'Jumlah artikel kecamatan dalam satu halaman yang ditampilkan',
                'kategori' => 'web',
                'option' => '{}',
            ],
            2 => [
                'id' => 3,
                'key' => 'artikel_desa_perhalaman',
                'value' => '10',
                'type' => 'number',
                'description' => 'Jumlah artikel desa dalam satu halaman yang ditampilkan',
                'kategori' => 'web',
                'option' => '{}',
            ],
            3 => [
                'id' => 4,
                'key' => 'jumlah_artikel_desa',
                'value' => '150',
                'type' => 'number',
                'description' => 'Jumlah semua artikel desa yang ditampilkan',
                'kategori' => 'web',
                'option' => '{}',
            ],
        ]);
    }
}
