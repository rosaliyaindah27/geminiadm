<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gemini Pro Trader') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-blue-900 to-blue-800 px-6 pb-4">
                <div class="flex h-16 shrink-0 items-center">
                    <img class="h-8 w-auto" src="{{ asset('images/logo-white.png') }}" alt="Gemini Pro Trader">
                    <span class="ml-2 text-white font-bold text-lg">Admin Panel</span>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <!-- Dashboard -->
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="{{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:text-white hover:bg-blue-700' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                        Dashboard
                                    </a>
                                </li>

                                <!-- User Management -->
                                <li>
                                    <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                                        <button @click="open = !open" 
                                                class="text-blue-200 hover:text-white hover:bg-blue-700 group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold">
                                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                            User Management
                                            <svg :class="open ? 'rotate-90' : ''" class="ml-auto h-5 w-5 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                        <ul x-show="open" class="mt-1 px-2">
                                            <li><a href="{{ route('admin.users.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">All Users</a></li>
                                            <li><a href="{{ route('admin.users.create') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Add User</a></li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- Financial Management -->
                                <li>
                                    <div x-data="{ open: {{ request()->routeIs('admin.financial.*') ? 'true' : 'false' }} }">
                                        <button @click="open = !open" 
                                                class="text-blue-200 hover:text-white hover:bg-blue-700 group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold">
                                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Financial Management
                                            <svg :class="open ? 'rotate-90' : ''" class="ml-auto h-5 w-5 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                        <ul x-show="open" class="mt-1 px-2">
                                            <li><a href="{{ route('admin.financial.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Overview</a></li>
                                            <li><a href="{{ route('admin.financial.transactions') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Transactions</a></li>
                                            <li><a href="{{ route('admin.financial.subscriptions') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Subscriptions</a></li>
                                            <li><a href="{{ route('admin.financial.plans') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Plans</a></li>
                                            <li><a href="{{ route('admin.financial.revenue-report') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Revenue Report</a></li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- System Settings -->
                                <li>
                                    <div x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
                                        <button @click="open = !open" 
                                                class="text-blue-200 hover:text-white hover:bg-blue-700 group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold">
                                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            System Settings
                                            <svg :class="open ? 'rotate-90' : ''" class="ml-auto h-5 w-5 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                        <ul x-show="open" class="mt-1 px-2">
                                            <li><a href="{{ route('admin.settings.general') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">General</a></li>
                                            <li><a href="{{ route('admin.settings.payment') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Payment</a></li>
                                            <li><a href="{{ route('admin.settings.email') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Email</a></li>
                                            <li><a href="{{ route('admin.settings.security') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Security</a></li>
                                            <li><a href="{{ route('admin.settings.maintenance') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Maintenance</a></li>
                                            <li><a href="{{ route('admin.settings.cache') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Cache</a></li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- Notifications -->
                                <li>
                                    <div x-data="{ open: {{ request()->routeIs('admin.notifications.*') ? 'true' : 'false' }} }">
                                        <button @click="open = !open" 
                                                class="text-blue-200 hover:text-white hover:bg-blue-700 group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold">
                                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                            </svg>
                                            Notifications
                                            <svg :class="open ? 'rotate-90' : ''" class="ml-auto h-5 w-5 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                        <ul x-show="open" class="mt-1 px-2">
                                            <li><a href="{{ route('admin.notifications.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Overview</a></li>
                                            <li><a href="{{ route('admin.notifications.send') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Send Notification</a></li>
                                            <li><a href="{{ route('admin.notifications.templates') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Templates</a></li>
                                            <li><a href="{{ route('admin.notifications.history') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">History</a></li>
                                            <li><a href="{{ route('admin.notifications.system-alerts') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">System Alerts</a></li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- Content Management -->
                                <li>
                                    <div x-data="{ open: {{ request()->routeIs('admin.content.*') ? 'true' : 'false' }} }">
                                        <button @click="open = !open" 
                                                class="text-blue-200 hover:text-white hover:bg-blue-700 group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold">
                                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            Content Management
                                            <svg :class="open ? 'rotate-90' : ''" class="ml-auto h-5 w-5 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                        <ul x-show="open" class="mt-1 px-2">
                                            <li><a href="{{ route('admin.content.pages') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Pages</a></li>
                                            <li><a href="{{ route('admin.content.posts') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Blog Posts</a></li>
                                            <li><a href="{{ route('admin.content.faqs') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">FAQs</a></li>
                                            <li><a href="{{ route('admin.content.announcements') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Announcements</a></li>
                                        </ul>
                                    </div>
                                </li>

                                <!-- Audit & Logs -->
                                <li>
                                    <div x-data="{ open: {{ request()->routeIs('admin.audit.*') ? 'true' : 'false' }} }">
                                        <button @click="open = !open" 
                                                class="text-blue-200 hover:text-white hover:bg-blue-700 group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold">
                                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                            Audit & Logs
                                            <svg :class="open ? 'rotate-90' : ''" class="ml-auto h-5 w-5 shrink-0 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </button>
                                        <ul x-show="open" class="mt-1 px-2">
                                            <li><a href="{{ route('admin.audit.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Overview</a></li>
                                            <li><a href="{{ route('admin.audit.admin-logs') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Admin Logs</a></li>
                                            <li><a href="{{ route('admin.audit.system-logs') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">System Logs</a></li>
                                            <li><a href="{{ route('admin.audit.security-logs') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">Security Logs</a></li>
                                            <li><a href="{{ route('admin.audit.user-activity') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm leading-6 text-blue-200 hover:text-white hover:bg-blue-700">User Activity</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Back to User Dashboard -->
                        <li class="mt-auto">
                            <a href="{{ route('dashboard') }}" 
                               class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-blue-200 hover:bg-blue-700 hover:text-white">
                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                </svg>
                                Back to User Dashboard
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-data="{ open: false }" class="lg:hidden">
            <!-- Mobile menu button -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button @click="open = true" type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <span class="text-sm font-semibold leading-6 text-gray-900">Admin Panel</span>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Profile dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" type="button" class="-m-1.5 flex items-center p-1.5">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full bg-gray-50" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->name }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile sidebar -->
            <div x-show="open" class="relative z-50 lg:hidden" x-description="Off-canvas menu for mobile, show/hide based on off-canvas menu state.">
                <div x-show="open" class="fixed inset-0 bg-gray-900/80"></div>
                <div class="fixed inset-0 flex">
                    <div x-show="open" class="relative mr-16 flex w-full max-w-xs flex-1">
                        <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                            <button @click="open = false" type="button" class="-m-2.5 p-2.5">
                                <span class="sr-only">Close sidebar</span>
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <!-- Mobile sidebar content (same as desktop) -->
                        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gradient-to-b from-blue-900 to-blue-800 px-6 pb-4">
                            <!-- Same content as desktop sidebar -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-72">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="relative flex flex-1">
                        <h1 class="text-xl font-semibold leading-6 text-gray-900">@yield('title', 'Admin Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Profile dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" type="button" class="-m-1.5 flex items-center p-1.5">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full bg-gray-50" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ auth()->user()->name }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.53a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>