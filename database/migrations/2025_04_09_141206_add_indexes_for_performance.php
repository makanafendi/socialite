<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = DB::getDriverName();

        if ($connection === 'sqlite') {
            // For SQLite, we'll use a different approach to create indexes
            // by checking if they exist first with a raw query
            $this->createSqliteIndexes();
        } else {
            // For MySQL, PostgreSQL, etc. use Schema builder
            $this->createSchemaIndexes();
        }
    }

    /**
     * Create indexes using SQLite-specific approach
     */
    private function createSqliteIndexes(): void
    {
        // Helper function to create index if it doesn't exist
        $createIndexIfNotExists = function($table, $columns, $indexName = null) {
            if (!$indexName) {
                $indexName = $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns) . '_index';
            }
            
            // Check if the index exists
            $indexExists = DB::select("SELECT name FROM sqlite_master 
                WHERE type = 'index' AND name = ?", [$indexName]);
                
            if (empty($indexExists)) {
                // Create the index - format column names
                $columnStr = is_array($columns) ? implode(', ', $columns) : $columns;
                DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$columnStr})");
            }
        };

        // Create indexes for user table
        $createIndexIfNotExists('users', 'username');
        $createIndexIfNotExists('users', 'name');
        $createIndexIfNotExists('users', 'created_at');
        
        // Create indexes for posts table
        $createIndexIfNotExists('posts', ['user_id', 'created_at'], 'posts_user_id_created_at_index');
        $createIndexIfNotExists('posts', 'created_at');
        
        // Create indexes for comments table
        $createIndexIfNotExists('comments', ['post_id', 'created_at'], 'comments_post_id_created_at_index');
        $createIndexIfNotExists('comments', ['user_id', 'created_at'], 'comments_user_id_created_at_index');
        
        // Create indexes for follows table (if it exists)
        if (Schema::hasTable('follows')) {
            $createIndexIfNotExists('follows', ['user_id', 'created_at'], 'follows_user_id_created_at_index');
            $createIndexIfNotExists('follows', ['followed_id', 'created_at'], 'follows_followed_id_created_at_index');
        }
        
        // Create indexes for likes table (if it exists)
        if (Schema::hasTable('likes')) {
            $createIndexIfNotExists('likes', ['post_id', 'user_id'], 'likes_post_id_user_id_index');
            $createIndexIfNotExists('likes', 'post_id');
        }
        
        // Create indexes for comment_likes table (if it exists)
        if (Schema::hasTable('comment_likes')) {
            $createIndexIfNotExists('comment_likes', ['comment_id', 'user_id'], 'comment_likes_comment_id_user_id_index');
            $createIndexIfNotExists('comment_likes', 'comment_id');
        }
    }

    /**
     * Create indexes using Schema builder for other database systems
     */
    private function createSchemaIndexes(): void
    {
        // Create indexes for user table
        Schema::table('users', function (Blueprint $table) {
            $table->index('username');
            $table->index('name');
            $table->index('created_at');
        });
        
        // Create indexes for posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
        
        // Create indexes for comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['post_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
        
        // Create indexes for follows table (if it exists)
        if (Schema::hasTable('follows')) {
            Schema::table('follows', function (Blueprint $table) {
                $table->index(['user_id', 'created_at']);
                $table->index(['followed_id', 'created_at']);
            });
        }
        
        // Create indexes for likes table (if it exists)
        if (Schema::hasTable('likes')) {
            Schema::table('likes', function (Blueprint $table) {
                $table->index(['post_id', 'user_id']);
                $table->index('post_id');
            });
        }
        
        // Create indexes for comment_likes table (if it exists)
        if (Schema::hasTable('comment_likes')) {
            Schema::table('comment_likes', function (Blueprint $table) {
                $table->index(['comment_id', 'user_id']);
                $table->index('comment_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not dropping indexes - they'll be removed when tables are dropped
    }
};
