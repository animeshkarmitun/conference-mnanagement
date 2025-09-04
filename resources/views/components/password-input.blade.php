@props(['disabled' => false])

<div class="relative">
    <input {{ $disabled ? 'disabled' : '' }} 
           type="password"
           {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pr-10']) !!}>
    
    <!-- Eye Icon Toggle -->
    <button type="button" 
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200"
            onclick="togglePasswordVisibility(this)"
            aria-label="Toggle password visibility">
        <!-- Eye Icon (Show Password) -->
        <svg class="w-5 h-5 password-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
        <!-- Eye Slash Icon (Hide Password) -->
        <svg class="w-5 h-5 password-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
        </svg>
    </button>
</div>

<script>
function togglePasswordVisibility(button) {
    const input = button.parentElement.querySelector('input');
    const showIcon = button.querySelector('.password-show');
    const hideIcon = button.querySelector('.password-hide');
    
    if (input.type === 'password') {
        input.type = 'text';
        showIcon.classList.add('hidden');
        hideIcon.classList.remove('hidden');
        button.setAttribute('aria-label', 'Hide password');
    } else {
        input.type = 'password';
        showIcon.classList.remove('hidden');
        hideIcon.classList.add('hidden');
        button.setAttribute('aria-label', 'Show password');
    }
}
</script>
