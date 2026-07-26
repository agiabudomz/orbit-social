<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Entrar - Orbit</title>
    <?php include 'config/head.php'; ?>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col justify-center font-['Poppins',sans-serif]">

    <div class="max-w-md mx-auto w-full px-4 py-10 font-['Poppins']">

        <!-- Header & Logo -->
        <div class="text-center mb-8">
            <div class="inline-block transition-transform duration-300 hover:scale-105">
                <img src="<?= url('assets/img/orbit_logo.png') ?>"
                    alt="Marketzap Logo"
                    class="w-[160px] md:w-[190px] h-auto mx-auto drop-shadow-sm">
            </div>

            <!-- Badge de Subtítulo Soft -->
            <div class="mt-3">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-blue-50 text-blue-600 font-semibold text-xs rounded-full">
                    <!-- <ion-icon name="storefront-outline" class="text-sm"></ion-icon> -->
                    <span>Entrar na sua conta!</span>
                </span>
            </div>
        </div>

        <!-- Formulário de Login (WeShare Soft-Card UI) -->
        <form id="loginForm" class="space-y-5 bg-white p-6 md:p-8 rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-slate-100">

            <!-- Campo E-mail ou Nome de Usuário -->
            <div class="space-y-1.5 group">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 px-1">
                    <ion-icon name="person-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>E-mail ou Usuário</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="mail-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="text" name="login" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="seu@email.com ou @usuario">
                </div>
            </div>

            <!-- Campo Senha -->
            <div class="space-y-1.5 group">
                <div class="flex justify-between items-center px-1">
                    <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                        <ion-icon name="lock-closed-outline" class="text-blue-600 text-sm"></ion-icon>
                        <span>Senha de Acesso</span>
                    </label>
                    <a href="#" class="text-xs font-medium text-blue-600 hover:underline">Esqueci a senha</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="key-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="password" name="password" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Checkbox Concordar com os Termos -->
            <div class="flex items-center gap-2.5 py-1 px-1">
                <input type="checkbox" id="terms" name="terms" required
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-600/20 accent-blue-600 cursor-pointer">
                <label for="terms" class="text-xs text-slate-500 font-normal leading-snug cursor-pointer">
                    Concordo com os <a href="#" class="text-blue-600 font-medium hover:underline">Termos de Uso</a> e a <a href="#" class="text-blue-600 font-medium hover:underline">Privacidade</a>.
                </label>
            </div>

            <!-- Botão de Ação -->
            <button type="submit" id="btnSubmit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-6 py-3.5 rounded-full transition-all shadow-sm flex items-center justify-center gap-2 active:scale-[0.99] cursor-pointer group">
                <span>Entrar na Conta</span>
                <ion-icon name="arrow-forward-outline" class="text-base group-hover:translate-x-0.5 transition-transform"></ion-icon>
            </button>

            <!-- Box Feedback Erro/Sucesso -->
            <div id="formFeedback" class="hidden p-4 rounded-xl flex items-center gap-3.5 border transition-all">
                <div id="feedbackIconContainer" class="w-9 h-9 text-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                    <ion-icon id="feedbackIcon" name="" class="text-lg"></ion-icon>
                </div>
                <div>
                    <h5 id="feedbackTitle" class="text-xs font-semibold leading-tight mb-0.5"></h5>
                    <p id="feedbackMessage" class="text-xs font-normal leading-relaxed"></p>
                </div>
            </div>
        </form>

        <!-- Footer / Link para Cadastro -->
        <div class="text-center mt-6">
            <p class="text-slate-500 text-xs font-normal">
                Não tem uma conta? 
                <a href="<?= url('/register') ?>" class="text-blue-600 font-semibold hover:underline ml-1">
                    Criar conta agora
                </a>
            </p>
        </div>
    </div>

    <!-- Estilos de Animação -->
    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 0.8s linear infinite;
        }
    </style>

    <!-- Script de Envio do Formulário -->
    <script>
        document.getElementById('loginForm').onsubmit = async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmit');
            const feedback = document.getElementById('formFeedback');
            const originalHTML = btn.innerHTML;

            // Estado de Loading
            btn.disabled = true;
            btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div><span>Entrando...</span>';
            feedback.classList.add('hidden');

            const formData = new FormData(this);

            try {
                const response = await fetch('<?= url("/api/login") ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showFeedback('bg-emerald-50 border-emerald-100', 'text-emerald-800', 'text-emerald-600', 'bg-emerald-500', 'checkmark-circle-outline', 'Sucesso', result.message);
                    setTimeout(() => {
                        window.location.href = result.data?.redirect || '<?= url("/dashboard") ?>';
                    }, 1200);
                } else {
                    showFeedback('bg-rose-50 border-rose-100', 'text-rose-800', 'text-rose-600', 'bg-rose-500', 'alert-circle-outline', 'Erro ao Acessar', result.message);
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (error) {
                showFeedback('bg-rose-50 border-rose-100', 'text-rose-800', 'text-rose-600', 'bg-rose-500', 'warning-outline', 'Erro de Conexão', 'Não foi possível se comunicar com o servidor.');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        };

        function showFeedback(bgColor, textColor, msgColor, iconBg, iconName, title, message) {
            const el = document.getElementById('formFeedback');

            el.className = `${bgColor} p-4 rounded-xl flex items-center gap-3.5 border transition-all animate-in fade-in duration-300`;
            document.getElementById('feedbackTitle').className = `text-xs font-semibold leading-tight mb-0.5 ${textColor}`;
            document.getElementById('feedbackMessage').className = `text-xs font-normal leading-relaxed ${msgColor}`;
            document.getElementById('feedbackIconContainer').className = `w-9 h-9 text-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm ${iconBg}`;

            document.getElementById('feedbackIcon').setAttribute('name', iconName);
            document.getElementById('feedbackTitle').innerText = title;
            document.getElementById('feedbackMessage').innerText = message;

            el.classList.remove('hidden');
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>

</body>
</html>
