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
                'name'               => 'Free',
                'name_translations'  => ['ru' => 'Бесплатный', 'en' => 'Free'],
                'slug'               => 'free',
                'description'        => 'Бесплатный тариф для базовой обработки изображений',
                'description_translations' => [
                    'ru' => 'Бесплатный тариф для базовой обработки изображений',
                    'en' => 'Free plan for basic image processing',
                ],
                'price_month'        => 0,
                'price_year'         => 0,
                'currency'           => 'RUB',
                'max_files_per_job'  => 10,
                'max_file_size_mb'   => 10,
                'daily_jobs_limit'   => 5,
                'monthly_credits'    => 0,
                'watermark'          => false,
                'api_access'         => false,
                'priority_queue'     => false,
                'allowed_formats'    => ['jpg', 'png', 'webp'],
                'allowed_operations' => ['resize', 'rotate', 'flip', 'crop'],
                'is_active'          => true,
                'sort_order'         => 1,
            ],
            [
                'name'               => 'Pro',
                'name_translations'  => ['ru' => 'Профессиональный', 'en' => 'Pro'],
                'slug'               => 'pro',
                'description'        => 'Для регулярной пакетной обработки и продвинутых функций',
                'description_translations' => [
                    'ru' => 'Для регулярной пакетной обработки и продвинутых функций',
                    'en' => 'For regular batch processing and advanced features',
                ],
                'price_month'        => 99000,
                'price_year'         => 990000,
                'currency'           => 'RUB',
                'max_files_per_job'  => 200,
                'max_file_size_mb'   => 50,
                'daily_jobs_limit'   => 100,
                'monthly_credits'    => 200,
                'watermark'          => false,
                'api_access'         => true,
                'priority_queue'     => true,
                'allowed_formats'    => ['jpg', 'png', 'webp', 'avif', 'gif', 'tiff'],
                'allowed_operations' => ['resize', 'rotate', 'flip', 'crop', 'watermark'],
                'is_active'          => true,
                'sort_order'         => 2,
            ],
            [
                'name'               => 'Team',
                'name_translations'  => ['ru' => 'Командный', 'en' => 'Team'],
                'slug'               => 'team',
                'description'        => 'Командный тариф с максимальными лимитами и API',
                'description_translations' => [
                    'ru' => 'Командный тариф с максимальными лимитами и API',
                    'en' => 'Team plan with maximum limits and API access',
                ],
                'price_month'        => 299000,
                'price_year'         => 2990000,
                'currency'           => 'RUB',
                'max_files_per_job'  => 1000,
                'max_file_size_mb'   => 100,
                'daily_jobs_limit'   => 500,
                'monthly_credits'    => 2000,
                'watermark'          => false,
                'api_access'         => true,
                'priority_queue'     => true,
                'allowed_formats'    => ['jpg', 'png', 'webp', 'avif', 'gif', 'tiff'],
                'allowed_operations' => ['resize', 'rotate', 'flip', 'crop', 'watermark'],
                'is_active'          => true,
                'sort_order'         => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
