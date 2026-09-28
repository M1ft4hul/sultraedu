<?php

namespace App\Models;

use CodeIgniter\Model;

class SekolahModel extends Model
{
    protected $table         = 'sekolah';
    protected $primaryKey    = 'id_sekolah';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'npsn',
        'nama_sekolah',
        'jenjang',
        'alamat',
        'kecamatan',
        'kabupaten_kota',
        'telepon',
        'email',
        'status',
    ];

    // created_at & updated_at diisi otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Aturan validasi (dipakai otomatis saat save/insert/update)
    protected $validationRules = [
        'id_sekolah'     => 'permit_empty|is_natural_no_zero',
        'npsn'           => 'required|exact_length[8]|alpha_numeric|is_unique[sekolah.npsn,id_sekolah,{id_sekolah}]',
        'nama_sekolah'   => 'required|max_length[150]',
        'jenjang'        => 'required|in_list[SMA,SMK,SLB]',
        'alamat'         => 'permit_empty',
        'kecamatan'      => 'permit_empty|max_length[100]',
        'kabupaten_kota' => 'required|max_length[100]',
        'telepon'        => 'permit_empty|max_length[20]',
        'email'          => 'permit_empty|valid_email|max_length[100]',
        'status'         => 'required|in_list[aktif,nonaktif]',
    ];

    protected $validationMessages = [
        'npsn' => [
            'required'      => 'NPSN wajib diisi.',
            'exact_length'  => 'NPSN harus terdiri dari 8 karakter.',
            'alpha_numeric' => 'NPSN hanya boleh berisi huruf dan angka.',
            'is_unique'     => 'NPSN ini sudah terdaftar untuk sekolah lain.',
        ],
        'nama_sekolah' => [
            'required' => 'Nama sekolah wajib diisi.',
        ],
        'jenjang' => [
            'required' => 'Jenjang wajib dipilih.',
            'in_list'  => 'Jenjang harus SMA, SMK, atau SLB.',
        ],
        'kabupaten_kota' => [
            'required' => 'Kabupaten/Kota wajib dipilih.',
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid.',
        ],
    ];
}
