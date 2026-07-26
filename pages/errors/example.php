<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Loja Suspensa - Marketzap</title>
    <?php include 'config/head.php'; ?>
</head>
<!-- Ajustado para flex-col para o header ficar no topo e o card centralizado no resto da tela -->
<body class="bg-emerald-50 min-h-screen flex flex-col">

    <!-- Header no topo -->
    <?php include 'components/header.php'; ?>

    <!-- Container do Card centralizado -->
    <div class="flex-1 flex items-center justify-center p-6">
        <div class="max-w-md w-full bg-white shadow-2xl shadow-emerald-200/50 p-10 text-center border-t-4 border-red-600 rounded-none relative overflow-hidden">

            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-50 rotate-45"></div>

            <div class="w-24 h-24 bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-8 relative z-10 rounded-full">
                <ion-icon name="alert-circle" class="text-6xl"></ion-icon>
            </div>

            <h1 class="text-3xl font-black text-slate-950 uppercase tracking-tighter leading-none mb-3 relative z-10">
                Loja <br><span class="text-red-600">Suspensa</span>
            </h1>

            <p class="text-slate-600 text-sm font-medium leading-relaxed mb-10 relative z-10">
                A loja <strong><?= htmlspecialchars($store->store_name) ?></strong> está temporariamente indisponível. O acesso foi suspenso devido à expiração do plano de serviços da plataforma Marketzap.
            </p>

            <?php
            // Verifica se quem está vendo a página é o dono da loja
            $isOwner = false;
            if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $store->owner_id) {
                $isOwner = true;
            }
            ?>

            <?php if($isOwner): ?>
                <div class="bg-slate-900 p-6 mb-8 text-left relative z-10 border-l-4 border-red-600">
                    <div class="flex items-center gap-3 mb-3 border-b border-slate-700 pb-3">
                        <ion-icon name="person-circle" class="text-red-500 text-2xl"></ion-icon>
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] text-white">Área do Proprietário</h4>
                    </div>
                    <p class="text-xs text-slate-300 font-medium leading-relaxed">
                        Sua assinatura expirou. Para reativar sua <strong class="text-white">Loja</strong> e voltar a receber pedidos via WhatsApp, é necessário realizar a renovação ou upgrade do seu plano.
                    </p>
                </div>

                <div class="flex flex-col gap-4 relative z-10">
                    <a href="<?= url('/plans') ?>" class="group w-full bg-red-600 text-white font-black py-5 text-xs uppercase tracking-[0.2em] shadow-xl shadow-red-200 hover:bg-red-700 transition-all flex items-center justify-center gap-3 active:scale-95">
                        <ion-icon name="card-outline" class="text-lg"></ion-icon>
                        Renovar Agora
                    </a>
                    <a href="<?= url('/dashboard') ?>" class="text-slate-500 hover:text-slate-950 text-xs font-bold uppercase tracking-widest">
                        Ir para o Painel de Controle
                    </a>
                </div>
            <?php else: ?>
                <a href="<?= url('/') ?>" class="inline-flex items-center gap-3 bg-slate-950 text-white font-black px-10 py-5 text-xs uppercase tracking-[0.2em] hover:bg-slate-800 transition-all relative z-10 active:scale-95">
                    <ion-icon name="home-outline"></ion-icon>
                    Voltar ao Início
                </a>
            <?php endif; ?>

            <div class="mt-14 pt-8 border-t border-slate-100 relative z-10">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">Marketzap &copy; 2026</p>
            </div>
        </div>
    </div>

</body>
</html>
