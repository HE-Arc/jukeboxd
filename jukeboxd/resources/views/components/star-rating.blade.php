@props(['rating' => 0])

<div class="relative inline-block text-2xl leading-none select-none mt-2 text-brand-green">
    <span>☆☆☆☆☆</span>

    <span class="absolute top-0 left-0 overflow-hidden pointer-events-none" style="width: {{ ($rating / 5) * 100 }}%">★★★★★</span>

    <input type="range" min="0" max="5" step="0.5" value="{{ $rating }}"
           oninput="this.previousElementSibling.style.width = (this.value * 20) + '%'"
           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
</div>
