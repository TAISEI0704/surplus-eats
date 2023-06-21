<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-jet-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-jet-section-border />
            @endif

            {{-- @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-jet-section-border />
            @endif --}}

            <div class="mt-10 sm:mt-0">
                {{-- @livewire('profile.logout-other-browser-sessions-form') --}}
                Purchase History
                <section class="text-gray-600 body-font">
                    <div class="container px-5 py-24 mx-auto">
                    {{-- @foreach($purchaseHistories as $purchaseHistory) --}}
                      <div class="flex flex-wrap -m-4">
                        @foreach($purchaseHistories as $purchaseHistory)
                        <div class="lg:w-1/4 md:w-1/2 p-4 w-full">
                          <a class="block relative h-48 rounded overflow-hidden">
                            <img alt="ecommerce" class="object-cover object-center w-full h-full block" src="{{ asset('storage/images/'.$purchaseHistory->product->image) }}">
                          </a>
                          <div class="mt-4">
                            <h3 class="text-gray-500 text-xs tracking-widest title-font mb-1">{{ $purchaseHistory->product->seller->name }}</h3>
                            <h2 class="text-gray-900 title-font text-lg font-medium">商品名:{{ $purchaseHistory->product->name }}</h2>
                            <p class="mt-1">金額:{{ $purchaseHistory->product->price }} Php</p>
                            <p class="mt-1">数量:{{ $purchaseHistory->purchase_quantity }}個</p>
                            <p class="mt-1">合計金額:{{ $purchaseHistory->purchase_quantity*$purchaseHistory->product->price }} Php</p>
                            <p class="mt-1">購入日:{{ date('Y-m-d H:i', strtotime($purchaseHistory->created_at)) }}</p>
                          </div>
                          <a href="{{ route('review.create') }}">
                            <button>
                             {{ __('Feedback') }}
                            </button>
                          </a>  
                        </div>
                      @endforeach
                      </div>
                    {{-- @endforeach --}}
                    </div>
                </section>
            </div> 

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-jet-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
