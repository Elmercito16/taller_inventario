@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Taller Pro')

@push('styles')
<style>
    /* Animaciones personalizadas */
    .login-container {
        animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Efectos de focus mejorados */
    .input-group {
        position: relative;
    }
    
    .input-group input:focus + .input-icon {
        color: #218786;
        transform: translateY(-50%) scale(1.1);
    }
    
    .input-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        transition: all 0.3s ease;
        pointer-events: none;
    }
    
    /* Efectos de botón */
    .btn-primary {
        background: linear-gradient(135deg, #218786 0%, #1d7874 100%);
        box-shadow: 0 4px 15px 0 rgba(33, 135, 134, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px 0 rgba(33, 135, 134, 0.4);
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    /* Estados de validación */
    .input-valid {
        border-color: #10b981;
        background-color: #ecfdf5;
    }
    
    .input-invalid {
        border-color: #ef4444;
        background-color: #fef2f2;
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    /* Efectos de loading */
    .loading-dots {
        display: inline-block;
    }
    
    .loading-dots::after {
        content: '';
        animation: dots 1.5s steps(4, end) infinite;
    }
    
    @keyframes dots {
        0%, 20% { content: ''; }
        40% { content: '.'; }
        60% { content: '..'; }
        80%, 100% { content: '...'; }
    }
    
    /* Efecto de brillo en el logo */
    .logo-shine {
        background: linear-gradient(135deg, #218786 0%, #20b2aa 50%, #218786 100%);
        background-size: 200% 200%;
        animation: shine 3s ease-in-out infinite;
    }
    
    @keyframes shine {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
</style>
@endpush

@section('content')
<div class="w-full max-w-md bg-white/95 backdrop-blur-sm p-6 rounded-2xl shadow-2xl border border-white/20 login-container" x-data="loginForm()">
    
    <!-- Logo y título -->
    <div class="text-center mb-6">
        <div class="mx-auto w-14 h-14 logo-shine rounded-2xl flex items-center justify-center mb-3 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Taller Pro</h2>
        <p class="text-gray-600 text-sm">Sistema de Gestión de Inventario</p>
    </div>

    <!-- Alertas mejoradas -->
    <div x-show="showAlert" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="mb-4">
        
        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center mb-1">
                    <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-red-800">Error de autenticación</h3>
                </div>
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-center">
                            <span class="w-1 h-1 bg-red-600 rounded-full mr-2"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Formulario de login -->
    <form action="{{ route('login') }}" method="POST" @submit.prevent="submitForm" class="space-y-4">
        @csrf

        <!-- Campo Email -->
        <div class="space-y-1">
            <label for="correo" class="block text-sm font-semibold text-gray-700">
                Correo electrónico
            </label>
            <div class="input-group">
                <input type="email" 
                       name="correo" 
                       id="correo"
                       x-model="form.correo"
                       @input="validateEmail"
                       @blur="validateEmail"
                       value="{{ old('correo') }}"
                       :class="emailValid ? 'input-valid' : (emailTouched && !emailValid ? 'input-invalid' : '')"
                       class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 pr-12"
                       placeholder="tu@correo.com" 
                       required>
                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p x-show="emailTouched && form.correo.length > 0 && !emailValid" 
               x-transition
               class="text-xs text-red-600 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Por favor ingresa un correo válido
            </p>
        </div>

        <!-- Campo Contraseña -->
        <div class="space-y-1">
            <label for="contraseña" class="block text-sm font-semibold text-gray-700">
                Contraseña
            </label>
            <div class="input-group">
                <input :type="showPassword ? 'text' : 'password'" 
                       name="contraseña" 
                       id="contraseña"
                       x-model="form.password"
                       @input="validatePassword"
                       @blur="validatePassword"
                       :class="passwordValid ? 'input-valid' : (passwordTouched && !passwordValid ? 'input-invalid' : '')"
                       class="w-full border border-gray-300 rounded-xl p-3 shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 pr-20"
                       placeholder="Tu contraseña" 
                       required>
                
                <!-- Botón mostrar/ocultar contraseña -->
                <button type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute right-12 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    </svg>
                </button>
                
                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <p x-show="passwordTouched && form.password.length > 0 && !passwordValid" 
               x-transition
               class="text-xs text-red-600 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                La contraseña debe tener al menos 6 caracteres
            </p>
        </div>

        <!-- Recordarme y Olvidé contraseña -->
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center cursor-pointer group">
                <input type="checkbox" 
                       name="remember" 
                       id="remember" 
                       x-model="form.remember"
                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded transition-colors">
                <span class="ml-2 text-gray-700 group-hover:text-gray-900 transition-colors">
                    Recordarme
                </span>
            </label>
            
            <a href="#" class="text-primary-600 hover:text-primary-800 font-medium transition-colors">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

        <!-- Botón de inicio -->
        <div class="pt-2">
            <button type="submit"
                    :disabled="isSubmitting"
                    :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                    class="w-full px-6 py-3 btn-primary text-white rounded-xl font-semibold transition-all duration-300 flex items-center justify-center">
                
                <!-- Loading spinner -->
                <svg x-show="isSubmitting" 
                     class="animate-spin w-5 h-5 mr-2" 
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <!-- Ícono normal -->
                <svg x-show="!isSubmitting" 
                     class="w-5 h-5 mr-2" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                
                <span x-text="isSubmitting ? 'Iniciando sesión' : 'Iniciar Sesión'"></span>
                <span x-show="isSubmitting" class="loading-dots"></span>
            </button>
        </div>
    </form>

    <!-- Divider -->
    <div class="my-5 flex items-center">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-3 text-sm text-gray-500 bg-white">o</span>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    <!-- Link de registro -->
    <div class="text-center">
        <p class="text-sm text-gray-600 mb-3">
            ¿No tienes cuenta?
        </p>
        <a href="{{ route('register') }}" 
           class="inline-flex items-center px-4 py-2 border-2 border-primary-600 text-primary-600 rounded-xl hover:bg-primary-600 hover:text-white font-semibold transition-all duration-300 group text-sm">
            <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Crear cuenta nueva
        </a>
    </div>

    <!-- Footer informativo compacto -->
    <div class="mt-5 pt-4 border-t border-gray-200 text-center">
        <p class="text-xs text-gray-500 mb-2">
            Al iniciar sesión, aceptas nuestros términos de servicio
        </p>
        <div class="flex items-center justify-center space-x-4 text-xs text-gray-400">
            <div class="flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                SSL
            </div>
            <div class="flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Seguro
            </div>
        </div>
    </div>
</div>vidaste tu contraseña?
            </a>
        </div>

        <!-- Botón de inicio -->
        <div class="pt-2">
            <button type="submit"
                    :disabled="isSubmitting"
                    :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                    class="w-full px-6 py-4 btn-primary text-white rounded-xl font-semibold text-lg transition-all duration-300 flex items-center justify-center">
                
                <!-- Loading spinner -->
                <svg x-show="isSubmitting" 
                     class="animate-spin w-6 h-6 mr-3" 
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <!-- Ícono normal -->
                <svg x-show="!isSubmitting" 
                     class="w-6 h-6 mr-3" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                
                <span x-text="isSubmitting ? 'Iniciando sesión' : 'Iniciar Sesión'"></span>
                <span x-show="isSubmitting" class="loading-dots"></span>
            </button>
        </div>
    </form>

    <!-- Divider -->
    <div class="my-8 flex items-center">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-4 text-sm text-gray-500 bg-white">o</span>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    <!-- Link de registro -->
    <div class="text-center">
        <p class="text-sm text-gray-600 mb-4">
            ¿No tienes cuenta?
        </p>
        <a href="{{ route('register') }}" 
           class="inline-flex items-center px-6 py-3 border-2 border-primary-600 text-primary-600 rounded-xl hover:bg-primary-600 hover:text-white font-semibold transition-all duration-300 group">
            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Crear cuenta nueva
        </a>
    </div>

    <!-- Footer informativo -->
    <div class="mt-8 pt-6 border-t border-gray-200 text-center">
        <p class="text-xs text-gray-500">
            Al iniciar sesión, aceptas nuestros términos de servicio y política de privacidad
        </p>
        <div class="flex items-center justify-center mt-3 space-x-4">
            <div class="flex items-center text-xs text-gray-400">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Conexión segura SSL
            </div>
            <div class="flex items-center text-xs text-gray-400">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Datos protegidos
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loginForm() {
    return {
        form: {
            correo: '{{ old('correo') }}',
            password: '',
            remember: false
        },
        
        // Estados de validación
        emailValid: false,
        passwordValid: false,
        emailTouched: false,
        passwordTouched: false,
        showPassword: false,
        isSubmitting: false,
        showAlert: {{ ($errors->any() || session('success')) ? 'true' : 'false' }},
        
        // Computed
        get formValid() {
            return this.form.correo.length > 0 && this.form.password.length > 0;
        },
        
        // Validaciones
        validateEmail() {
            this.emailTouched = true;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.emailValid = emailRegex.test(this.form.correo);
        },
        
        validatePassword() {
            this.passwordTouched = true;
            this.passwordValid = this.form.password.length >= 6;
        },
        
        // Envío del formulario
        async submitForm(event) {
            if (!this.formValid || this.isSubmitting) {
                return;
            }
            
            this.isSubmitting = true;
            
            // Simular delay mínimo para UX
            await new Promise(resolve => setTimeout(resolve, 800));
            
            // Enviar formulario
            event.target.submit();
        },
        
        // Inicialización
        init() {
            // Validar email inicial si existe
            if (this.form.correo) {
                this.validateEmail();
            }
            
            // Auto-ocultar alertas después de 5 segundos
            if (this.showAlert) {
                setTimeout(() => {
                    this.showAlert = false;
                }, 5000);
            }
            
            // Auto-focus en primer campo vacío
            this.$nextTick(() => {
                if (!this.form.correo) {
                    document.getElementById('correo').focus();
                } else {
                    document.getElementById('contraseña').focus();
                }
            });
        }
    }
}

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Enter para enviar formulario
    if (e.key === 'Enter' && (e.target.tagName === 'INPUT')) {
        e.preventDefault();
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton && !submitButton.disabled) {
            submitButton.click();
        }
    }
});

// Efectos de partículas de fondo (opcional)
document.addEventListener('DOMContentLoaded', function() {
    // Crear efecto de partículas sutiles
    function createParticle() {
        const particle = document.createElement('div');
        particle.className = 'absolute w-1 h-1 bg-primary-200 rounded-full opacity-20 animate-ping';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 3 + 's';
        particle.style.animationDuration = (2 + Math.random() * 3) + 's';
        
        document.body.appendChild(particle);
        
        setTimeout(() => {
            particle.remove();
        }, 5000);
    }
    
    // Crear partículas ocasionalmente
    setInterval(createParticle, 3000);
});
</script>
@endpush

@endsection