<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'nama_admin' => 'Admin Pusat EDUVATION',
            'username'   => 'adminpusat',
            'password'   => password_hash('admin12345', PASSWORD_DEFAULT),
            'role'       => 'admin_pusat',
            'id_sekolah' => null, // admin pusat tidak terikat ke sekolah
            'email'      => 'admin@eduvation.sultraprov.go.id',
            'status'     => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $sudahAda = $this->db->table('admin')
            ->where('username', $data['username'])
            ->countAllResults();

        if ($sudahAda === 0) {
            $this->db->table('admin')->insert($data);
        }
    }
}
