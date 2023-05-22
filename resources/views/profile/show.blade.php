@extends('layouts.app')

@section('page-title', __('Update Password'))

@section('content')
    <article>
        <div>
            @if(Auth::user()->password_updated_at !== null)
            <h1>@lang('Update Password')</h1>
            @else
            <h1>@lang('Activate account')</h1>

            <p>@lang('Choose a new strong password to activate you account.')</p>
            <p>@lang('Tip: a good password is long, easy to remember & type, but hard to guess. For example: unless someone who knows you well could guess it, "quantum danger banana bread puppy" is a good password.')</p>
            @endif

            <p>@lang('Your password must adhere to the following rules:')</p>
            <ul>
                <li>@lang('It must be at least 14 characters long.')</li>
                <li>@lang('Cannot be similar to your name or email address.')</li>
                <li>@lang('Is not a "common" password.')</li>
                <li>@lang('Cannot contain only numerics.')</li>
            </ul>

            <form
                class="horizontal-view"
                method="post"
                action="{{ route('profile.update_password') }}"
            >
                @csrf
                <div>
                    <label for="current_password">@lang('Current Password')</label>
                    <div>
                        <x-required />
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            required
                            autocomplete="current_password"
                            aria-describedby="current_password_error"
                        >
                        <x-input-error for="current_password" id="current_password_error" />
                    </div>
                </div>

                <div>
                    <label for="password">@lang('New Password')</label>
                    <div>
                        <x-required />
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="password"
                            aria-describedby="password_error"
                        >
                        <x-input-error for="password" id="password_error" />
                    </div>
                </div>

                <div>
                     <label for="password_confirmation">@lang('Confirm Password')</label>
                     <div>
                         <x-required />
                         <input
                             id="password_confirmation"
                             name="password_confirmation"
                             type="password"
                             required
                             autocomplete="password_confirmation"
                             aria-describedby="password_confirmation_error"
                         >
                        <x-input-error for="password_confirmation" id="password_confirmation_error" />
                    </div>
                </div>
                <button>@lang('Save')</button>
            </form>
        </div>
    </article>

@endsection
