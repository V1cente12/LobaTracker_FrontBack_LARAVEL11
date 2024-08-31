<div>
    <!-- Botón para Crear Juego en la parte superior -->
    <div class="p-4 bg-white shadow sm:rounded-lg mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Juegos Activos</h2>
            <button wire:click="showCreateGameModal" class="bg-blue-500 text-white px-4 py-2 rounded">
                Crear Juego
            </button>
        </div>

        @if($games->isEmpty())
            <p>No hay juegos activos ahora.</p>
        @else
            <!-- Tabla de Juegos Activos -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Creación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio de Parada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio de Reenganche</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creador</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($games as $game)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $game->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $game->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $game->initial_price }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $game->rejoin_price }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $game->creator->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="showJoinGameModal({{ $game->id }})" class="bg-green-500 text-white px-4 py-2 rounded">Unirse</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal para Crear Juego -->
    <div x-data="{ open: @entangle('showToCreateGameModal') }" x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 z-50 overflow-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
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
                    <button type="button" wire:click="resetModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Unirse a un Juego -->
    <div x-data="{ open: @entangle('showToJoinGameModal') }" x-show="open" @keydown.escape.window="open = false" class="fixed inset-0 z-50 overflow-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg max-w-md mx-auto">
            <h2 class="text-lg font-semibold mb-4">Unirse al Juego</h2>
            <form wire:submit.prevent="joinGame">
                <input type="hidden" wire:model="selectedGameId">
                <div class="mb-4">
                    <label for="nickname" class="block text-gray-700">Nickname</label>
                    <input type="text" id="nickname" wire:model="nickname" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('nickname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Unirse</button>
                    <button type="button" wire:click="resetModal" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
