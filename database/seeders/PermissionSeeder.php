<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * General Permissions or Super Administrator Permissions
         *
         */
        Permission::create([
            'name' => 'full-access-and-permssions',
            'display_name' => 'Full Access and Permissions',
            'description' => 'Full access and permissions for all modules',
            'module' => 'All Modules',
        ]);

        $modules = [
            'account-types' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'view-prices'],
            'accounts' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'view-prices'],
            'bill-payments' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'view-prices'],
            'bills' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'view-prices'],
            'brands' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'budgets' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'businesses' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'charge-invoice-payments' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'charge-invoices' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'clusters' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'contacts' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'conversions' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'cost-centers' => ['view'],
            'credit-memorandums' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'debit-memorandums' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'deliveries' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'return'],
            'delivery-returns' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'departments' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'expenses' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'groups' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'industries' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'invoice-payments' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'invoices' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'item-classifications' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'items' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'view-default-purchase-item-price', 'view-default-sale-item-price'],
            'item-types' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'measuring-masses' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'journals' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'permissions' => ['view'],
            'purchase-item-prices' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'purchase-orders' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'approve', 'cancel', 'view-prices'],
            'quotaions' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'receive-orders' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'recurring-charge-invoices' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'recurring-invoices' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'roles' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'assign-permissions-for'],
            'sale-item-prices' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'services' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'service-prices' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'setttings' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'stock-receives' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'receive', 'return'],
            'stock-requests' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'cancel', 'pending', 'approve', 'unapprove'],
            'stock-transfers' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'return'],
            'stock-returns' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'stocks' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'unit-of-measurements' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'users' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete', 'assign-roles', 'can-reset-passwords', 'can-reset-security-pin'],
            'vouchers' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
            'warehouses' => ['create', 'view', 'update', 'delete', 'restore', 'force-delete'],
        ];

        $data = [];

        $now = now();

        foreach ($modules as $moduleName => $modulePermissions) {
            foreach ($modulePermissions as $modulePermission) {
                $name = $modulePermission . '-' . $moduleName;
                $display = str_replace('-', ' ', $name);
                $permission = [
                    'id' => Str::uuid()->toString(),
                    'name' => $name,
                    'display_name' => ucwords($display),
                    'module' => ucwords(str_replace('-', ' ', $moduleName)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $data[] = $permission;
            }
        }

        Permission::insert($data);
    }
}
