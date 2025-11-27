<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
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
        // 1. Create a collection of tags first
        $tags = Tag::factory(15)->create();

        // 2. Create a main user for easy testing
        $mainUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // 3. Create 10 other random users
        $otherUsers = User::factory(10)->create();
        $allUsers = $otherUsers->push($mainUser);

        // 4. Create posts and associate relationships
        Post::factory(30)
            ->recycle($allUsers) // Assign posts to users from the created collection
            ->create()
            ->each(function ($post) use ($allUsers, $tags) {
                // For each post, attach 1 to 3 random tags from the pre-existing collection
                $post->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')->toArray()
                );

                // For each post, create 2 to 8 comments from random users
                Comment::factory(rand(2, 8))
                    ->recycle($allUsers) // Comments are from the same user pool
                    ->create(['post_id' => $post->id]);
            });
    }
}
