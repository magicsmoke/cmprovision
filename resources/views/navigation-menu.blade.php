<nav x-data="{ open: false, darkMode: localStorage.getItem('darkMode') === 'true' }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
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

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Dark mode toggle -->
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)"
                        class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                    <!-- Moon icon (shown in light mode) -->
                    <svg x-show="!darkMode" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <!-- Sun icon (shown in dark mode) -->
                    <svg x-show="darkMode" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                @if (config('app.password'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <!-- Mobile dark mode toggle -->
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.documentElement.classList.toggle('dark', darkMode)"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out mr-1">
                    <svg x-show="!darkMode" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
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
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
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
