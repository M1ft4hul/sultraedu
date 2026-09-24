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
