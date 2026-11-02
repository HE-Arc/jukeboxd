@props(['rating' => 0])

<div class="relative inline-block text-2xl leading-none select-none mt-2 text-brand-green font-mono tracking-wider">
    <span>☆☆☆☆☆</span>

    <span class="absolute top-0 left-0 overflow-hidden pointer-events-none whitespace-nowrap" 
          style="width: {{ ($rating / 5) * 100 }}%">★★★★★</span>

    <input type="range" min="0" max="5" step="0.5" value="{{ $rating }}"
           oninput="this.previousElementSibling.style.width = (this.value * 20) + '%'"
           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer appearance-none m-0 p-0 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-0 [&::-webkit-slider-thumb]:h-0 [&::-moz-range-thumb]:w-0 [&::-moz-range-thumb]:h-0 [&::-moz-range-thumb]:border-0">
</div>
