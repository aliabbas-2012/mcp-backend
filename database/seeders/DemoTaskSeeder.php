<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\ActivityLog;

class DemoTaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::truncate();
        ActivityLog::truncate();

        $demoTasks = [
            [
                'id' => 'task-101',
                'title' => 'Implement OAuth 2.0 & Google Sign-In',
                'description' => 'Integrate secure JWT authentication with Google and GitHub single sign-on.',
                'status' => 'todo',
                'priority' => 'medium',
                'assignee' => 'Unassigned',
                'tags' => ['auth', 'security', 'backend'],
                'due_date' => '2026-09-10',
                'position' => 0,
                'points' => 5,
                'subtasks' => [],
            ],
            [
                'id' => 'task-102',
                'title' => 'Optimize Database Query Indexes & Latency',
                'description' => 'Analyze slow query logs and add composite indexes to achieve sub-20ms response time.',
                'status' => 'todo',
                'priority' => 'low',
                'assignee' => 'David Miller',
                'tags' => ['database', 'mysql', 'performance'],
                'due_date' => '2026-09-15',
                'position' => 1,
                'points' => 3,
                'subtasks' => [],
            ],
            [
                'id' => 'task-103',
                'title' => 'Build Responsive Checkout UI Modal',
                'description' => 'Create multi-currency checkout modal with real-time credit card validation and Apple Pay.',
                'status' => 'in_progress',
                'priority' => 'urgent',
                'assignee' => 'Sarah Connor',
                'tags' => ['frontend', 'react', 'payments'],
                'due_date' => '2026-08-30',
                'position' => 0,
                'points' => 8,
                'subtasks' => [],
            ],
            [
                'id' => 'task-104',
                'title' => 'Setup Stripe Webhook Idempotency in Redis',
                'description' => 'Prevent duplicate charging on network retries by caching payment intent event hashes.',
                'status' => 'in_progress',
                'priority' => 'high',
                'assignee' => 'David Miller',
                'tags' => ['backend', 'redis', 'stripe'],
                'due_date' => '2026-09-02',
                'position' => 1,
                'points' => 5,
                'subtasks' => [],
            ],
            [
                'id' => 'task-105',
                'title' => 'Automated Fraud Anomaly Scoring Pipeline',
                'description' => 'Real-time velocity checks flagging IP hopping and high-frequency card testing attempts.',
                'status' => 'in_review',
                'priority' => 'high',
                'assignee' => 'Alice Chen',
                'tags' => ['security', 'fraud', 'ml'],
                'due_date' => '2026-08-28',
                'position' => 0,
                'points' => 5,
                'subtasks' => [],
            ],
            [
                'id' => 'task-106',
                'title' => 'Design System & Dark Mode UI Palette',
                'description' => 'Standardize typography scale, glassmorphism cards, and contrast compliance.',
                'status' => 'done',
                'priority' => 'medium',
                'assignee' => 'Sarah Connor',
                'tags' => ['design', 'tailwind', 'ui'],
                'due_date' => '2026-08-20',
                'position' => 0,
                'points' => 3,
                'subtasks' => [],
            ],
        ];

        foreach ($demoTasks as $task) {
            Task::create($task);
        }

        ActivityLog::log(
            'SEED',
            null,
            'Initialized demo environment with 6 realistic enterprise engineering tasks',
            'USER',
            10
        );
    }
}
