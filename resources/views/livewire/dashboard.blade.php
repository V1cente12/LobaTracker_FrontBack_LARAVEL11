<div>
    <div class="p-4 bg-gradient-to-r from-green-400 to-green-500 shadow sm:rounded-lg mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-white">Lobby de Juegos</h2>
            <button wire:click="showCreateGameModal" class="bg-yellow-400 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500">
                + Crear Nuevo Juego
            </button>
        </div>
    </div>

    <!-- Mostrar Juegos Activos -->
    @if($games->isEmpty())
        <div class="p-4 bg-white shadow sm:rounded-lg text-center">
            <p class="text-lg text-gray-600">No hay juegos activos ahora. ¡Crea uno para empezar!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($games as $game)
                <div class="bg-green-200 shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $game->name }}</h3>
                    <p class="text-gray-700 mb-4">Creado el: {{ $game->created_at->format('d-m-Y H:i') }}</p>
                    <p class="text-gray-700">Parada: <span class="font-semibold">{{ $game->initial_price }} Bs</span></p>
                    <p class="text-gray-700">Reenganche: <span class="font-semibold">{{ $game->rejoin_price }} Bs</span></p>
                    <p class="text-gray-700">Creador: <span class="font-semibold">{{ $game->creator->name }}</span></p>
                    <div class="mt-4 flex justify-end">
                        <button wire:click="showJoinGameModal({{ $game->id }})" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            Unirse
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal para Crear Juego -->
    <div x-data="{ open: @entangle('showToCreateGameModal') }" x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-6">Crear Nuevo Juego</h2>
            <form wire:submit.prevent="createGame" class="space-y-6">
                <div>
                    <label for="gameName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del Juego</label>
                    <input type="text" id="gameName" wire:model="gameName" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    @error('gameName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="initialPrice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio de Parada</label>
                    <input type="number" id="initialPrice" wire:model="initialPrice" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" step="0.01" required>
                    @error('initialPrice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="rejoinPrice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio de Reenganche</label>
                    <input type="number" id="rejoinPrice" wire:model="rejoinPrice" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" step="0.01" required>
                    @error('rejoinPrice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" wire:click="resetModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring focus:ring-gray-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring focus:ring-indigo-300">Crear Juego</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Unirse a un Juego -->
    <div x-data="{ open: @entangle('showToJoinGameModal') }" x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-6">Unirse al Juego</h2>
            <form wire:submit.prevent="joinGame" class="space-y-6">
                <input type="hidden" wire:model="selectedGameId">

                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nickname</label>
                    <input type="text" id="nickname" wire:model="nickname" class="mt-1 block w-full p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    @error('nickname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" wire:click="resetModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring focus:ring-gray-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring focus:ring-indigo-300">Unirse</button>
                </div>
            </form>
        </div>
    </div>
</div>
