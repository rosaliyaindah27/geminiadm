<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $bot->name }}
            </h2>
            <a href="{{ route('bots.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Bots
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Bot Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $bot->name }}</h1>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                                            @if($bot->bot_type === 'scalping') bg-red-100 text-red-800
                                            @elseif($bot->bot_type === 'swing') bg-blue-100 text-blue-800
                                            @elseif($bot->bot_type === 'grid') bg-green-100 text-green-800
                                            @elseif($bot->bot_type === 'dca') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($bot->bot_type) }} Strategy
                                        </span>
                                        <span class="text-sm text-gray-500">Version {{ $bot->version }}</span>
                                    </div>
                                </div>
                                @if($userBot)
                                    <div class="text-right">
                                        <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">
                                            ✓ Downloaded
                                        </span>
                                        @if($userBot->is_active)
                                            <div class="mt-2">
                                                <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    Active
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <p class="text-gray-700 text-lg mb-6">{{ $bot->description }}</p>

                            <!-- Bot Statistics -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">Min Balance</div>
                                    <div class="text-lg font-semibold text-gray-900">${{ number_format($bot->min_balance, 2) }}</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">Exchanges</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ count($bot->supported_exchanges) }}</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">Trading Pairs</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ count($bot->supported_pairs) }}</div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">License Required</div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        @if($bot->requires_license)
                                            <span class="text-orange-600">Yes</span>
                                        @else
                                            <span class="text-green-600">No</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            @if(isset($bot->default_config['features']))
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Features</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($bot->default_config['features'] as $feature)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $feature }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Installation Guide -->
                            @if($bot->installation_guide)
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Installation Guide</h3>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm text-gray-700">{{ $bot->installation_guide }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Download/Action Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bot Actions</h3>
                            
                            @if($userBot)
                                @if($userBot->is_active)
                                    <div class="space-y-3">
                                        <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg font-medium">
                                            ✓ Bot Active
                                        </button>
                                        <form action="{{ route('bots.deactivate', $bot) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="w-full bg-red-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-red-700"
                                                    onclick="return confirm('Are you sure you want to deactivate this bot?')">
                                                Deactivate Bot
                                            </button>
                                        </form>
                                        @if($bot->requires_license && $userBot->license_key)
                                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                                <div class="text-sm text-blue-800 font-medium">License Key:</div>
                                                <div class="text-sm text-blue-600 font-mono">{{ $userBot->license_key }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        <form action="{{ route('bots.activate', $bot) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700">
                                                Activate Bot
                                            </button>
                                        </form>
                                        <form action="{{ route('bots.download', $bot) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-gray-700">
                                                Re-download Bot
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <form action="{{ route('bots.download', $bot) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700">
                                        Download Bot
                                    </button>
                                </form>
                                <p class="text-sm text-gray-500 mt-3">
                                    Download this bot to start automated trading with your preferred exchange.
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Supported Exchanges -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Supported Exchanges</h3>
                            <div class="space-y-2">
                                @foreach($bot->supported_exchanges as $exchange)
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                        <span class="text-sm text-gray-700">{{ $exchange }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Supported Trading Pairs -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Supported Trading Pairs</h3>
                            <div class="space-y-2">
                                @foreach($bot->supported_pairs as $pair)
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-sm text-gray-700 font-mono">{{ $pair }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Requirements -->
                    @if(isset($bot->default_config['requirements']))
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Requirements</h3>
                                <div class="space-y-2">
                                    @foreach($bot->default_config['requirements'] as $requirement)
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                            <span class="text-sm text-gray-700">{{ $requirement }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>