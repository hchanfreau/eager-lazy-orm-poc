<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;

class OrmPocTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the /test-orm endpoint to ensure it returns users with their posts, comments, and tags.
     */
    public function testOrmPocEndpointReturnsCorrectData()
    {
        // Seed the database with some data
        $this->seed();

        // Make a GET request to the /test-orm endpoint
        $response = $this->get('/test-orm');

        // Assert that the response was successful
        $response->assertStatus(200);

        // Assert that the response is JSON
        $response->assertJsonStructure([
            '*' => [ // Array of users
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
                'posts' => [
                    '*' => [ // Array of posts for each user
                        'id',
                        'user_id',
                        'title',
                        'content',
                        'published_at',
                        'created_at',
                        'updated_at',
                        'comments' => [
                            '*' => [ // Array of comments for each post
                                'id',
                                'user_id',
                                'post_id',
                                'content',
                                'created_at',
                                'updated_at',
                            ]
                        ],
                        'tags' => [
                            '*' => [ // Array of tags for each post
                                'id',
                                'name',
                                'created_at',
                                'updated_at',
                                'pivot' => [
                                    'post_id',
                                    'tag_id',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        // Further assertions to ensure data integrity (optional, but good practice)
        $users = User::with(['posts.comments', 'posts.tags'])->get();
        $response->assertJson($users->toArray());
    }
}
