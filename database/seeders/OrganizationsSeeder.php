<?php
namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationsSeeder extends Seeder
{
    public function run(): void
    {
        $orgs = [
            // slug                    => [name, type, scope, short]
            'ukm-pemrograman-unicode'  => ['UKM Pemrograman UNICODE', 'ukm', 'minat', 'UNICODE'],
            'bem-universitas'          => ['BEM Universitas Bhakti Asih', 'bem', 'universitas', 'BEM'],
            'hima-informatika'         => ['HIMA Informatika', 'hima', 'prodi', 'HIMA IF'],
            'hima-keperawatan'         => ['HIMA Keperawatan', 'hima', 'prodi', 'HIMA Kep'],
            'hima-kebidanan'           => ['HIMA Kebidanan', 'hima', 'prodi', 'HIMA Keb'],
            'hima-manajemen'           => ['HIMA Manajemen', 'hima', 'prodi', 'HIMA MJ'],
            'hima-hukum'               => ['HIMA Hukum', 'hima', 'prodi', 'HIMA HK'],
            'hima-ars'                 => ['HIMA ARS', 'hima', 'prodi', 'HIMA ARS'],
            'ukm-multimedia'           => ['UKM Multimedia', 'ukm', 'minat', 'MM'],
            'ukm-nova'                 => ['UKM NOVA', 'ukm', 'minat', 'NOVA'],
            'ukm-badminton'            => ['UKM Badminton', 'ukm', 'minat', 'BDM'],
            'ukm-basket'               => ['UKM Basket', 'ukm', 'minat', 'BSK'],
            'ukm-teater-teras'         => ['UKM Teater Teras', 'ukm', 'minat', 'TT'],
            'ukm-aksara-tarana'        => ['UKM Aksara Tarana', 'ukm', 'minat', 'AT'],
            'ukm-savora-padus'         => ['UKM Savora Padus', 'ukm', 'minat', 'SP'],
            'ukm-futsal'               => ['UKM Futsal', 'ukm', 'minat', 'FTS'],
        ];

        foreach ($orgs as $slug => [$name, $type, $scope, $short]) {
            Organization::updateOrCreate(
                ['slug' => $slug],
                [
                    'uuid'       => (string) Str::uuid(),
                    'name'       => $name,
                    'short_name' => $short,
                    'type'       => $type,
                    'scope_type' => $scope,
                    'status'     => 'active',
                    'settings'   => json_encode([
                        'modules' => $slug === 'ukm-pemrograman-unicode'
                            ? ['attendance' => true, 'finance' => true, 'lms' => true, 'game' => true, 'qr' => true]
                            : ['attendance' => false, 'finance' => false, 'lms' => false, 'game' => false, 'qr' => false],
                        'branding' => ['primary_color' => $slug === 'ukm-pemrograman-unicode' ? '#7c3aed' : '#6366f1'],
                    ]),
                ]
            );
        }
    }
}