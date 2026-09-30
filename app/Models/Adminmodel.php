<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table         = 'admin';
    protected $primaryKey    = 'id_admin';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nama_admin',
        'username',
        'password',
        'role',
        'id_sekolah',
        'email',
        'keterangan',
        'status',
    ];

    // created_at & updated_at diisi otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Password otomatis di-hash saat tambah/ubah akun lewat model
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (! empty($data['data']['password'])) {
            $password = $data['data']['password'];

            // Hindari hash ganda kalau password sudah dalam bentuk hash
            if (password_get_info($password)['algo'] === null) {
                $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        return $data;
    }

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
