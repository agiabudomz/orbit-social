<?php
// Fallbacks de segurança caso as variáveis não venham diretamente da controller
$isLoggedIn = $isLoggedIn ?? (class_exists('Auth') ? Auth::check() : false);
$currentUser = $currentUser ?? ($isLoggedIn ? Auth::user() : null);
?>    
    
<!-- NAVBAR SUPERIOR TECNOLÓGICA (SEM ARREDONDAMENTOS) -->
<header class="bg-white/95 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40 shadow-sm font-['Poppins']">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">

        <!-- Logo & Brand (Orbit) -->
        <a href="<?= function_exists('url') ? url('/feed') : '/feed' ?>" class="flex items-center group">
            <!-- Logo Desktop (Escondido no Mobile) -->
            <img src="<?= url('assets/img/orbit_logo.png') ?>" 
                alt="Orbit Logo" 
                class="hidden md:block h-8 w-auto transition-transform duration-300 group-hover:scale-105">

            <!-- Logo 2 (Apenas Mobile) -->
            <img src="<?= url('assets/img/orbit_logo2.png') ?>" 
                alt="Orbit Logo" 
                class="block md:hidden h-8 w-auto transition-transform duration-300 group-hover:scale-105">
        </a>

        <!-- Ações do Cabeçalho -->
        <div class="flex items-center gap-3">
            <?php if ($isLoggedIn): ?>
                
                <!-- Botão Criar Publicação (Totalmente oculto no Mobile) -->
                <a href="<?= function_exists('url') ? url('/newpost') : '/newpost' ?>"
                class="hidden sm:flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-4 py-2 rounded-full shadow-sm hover:shadow transition-all active:scale-95">
                    <ion-icon name="add-outline" class="text-base"></ion-icon>
                    <span>Criar Publicação</span>
                </a>

                <!-- Botão Criar Publicação (Apenas Mobile - Circular) -->
                <a href="<?= function_exists('url') ? url('/newpost') : '/newpost' ?>"
                    class="flex sm:hidden items-center justify-center w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-md shadow-blue-500/40 hover:scale-110 active:scale-90 transition-all duration-300 animate-pulse hover:animate-none"
                    title="Criar Publicação">
                        <ion-icon name="add-outline" class="text-lg"></ion-icon>
                    </a>

                <!-- para admin -->
                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                    <!-- Botão Criar com IA (Desktop) -->
                    <a href="<?= function_exists('url') ? url('/newpost-ai') : '/newpost-ai' ?>"
                    class="hidden sm:flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium text-xs px-4 py-2 rounded-full shadow-sm hover:shadow-md transition-all active:scale-95"
                    title="Gerar Posts com IA">
                        <ion-icon name="sparkles-outline" class="text-base text-blue-200"></ion-icon>
                        <span>Criar com IA</span>
                    </a>

                    <!-- Botão Criar com IA (Apenas Mobile - Circular) -->
                    <a href="<?= function_exists('url') ? url('/newpost-ai') : '/newpost-ai' ?>"
                    class="flex sm:hidden items-center justify-center w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-full shadow-md shadow-indigo-500/30 hover:scale-110 active:scale-90 transition-all duration-300"
                    title="Gerar Posts com IA">
                        <ion-icon name="sparkles-outline" class="text-lg text-blue-200"></ion-icon>
                    </a>
                <?php endif; ?>

                <!-- Perfil do Usuário & Logout -->
                <div class="flex items-center gap-3 pl-3 border-l border-slate-100">
                    <div class="flex items-center gap-2.5 cursor-pointer">
                        <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 border border-blue-100 font-semibold text-xs flex items-center justify-center">
                            <?= strtoupper(substr($currentUser->name ?? $currentUser['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="hidden md:inline text-xs font-semibold text-slate-800">
                            <?= htmlspecialchars($currentUser->name ?? $currentUser['name'] ?? 'Usuário') ?>
                        </span>
                    </div>

                    <!-- Botão Logout -->
                    <a href="<?= function_exists('url') ? url('/logout') : '/logout' ?>"
                    title="Sair da Conta"
                    class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 p-2 rounded-full transition-all flex items-center justify-center">
                        <ion-icon name="log-out-outline" class="text-lg"></ion-icon>
                    </a>
                </div>
            <?php else: ?>
                <!-- Visitante / Não Logado -->
                <a href="<?= function_exists('url') ? url('/login') : '/login' ?>"
                class="text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 px-4 py-2 rounded-full transition-all">
                    Entrar
                </a>
                <a href="<?= function_exists('url') ? url('/register') : '/register' ?>"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2 rounded-full transition-all shadow-sm active:scale-95">
                    Criar Conta
                </a>
            <?php endif; ?>
        </div>

    </div>
</header>