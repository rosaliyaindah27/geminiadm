<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Trading Bot Hub') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Available Bots Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">Available Trading Bots</h3>
                    
                    @if($availableBots->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($availableBots as $bot)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $bot->name }}</h4>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($bot->bot_type === 'scalping') bg-red-100 text-red-800
                                            @elseif($bot->bot_type === 'swing') bg-blue-100 text-blue-800
                                            @elseif($bot->bot_type === 'grid') bg-green-100 text-green-800
                                            @elseif($bot->bot_type === 'dca') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($bot->bot_type) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-sm mb-4">{{ $bot->description }}</p>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Min Balance:</span>
                                            <span class="font-medium">${{ number_format($bot->min_balance, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Version:</span>
                                            <span class="font-medium text-blue-600">{{ $bot->version }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Exchanges:</span>
                                            <span class="font-medium text-gray-600">
                                                {{ count($bot->supported_exchanges) }} supported
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @php
                                        $userHasBot = $userBots->where('trading_bot_id', $bot->id)->first();
                                    @endphp
                                    
                                    @if($userHasBot)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-green-600 font-medium">✓ Downloaded</span>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('bots.show', $bot) }}" 
                                                   class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                                    View Details
                                                </a>
                                                @if($userHasBot->is_active)
                                                    <form action="{{ route('bots.deactivate', $bot) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                                            Deactivate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('bots.activate', $bot) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
                                                            Activate
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-500">Not downloaded</span>
                                            <a href="{{ route('bots.show', $bot) }}" 
                                               class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                                Download Bot
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-lg mb-2">No trading bots available</div>
                            <p class="text-gray-500">Check back later for new trading bots.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- My Active Bots Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">My Active Bots</h3>
                    
                    @php
                        $activeBots = $userBots->where('is_active', true);
                    @endphp
                    
                    @if($activeBots->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bot Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Strategy
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Activated
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activeBots as $userBot)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $userBot->tradingBot->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                    @if($userBot->tradingBot->bot_type === 'scalping') bg-red-100 text-red-800
                                                    @elseif($userBot->tradingBot->bot_type === 'swing') bg-blue-100 text-blue-800
                                                    @elseif($userBot->tradingBot->bot_type === 'grid') bg-green-100 text-green-800
                                                    @elseif($userBot->tradingBot->bot_type === 'dca') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($userBot->tradingBot->bot_type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                    Active
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $userBot->activated_at ? $userBot->activated_at->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('bots.show', $userBot->tradingBot) }}" 
                                                       class="text-blue-600 hover:text-blue-900">
                                                        View
                                                    </a>
                                                    <form action="{{ route('bots.deactivate', $userBot->tradingBot) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900"
                                                                onclick="return confirm('Are you sure you want to deactivate this bot?')">
                                                            Deactivate
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-lg mb-2">No active bots</div>
                            <p class="text-gray-500">Download and activate a trading bot to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>