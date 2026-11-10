<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\CostCenter;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment() == 'production') {
            $contact = Contact::create([
                'name' => 'Walk-In Customer',
            ]);

            CostCenter::create([
                'business_id'          => 1,
                'created_by_id'        => 1,
                'cost_centerable_id'   => $contact->id,
                'cost_centerable_type' => 'App\\Models\\Contact',
            ]);
        } else {
            $contact = Contact::create([
                'name' => 'Walk-In Customer',
            ]);

            CostCenter::create([
                'business_id'          => 1,
                'created_by_id'        => 1,
                'cost_centerable_id'   => $contact->id,
                'cost_centerable_type' => 'App\\Models\\Contact',
            ]);

            Contact::factory()->count(50)->companyName()->create()->each(function ($contact) {
                CostCenter::create([
                    'business_id'          => 1,
                    'created_by_id'        => 1,
                    'cost_centerable_id'   => $contact->id,
                    'cost_centerable_type' => 'App\\Models\\Contact',
                ]);
            });
        }
    }
}
