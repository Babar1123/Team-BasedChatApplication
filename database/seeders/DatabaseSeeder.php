<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\ChannelMember;
use App\Models\Company;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\Team;
use App\Models\TeamMember;
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
        $companyName = env('DEMO_COMPANY_NAME', 'Acme Labs');

        $demoUser = User::firstOrCreate(
            ['email' => 'demo@teamchat.test'],
            [
                'name' => 'Demo Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $users = [
            $demoUser,
            User::firstOrCreate(
                ['email' => 'jane@teamchat.test'],
                ['name' => 'Jane Smith', 'password' => bcrypt('password'), 'email_verified_at' => now()]
            ),
            User::firstOrCreate(
                ['email' => 'mike@teamchat.test'],
                ['name' => 'Mike Johnson', 'password' => bcrypt('password'), 'email_verified_at' => now()]
            ),
            User::firstOrCreate(
                ['email' => 'sarah@teamchat.test'],
                ['name' => 'Sarah Lee', 'password' => bcrypt('password'), 'email_verified_at' => now()]
            ),
        ];

        $company = Company::firstOrCreate(
            ['name' => $companyName],
            ['name' => $companyName, 'owner_id' => $demoUser->id]
        );

        $team = Team::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Product Team'],
            ['company_id' => $company->id, 'name' => 'Product Team']
        );

        $teamMembers = [
            ['user_id' => $demoUser->id, 'role' => 'owner'],
            ['user_id' => $users[1]->id, 'role' => 'admin'],
            ['user_id' => $users[2]->id, 'role' => 'member'],
            ['user_id' => $users[3]->id, 'role' => 'member'],
        ];

        foreach ($teamMembers as $member) {
            TeamMember::firstOrCreate(
                ['team_id' => $team->id, 'user_id' => $member['user_id']],
                [
                    'team_id' => $team->id,
                    'user_id' => $member['user_id'],
                    'role' => $member['role'],
                    'joined_at' => now(),
                ]
            );
        }

        $generalChannel = Channel::firstOrCreate(
            ['team_id' => $team->id, 'name' => 'general'],
            ['team_id' => $team->id, 'name' => 'general', 'is_private' => false]
        );

        $privateChannel = Channel::firstOrCreate(
            ['team_id' => $team->id, 'name' => 'leadership'],
            ['team_id' => $team->id, 'name' => 'leadership', 'is_private' => true]
        );

        foreach ([$demoUser->id, $users[1]->id, $users[2]->id] as $userId) {
            ChannelMember::firstOrCreate(
                ['channel_id' => $privateChannel->id, 'user_id' => $userId],
                ['channel_id' => $privateChannel->id, 'user_id' => $userId]
            );
        }

        $welcomeMessage = Message::firstOrCreate(
            ['channel_id' => $generalChannel->id, 'user_id' => $demoUser->id, 'message' => 'Welcome to the Product Team channel!'],
            ['channel_id' => $generalChannel->id, 'user_id' => $demoUser->id, 'message' => 'Welcome to the Product Team channel!']
        );

        $statusMessage = Message::firstOrCreate(
            ['channel_id' => $generalChannel->id, 'user_id' => $users[1]->id, 'message' => 'The new sprint review is scheduled for Friday.'],
            ['channel_id' => $generalChannel->id, 'user_id' => $users[1]->id, 'message' => 'The new sprint review is scheduled for Friday.']
        );

        $reply = Message::firstOrCreate(
            ['channel_id' => $generalChannel->id, 'user_id' => $users[2]->id, 'parent_id' => $statusMessage->id, 'message' => 'Thanks, I will prepare the dashboard update.'],
            ['channel_id' => $generalChannel->id, 'user_id' => $users[2]->id, 'parent_id' => $statusMessage->id, 'message' => 'Thanks, I will prepare the dashboard update.']
        );

        $privateMessage = Message::firstOrCreate(
            ['channel_id' => $privateChannel->id, 'user_id' => $demoUser->id, 'message' => 'Leadership notes: release approval is pending.'],
            ['channel_id' => $privateChannel->id, 'user_id' => $demoUser->id, 'message' => 'Leadership notes: release approval is pending.']
        );

        MessageReaction::firstOrCreate(
            ['message_id' => $statusMessage->id, 'user_id' => $demoUser->id, 'reaction' => 'like'],
            ['message_id' => $statusMessage->id, 'user_id' => $demoUser->id, 'reaction' => 'like']
        );

        MessageReaction::firstOrCreate(
            ['message_id' => $reply->id, 'user_id' => $users[1]->id, 'reaction' => 'rocket'],
            ['message_id' => $reply->id, 'user_id' => $users[1]->id, 'reaction' => 'rocket']
        );

        $this->command->info('Demo data seeded successfully.');
    }
}
