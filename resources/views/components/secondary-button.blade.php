<button {{ $attributes->merge(['type' => 'button', 'class' => 'border-white']) }}>
    {{ $slot }}
</button>
