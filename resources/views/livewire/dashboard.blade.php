<div class="max-w-sm sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-screen-xl mx-auto bg-gradient-to-b from-gray-100 to-gray-300 p-4 shadow-lg rounded-lg mt-20">
    <h1 class="text-2xl font-bold mb-4">Juegos Disponibles</h1>
    <div class="flex flex-wrap -mx-2">
        @foreach($gameTypes as $gameType)
            <div class="w-full md:w-1/3 px-2 mb-4">
                <div wire:click="goToGameLobby({{ $gameType->id }})" class="cursor-pointer">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="{{ asset($gameType->image_path) }}" alt="{{ $gameType->name }}" class="w-full h-48 object-cover">
                        <div class="p-4 text-center">
                            <h2 class="text-xl font-semibold">{{ $gameType->name }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
