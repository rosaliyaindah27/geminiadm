<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gemini RealTime Signal') }}
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Signal Generator -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Generate Real-Time Signal</h3>
                            
                            <form id="signalForm" class="space-y-6">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="symbol" class="block text-sm font-medium text-gray-700 mb-2">
                                            Trading Pair
                                        </label>
                                        <select id="symbol" name="symbol" required 
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select a trading pair</option>
                                            <option value="BTCUSDT">BTC/USDT</option>
                                            <option value="ETHUSDT">ETH/USDT</option>
                                            <option value="BNBUSDT">BNB/USDT</option>
                                            <option value="ADAUSDT">ADA/USDT</option>
                                            <option value="DOTUSDT">DOT/USDT</option>
                                            <option value="LINKUSDT">LINK/USDT</option>
                                            <option value="LTCUSDT">LTC/USDT</option>
                                            <option value="XRPUSDT">XRP/USDT</option>
                                            <option value="SOLUSDT">SOL/USDT</option>
                                            <option value="MATICUSDT">MATIC/USDT</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="timeframe" class="block text-sm font-medium text-gray-700 mb-2">
                                            Timeframe
                                        </label>
                                        <select id="timeframe" name="timeframe" required 
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="1m">1 Minute</option>
                                            <option value="5m" selected>5 Minutes</option>
                                            <option value="15m">15 Minutes</option>
                                            <option value="1h">1 Hour</option>
                                            <option value="4h">4 Hours</option>
                                            <option value="1d">1 Day</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="analysis_type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Analysis Type
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="analysis_type" value="technical" checked 
                                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm">Technical Analysis</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="analysis_type" value="sentiment" 
                                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm">Sentiment Analysis</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="analysis_type" value="combined" 
                                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                                            <span class="text-sm">Combined Analysis</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-center">
                                    <button type="submit" id="generateBtn"
                                            class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span id="btnText">Generate Signal</span>
                                        <span id="btnLoading" style="display: none;">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Analyzing...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Signal Result -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Signal Result</h3>
                            
                            <div id="signalResult" class="hidden">
                                <!-- Signal will be displayed here -->
                            </div>

                            <div id="noSignal" class="text-center py-8">
                                <div class="text-gray-400 mb-4">
                                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600">Select a trading pair and generate a signal to see the analysis.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Signals History -->
            <div class="mt-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Signals</h3>
                        
                        <div id="recentSignals" class="space-y-4">
                            <!-- Recent signals will be loaded here -->
                            <div class="text-center py-8">
                                <p class="text-gray-600">No recent signals. Generate your first signal above!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            
            // Reset button state on page load
            const btn = document.getElementById('generateBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            
            console.log('Button elements:', {btn, btnText, btnLoading});
            
            btn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
            
            // Get form element
            const form = document.getElementById('signalForm');
            console.log('Form element:', form);
            
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    console.log('Form submitted!');
            
            const btn = document.getElementById('generateBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            const signalResult = document.getElementById('signalResult');
            const noSignal = document.getElementById('noSignal');
            
            // Show loading state
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            
            const formData = new FormData(this);
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                console.log('CSRF Token:', csrfToken);
                
                const response = await fetch('{{ route("signals.generate") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    displaySignal(data.signal);
                    addToRecentSignals(data.signal);
                    noSignal.classList.add('hidden');
                    signalResult.classList.remove('hidden');
                } else {
                    alert('Error generating signal: ' + data.message);
                }
            } catch (error) {
                alert('Error generating signal: ' + error.message);
                console.error('Error:', error);
            } finally {
                // Reset button state
                btn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
                });
            } else {
                console.error('Form not found!');
            }
            
            function displaySignal(signal) {
            const signalResult = document.getElementById('signalResult');
            
            const signalClass = signal.signal === 'BUY' ? 'bg-green-50 border-green-200' : 
                               signal.signal === 'SELL' ? 'bg-red-50 border-red-200' : 
                               'bg-yellow-50 border-yellow-200';
            
            const signalColor = signal.signal === 'BUY' ? 'text-green-800' : 
                               signal.signal === 'SELL' ? 'text-red-800' : 
                               'text-yellow-800';
            
            signalResult.innerHTML = `
                <div class="border rounded-lg p-4 ${signalClass}">
                    <div class="text-center mb-4">
                        <div class="text-2xl font-bold ${signalColor}">${signal.signal}</div>
                        <div class="text-sm text-gray-600">${signal.symbol} - ${signal.timeframe}</div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Entry Price:</span>
                            <span class="font-semibold">$${signal.entry_price}</span>
                        </div>
                        
                        ${signal.take_profit ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Take Profit:</span>
                            <span class="font-semibold text-green-600">$${signal.take_profit}</span>
                        </div>
                        ` : ''}
                        
                        ${signal.stop_loss ? `
                        <div class="flex justify-between">
                            <span class="text-gray-600">Stop Loss:</span>
                            <span class="font-semibold text-red-600">$${signal.stop_loss}</span>
                        </div>
                        ` : ''}
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Confidence:</span>
                            <span class="font-semibold">${signal.confidence}%</span>
                        </div>
                    </div>
                    
                    ${signal.reasoning ? `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h5 class="font-medium text-gray-900 mb-2">Analysis:</h5>
                        <p class="text-sm text-gray-700">${signal.reasoning}</p>
                    </div>
                    ` : ''}
                    
                    <div class="mt-4 text-xs text-gray-500 text-center">
                        Generated at ${new Date(signal.created_at).toLocaleString()}
                    </div>
                </div>
            `;
        }
        
        function addToRecentSignals(signal) {
            const recentSignals = document.getElementById('recentSignals');
            
            // Create signal card for recent signals
            const signalCard = document.createElement('div');
            signalCard.className = 'border border-gray-200 rounded-lg p-4';
            
            const signalColor = signal.signal === 'BUY' ? 'text-green-600' : 
                               signal.signal === 'SELL' ? 'text-red-600' : 
                               'text-yellow-600';
            
            signalCard.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold ${signalColor}">${signal.signal}</span>
                            <span class="text-gray-600">${signal.symbol}</span>
                            <span class="text-sm text-gray-500">${signal.timeframe}</span>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            Entry: $${signal.entry_price} | Confidence: ${signal.confidence}%
                        </div>
                    </div>
                    <div class="text-xs text-gray-500">
                        ${new Date(signal.created_at).toLocaleTimeString()}
                    </div>
                </div>
            `;
            
            // Add to top of recent signals
            if (recentSignals.children.length === 1 && recentSignals.children[0].textContent.includes('No recent signals')) {
                recentSignals.innerHTML = '';
            }
            
            recentSignals.insertBefore(signalCard, recentSignals.firstChild);
            
            // Keep only last 10 signals
            while (recentSignals.children.length > 10) {
                recentSignals.removeChild(recentSignals.lastChild);
            }
        }
        
        }); // End of DOMContentLoaded
    </script>
</x-app-layout>