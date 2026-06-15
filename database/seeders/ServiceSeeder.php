<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'SMS Gateway', 'category' => 'Communication', 'status' => 'healthy', 'description' => 'SMS delivery gateway for notifications and alerts'],
            ['name' => 'Payment Gateway', 'category' => 'Financial', 'status' => 'healthy', 'description' => 'Payment processing and transaction management'],
            ['name' => 'API Gateway', 'category' => 'Infrastructure', 'status' => 'healthy', 'description' => 'Centralized API routing and rate limiting'],
            ['name' => 'Authentication Service', 'category' => 'Security', 'status' => 'healthy', 'description' => 'User authentication and session management'],
            ['name' => 'Reporting Engine', 'category' => 'Application', 'status' => 'healthy', 'description' => 'Report generation and analytics processing'],
            ['name' => 'Database Cluster', 'category' => 'Infrastructure', 'status' => 'healthy', 'description' => 'PostgreSQL database cluster for data storage'],
            ['name' => 'Email Service', 'category' => 'Communication', 'status' => 'healthy', 'description' => 'Email delivery service for notifications'],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
