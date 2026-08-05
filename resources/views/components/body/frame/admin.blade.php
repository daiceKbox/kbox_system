@props([
    'name'  =>  "",
])

@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- csrf-token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- noindex --}}
    {{-- <meta name="robots" content="noindex" /> --}}
    {{-- google font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- google font zen maru gothic --}}
    <link href="https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic&display=swap" rel="stylesheet">
    {{-- google font zen old mincho --}}
    <link href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho&display=swap" rel="stylesheet">
    {{-- font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- title --}}
    <title>{{ $title ? $title . " " : null }} KBOX SYSTEM</title>
    {{-- css --}}
    <link rel="stylesheet" href="{{url("/css/style.css")}}">
    @if($name && file_exists(public_path("css/".$name.".css")))
        <link rel="stylesheet" href="{{ url('css/'.$name.'.css') }}">
    @endif
    {{-- add head --}}
    {!! $head ?? null !!}
</head>
<body id={{ $name ?? null }}>
    {{-- header --}}
    <header>
        <h1><a href="{{ url('/') }}">KBOX SYSTEM</a></h1>
        {{-- <x-parts.header.user_portal :user="auth()->user()" /> --}}
        <h2>{{ $title ?? null }}</h2>
        {{-- <x-parts.header.page_transition /> --}}
        {{ $header ?? null }}
    </header>
    {{-- main --}}
    <main>
        {{ $main ?? null }}
    </main>
    {{-- footer --}}
    <footer>
        {{ $footer ?? null }}
        {{-- <x-parts.footer.copyright name="Kitazumi Shiki Inc." /> --}}
    </footer>
    {{-- UI parts --}}
    <div class="hidden" style="display: none;">
        {{ $hidden ?? null }}
    </div>
    @auth
        <form id="form-logout" style="display: none;" action="{{ asset('logout') }}" name="logout" method="POST"> @csrf @method("post")</form>
    @endauth
    {{-- <x-script.to_the_top /> --}}
    {{-- script --}}
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    {{ $script ?? null }}
</body>
</html>
