
<div class="max-w-sm sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-screen-xl mx-auto bg-gradient-to-b rounded-lg mt-20">
    <div class="p-4 bg-gradient-to-r from-[#FF0000] to-[#FF3333] shadow-lg rounded-lg mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-white">Lobby de Juegos</h2>
            <button wire:click="showCreateGameModal" class="bg-yellow-400 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500">
                + Crear Nuevo Juego
            </button>
        </div>
    </div>

    <!-- Mostrar Juegos -->
    @if($games->isEmpty())
        <div class="p-4 bg-white shadow sm:rounded-lg text-center">
            <p class="text-lg text-gray-600">No hay juegos ahora. Crea uno para empezar!</p>
        </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($games as $game)
            @php
                $bgClass = 'from-green-500 to-[#199949]'; 
                $buttonDisabled = false;
                $buttonClass = 'bg-yellow-400 hover:bg-yellow-500 text-gray-800';
    
                if ($game->status === 'in_progress') {
                    $bgClass = 'from-yellow-500 to-yellow-600'; 
                } elseif ($game->status === 'finished') {
                    $bgClass = 'from-gray-500 to-gray-600'; 
                    $buttonClass = 'bg-yellow-400 hover:bg-yellow-500 text-gray-800';
                }
            @endphp
            <div class="bg-gradient-to-b {{ $bgClass }} border-gray-100 shadow-lg rounded-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-xl font-semibold text-white mb-2">{{ $game->name }}</h3>
                <p class="text-gray-100 mb-4">
                    <span class="font-semibold">Creado el: </span> 
                    <span class="text-lg text-gray-200">
                        {{ $game->created_at->format('d F Y') }}
                    </span>
                    <span class="text-sm text-gray-300 ml-2">
                        a las {{ $game->created_at->format('H:i') }}
                    </span>
                </p>
                <p class="text-gray-100">Parada: <span class="font-semibold">{{ $game->initial_price }} Bs</span></p>
                <p class="text-gray-100">Reenganche: <span class="font-semibold">{{ $game->rejoin_price }} Bs</span></p>
                <p class="text-gray-100">Creador: <span class="font-semibold">{{ $game->creator->name }}</span></p>
                <p class="text-gray-100">
                    Ganador: 
                    @if($game->winnerPlayer && $game->winnerPlayer->user)
                        <span class="text-yellow-400 font-semibold">{{ $game->winnerPlayer->user->name }}</span>
                    @else
                        <span class="text-yellow-400">Aún no hay ganador en este juego</span>
                    @endif
                </p>
    
                <div class="mt-4 flex justify-end">
                    <button 
                        wire:click="showJoinGameModal({{ $game->id }})" 
                        class="{{ $buttonClass }} px-4 py-2 rounded-lg font-semibold"
                        @if($buttonDisabled) disabled @endif
                    >
                        Unirse
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Modal create -->
    <div x-data="{ open: @entangle('showToCreateGameModal') }"
    x-show="open"
    @keydown.escape.window="open = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75">
        <div class="bg-gradient-to-b from-[#199949] to-green-500 p-8 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-xl font-semibold text-white mb-6">Crear Nuevo Juego</h2>
            <form wire:submit.prevent="createGame" class="space-y-6">
                <div>
                    <label for="gameName" class="block text-base font-medium text-white">Nombre del Juego</label>
                    <input type="text" id="gameName" wire:model="gameName" class="mt-1 block w-full p-2 bg-gray-100 text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    @error('gameName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="initialPrice" class="block text-base font-medium text-white">Precio de Parada</label>
                    <input type="number" id="initialPrice" wire:model="initialPrice" class="mt-1 block w-full p-2 bg-gray-100 text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" step="0.01" required>
                    @error('initialPrice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="rejoinPrice" class="block text-base font-medium text-white">Precio de Reenganche</label>
                    <input type="number" id="rejoinPrice" wire:model="rejoinPrice" class="mt-1 block w-full p-2 bg-gray-100 text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" step="0.01" required>
                    @error('rejoinPrice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" wire:click="resetModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring focus:ring-gray-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-400 text-gray-800 rounded-lg hover:bg-yellow-500 focus:ring focus:ring-yellow-300">Crear Juego</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal  join -->
    <div x-data="{ open: @entangle('showToJoinGameModal') }"
    x-show="open"
    @keydown.escape.window="open = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75">
        <div class="bg-gradient-to-b from-[#199949] to-green-500 p-8 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-xl font-semibold text-white mb-6">Unirse al Juego</h2>
            <form wire:submit.prevent="joinGame" class="space-y-6">
                <input type="hidden" wire:model="selectedGameId">

                <div>
                    <label for="nickname" class="block text-base font-medium text-white">Nickname</label>
                    <input type="text" id="nickname" wire:model="nickname" class="mt-1 block w-full p-2 bg-gray-100 text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    @error('nickname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" wire:click="resetModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring focus:ring-gray-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-400 text-gray-800 rounded-lg hover:bg-yellow-500 focus:ring focus:ring-yellow-300">Unirse</button>
                </div>
            </form>
        </div>
    </div>
</div>
