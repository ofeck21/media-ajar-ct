<?php

namespace Database\Seeders;

use App\Models\DiagnosticQuestion;
use Illuminate\Database\Seeder;

class DiagnosticQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus soal lama jika ada agar tidak menumpuk saat re-seed
        DiagnosticQuestion::truncate();

        $questions = [
            [
                'question' => 'Budi ingin membuat mi instan. Urutan langkah yang paling benar dan logis adalah...',
                'options' => [
                    'Rebus air -> Masukkan mi -> Campur bumbu -> Sajikan',
                    'Masukkan mi -> Tiriskan -> Rebus air -> Sajikan',
                    'Sajikan -> Rebus air -> Masukkan mi -> Campur bumbu',
                    'Campur bumbu -> Tiriskan -> Rebus air -> Masukkan mi'
                ],
                'correct_answer_index' => 0,
            ],
            [
                'question' => 'Kelompokmu mendapat tugas membuat mading sekolah. Langkah awal memecah masalah (Dekomposisi) yang tepat adalah...',
                'options' => [
                    'Membagi tugas: ada yang cari berita, membuat hiasan, dan menulis artikel',
                    'Membeli bahan mading tanpa rencana apa yang mau dibuat',
                    'Langsung menempelkan semua kertas tanpa pembagian tugas',
                    'Menunggu sampai hari pengumpulan baru dikerjakan bersama'
                ],
                'correct_answer_index' => 0,
            ],
            [
                'question' => 'Perhatikan pola bilangan berikut: 3, 6, 12, 24, ... Angka berikutnya yang tepat adalah?',
                'options' => ['30', '36', '48', '60'],
                'correct_answer_index' => 2,
            ],
            [
                'question' => 'Saat kamu membuat peta rute perjalanan dari rumah ke sekolah, hal yang PENTING untuk dicantumkan adalah...',
                'options' => [
                    'Warna pagar rumah tetangga di sepanjang jalan',
                    'Nama jalan utama dan bangunan patokan (seperti masjid/toko)',
                    'Jumlah kucing yang sering lewat di jalan',
                    'Merk sepatu yang kamu pakai saat berangkat'
                ],
                'correct_answer_index' => 1,
            ],
            [
                'question' => 'Ani, Budi, dan Cici sedang mengantre di kantin. Budi ada di depan Ani. Cici ada di belakang Ani. Siapakah yang antre paling depan?',
                'options' => ['Ani', 'Budi', 'Cici', 'Semuanya bersamaan'],
                'correct_answer_index' => 1,
            ],
        ];

        foreach ($questions as $q) {
            DiagnosticQuestion::create($q);
        }
    }
}
