
@extends('user.layout.layout')
@section('content')
   <p class="mb-4 text-sm text-gray-600">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
        If you didn't receive the email, we will gladly send you another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <p class="mb-4 font-medium text-sm text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </p>
    @endif

    <div class="mt-4 flex justify-between items-center">

        <!-- Resend Verification Email -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Resend Verification Email
            </button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="underline text-sm text-gray-600 hover:text-gray-900">
                Log Out
            </button>
        </form>

    </div>

@endsection
