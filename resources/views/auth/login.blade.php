@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h2>
    <p class="text-sm text-gray-500 mb-8">Sign in to your account to continue.</p>

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
        @csrf

        <x-ui.input name="email" label="Email address" type="email" autocomplete="email" autofocus />

        <x-ui.input name="password" label="Password" type="password" autocomplete="current-password" />

        <x-ui.button>Sign in</x-ui.button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">Register</a>
    </p>
@endsection
