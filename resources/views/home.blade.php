@extends('layouts.app')

@section('content')
<div class="container max-w-[640px] mx-auto py-4">
    @foreach($posts as $post)
    <div class="bg-white shadow-sm rounded-xl overflow-hidden mb-8">
        <!-- Header with user info -->
        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div>
                    <img src="{{ $post->user->profile->profileImage() }}" alt="" class="w-[40px] h-[40px] rounded-full object-cover border-2 border-gray-100">
                </div>
                <h3 class="text-sm font-semibold">
                    <a href="/profile/{{ $post->user->id }}" class="hover:text-blue-500 transition-colors">{{ $post->user->username }}</a>
                </h3>
            </div>
            <div>
                @cannot('update', $post->user->profile)
                <div x-data="{
                    isFollowing: {{ Auth::user()->following->contains($post->user) ? 'true' : 'false' }},
                    isLoading: false,
                    toggleFollow() {
                        this.isLoading = true;
                        const url = this.isFollowing
                            ? '{{ route('unfollow', $post->user->profile) }}'
                            : '{{ route('follow', $post->user->profile) }}';

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => {
                            if (response.ok) {
                                this.isFollowing = !this.isFollowing;
                            }
                            this.isLoading = false;
                        })
                        .catch(error => {
                            console.error('Error toggling follow:', error);
                            this.isLoading = false;
                        });
                    }
                }">
                    <button
                        @click="toggleFollow"
                        :disabled="isLoading"
                        :class="{
                            'text-blue-500 border-blue-500 hover:bg-blue-500 hover:text-white': !isFollowing,
                            'text-red-500 border-red-500 hover:bg-red-500 hover:text-white': isFollowing,
                            'opacity-75 cursor-wait': isLoading
                        }"
                        class="px-4 py-1.5 text-sm rounded-full font-medium transition-colors duration-300 border flex items-center gap-1">
                        <span x-show="isLoading" class="inline-block">
                            <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span x-text="isFollowing ? 'Unfollow' : 'Follow'"></span>
                    </button>
                </div>
                @endcannot
            </div>
        </div>

        <!-- Post Image -->
        <div class="w-full aspect-square">
            <a href="/p/{{ $post->id }}">
                <img src="/storage/{{ $post->image }}" alt="Post by {{ $post->user->username }}" class="w-full h-full object-cover">
            </a>
        </div>

        <!-- Post Content -->
        <div class="p-5">
            <!-- Like system and comments count -->
            <div class="flex items-center gap-4 mb-3"
                x-data="{
                    liked: {{ $post->likedBy(auth()->user()) ? 'true' : 'false' }},
                    likeCount: {{ $post->likes->count() }},
                    commentCount: {{ $post->comments ? $post->comments->count() : 0 }},

                    toggleLike() {
                        fetch('/p/{{ $post->id }}/like', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.liked = data.liked;
                            this.likeCount = data.count;
                        });
                    }
                }">
                <button
                    type="button"
                    @click="toggleLike"
                    class="flex items-center gap-1 transition-transform hover:scale-110">
                    <!-- Unlike heart icon -->
                    <svg
                        x-show="!liked"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>

                    <!-- Like heart icon -->
                    <svg
                        x-show="liked"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="w-6 h-6 text-red-500"
                        fill="currentColor"
                        viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>

                    <!-- Like count -->
                    <span
                        class="text-sm font-medium"
                        x-text="likeCount"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"></span>
                </button>

                <!-- Comment icon with link to post -->
                <a href="/p/{{ $post->id }}" class="flex items-center gap-1 transition-transform hover:scale-110">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm font-medium" x-text="commentCount"></span>
                </a>
            </div>

            <!-- Caption -->
            <div class="mb-4">
                <p class="text-sm">
                    <span class="font-semibold">{{ $post->user->username }}</span> {{ $post->caption }}
                </p>
            </div>

            <!-- Enhanced Comments Section -->
            <div class="mb-4" x-data="commentsSystem{{ $post->id }}">
                <!-- Edit Comment Modal -->
                <div x-show="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-cloak>
                    <div @click.away="showEditModal = false" class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
                        <h3 class="text-lg font-semibold mb-4">Edit Comment</h3>
                        <textarea 
                            x-model="editCommentText" 
                            class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 overflow-hidden"
                            rows="3"
                            @input="autoGrow($event.target)"
                        ></textarea>
                        <div class="flex justify-end mt-4 gap-2">
                            <button @click="showEditModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                                Cancel
                            </button>
                            <button @click="updateComment" class="px-4 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Quick comment form -->
                <form
                    class="flex items-start gap-3 mb-4"
                    x-data="{
                        comment: '',
                        isSubmitting: false,
                        autoGrow(el) {
                            el.style.height = 'auto';
                            el.style.height = (el.scrollHeight) + 'px';
                        },
                        submitForm(e) {
                            e.preventDefault();
                            if (this.comment.trim() === '') return;

                            this.isSubmitting = true;

                            fetch('/p/{{ $post->id }}/comments', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    comment: this.comment
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.comment = '';
                                    // Update comment count
                                    const parentDiv = this.$el.closest('.p-5').querySelector('.flex.items-center.gap-4.mb-3');
                                    if (parentDiv && parentDiv.__x) {
                                        parentDiv.__x.getUnobservedData().commentCount++;
                                    }
                                    
                                    // Create a comment object with the response data
                                    const newComment = {
                                        id: data.comment.id,
                                        user_id: data.comment.user_id,
                                        post_id: data.comment.post_id,
                                        comment: data.comment.comment,
                                        created_at: data.comment.created_at,
                                        likes_count: 0,
                                        liked: false,
                                        user: data.user
                                    };
                                    
                                    // Add to comments and update total
                                    commentsSystem{{ $post->id }}.comments.unshift(newComment);
                                    commentsSystem{{ $post->id }}.totalComments++;
                                    
                                    // Reset textarea height
                                    this.$nextTick(() => {
                                        if (this.$refs.commentTextarea) {
                                            this.autoGrow(this.$refs.commentTextarea);
                                        }
                                    });
                                }
                                this.isSubmitting = false;
                            })
                            .catch(error => {
                                console.error('Error adding comment:', error);
                                this.isSubmitting = false;
                            });
                        }
                    }">
                    @csrf
                    <img src="{{ auth()->user()->profile->profileImage() }}" class="w-8 h-8 rounded-full object-cover">
                    <div class="flex-1 relative">
                        <div class="flex items-center">
                            <textarea
                                x-model="comment"
                                placeholder="Add a comment..."
                                class="w-full border rounded-md px-3 py-2 pr-16 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 overflow-hidden"
                                rows="1"
                                @input="autoGrow($event.target)"
                                x-ref="commentTextarea"></textarea>
                            <div x-show="isSubmitting" class="absolute right-16 top-1/2 transform -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <button
                                type="submit"
                                @click="submitForm"
                                :disabled="isSubmitting || comment.trim() === ''"
                                :class="{'opacity-50 cursor-not-allowed': isSubmitting || comment.trim() === ''}"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-500 font-semibold text-sm hover:text-blue-700">
                                Post
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Loading state -->
                <div x-show="loading" class="flex justify-center py-2">
                    <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <!-- Comments list -->
                <template x-if="!loading">
                    <div>
                        <template x-for="comment in comments" :key="comment.id">
                            <div class="flex items-start gap-2 mb-3 group">
                                <img :src="comment.user.profile_image || '/storage/profile/default-avatar.png'" class="w-7 h-7 rounded-full object-cover" :alt="comment.user.username">
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-semibold text-sm" x-text="comment.user.username"></span>
                                            <span class="text-sm" x-text="comment.comment"></span>
                                        </div>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="comment.user_id == {{ auth()->id() }}">
                                            <button @click="editComment(comment)" class="text-gray-500 hover:text-gray-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="deleteComment(comment.id)" class="text-gray-500 hover:text-red-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <button 
                                            @click="toggleLikeComment(comment)" 
                                            class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1"
                                            :class="{'text-red-500 hover:text-red-700': comment.liked}">
                                            <svg 
                                                xmlns="http://www.w3.org/2000/svg" 
                                                class="h-3 w-3" 
                                                :fill="comment.liked ? 'currentColor' : 'none'" 
                                                viewBox="0 0 24 24" 
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            <span x-text="comment.likes_count"></span>
                                        </button>
                                        <span class="text-xs text-gray-400" x-text="formatTimestamp(comment.created_at)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Load More / View All Comments -->
                        <div class="flex justify-between items-center mt-2">
                            <button 
                                x-show="hasMoreComments" 
                                @click="loadMoreComments" 
                                class="text-sm text-gray-500 hover:text-gray-700 font-medium flex items-center gap-1"
                                :class="{'opacity-75 cursor-wait': loadingMore}"
                                :disabled="loadingMore">
                                <span>Load more</span>
                                <svg x-show="loadingMore" class="animate-spin h-3 w-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                            <a href="/p/{{ $post->id }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                                View all <span x-text="totalComments"></span> comments
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    @endforeach

    <div class="my-8">
        {{ $posts->links() }}
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        @foreach($posts as $post)
        Alpine.data('commentsSystem{{ $post->id }}', () => ({
            comments: [],
            totalComments: {{ $post->comments ? $post->comments->count() : 0 }},
            loading: true,
            showEditModal: false,
            editCommentId: null,
            editCommentText: '',
            page: 1,
            hasMoreComments: false,
            loadingMore: false,
            
            init() {
                this.fetchComments();
            },
            
            autoGrow(el) {
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight) + 'px';
            },
            
            fetchComments(loadMore = false) {
                if (!loadMore) {
                    this.loading = true;
                    this.page = 1;
                } else {
                    this.loadingMore = true;
                }
                
                fetch(`/p/{{ $post->id }}/comments?page=${this.page}&limit=5`)
                    .then(response => response.json())
                    .then(data => {
                        if (loadMore) {
                            this.comments = [...this.comments, ...(data.data || [])];
                        } else {
                            this.comments = data.data || [];
                        }
                        
                        // Check if there are more comments to load
                        this.hasMoreComments = data.total > this.comments.length;
                        this.totalComments = data.total;
                        this.loading = false;
                        this.loadingMore = false;
                    })
                    .catch(error => {
                        console.error('Error fetching comments:', error);
                        this.loading = false;
                        this.loadingMore = false;
                    });
            },
            
            editComment(comment) {
                this.editCommentId = comment.id;
                this.editCommentText = comment.comment;
                this.showEditModal = true;
            },
            
            updateComment() {
                if (this.editCommentText.trim() === '') return;
                
                fetch(`/comments/${this.editCommentId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        comment: this.editCommentText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the comment in the UI
                        const commentIndex = this.comments.findIndex(c => c.id === this.editCommentId);
                        if (commentIndex !== -1) {
                            this.comments[commentIndex].comment = this.editCommentText;
                        }
                        
                        this.showEditModal = false;
                        this.editCommentId = null;
                        this.editCommentText = '';
                    }
                })
                .catch(error => {
                    console.error('Error updating comment:', error);
                });
            },
            
            deleteComment(commentId) {
                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the comment from the UI
                        this.comments = this.comments.filter(c => c.id !== commentId);
                        this.totalComments--;
                        
                        // Update comment count in the parent component
                        const parentDiv = this.$el.closest('.p-5').querySelector('.flex.items-center.gap-4.mb-3');
                        if (parentDiv && parentDiv.__x) {
                            parentDiv.__x.getUnobservedData().commentCount--;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error deleting comment:', error);
                });
            },
            
            toggleLikeComment(comment) {
                fetch(`/comments/${comment.id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the comment in the UI
                        const commentIndex = this.comments.findIndex(c => c.id === comment.id);
                        if (commentIndex !== -1) {
                            this.comments[commentIndex].liked = data.liked;
                            this.comments[commentIndex].likes_count = data.count;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error liking comment:', error);
                });
            },
            
            loadMoreComments() {
                this.page++;
                this.fetchComments(true);
            },
            
            formatTimestamp(timestamp) {
                const date = new Date(timestamp);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) {
                    return 'just now';
                } else if (diffInSeconds < 3600) {
                    return `${Math.floor(diffInSeconds / 60)}m ago`;
                } else if (diffInSeconds < 86400) {
                    return `${Math.floor(diffInSeconds / 3600)}h ago`;
                }
                
                return `${Math.floor(diffInSeconds / 86400)}d ago`;
            }
        }));
        @endforeach
    });
</script>
@endsection
