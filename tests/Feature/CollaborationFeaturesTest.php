<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaborationFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser(User $user): void
    {
        $token = UserToken::create([
            'user_id' => $user->id,
            'token' => 'test-token-'.$user->id,
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token->token);
    }

    public function test_team_member_can_be_added(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $company = Company::create(['name' => 'Acme', 'owner_id' => $owner->id]);
        $team = Team::create(['company_id' => $company->id, 'name' => 'Platform']);

        $this->actingAsUser($owner);

        $response = $this->postJson('/api/teams/'.$team->id.'/members', [
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user_id', $member->id)
            ->assertJsonPath('data.role', 'member');
    }

    public function test_private_channel_blocks_non_member_access(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $company = Company::create(['name' => 'Acme', 'owner_id' => $owner->id]);
        $team = Team::create(['company_id' => $company->id, 'name' => 'Platform']);
        $channel = Channel::create(['team_id' => $team->id, 'name' => 'Private Board', 'is_private' => true]);

        $this->actingAsUser($outsider);

        $response = $this->getJson('/api/channels/'.$channel->id);

        $response->assertStatus(403);
    }

    public function test_message_reply_and_reaction_are_supported(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['name' => 'Acme', 'owner_id' => $user->id]);
        $team = Team::create(['company_id' => $company->id, 'name' => 'Platform']);
        $channel = Channel::create(['team_id' => $team->id, 'name' => 'General']);

        $this->actingAsUser($user);

        $messageResponse = $this->postJson('/api/channels/'.$channel->id.'/messages', [
            'message' => 'Initial message',
        ]);

        $message = $messageResponse->json('data');

        $replyResponse = $this->postJson('/api/messages/'.$message['id'].'/replies', [
            'message' => 'Reply message',
        ]);

        $replyResponse->assertStatus(200)
            ->assertJsonPath('data.message', 'Reply message');

        $reactionResponse = $this->postJson('/api/messages/'.$message['id'].'/reactions', [
            'reaction' => 'like',
        ]);

        $reactionResponse->assertStatus(200)
            ->assertJsonPath('data.reaction', 'like');
    }
}
