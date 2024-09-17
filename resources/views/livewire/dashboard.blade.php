
<div class="max-w-sm sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-screen-xl mx-auto rounded-lg mt-20">
    <div class="flex flex-wrap justify-center gap-4 sm:gap-10 mb-4">
        @foreach($gameTypes as $gameType)
        <div class="w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/5 px-2 mb-4"> 
           <div wire:click="goToGameLobby({{ $gameType->id }})" class="cursor-pointer" style="cursor: pointer;">

                <!-- Aquí aplicamos el hover con transform -->
                <div class="rounded-lg overflow-hidden bg-white shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="w-full" style="aspect-ratio: 3 / 4;">
                        <img src="{{ asset($gameType->image_path) }}" alt="{{ $gameType->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-2 text-center">
                        <h2 class="text-sm sm:text-lg font-semibold">{{ $gameType->name }}</h2>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
