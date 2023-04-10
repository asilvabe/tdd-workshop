<h1>{{ trans('posts.title') }}</h1>

<ul>
    @foreach($posts as $post)
        <li>{{ $post->title }} - {{ $post->created_at->format('d/m/Y') }}</li>
    @endforeach
</ul>

{{ $posts->links() }}
