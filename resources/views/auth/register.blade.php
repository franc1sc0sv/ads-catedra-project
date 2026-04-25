@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 mb-1">Create account</h2>
    <p class="text-sm text-gray-500 mb-8">Fill in your details to get started.</p>

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-5">
        @csrf

        <x-ui.input name="name" label="Full name" type="text" autocomplete="name" autofocus />

        <x-ui.input name="email" label="Email address" type="email" autocomplete="email" />

        <x-ui.input name="password" label="Password" type="password" autocomplete="new-password" />

        <x-ui.input name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" />

        <x-ui.button>Create account</x-ui.button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-primary font-medium hover:underline">Sign in</a>
    </p>
@endsection
