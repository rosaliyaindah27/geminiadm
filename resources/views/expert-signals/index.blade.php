<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expert Signals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter and Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                            <div>
                                <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status_filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="closed">Closed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="signal_filter" class="block text-sm font-medium text-gray-700 mb-1">Signal Type</label>
                                <select id="signal_filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">All Signals</option>
                                    <option value="BUY">Buy</option>
                                    <option value="SELL">Sell</option>
                                    <option value="HODL">Hold</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="pair_filter" class="block text-sm font-medium text-gray-700 mb-1">Trading Pair</label>
                                <select id="pair_filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">All Pairs</option>
                                    <option value="BTCUSDT">BTC/USDT</option>
                                    <option value="ETHUSDT">ETH/USDT</option>
                                    <option value="BNBUSDT">BNB/USDT</option>
                                    <option value="ADAUSDT">ADA/USDT</option>
                                    <option value="DOTUSDT">DOT/USDT</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <button onclick="applyFilters()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                Apply Filters
                            </button>
                            <button onclick="clearFilters()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Signals -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Latest Expert Signals</h3>
                    
                    @if($latestSignals->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($latestSignals as $signal)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-semibold text-lg
                                                    @if($signal->signal === 'BUY') text-green-600
                                                    @elseif($signal->signal === 'SELL') text-red-600
                                                    @else text-yellow-600
                                                    @endif">
                                                    {{ $signal->signal }}
                                                </span>
                                                <span class="text-gray-600 font-medium">{{ $signal->symbol }}</span>
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $signal->timeframe }}</div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($signal->status === 'active') bg-green-100 text-green-800
                                            @elseif($signal->status === 'closed') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($signal->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Entry:</span>
                                            <span class="font-medium">${{ number_format($signal->entry_price, 4) }}</span>
                                        </div>
                                        
                                        @if($signal->take_profit)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">TP:</span>
                                                <span class="font-medium text-green-600">${{ number_format($signal->take_profit, 4) }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($signal->stop_loss)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">SL:</span>
                                                <span class="font-medium text-red-600">${{ number_format($signal->stop_loss, 4) }}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Confidence:</span>
                                            <span class="font-medium">{{ $signal->confidence }}%</span>
                                        </div>
                                    </div>
                                    
                                    @if($signal->reasoning)
                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <p class="text-xs text-gray-600 line-clamp-2">{{ $signal->reasoning }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3 flex justify-between items-center text-xs text-gray-500">
                                        <span>{{ $signal->created_at->format('M d, H:i') }}</span>
                                        <span>by {{ $signal->creator->name ?? 'Expert' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="#all-signals" class="text-blue-600 hover:text-blue-800 font-medium">
                                View All Signals →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">No Expert Signals Available</h4>
                            <p class="text-gray-600">Expert signals will appear here when they are published by our trading experts.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- All Signals Table -->
            <div id="all-signals" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">All Expert Signals</h3>
                        <div class="text-sm text-gray-600">
                            Showing {{ $signals->firstItem() ?? 0 }} to {{ $signals->lastItem() ?? 0 }} of {{ $signals->total() }} signals
                        </div>
                    </div>
                    
                    @if($signals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pair</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entry</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TP/SL</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($signals as $signal)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                                        @if($signal->signal === 'BUY') bg-green-100 text-green-800
                                                        @elseif($signal->signal === 'SELL') bg-red-100 text-red-800
                                                        @else bg-yellow-100 text-yellow-800
                                                        @endif">
                                                        {{ $signal->signal }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $signal->symbol }}</div>
                                                <div class="text-sm text-gray-500">{{ $signal->timeframe }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($signal->entry_price, 4) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($signal->take_profit)
                                                    <div class="text-green-600">TP: ${{ number_format($signal->take_profit, 4) }}</div>
                                                @endif
                                                @if($signal->stop_loss)
                                                    <div class="text-red-600">SL: ${{ number_format($signal->stop_loss, 4) }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $signal->confidence }}%
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    @if($signal->status === 'active') bg-green-100 text-green-800
                                                    @elseif($signal->status === 'closed') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($signal->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $signal->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button onclick="viewSignalDetails({{ $signal->id }})" 
                                                        class="text-blue-600 hover:text-blue-900">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $signals->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600">No signals found matching your criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Signal Details Modal -->
    <div id="signalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Signal Details</h3>
                        <button onclick="closeSignalModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="signalModalContent">
                        <!-- Signal details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            const status = document.getElementById('status_filter').value;
            const signal = document.getElementById('signal_filter').value;
            const pair = document.getElementById('pair_filter').value;
            
            const params = new URLSearchParams();
            if (status) params.append('status', status);
            if (signal) params.append('signal', signal);
            if (pair) params.append('pair', pair);
            
            window.location.href = '{{ route("expert-signals.index") }}?' + params.toString();
        }
        
        function clearFilters() {
            document.getElementById('status_filter').value = '';
            document.getElementById('signal_filter').value = '';
            document.getElementById('pair_filter').value = '';
            window.location.href = '{{ route("expert-signals.index") }}';
        }
        
        async function viewSignalDetails(signalId) {
            try {
                const response = await fetch(`/expert-signals/${signalId}`);
                const data = await response.json();
                
                if (data.success) {
                    displaySignalModal(data.signal);
                } else {
                    alert('Error loading signal details');
                }
            } catch (error) {
                alert('Error loading signal details');
                console.error('Error:', error);
            }
        }
        
        function displaySignalModal(signal) {
            const modal = document.getElementById('signalModal');
            const content = document.getElementById('signalModalContent');
            
            const signalClass = signal.signal === 'BUY' ? 'bg-green-50 border-green-200' : 
                               signal.signal === 'SELL' ? 'bg-red-50 border-red-200' : 
                               'bg-yellow-50 border-yellow-200';
            
            const signalColor = signal.signal === 'BUY' ? 'text-green-800' : 
                               signal.signal === 'SELL' ? 'text-red-800' : 
                               'text-yellow-800';
            
            content.innerHTML = `
                <div class="border rounded-lg p-4 ${signalClass} mb-4">
                    <div class="text-center mb-4">
                        <div class="text-3xl font-bold ${signalColor}">${signal.signal}</div>
                        <div class="text-lg text-gray-600">${signal.symbol} - ${signal.timeframe}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-sm text-gray-600">Entry Price</div>
                            <div class="text-xl font-semibold">$${signal.entry_price}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-600">Confidence</div>
                            <div class="text-xl font-semibold">${signal.confidence}%</div>
                        </div>
                    </div>
                    
                    ${signal.take_profit || signal.stop_loss ? `
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        ${signal.take_profit ? `
                        <div class="text-center">
                            <div class="text-sm text-gray-600">Take Profit</div>
                            <div class="text-lg font-semibold text-green-600">$${signal.take_profit}</div>
                        </div>
                        ` : '<div></div>'}
                        
                        ${signal.stop_loss ? `
                        <div class="text-center">
                            <div class="text-sm text-gray-600">Stop Loss</div>
                            <div class="text-lg font-semibold text-red-600">$${signal.stop_loss}</div>
                        </div>
                        ` : '<div></div>'}
                    </div>
                    ` : ''}
                </div>
                
                ${signal.reasoning ? `
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-900 mb-2">Analysis & Reasoning</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700">${signal.reasoning}</p>
                    </div>
                </div>
                ` : ''}
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium ml-2">${signal.status.charAt(0).toUpperCase() + signal.status.slice(1)}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium ml-2">${new Date(signal.created_at).toLocaleString()}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Expert:</span>
                        <span class="font-medium ml-2">${signal.creator ? signal.creator.name : 'Trading Expert'}</span>
                    </div>
                    ${signal.updated_at !== signal.created_at ? `
                    <div>
                        <span class="text-gray-600">Updated:</span>
                        <span class="font-medium ml-2">${new Date(signal.updated_at).toLocaleString()}</span>
                    </div>
                    ` : '<div></div>'}
                </div>
            `;
            
            modal.classList.remove('hidden');
        }
        
        function closeSignalModal() {
            document.getElementById('signalModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('signalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSignalModal();
            }
        });
    </script>
</x-app-layout>