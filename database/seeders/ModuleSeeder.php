<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::create([
            'name' => 'bill-payments',
        ]);

        Module::create([
            'name' => 'bills',
        ]);

        Module::create([
            'name' => 'brands',
        ]);

        Module::create([
            'name' => 'businesses',
        ]);

        Module::create([
            'name' => 'contacts',
        ]);

        Module::create([
            'name' => 'conversions',
        ]);

        Module::create([
            'name' => 'credit-memorandums',
        ]);

        Module::create([
            'name' => 'debit-memorandums',
        ]);

        Module::create([
            'name' => 'deliveries',
        ]);

        Module::create([
            'name' => 'delivery-returns',
        ]);

        Module::create([
            'name' => 'invoice-payments',
        ]);

        Module::create([
            'name' => 'invoices',
        ]);

        Module::create([
            'name' => 'item-classifications',
        ]);

        Module::create([
            'name' => 'item-types',
        ]);

        Module::create([
            'name' => 'items',
        ]);

        Module::create([
            'name' => 'measuring-masses',
        ]);

        Module::create([
            'name' => 'permissions',
        ]);

        Module::create([
            'name' => 'purchase-item-prices',
        ]);

        Module::create([
            'name' => 'purchase-orders',
        ]);

        Module::create([
            'name' => 'quotaions',
        ]);

        Module::create([
            'name' => 'receive-orders',
        ]);

        Module::create([
            'name' => 'roles',
        ]);

        Module::create([
            'name' => 'sale-item-prices',
        ]);

        Module::create([
            'name' => 'services',
        ]);

        Module::create([
            'name' => 'service-prices',
        ]);

        Module::create([
            'name' => 'setttings',
        ]);

        Module::create([
            'name' => 'stock-receives',
        ]);

        Module::create([
            'name' => 'stock-requests',
        ]);

        Module::create([
            'name' => 'stock-transfers',
        ]);

        Module::create([
            'name' => 'stock-returns',
        ]);

        Module::create([
            'name' => 'stocks',
        ]);

        Module::create([
            'name' => 'unit-of-measurements',
        ]);

        Module::create([
            'name' => 'users',
        ]);

        Module::create([
            'name' => 'warehouses',
        ]);
    }
}
