public function like(Post $post)
{
    $liked = $post->likes()->where('user_id', auth()->id())->exists();

    if ($liked) {
        $post->likes()->where('user_id', auth()->id())->delete();
    } else {
        $post->likes()->create([
            'user_id' => auth()->id()
        ]);
    }

    return response()->json([
        'liked' => !$liked,
        'count' => $post->likes()->count()
    ]);
}