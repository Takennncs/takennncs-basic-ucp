<?php
session_start();
if (!isset($_SESSION['steamid'])) {
    header("Location: index.php");
    exit;
}
require 'steamauth/userInfo.php';
?>

<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>BSFRP — Töölaud</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen antialiased" x-data>

  <form id="notifForm" method="POST" class="hidden">
    <input type="hidden" name="action" x-ref="action">
    <input type="hidden" name="notification_id" x-ref="id">
  </form>

  <div class="flex h-screen" style="background-image: linear-gradient(rgba(15,23,42,0.9), rgba(15,23,42,1)), url('img/background.jpg'); background-size: cover; background-position: center;">

    <aside id="sidebar" class="w-72 bg-slate-900/80 glass border-r border-slate-700/50 p-5 hidden md:flex flex-col justify-between transition-all duration-300 ease-out backdrop-blur-xl">
      <div class="space-y-6">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 from-indigo-500">
            <div class="w-full h-full rounded-lg bg-slate-900 flex items-center justify-center">
              <img src="img/logo.png" alt="Logo" class="w-7 h-7 rounded">
            </div>
          </div>
          <div>
            <h1 class="text-base font-bold text-white">takenncs-template</h1>
            <p class="text-xs text-slate-400 leading-tight">Basic UCP</p>
          </div>
        </div>

        <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-slate-800/50 border border-slate-700/50">
          <span class="text-xs text-slate-400">Server</span>
          <span class="text-xs font-medium text-emerald-400 flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Aktiivne
          </span>
        </div>

        <nav class="space-y-1">
        <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg border transition-all duration-200 group bg-indigo-600/20 text-indigo-300 border-indigo-500/50">
        <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 17h18M3 12h18" />
        </svg>
        <span class="text-sm font-medium">Töölaud</span>
      </a>
        </nav>
      </div>

      <div class="border-t border-slate-700/50 pt-4">
        <p class="text-xs text-slate-500 text-center mt-3">© <span id="year"></span> takenncs</p>
      </div>
    </aside>

    <main class="flex-1 p-6 overflow-auto">
      <header class="flex items-center justify-between mb-6 gap-4">
        <div class="flex items-center gap-4">
          <button id="toggleSidebar" class="md:hidden p-2 rounded-lg bg-slate-800 hover:bg-slate-700">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
          <div>
            <h2 class="text-2xl font-bold">Töölaud</h2>
            <p class="text-gray-300">Tere, <strong><?php echo htmlspecialchars($steamprofile['personaname']); ?></strong></p>
          </div>
        </div>

        <div class="flex items-center gap-4">


    <div x-data="{
        userMenuOpen: false,
        activeTab: 'info' // info | connections | data | history
    }" class="relative">

<div x-data="{ userMenuOpen: false }" class="relative">
<button @click="userMenuOpen = true; $dispatch('close-others', 'user')" 
          class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-800 transition-all duration-200 group">
    <img src="<?php echo htmlspecialchars($steamprofile['avatarfull']); ?>" 
         alt="Avatar" class="w-10 h-10 rounded-full ring-2 ring-slate-700 group-hover:ring-indigo-500 transition">
    <div class="text-left hidden sm:block">
      <div class="text-sm font-semibold text-slate-200 group-hover:text-white">
        <?php echo htmlspecialchars($steamprofile['personaname']); ?>
      </div>
      <div class="text-xs text-slate-400"><?php echo $user_role; ?></div>
    </div>
  </button>

  <!-- Downtown menu -->
  <div x-show="userMenuOpen"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 scale-95"
       x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-95"
       @click.away="userMenuOpen = false"
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

    <div class="w-full max-w-sm rounded-2xl bg-slate-900 border border-slate-700 p-6 shadow-xl">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold">Basic</h4>
        <button @click="userMenuOpen = false" class="text-slate-400 hover:text-white">X</button>
      </div>

      <div class="space-y-4">
        <div class="flex items-center gap-3">
          <img src="<?php echo htmlspecialchars($steamprofile['avatarfull']); ?>" 
               alt="Avatar" class="w-12 h-12 rounded-full">
          <div>
            <div class="text-sm text-slate-200 font-semibold"><?php echo htmlspecialchars($steamprofile['personaname']); ?></div>
            <div class="text-xs text-slate-400"><?php echo $user_role; ?></div>
          </div>
        </div>

        <div class="pt-2 border-t border-slate-700"></div>

        <form action="logout.php" method="POST">
          <button type="submit" 
                  class="w-full px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-sm font-medium">
            Logi välja
          </button>
        </form>
      </div>
    </div>

  </div>
