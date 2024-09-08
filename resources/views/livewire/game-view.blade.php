<div class="container mx-auto bg-gradient-to-b from-gray-100 to-gray-300 p-8 shadow-lg rounded-lg">
    <div class="p-4 bg-gradient-to-r from-[#FF0000] to-[#FF3333] shadow sm:rounded-lg mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-white">{{ $game->name }}</h2>
            <button wire:click="showReportPointsModal({{ $game->id }})" class="bg-yellow-400 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500">
                Reportar Puntos
            </button>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($players as $player)
            <div class="bg-white rounded-lg shadow-lg p-4">
                <h2 class="text-xl font-semibold mb-4">{{ $player->nickname }}</h2>
                <div class="space-y-2">
                    @foreach($player->scores as $score)
                        <div class="flex justify-between items-center bg-gray-100 p-2 rounded-md">
                            <span>{{ $score->points }} - {{$player->total_points}}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <!-- Modal para Reportar Puntos -->
    <div x-data="{ open: @entangle('showToReportPointsModal') }"
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
            <h2 class="text-xl font-semibold text-white mb-6">Reportar Puntos</h2>
            <form wire:submit.prevent="reportPoints" class="space-y-6">
                <input type="hidden" wire:model="selectedPlayerId">
                <input type="hidden" wire:model="selectedGameId">

                <div>
                    <label for="points" class="block text-base font-medium text-white">Puntaje</label>
                    <input type="number" id="points" wire:model="points" class="mt-1 block w-full p-2 bg-gray-100 text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" step="0.01" required>
                    @error('points') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" wire:click="hideReportPointsModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring focus:ring-gray-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-400 text-gray-800 rounded-lg hover:bg-yellow-500 focus:ring focus:ring-yellow-300">Reportar</button>
                </div>
            </form>
        </div>
    </div>
</div>
