<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Island Hotel - Sistema de Gestión</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-orange-50 min-h-screen">
    <div class="min-h-screen flex">
        <!-- Left Column: Branding & Info -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 bg-gradient-to-br from-[#1e3a5f] via-[#2d5a7b] to-[#c17a4a] p-12 flex-col justify-between relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-orange-400/10 rounded-full blur-3xl"></div>
            
            <!-- Logo & Brand -->
            <div class="relative z-10 fade-in text-center">
                <div class="inline-block mb-6">
                    <img src="{{ asset('images/island-hotel-logo.jpg') }}" 
                         alt="Island Hotel" 
                         class="w-32 h-32 rounded-full shadow-2xl object-cover border-4 border-white/20 float-animation mx-auto">
                </div>
                <h1 class="text-white text-4xl font-black tracking-tight mb-2">ISLAND HOTEL</h1>
                <p class="text-blue-200 text-lg font-medium">Tu hotel, en tu bolsillo</p>
            </div>

            <!-- Main Message -->
            <div class="relative z-10 fade-in" style="animation-delay: 0.2s;">
                <h2 class="text-white text-5xl font-black leading-tight mb-6">
                    Gestión Hotelera<br>
                    <span class="text-orange-300">Inteligente</span>
                </h2>
                <p class="text-blue-100 text-xl leading-relaxed max-w-lg">
                    Control total de tus reservas, habitaciones y finanzas. Todo en tiempo real, desde cualquier lugar.
                </p>
                
                <!-- Features -->
                <div class="mt-12 space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-white font-medium">Gestión de reservas en tiempo real</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-white font-medium">Reportes financieros automáticos</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-white font-medium">Acceso desde cualquier dispositivo</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="relative z-10 text-blue-100 text-sm fade-in" style="animation-delay: 0.4s;">
                <p>© 2026 Island Hotel. Todos los derechos reservados.</p>
            </div>
        </div>

        <!-- Right Column: Login Form -->
        <div class="w-full lg:w-1/2 xl:w-2/5 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md fade-in" style="animation-delay: 0.3s;">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('images/island-hotel-logo.jpg') }}" alt="Island Hotel" class="w-24 h-24 rounded-2xl shadow-xl mx-auto mb-4">
                    <h1 class="text-2xl font-black text-gray-900">ISLAND HOTEL</h1>
                    <p class="text-gray-500 text-sm mt-1">Sistema de Gestión</p>
                </div>

                <!-- Welcome Message -->
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Bienvenido de nuevo</h2>
                    <p class="text-gray-600">Inicia sesión para acceder a tu panel de control</p>
                </div>

                <!-- Error Alert -->
                @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            @if(session('unregistered'))
                            <a href="https://wa.me/51905562625?text=Hola%2C%20mi%20cuenta%20no%20está%20registrada%20y%20me%20gustaría%20obtener%20una%20suscripción%20o%20la%20prueba%20gratuita%20de%203%20días" 
                               target="_blank"
                               class="inline-flex items-center gap-2 mt-3 bg-[#25D366] text-white text-sm font-semibold py-2.5 px-4 rounded-lg hover:bg-[#20ba59] transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.45L0 24l7.105-1.864a11.834 11.834 0 005.735 1.486c.002 0 .004 0 .006 0 6.55 0 11.885-5.336 11.888-11.892a11.83 11.83 0 00-3.411-8.412"/></svg>
                                Obtener Suscripción / Prueba Gratis
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Google Login Button -->
                <a href="{{ route('auth.google') }}" 
                   class="w-full flex items-center justify-center gap-3 bg-white border-2 border-gray-200 hover:border-blue-400 hover:bg-blue-50 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-all duration-300 shadow-sm hover:shadow-lg active:scale-[0.98] group">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-base">Continuar con Google</span>
                </a>

                <!-- Divider -->
                <div class="relative flex py-6 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-xs font-semibold uppercase tracking-wider">Seguridad Garantizada</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Privacy Notice -->
                <p class="text-xs text-gray-500 text-center leading-relaxed">
                    Al iniciar sesión, confirmas que has leído y aceptas nuestra 
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-medium hover:underline">Política de Privacidad</a> y los 
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-medium hover:underline">Términos de Servicio</a>.
                </p>

                <!-- Footer -->
                <div class="mt-12 text-center">
                    <p class="text-xs text-gray-400 font-medium">
                        IMPULSADO POR <span class="text-blue-600 font-bold">TEAM DOJ</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    <div class="fixed bottom-6 right-6 z-50 group">
        <div class="absolute bottom-full right-0 mb-4 w-64 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none transform translate-y-2 group-hover:translate-y-0">
            <div class="bg-white rounded-2xl shadow-2xl p-4 border border-gray-100">
                <h4 class="text-gray-900 font-bold text-sm mb-1">¿Necesitas ayuda?</h4>
                <p class="text-gray-600 text-xs leading-relaxed">Nuestro equipo está listo para asistirte</p>
            </div>
            <div class="absolute bottom-[-6px] right-6 w-3 h-3 bg-white border-r border-b border-gray-100 transform rotate-45"></div>
        </div>
        <a href="https://wa.me/51905562625?text=Hola%2C%20tengo%20una%20duda%20sobre%20el%20sistema%20de%20gestión%20hotelera" 
           target="_blank" 
           class="flex items-center justify-center w-14 h-14 bg-[#25D366] text-white rounded-full shadow-lg hover:shadow-xl hover:bg-[#22c35e] transition-all duration-300 hover:scale-110 active:scale-95">
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.45L0 24l7.105-1.864a11.834 11.834 0 005.735 1.486c.002 0 .004 0 .006 0 6.55 0 11.885-5.336 11.888-11.892a11.83 11.83 0 00-3.411-8.412"/>
            </svg>
        </a>
    </div>
</body>
</html>
