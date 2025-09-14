<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Seller-Profile') }}
        </h2>
    </x-slot>
    

    <div>
        <div style="background-color: #F8F7EE" class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <section class="text-gray-600 body-font">
              <form method="post" action="{{ route('seller.profile.update', ['id' => $user->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="container px-5 py-24 mx-auto flex flex-col">
                  
                  <div class="lg:w-4/6 mx-auto">
                    <div class="rounded-lg h-64 overflow-hidden">
                      <img alt="content" class="object-cover object-center h-full w-full" src="{{ asset('storage/images/'.$user->image) }}" style="object-fit: contain; max-height: 100%;" >
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
                          <h2 class="text-base">Name of Restaurant<input type="text" id="name" name="name" value="{{ $user->name }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></h2>
                          {{-- <div class="w-12 h-1 bg-indigo-500 rounded mt-2 mb-4"></div> --}}
                          <p class="text-base">Number<input type="text" id="phone" name="phone" value="{{ $user->phone }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></p>
                          <p class="text-base">Email<input type="text" id="email" name="email" value="{{ $user->email }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></p>
                          <p class="text-base">Address<input type="text" id="address" name="address" value="{{ $user->address }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out text-center"></p>
                        </div>
                      </div>
                      <div class=" sm:w-2/3 sm:pl-8 sm:py-8 sm:border-l border-gray-200 sm:border-t-0 border-t mt-4 pt-4 sm:mt-0 text-center sm:text-left">
                        <p class="mt-10 leading-relaxed text-lg mb-4"><textarea id="content" name="content" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out resize-none" style="height: 200px; white-space: pre-wrap; word-wrap: break-word;" >{{ $user->content }}</textarea></p>
                      {{-- </div>   --}}
        
                    {{-- @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))

                        <div class="mt-10 sm:mt-0">
                            @livewire('profile.update-password-form')
                        </div>
        
                        <x-section-border />
                    @endif --}}
                      <div style="text-align: right;">
                        <x-button type="submit">
                            {{ __('edit') }}
                          </x-button>
                      </div>
                        {{-- <a class="text-indigo-500 inline-flex items-center">Learn More
                          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                          </svg>
                        </a> --}}
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </section>
            {{-- @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif --}}

            {{-- @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif  --}}

            {{-- @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div> --}}

            {{-- @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif --}}
            <section class="text-gray-600 body-font">
              <div class="container px-5 py-24 mx-auto">
                <div class="flex flex-col text-center w-full mb-20">
                  {{-- <h2 class="text-xs text-indigo-500 tracking-widest font-medium title-font mb-1">ROOF PARTY POLAROID</h2> --}}
                  <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Reviews</h1>
                  {{-- <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Whatever cardigan tote bag tumblr hexagon brooklyn asymmetrical gentrify, subway tile poke farm-to-table. Franzen you probably haven't heard of them man bun deep jianbing selfies heirloom prism food truck ugh squid celiac humblebrag.</p> --}}
                </div> 
                <div class="flex flex-wrap">
                @foreach($reviews as $review) 
                  <div class="xl:w-1/4 lg:w-1/2 md:w-full px-8 py-6 border-l-2 border-gray-200 border-opacity-60">
                    <h2 class="text-lg sm:text-xl text-gray-900 font-medium title-font mb-2">{{ $review->product->name }}</h2>
                    <img alt="ecommerce" class="object-cover object-center w-100% block" src="{{ asset('storage/images/'.$review->product->image) }}">
                    <p class="mt-1"><i class="fas fa-tag fa-rotate-90" style="margin-right: 10px;"></i>{{ $review->product->category}}</p>
                    <p class="mt-1">{{ $review->product->available_time }}</p>
                    {{-- <p class="leading-relaxed text-base mb-4">{{ $review->product->content }}</p> --}}
                    <p class="mt-1">Price:{{ $review->product->price }}Php</p>
                    {{-- <a class="text-indigo-500 inline-flex items-center">Learn More
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                      </svg>
                    </a> --}}
                  </div>
                  <div class="xl:w-1/4 lg:w-1/2 md:w-full px-8 py-6 border-l-2 border-gray-200 border-opacity-60">
                    {{-- <h2 class="text-lg sm:text-xl text-gray-900 font-medium title-font mb-2">Shooting Stars</h2> --}}
                    <h2 class="text-lg sm:text-xl text-gray-900 font-medium title-font mb-2">ユーザー名:{{ $review->user->name }}</h2>
                    <h2 class="leading-relaxed text-base mb-4">投稿日:{{ \Carbon\Carbon::parse($review->created_at)->format('Y-m-d') }}</h2>
                    {{-- <a class="text-indigo-500 inline-flex items-center">Learn More
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                      </svg>
                    </a> --}}
                  </div>
                  <div class="xl:w-2/4 lg:w-1/2 md:w-full px-8 py-6 border-l-2 border-gray-200 border-opacity-60">
                    <h2 class="text-lg sm:text-xl text-gray-900 font-medium title-font mb-2">評価:{{ $review->rating }}</h2>
                    <h3 class="text-lg sm:text-xl text-gray-900 font-medium title-font mb-2">{{ $review->name }}</h3>
                    <p class="leading-relaxed text-base mb-4">{{ $review->content }}</p>
                    {{-- <a class="text-indigo-500 inline-flex items-center">Learn More
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                      </svg>
                    </a> --}}
                  </div>
                  {{-- <div class="xl:w-1/4 lg:w-1/2 md:w-full px-8 py-6 border-l-2 border-gray-200 border-opacity-60">
                    <h2 class="text-lg sm:text-xl text-gray-900 font-medium title-font mb-2">Melanchole</h2>
                    <p class="leading-relaxed text-base mb-4">Fingerstache flexitarian street art 8-bit waistcoat. Distillery hexagon disrupt edison bulbche.</p>
                    <a class="text-indigo-500 inline-flex items-center">Learn More
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-2" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                      </svg>
                    </a>
                  </div> --}}
                @endforeach  
                </div>
                {{-- <button class="flex mx-auto mt-16 text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Button</button> --}}
              </div>
            </section>
        </div>
    </div>
</x-app-layout>