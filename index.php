<!DOCTYPE html>
<html lang="et" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>takenncs-webdev | QBCore Login Template</title>
    <meta name="description" content="Lihtne, puhas ja moodne QBCore UCP login leht – valmis kasutamiseks.">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Inter"', 'ui-sans-serif', 'system-ui'] },
                    colors: { primary: '#0ea5e9', dark: '#0f172a' },
                    animation: { 'fade-in': 'fadeIn 1s ease-out forwards' },
                    keyframes: { fadeIn: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } } }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .gradient-text { background: linear-gradient(to right, #0ea5e9, #3b82f6); -webkit-background-clip: text; background-clip: text; color: transparent; }
    </style>
</head>

<body class="bg-slate-950 text-white min-h-screen overflow-x-hidden">

    <div class="fixed inset-0 -z-10">
              <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-black to-slate-950"></div>
              <div class="absolute inset-0 bg-cover bg-center bg-fixed opacity-80"
                  style="background-image: url('img/background.jpg');">
              </div>
              <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/70"></div>
          </div>

    <nav class="fixed top-0 w-full z-50 border-b border-white/10 bg-black/50 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-3">
                <img src="img/logo.png" alt="Logo" class="h-9 w-9 rounded-lg">
                <span class="text-2xl font-black">takenncs<span class="text-primary">DEV</span></span>
            </a>

            <a href="steamauth/login.php" class="bg-primary hover:bg-sky-500 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg hover:shadow-primary/30 flex items-center gap-3">
                <i class="fab fa-steam"></i>
                Logi sisse
            </a>
        </div>
    </nav>

    <section class="relative min-h-screen flex items-center justify-center px-6 pt-20">
        <div class="text-center max-w-5xl mx-auto">
            <h1 class="text-6xl md:text-8xl font-black leading-tight">
                <span class="gradient-text">Template</span><br>
                <span class="text-white">Login</span>
            </h1>
            <p class="mt-8 text-xl md:text-2xl text-gray-300 max-w-2xl mx-auto">
                Puhas ja minimalistlik QBCore UCP login leht<br>
                Template
            </p>
            <div class="mt-12 flex flex-col sm:flex-row gap-6 justify-center">
                <a href="steamauth/login.php" 
                   class="group inline-flex items-center px-12 py-6 bg-primary hover:bg-sky-500 text-white text-xl font-bold rounded-2xl transition-all shadow-2xl hover:shadow-primary/50">
                    <i class="fab fa-steam mr-4 text-2xl"></i>
                    Logi sisse Steamiga
                    <svg class="ml-4 w-6 h-6 group-hover:translate-x-3 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="https://discord.gg/UsRpQn9Xzu" target="_blank"
                   class="px-12 py-6 border-2 border-gray-700 hover:border-primary rounded-2xl font-bold text-xl backdrop-blur transition">
                    <i class="fab fa-discord mr-3"></i> Discord
                </a>
            </div>
            <p class="mt-20 text-gray-500 text-sm">
                Made with <span class="text-red-500">♥</span> by takenncs-webdev
            </p>
        </div>
    </section>

</body>
</html>