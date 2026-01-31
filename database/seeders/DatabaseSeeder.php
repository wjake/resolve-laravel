<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories first
        $this->call([
            CategorySeeder::class,
        ]);

        // Create admin user if it doesn't exist
        if (!User::where('email', 'admin@resolve.test')->exists()) {
            $adminUser = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@resolve.test',
                'password' => bcrypt('password'),
                'is_agent' => true,
            ]);
        } else {
            $adminUser = User::where('email', 'admin@resolve.test')->first();
        }

        // Create demo user if it doesn't exist
        if (!User::where('email', 'demo@resolve.test')->exists()) {
            $demoUser = User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@resolve.test',
                'password' => bcrypt('password'),
            ]);

            // Create sample tickets for demo user
            $categories = \App\Models\Category::all();
            
            $tickets = [
                [
                    'title' => 'Unable to access my account',
                    'description' => 'I\'ve been trying to log in for the past hour but keep getting an error message. Can you help me reset my password?',
                    'category' => 'Technical Support',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'assigned' => true,
                ],
                [
                    'title' => 'Question about my invoice',
                    'description' => 'I received my monthly invoice but noticed a charge I don\'t recognize. Could you please clarify what the $49.99 charge is for?',
                    'category' => 'Billing',
                    'status' => 'in_progress',
                    'priority' => 'medium',
                    'assigned' => true,
                ],
                [
                    'title' => 'Add dark mode to the application',
                    'description' => 'It would be great if the application had a dark mode option. I often work late at night and the bright interface strains my eyes.',
                    'category' => 'Feature Request',
                    'status' => 'open',
                    'priority' => 'low',
                    'assigned' => false,
                ],
                [
                    'title' => 'Dashboard not loading properly',
                    'description' => 'When I click on the dashboard, it shows a blank screen for about 30 seconds before loading. This started happening after the latest update.',
                    'category' => 'Bug Report',
                    'status' => 'resolved',
                    'priority' => 'high',
                    'assigned' => true,
                ],
                [
                    'title' => 'Export feature not working',
                    'description' => 'I\'m trying to export my data to CSV but the download button doesn\'t do anything when I click it.',
                    'category' => 'Bug Report',
                    'status' => 'open',
                    'priority' => 'medium',
                    'assigned' => false,
                ],
                [
                    'title' => 'Need help setting up email notifications',
                    'description' => 'I want to receive email notifications when someone comments on my tickets, but I can\'t find the settings for this.',
                    'category' => 'Technical Support',
                    'status' => 'open',
                    'priority' => 'low',
                    'assigned' => false,
                ],
                [
                    'title' => 'Subscription renewal failed',
                    'description' => 'My subscription was supposed to renew yesterday but I received an email saying the payment failed. I need to update my payment method.',
                    'category' => 'Billing',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'assigned' => true,
                ],
                [
                    'title' => 'Mobile app crashes on startup',
                    'description' => 'Ever since the latest iOS update, the mobile app crashes immediately when I try to open it. I\'ve tried reinstalling but the problem persists.',
                    'category' => 'Bug Report',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'assigned' => true,
                ],
                [
                    'title' => 'Request for API documentation',
                    'description' => 'I\'m a developer looking to integrate with your API. Could you provide documentation or examples?',
                    'category' => 'General Inquiry',
                    'status' => 'open',
                    'priority' => 'medium',
                    'assigned' => false,
                ],
                [
                    'title' => 'Can\'t upload files larger than 5MB',
                    'description' => 'I need to upload some documents but they\'re all around 8-10MB in size. The system keeps rejecting them.',
                    'category' => 'Technical Support',
                    'status' => 'resolved',
                    'priority' => 'medium',
                    'assigned' => true,
                ],
                [
                    'title' => 'Add support for two-factor authentication',
                    'description' => 'For security reasons, I\'d like to enable 2FA on my account. Is this feature available or planned?',
                    'category' => 'Feature Request',
                    'status' => 'open',
                    'priority' => 'medium',
                    'assigned' => false,
                ],
                [
                    'title' => 'Wrong language displayed after update',
                    'description' => 'After the recent update, part of the interface is showing in Spanish instead of English. I can\'t find a language setting.',
                    'category' => 'Bug Report',
                    'status' => 'open',
                    'priority' => 'low',
                    'assigned' => false,
                ],
                [
                    'title' => 'Refund request for duplicate charge',
                    'description' => 'I was accidentally charged twice for my monthly subscription. Can you please refund the duplicate payment?',
                    'category' => 'Billing',
                    'status' => 'resolved',
                    'priority' => 'high',
                    'assigned' => true,
                ],
                [
                    'title' => 'Keyboard shortcuts not working',
                    'description' => 'None of the keyboard shortcuts mentioned in the help documentation seem to work for me. I\'m using Chrome on Windows.',
                    'category' => 'Bug Report',
                    'status' => 'open',
                    'priority' => 'low',
                    'assigned' => false,
                ],
                [
                    'title' => 'How to change my username?',
                    'description' => 'I\'d like to change my username but I don\'t see an option for this in my profile settings.',
                    'category' => 'General Inquiry',
                    'status' => 'closed',
                    'priority' => 'low',
                    'assigned' => true,
                ],
                [
                    'title' => 'Add bulk edit functionality',
                    'description' => 'It would save a lot of time if I could select multiple items and edit them all at once instead of one by one.',
                    'category' => 'Feature Request',
                    'status' => 'open',
                    'priority' => 'medium',
                    'assigned' => false,
                ],
                [
                    'title' => 'Search function returns no results',
                    'description' => 'When I try to search for anything using the search bar, it always says "No results found" even though I know the content exists.',
                    'category' => 'Bug Report',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'assigned' => true,
                ],
                [
                    'title' => 'Need invoice for tax purposes',
                    'description' => 'I need a detailed invoice with your VAT number for my annual tax filing. The standard invoice doesn\'t include this information.',
                    'category' => 'Billing',
                    'status' => 'resolved',
                    'priority' => 'medium',
                    'assigned' => true,
                ],
                [
                    'title' => 'Performance issues during peak hours',
                    'description' => 'The application becomes very slow between 2-4 PM every day. Pages take forever to load during this time.',
                    'category' => 'Technical Support',
                    'status' => 'open',
                    'priority' => 'high',
                    'assigned' => false,
                ],
                [
                    'title' => 'Integration with Slack',
                    'description' => 'Would be amazing if notifications could be sent to Slack channels. Our whole team uses Slack and this would be very helpful.',
                    'category' => 'Feature Request',
                    'status' => 'open',
                    'priority' => 'medium',
                    'assigned' => false,
                ],
            ];

            foreach ($tickets as $ticketData) {
                $category = $categories->where('name', $ticketData['category'])->first() ?? $categories->first();
                
                \App\Models\Ticket::factory()->create([
                    'user_id' => $demoUser->id,
                    'category_id' => $category->id,
                    'title' => $ticketData['title'],
                    'description' => $ticketData['description'],
                    'status' => $ticketData['status'],
                    'priority' => $ticketData['priority'],
                    'assigned_to' => $ticketData['assigned'] ? $adminUser->id : null,
                ]);
            }
        }
    }
}
