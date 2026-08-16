<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el usuario normal para asignarle inventarios
        $usuario = User::where('username', 'cmartinez')->first();

        if (!$usuario) {
            $this->command->error('Usuario cmartinez no encontrado. Asegúrate de que DatabaseSeeder se ejecute primero.');
            return;
        }

        // Inventarios típicos de una empresa
        $inventarios = [
            // EQUIPOS DE COMPUTO
            [
                'category' => 'Asignación Equipos Computo',
                'description' => 'Dell OptiPlex 7090 - Procesador Intel Core i7, 16GB RAM, SSD 512GB',
                'serial_number' => 'DELL-7090-001',
            ],
            [
                'category' => 'Asignación Equipos Computo',
                'description' => 'Lenovo ThinkCentre M80t - Procesador Intel Core i5, 8GB RAM, SSD 256GB',
                'serial_number' => 'LENOVO-M80T-002',
            ],
            [
                'category' => 'Asignación Equipos Computo',
                'description' => 'HP EliteDesk 800 G6 - Procesador AMD Ryzen 5, 16GB RAM, SSD 512GB',
                'serial_number' => 'HP-ED800-003',
            ],
            [
                'category' => 'Asignación Equipos Computo',
                'description' => 'Apple MacBook Pro 14" - M1 Pro, 16GB RAM, SSD 512GB',
                'serial_number' => 'APPLE-MBP-004',
            ],

            // MONITORES
            [
                'category' => 'Asignacion Monitores',
                'description' => 'Dell UltraSharp U2720Q - Monitor 4K 27", resolución 3840x2160',
                'serial_number' => 'DELL-U2720-001',
            ],
            [
                'category' => 'Asignacion Monitores',
                'description' => 'LG 24UP550 - Monitor 24" 4K para diseño gráfico',
                'serial_number' => 'LG-24UP-002',
            ],
            [
                'category' => 'Asignacion Monitores',
                'description' => 'HP E24 G5 - Monitor 24" Full HD IPS',
                'serial_number' => 'HP-E24G5-003',
            ],
            [
                'category' => 'Asignacion Monitores',
                'description' => 'ASUS PA248QV - Monitor 24" profesional para fotografía',
                'serial_number' => 'ASUS-PA248-004',
            ],

            // TECLADOS
            [
                'category' => 'Asignacion Teclado',
                'description' => 'Logitech MX Keys - Teclado inalámbrico retroiluminado',
                'serial_number' => 'LOGI-MXKEYS-001',
            ],
            [
                'category' => 'Asignacion Teclado',
                'description' => 'Microsoft Sculpt Ergonomic - Teclado ergonómico alámbrico',
                'serial_number' => 'MSFT-SCULPT-002',
            ],
            [
                'category' => 'Asignacion Teclado',
                'description' => 'Corsair K95 Platinum - Teclado mecánico gaming RGB',
                'serial_number' => 'CORSAIR-K95-003',
            ],
            [
                'category' => 'Asignacion Teclado',
                'description' => 'Apple Magic Keyboard - Teclado inalámbrico Apple',
                'serial_number' => 'APPLE-MAGIC-004',
            ],

            // MOUSE
            [
                'category' => 'Asignacion Mouse',
                'description' => 'Logitech MX Master 3S - Ratón inalámbrico profesional',
                'serial_number' => 'LOGI-MXM3S-001',
            ],
            [
                'category' => 'Asignacion Mouse',
                'description' => 'Microsoft Sculpt Comfort - Ratón ergonómico alámbrico',
                'serial_number' => 'MSFT-SCULPT-002',
            ],
            [
                'category' => 'Asignacion Mouse',
                'description' => 'Logitech MX Vertical - Ratón vertical ergonómico',
                'serial_number' => 'LOGI-MXVERT-003',
            ],
            [
                'category' => 'Asignacion Mouse',
                'description' => 'Apple Magic Mouse - Ratón multi-touch inalámbrico',
                'serial_number' => 'APPLE-MAGIC-MOUSE-004',
            ],

            // MODEM
            [
                'category' => 'Asignacion Modem',
                'description' => 'TP-Link Archer AX6000 - Router WiFi 6 gigabit',
                'serial_number' => 'TPLINK-AX6000-001',
            ],
            [
                'category' => 'Asignacion Modem',
                'description' => 'Netgear Nighthawk AX12 - Router WiFi 6 12 Stream',
                'serial_number' => 'NETGEAR-AX12-002',
            ],
            [
                'category' => 'Asignacion Modem',
                'description' => 'ASUS RT-AX88U Pro - Router gaming WiFi 6',
                'serial_number' => 'ASUS-AX88U-003',
            ],
            [
                'category' => 'Asignacion Modem',
                'description' => 'Arris SURFboard SB8200 - Módem DOCSIS 3.1 gigabit',
                'serial_number' => 'ARRIS-SB8200-004',
            ],

            // PLANES CELULARES
            [
                'category' => 'Asignacion Plan Celular',
                'description' => 'iPhone 14 Pro Max - Plan Claro 15GB datos + ilimitadas',
                'serial_number' => 'APPLE-IP14-001',
            ],
            [
                'category' => 'Asignacion Plan Celular',
                'description' => 'Samsung Galaxy S23 Ultra - Plan Movistar 20GB datos',
                'serial_number' => 'SAMSUNG-S23U-002',
            ],
            [
                'category' => 'Asignacion Plan Celular',
                'description' => 'Google Pixel 7 Pro - Plan ETB 10GB datos + roaming',
                'serial_number' => 'GOOGLE-PIXEL7-003',
            ],
            [
                'category' => 'Asignacion Plan Celular',
                'description' => 'OnePlus 11 - Plan Claro 25GB datos ejecutivo',
                'serial_number' => 'ONEPLUS-11-004',
            ],

            // OTROS DISPOSITIVOS
            [
                'category' => 'Otros Dispositivos',
                'description' => 'Impresora Xerox VersaLink C405 - Multifunción color A4',
                'serial_number' => 'XEROX-C405-001',
            ],
            [
                'category' => 'Otros Dispositivos',
                'description' => 'Scanner HP ScanJet Enterprise 7000 - Escáner ADF 600 ppm',
                'serial_number' => 'HP-SCAN7000-002',
            ],
            [
                'category' => 'Otros Dispositivos',
                'description' => 'UPS APC Smart-UPS 1500VA - Sistema de respaldo eléctrico',
                'serial_number' => 'APC-SMT1500-003',
            ],
            [
                'category' => 'Otros Dispositivos',
                'description' => 'Webcam Logitech C920 - Cámara HD 1080p con micrófono',
                'serial_number' => 'LOGI-C920-004',
            ],
            [
                'category' => 'Otros Dispositivos',
                'description' => 'Headset Plantronics Voyager 8200 - Auriculares inalámbricos',
                'serial_number' => 'PLANT-V8200-005',
            ],
            [
                'category' => 'Otros Dispositivos',
                'description' => 'Switch Netgear Managed 24-port - Switch administrable 10G',
                'serial_number' => 'NETGEAR-SW24-006',
            ],
        ];

        // Crear los inventarios
        foreach ($inventarios as $inventario) {
            Inventory::create([
                'user_id' => $usuario->id,
                'category' => $inventario['category'],
                'description' => $inventario['description'],
                'serial_number' => $inventario['serial_number'],
            ]);
        }

        $this->command->info('✓ ' . count($inventarios) . ' inventarios cargados exitosamente para el usuario: ' . $usuario->name);
    }
}
