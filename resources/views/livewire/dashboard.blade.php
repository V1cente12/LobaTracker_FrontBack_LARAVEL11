<div class="max-w-sm sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-screen-xl mx-auto rounded-lg mt-20">
    <div class="flex flex-wrap justify-between gap-2 mb-2">
        @foreach($gameTypes as $gameType)
        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 px-2 mb-4"> <!-- Ajustamos el ancho por pantalla -->
            <div wire:click="goToGameLobby({{ $gameType->id }})" class="cursor-pointer">
                <div class="rounded-lg overflow-hidden bg-white shadow-lg">
                    <div class="w-full" style="aspect-ratio: 3 / 4;"> <!-- Usamos aspect-ratio para una relación de 3:4 -->
                        <img src="{{ asset($gameType->image_path) }}" alt="{{ $gameType->name }}" class="w-full h-full object-cover"> <!-- Mantener object-cover para que las imágenes se ajusten bien -->
                    </div>
                    <div class="p-4 text-center"> <!-- Ajustamos el contenedor inferior para texto -->
                        <h2 class="text-xl font-semibold">{{ $gameType->name }}</h2>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
