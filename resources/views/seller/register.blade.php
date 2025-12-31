<x-guest-layout class="bg-custom-edebda">
    <x-authentication-card>
        <x-slot name="logo">
            <img src="img/logo.png" style="width: 100px; height: 100px;"/>
        </x-slot>

        <x-validation-errors class="mb-4" />
            seller
        <form method="POST" action="{{ route('seller.register.post') }}"  enctype="multipart/form-data">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-label for="phone" value="{{ __('TEL') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required />
            </div>

            <div class="mt-4">
                <x-label for="address" value="{{ __('Address') }}" />
                <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
            </div>

            <div class="mt-4">
                <x-label for="content" value="{{ __('content') }}" />
                {{-- <x-input id="content" class="block mt-1 w-full" type="textarea" name="content" :value="old('content')" required /> --}}
                <textarea id="content" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" style="background-color: white;" name="content" required>{{ old('content') }}</textarea>

            </div>

           
            <div class="mt-4">
                <x-label for="image" value="{{ __('Image') }}" />
                <x-input id="image" class="block mt-1 w-full" type="file" name="image" accept="image/png, image/jpeg, image/jpg" required />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <button type="button" onclick="window.history.back()" class="ml-4 bg-gray-500 hover:bg-gray-600 text-white font-bold py-1 px-4 rounded">
                    {{ __('Return') }}
                </button>
                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>