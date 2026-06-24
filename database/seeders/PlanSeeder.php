<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'free',
                'name' => 'Free',
                'level' => 1,
                'price_cents' => 0,
                'interval' => 'month',
                'minutes_limit' => 30,
                'engine' => 'browser',
                'allow_system_audio' => false,
                'ads' => true,
                'sort' => 1,
                'features' => [
                    '30 minutes / month',
                    'On-device voice engine',
                    'All languages',
                    'Microphone source',
                ],
            ],
            [
                'slug' => 'premium',
                'name' => 'Premium',
                'level' => 2,
                'price_cents' => 599,
                'interval' => 'month',
                'minutes_limit' => 1500,
                'engine' => 'cloud',
                'allow_system_audio' => true,
                'ads' => false,
                'sort' => 2,
                'features' => [
                    '25 hours / month',
                    'High-quality cloud voices',
                    'System & tab audio capture',
                    'No ads',
                    'Priority support',
                ],
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro',
                'level' => 3,
                'price_cents' => 1499,
                'interval' => 'month',
                'minutes_limit' => null,
                'engine' => 'cloud',
                'allow_system_audio' => true,
                'ads' => false,
                'sort' => 3,
                'features' => [
                    'Unlimited minutes',
                    'Premium cloud voices',
                    'System & tab audio capture',
                    'No ads',
                    'Team seats & API access',
                    'Priority support',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan + ['currency' => 'EUR', 'is_active' => true]);
        }
    }
}