</div>

                          </form>
                        </div>
                  </div>
              </header>

      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
          <div class="p-4 rounded-lg bg-slate-800 border border-slate-700">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold">Karakterihaldus</h3>
              <div class="text-sm text-slate-400"><?= count($characters) ?> / <?= $max_characters ?> kasutusel</div>
            </div>
            <div class="space-y-3">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <?php
                for ($i = 0; $i < $max_characters; $i++) {
                    if (isset($characters[$i])) {
                        $char = $characters[$i];
                        $char_name = htmlspecialchars($char['firstname'] . ' ' . $char['lastname'], ENT_QUOTES, 'UTF-8');
                        $citizen_id = htmlspecialchars($char['citizenid'] ?? '', ENT_QUOTES, 'UTF-8');
                        $jobs = htmlspecialchars($char['jobs'] ?? 'Töötu', ENT_QUOTES, 'UTF-8');
                        $profilepic = $char['profilepic'] ?: 'https://via.placeholder.com/48';
                        echo '
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-700 flex items-center gap-3 hover:bg-slate-800/50 transition-colors">
                          <div class="w-12 h-12 rounded-md bg-slate-800 border border-slate-700 flex items-center justify-center">
                            <img class="h-12 w-12 rounded-md object-cover" src="' . $profilepic . '" alt="' . $char_name . '">
                          </div>
                          <div>
                            <div class="text-sm text-slate-300">' . $char_name . '</div>
                            <div class="text-xs text-slate-500">ID: ' . $citizen_id . '</div>
                            <div class="text-xs text-slate-500">Fraktsioon: ' . $jobs . '</div>
                          </div>
                        </div>
                        ';
                    } else {
                        echo '
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-700 flex items-center gap-3 hover:bg-slate-800/50 transition-colors">
                          <div class="w-12 h-12 rounded-md bg-slate-800 border border-slate-700 flex items-center justify-center">
                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                          </div>
                          <div>
                            <div class="text-sm text-slate-300">Karakter puudub</div>
                            <div class="text-xs text-slate-500">Loo karakter</div>
                          </div>
                        </div>
                        ';
                    }
                }
                ?>
              </div>

              <div class="pt-2 border-t border-slate-700"></div>
              <div class="flex gap-2">
              </div>
            </div>
          </div>
        </div>

<aside class="space-y-4">
  <div class="p-4 rounded-lg bg-slate-800 border border-slate-700">
    <h3 class="font-semibold mb-3">Top mängijad</h3>

    <?php if (!empty($topPlayers)): ?>
      <table class="w-full text-sm text-left">
        <thead class="text-slate-400 text-xs uppercase">
          <tr>
            <th class="py-2">Steam</th>
            <th class="py-2">Roll</th>
            <th class="py-2">Tunnid</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($topPlayers as $p): ?>
            <tr class="border-t border-slate-700">
              <td class="py-2"><?= htmlspecialchars($p['personaname'] ?? 'Tundmatu', ENT_QUOTES) ?></td>
              <td><?= htmlspecialchars($p['role'] ?? 'User', ENT_QUOTES) ?></td>
              <td class="text-slate-300"><?= (int)$p['gametime'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-slate-400">Ühtegi aktiivset mängijat pole.</p>
    <?php endif; ?>

  </div>


        </aside>
      </section>


</html>
