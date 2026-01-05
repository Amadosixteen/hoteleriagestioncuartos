<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Gestión Hotelera</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 3s infinite ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen overflow-hidden">
    <div class="flex min-h-screen">
        <!-- Columna Izquierda: Imagen de Fondo (Premium) -->
        <div class="hidden lg:block lg:w-3/5 relative">
            <img src="{{ asset('assets/images/login-background.png') }}" 
                 alt="Luxury Hotel Lobby" 
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
            <div class="absolute bottom-12 left-12 text-white animate-fade-in">
                <h2 class="text-5xl font-bold mb-4 drop-shadow-md"> Gestión de Reservas Inteligente</h2>
                <p class="text-xl text-gray-100 max-w-md drop-shadow-sm">Control Total, en tiempo real, en la Nube</p>
            </div>
        </div>

        <!-- Columna Derecha: Formulario de Login -->
        <div class="w-full lg:w-2/5 flex flex-col justify-center items-center p-8 bg-white shadow-2xl z-10">
            <div class="max-w-md w-full animate-fade-in" style="animation-delay: 0.2s;">
                <!-- Logo -->
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 text-white rounded-2xl shadow-lg mb-4 transform hover:rotate-6 transition-transform">
                        <span class="text-4xl">🏨</span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Sistema de Gestión</h1>
                    <p class="text-gray-500 mt-2 font-medium">Bienvenido de nuevo. Por favor, accede a tu cuenta.</p>
                </div>

                <!-- Card de Login -->
                <div class="space-y-6">
                    @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r shadow-sm">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                    @endif

                    <a href="{{ route('auth.google') }}" 
                       class="w-full flex items-center justify-center gap-4 bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/30 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-all duration-300 hover:shadow-lg active:scale-95 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Continuar con Google</span>
                    </a>

                    <div class="relative flex py-5 items-center">
                        <div class="flex-grow border-t border-gray-100"></div>
                        <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase tracking-widest">Seguridad Garantizada</span>
                        <div class="flex-grow border-t border-gray-100"></div>
                    </div>

                    <p class="text-xs text-gray-400 text-center leading-relaxed">
                        Al ingresar, confirmas que has leído y aceptas nuestra 
                        <a href="#" class="text-indigo-600 hover:underline font-medium">Política de Privacidad</a> y los 
                        <a href="#" class="text-indigo-600 hover:underline font-medium">Términos de Servicio</a>.
                    </p>
                </div>

                <!-- Footer -->
                <div class="mt-16 text-center">
                    <p class="text-xs text-gray-400 font-medium tracking-wide">
                        IMPULSADO POR <span class="text-indigo-600 font-bold">TEAM DOJ</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Burbuja Flotante de WhatsApp Soporte (Rediseñada) -->
    <div class="fixed bottom-8 right-8 z-50 group">
        <div class="absolute bottom-full right-0 mb-6 w-72 opacity-0 group-hover:opacity-100 transition-all duration-500 pointer-events-none translate-y-4 group-hover:translate-y-0">
            <div class="glass-card shadow-2xl rounded-3xl p-5 border border-white/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/10 rounded-full blur-2xl -mr-12 -mt-12"></div>
                <h4 class="text-gray-900 font-bold text-base mb-2">¿Necesitas asistencia?</h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-1">Nuestro equipo de soporte está listo para ayudarte con el sistema.</p>
                <div class="w-10 h-1 h-indigo-500 rounded-full mt-3"></div>
            </div>
            <!-- Flecha -->
            <div class="absolute bottom-[-10px] right-8 w-4 h-4 glass-card border-none rotate-45 transform skew-x-12"></div>
        </div>
        <a href="https://wa.me/51905562625?text=Hola%2C%20tengo%20una%20duda%20sobre%20el%20sistema%20de%20gesti%C3%B3n%20hotelera" 
           target="_blank" 
           class="flex items-center justify-center w-16 h-16 bg-[#25D366] text-white rounded-full shadow-[0_10px_40px_-10px_rgba(37,211,102,0.5)] hover:bg-[#22c35e] transition-all duration-300 hover:scale-110 active:scale-90 animate-bounce-subtle">
            <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.45L0 24l7.105-1.864a11.834 11.834 0 005.735 1.486c.002 0 .004 0 .006 0 6.55 0 11.885-5.336 11.888-11.892a11.83 11.83 0 00-3.411-8.412"/>
            </svg>
        </a>
    </div>
</body>
</html>
