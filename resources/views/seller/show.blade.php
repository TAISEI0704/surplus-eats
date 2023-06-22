<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Seller-Profile') }}
        </h2>
    </x-slot>
    

    <div>
        <div style="background-color: #F8F7EE" class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <section class="text-gray-600 body-font">
                <div class="container px-5 py-24 mx-auto flex flex-col">
                  
                  <div class="lg:w-4/6 mx-auto">
                    <div class="rounded-lg h-64 overflow-hidden">
                      <img alt="content" class="object-cover object-center h-full w-full" src="{{ asset('storage/images/'.$user->image) }}" style="object-fit: contain; max-height: 100%;" >
                      {{-- <input type="file" id="image" name="image"> --}}
                    </div>
                    <div style="text-align: center;">
                      <input type="file" id="image" name="image" style="margin-top: 10px; text-align: center;">
                    </div>
                    <div class="flex flex-col sm:flex-row mt-10">
                      <div class="sm:w-1/3 text-center sm:pr-8 sm:py-8">
                        {{-- <div class="w-20 h-20 rounded-full inline-flex items-center justify-center bg-gray-200 text-gray-400">
                          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-10 h-10" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                          </svg>
                        </div> --}}
                        <div class="flex flex-col items-center text-center justify-center">
                          <h2 class="font-medium title-font mt-4 text-gray-900 text-lg">name of restaurant<input type="text" id="name" name="name" value="{{ $user->name }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></h2>
                          <div class="w-12 h-1 bg-indigo-500 rounded mt-2 mb-4"></div>
                          <p class="text-base">number<input type="text" id="name" name="name" value="{{ $user->phone }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></p>
                          <p class="text-base">email<input type="text" id="name" name="name" value="{{ $user->email }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></p>
                          <p class="text-base">address<input type="text" id="name" name="name" value="{{ $user->address }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></p>
                        </div>
                      </div>
                      <div class="sm:w-2/3 sm:pl-8 sm:py-8 sm:border-l border-gray-200 sm:border-t-0 border-t mt-4 pt-4 sm:mt-0 text-center sm:text-left">
                        <p class="leading-relaxed text-lg mb-4"><textarea id="name" name="name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out resize-none" style="height: 200px; white-space: pre-wrap; word-wrap: break-word;" >{{ $user->content }}</textarea></p>
                        
        
                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::sellerUpdate()))
                        <div class="mt-10 sm:mt-0">
                            @livewire('profile.update-password-form')
                        </div>
        
                        <x-jet-section-border />
                    @endif
                        <x-jet-button>
                            {{ __('edit') }}
                          </x-jet-button>
                        {{-- <a class="text-indigo-500 inline-flex items-center">Learn More
                          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                          </svg>
                        </a> --}}
                      </div>
                    </div>
                  </div>
                </div>
            </section>
            {{-- @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-jet-section-border />
            @endif --}}

            {{-- @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-jet-section-border />
            @endif  --}}

            {{-- @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-jet-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div> --}}

            {{-- @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-jet-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif --}}
        </div>
    </div>
</x-app-layout>