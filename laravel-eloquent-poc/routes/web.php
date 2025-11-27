<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-orm', function () {
    error_log("--- Starting EAGER Loading for /test-orm ---"); // Marker
    $users = App\Models\User::with(['posts.comments', 'posts.tags'])->get(); // Eager loads all relationships
    error_log("--- Finished EAGER Loading for /test-orm ---"); // Marker
    return response()->json($users);
});

Route::get('/lazy-orm', function () {
    error_log("--- Starting LAZY Loading for /lazy-orm ---"); // Marker
    error_log("Fetching all users...");
    $users = App\Models\User::all(); // Fetches all users (1 query)
    error_log("Found " . count($users) . " users.");
    $data = [];
    foreach ($users as $user) {
        error_log("--- Processing User ID: " . $user->id . " (Name: " . $user->name . ") ---");
        error_log("  Fetching posts for User ID: " . $user->id . " (LAZY LOAD)");
        $userPosts = [];
        foreach ($user->posts as $post) { // Lazy loads posts for each user (N queries for posts)
            error_log("    Processing Post ID: " . $post->id . " (Title: " . $post->title . ") ---");
            error_log("      Fetching comments for Post ID: " . $post->id . " (LAZY LOAD)");
            $postComments = [];
            foreach ($post->comments as $comment) { // Lazy loads comments for each post (M queries for comments)
                $postComments[] = $comment->toArray();
            }
            error_log("      Fetching tags for Post ID: " . $post->id . " (LAZY LOAD)");
            $postTags = [];
            foreach ($post->tags as $tag) { // Lazy loads tags for each post (P queries for tags)
                $postTags[] = $tag->toArray();
            }
            $userPosts[] = array_merge($post->toArray(), ['comments' => $postComments, 'tags' => $postTags]);
        }
        $data[] = array_merge($user->toArray(), ['posts' => $userPosts]);
    }
    error_log("--- Finished LAZY Loading for /lazy-orm ---"); // Marker
    return response()->json($data);
});

