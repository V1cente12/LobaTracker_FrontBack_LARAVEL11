<div>
    <div class="p-4 bg-white shadow sm:rounded-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Juegos Activos</h2>
            <button wire:click="showCreateGameModal" class="bg-blue-500 text-white px-4 py-2 rounded">
                Crear Juego
            </button>
        </div>

        @if($games->isEmpty())
            <p>No hay juegos activos ahora.</p>
        @else
            <ul>
               
                @foreach($games as $game)
               
                <li class="border-b py-2">
                    <span class="font-bold">{{ $game->name }}</span> - 
                    {{ $game->created_at->format('d-m-Y H:i') }} -
                    <span class="font-bold">{{ $game->initial_price }}</span> - 
                    <span class="font-bold">{{ $game->rejoin_price }}</span> -
                    <span class="font-bold">{{ $game->creator->name }}</span>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Modal -->
    <div x-data="{ open: @entangle('showModal') }" x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 z-50 overflow-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg max-w-md mx-auto">
            <h2 class="text-lg font-semibold mb-4">Crear Nuevo Juego</h2>
            <form wire:submit.prevent="createGame">
                <div class="mb-4">
                    <label for="gameName" class="block text-gray-700">Nombre del Juego</label>
                    <input type="text" id="gameName" wire:model="gameName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('gameName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="initialPrice" class="block text-gray-700">Precio de Parada</label>
                    <input type="number" id="initialPrice" wire:model="initialPrice" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01" required>
                    @error('initialPrice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="rejoinPrice" class="block text-gray-700">Precio de Reenganche</label>
                    <input type="number" id="rejoinPrice" wire:model="rejoinPrice" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" step="0.01" required>
                    @error('rejoinPrice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Crear Juego</button>
                    <button type="button" wire:click="$set('showModal', false)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
