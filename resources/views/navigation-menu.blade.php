<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-jet-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-jet-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('cms') }}" :active="request()->routeIs('cms')">
                        {{ __('CMs') }}
                    </x-jet-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('images') }}" :active="request()->routeIs('images')">
                        {{ __('Images') }}
                    </x-jet-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('projects') }}" :active="request()->routeIs('projects')">
                        {{ __('Projects') }}
                    </x-jet-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('scripts') }}" :active="request()->routeIs('scripts')">
                        {{ __('Scripts') }}
                    </x-jet-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('labels') }}" :active="request()->routeIs('labels')">
                        {{ __('Labels') }}
                    </x-jet-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('firmware') }}" :active="request()->routeIs('firmware')">
                        {{ __('Firmware') }}
                    </x-jet-nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                        {{ __('Other settings') }}
                    </x-jet-nav-link>
                </div>
            </div>

            @if (config('app.password'))
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            @endif

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
            <x-jet-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('cms') }}" :active="request()->routeIs('cms')">
                {{ __('CMs') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('images') }}" :active="request()->routeIs('images')">
                {{ __('Images') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('projects') }}" :active="request()->routeIs('projects')">
                {{ __('Projects') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('scripts') }}" :active="request()->routeIs('scripts')">
                {{ __('Scripts') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('labels') }}" :active="request()->routeIs('labels')">
                {{ __('Labels') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('firmware') }}" :active="request()->routeIs('firmware')">
                {{ __('Firmware') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                {{ __('Other settings') }}
            </x-jet-responsive-nav-link>
        </div>

        @if (config('app.password'))
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-jet-responsive-nav-link href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-jet-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endif
    </div>
</nav>
