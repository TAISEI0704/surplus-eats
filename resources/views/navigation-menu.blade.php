<nav x-data="{ open: false }" style="background-color: #F8F7EE" class="border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->is_seller ? route('seller.dashboard') : route('dashboard') }}">
                        {{-- <x-application-mark class="block h-9 w-auto" /> --}}
                        <img src="{{ asset('img/logo.png') }}" alt="" class="block h-20 w-auto">
                    </a>
                </div>


                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex ">
                    <x-nav-link href="{{ Auth::user()->is_seller ? route('seller.dashboard') : route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Product List') }}
                    </x-nav-link>
                </div>
                @if (Auth::user()->is_seller == true)
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('post.create')" :active="request()->routeIs('post.create')">
                        {{ __('Create Post') }}
                    </x-nav-link>
                </div>
                @endif
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    {{ Auth::user()->name }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            @if (Auth::user()->is_seller == false)
                                <x-dropdown-link href="{{ route('cart.show', Auth::user()->id) }}">
                                    {{ __('Cart') }}
                                </x-dropdown-link>
                            @endif

                            @if (Auth::user()->is_seller == true)
                                <x-dropdown-link href="{{ route('notification.show') }}">
                                    {{ __('Notifications') }}
                                </x-dropdown-link>
                            @endif
                            @if (Auth::user()->is_seller == false)
                                <x-dropdown-link href="{{ route('user.notification.show') }}">
                                    {{ __('Notifications') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link href="{{ Auth::user()->is_seller ? route('seller.profile.show') : route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ Auth::user()->is_seller ? route('seller.logout') : route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ Auth::user()->is_seller ? route('seller.logout') : route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ Auth::user()->is_seller ? route('seller.dashboard') : route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ Auth::user()->is_seller ? route('seller.profile.show') : route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ Auth::user()->is_seller ? route('seller.logout') : route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ Auth::user()->is_seller ? route('seller.logout') : route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>