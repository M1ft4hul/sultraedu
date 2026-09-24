<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table         = 'guru';
    protected $primaryKey    = 'id_guru';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nip',
        'nama_guru',
        'username', 
        'password',
        'id_sekolah',
        'mapel',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (! empty($data['data']['password'])) {
            $password = $data['data']['password'];

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