<button {{ $attributes->merge(['type' => 'submit', 'class' => 'full-white']) }}>
    {{ $slot }}
</button>
