<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Criar Conta - Orbit</title>
    <?php include 'config/head.php'; ?>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col justify-center font-['Poppins',sans-serif] py-12 relative">

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
                    <ion-icon name="person-add-outline" class="text-sm"></ion-icon>
                    <span>Crie sua conta grátis</span>
                </span>
            </div>
        </div>

        <!-- Formulário de Registro (WeShare Soft-Card UI) -->
        <form id="registerForm" class="space-y-5 bg-white p-6 md:p-8 rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.03)] border border-slate-100">

            <!-- Campo Nome Completo -->
            <div class="space-y-1.5 group">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 px-1">
                    <ion-icon name="person-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Nome Completo</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="person-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="text" name="name" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="Seu nome completo">
                </div>
            </div>

            <!-- Campo Nome de Usuário (Username) -->
            <div class="space-y-1.5 group">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 px-1">
                    <ion-icon name="at-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Nome de Usuário (@)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="at-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="text" name="username" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="ex: joaodev">
                </div>
            </div>

            <!-- Campo E-mail -->
            <div class="space-y-1.5 group">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 px-1">
                    <ion-icon name="mail-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Endereço de E-mail</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="mail-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="email" name="email" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="seu@email.com">
                </div>
            </div>

            <!-- Campo Título/Cargo (Abre Modal ao Clicar) -->
            <div class="space-y-1.5 group">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 px-1">
                    <ion-icon name="briefcase-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Título / Cargo</span>
                </label>
                <div class="relative cursor-pointer" onclick="openTitleModal()">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-hover:text-blue-600 transition-colors">
                        <ion-icon name="briefcase-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="text" id="titleInput" name="title" readonly required
                        class="w-full pl-11 pr-10 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all cursor-pointer select-none outline-none"
                        placeholder="Clique para selecionar seu cargo">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 group-hover:text-blue-600 transition-colors">
                        <ion-icon name="chevron-down-outline" class="text-base"></ion-icon>
                    </div>
                </div>
            </div>

            <!-- Campo Senha -->
            <div class="space-y-1.5 group">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 px-1">
                    <ion-icon name="lock-closed-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Senha</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="key-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="password" name="password" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="Mínimo de 6 caracteres">
                </div>
            </div>

            <!-- Campo Confirmar Senha -->
            <div class="space-y-1.5 group">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5 px-1">
                    <ion-icon name="shield-checkmark-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Confirmar Senha</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                        <ion-icon name="key-outline" class="text-lg"></ion-icon>
                    </div>
                    <input type="password" name="password_confirmation" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="Repita sua senha">
                </div>
            </div>

            <!-- Checkbox Concordar com os Termos -->
            <div class="flex items-center gap-2.5 py-1 px-1">
                <input type="checkbox" id="terms" name="terms" required
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-600/20 accent-blue-600 cursor-pointer">
                <label for="terms" class="text-xs text-slate-500 font-normal leading-snug cursor-pointer">
                    Concordo com os <a href="#" class="text-blue-600 font-medium hover:underline">Termos de Serviço</a> e a <a href="#" class="text-blue-600 font-medium hover:underline">Política de Privacidade</a>.
                </label>
            </div>

            <!-- Botão de Ação -->
            <button type="submit" id="btnSubmit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-6 py-3.5 rounded-full transition-all shadow-sm flex items-center justify-center gap-2 active:scale-[0.99] cursor-pointer group">
                <span>Criar Minha Conta</span>
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

        <!-- Footer / Link para Login -->
        <div class="text-center mt-6">
            <p class="text-slate-500 text-xs font-normal">
                Já possui uma conta?
                <a href="<?= url('/login') ?>" class="text-blue-600 font-semibold hover:underline ml-1">
                    Fazer login agora
                </a>
            </p>
        </div>

    </div>

    <!-- MODAL DE SELEÇÃO DE CARGO / TÍTULO (WESHARE SOFT-CARD UI) -->
    <div id="titleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 hidden animate-in fade-in duration-200 font-['Poppins']">

        <!-- Container do Modal -->
        <div class="bg-white border border-slate-100 shadow-[0_10px_30px_rgba(0,0,0,0.08)] rounded-2xl max-w-lg w-full p-6 md:p-7 space-y-5 relative transform transition-all">

            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <ion-icon name="briefcase-outline" class="text-lg"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Selecione seu Cargo</h3>
                        <p class="text-xs text-slate-400 font-normal">Escolha a opção que melhor descreve seu perfil</p>
                    </div>
                </div>

                <!-- Botão Fechar Modal -->
                <button type="button" onclick="closeTitleModal()"
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100/80 text-slate-400 hover:bg-slate-200/80 hover:text-slate-600 transition-all cursor-pointer">
                    <ion-icon name="close-outline" class="text-lg"></ion-icon>
                </button>
            </div>

            <!-- Lista de Opções Pré-selecionadas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-[320px] overflow-y-auto pr-1">

                <button type="button" onclick="selectTitle('Gestor de Loja')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="storefront-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Gestor de Loja</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Administrador / Gerente</span>
                    </div>
                </button>

                <button type="button" onclick="selectTitle('Desenvolvedor Fullstack')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="code-slash-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Desenvolvedor</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Software & Web</span>
                    </div>
                </button>

                <button type="button" onclick="selectTitle('Empreendedor')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="rocket-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Empreendedor</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Fundador / CEO</span>
                    </div>
                </button>

                <button type="button" onclick="selectTitle('Vendedor / Atendente')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="cart-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Vendedor</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Atendimento ao Cliente</span>
                    </div>
                </button>

                <button type="button" onclick="selectTitle('Gestor de Tráfego & Mídia')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="megaphone-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Marketing & Mídia</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Social Media & Anúncios</span>
                    </div>
                </button>

                <button type="button" onclick="selectTitle('Designer UI/UX')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="color-palette-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Designer</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Criação Visual</span>
                    </div>
                </button>

                <button type="button" onclick="selectTitle('Consultor Comercial')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="briefcase-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Consultor</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Vendas e Processos</span>
                    </div>
                </button>

                <button type="button" onclick="selectTitle('Autônomo / Freelancer')"
                        class="title-option flex items-center gap-3 p-3 bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200/80 rounded-xl text-left transition-all group cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200/60 flex items-center justify-center text-slate-500 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors shadow-sm flex-shrink-0">
                        <ion-icon name="flash-outline" class="text-base"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-xs font-semibold text-slate-700 group-hover:text-blue-600 truncate">Autônomo</span>
                        <span class="block text-[11px] text-slate-400 font-normal truncate">Prestador de Serviços</span>
                    </div>
                </button>

            </div>

            <!-- Footer do Modal -->
            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeTitleModal()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200/80 text-slate-600 text-xs font-medium rounded-full transition-all cursor-pointer">
                    Cancelar
                </button>
            </div>

        </div>
    </div>

    <!-- Estilos de Animação Soft -->
    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 0.8s linear infinite;
        }
    </style>

    <!-- Scripts do Modal e Envio do Formulário -->
    <script>
        // --- FUNÇÕES DO MODAL ---
        function openTitleModal() {
            const modal = document.getElementById('titleModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Bloqueia a rolagem do fundo
        }

        function closeTitleModal() {
            const modal = document.getElementById('titleModal');
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restaura a rolagem
        }

        function selectTitle(titleValue) {
            const input = document.getElementById('titleInput');
            if (input) {
                input.value = titleValue;

                // Destaca o input preenchido com estilo suave WeShare
                input.classList.remove('placeholder:text-slate-400');
                input.classList.add('border-blue-500/40', 'bg-blue-50/30');
            }

            closeTitleModal();
        }

        // Fechar modal ao clicar no Backdrop (área fora do modal)
        document.getElementById('titleModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTitleModal();
            }
        });

        // Fechar modal com a tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTitleModal();
            }
        });

        // --- SUBMISSÃO ASSÍNCRONA DO FORMULÁRIO ---
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.onsubmit = async function(e) {
                e.preventDefault();

                const btn = document.getElementById('btnSubmit');
                const feedback = document.getElementById('formFeedback');
                const originalHTML = btn.innerHTML;

                // Validação simples do campo de cargo/título
                const titleInput = document.getElementById('titleInput');
                const titleVal = titleInput ? titleInput.value.trim() : '';

                if (!titleVal) {
                    showFeedback(
                        'bg-rose-50 border-rose-100', 
                        'text-rose-800', 
                        'text-rose-600', 
                        'bg-rose-500', 
                        'alert-circle-outline', 
                        'Atenção', 
                        'Por favor, selecione seu cargo ou perfil profissional.'
                    );
                    openTitleModal();
                    return;
                }

                // Ativa estado de carregamento (Loading state)
                btn.disabled = true;
                btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div><span>Cadastrando...</span>';
                feedback.classList.add('hidden');

                const formData = new FormData(this);

                try {
                    const response = await fetch('<?= url("/api/register") ?>', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        showFeedback(
                            'bg-emerald-50 border-emerald-100', 
                            'text-emerald-800', 
                            'text-emerald-600', 
                            'bg-emerald-500', 
                            'checkmark-circle-outline', 
                            'Sucesso!', 
                            result.message
                        );

                        const redirectUrl = result.data?.redirect || '<?= url("/dashboard") ?>';
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 1200);

                    } else {
                        showFeedback(
                            'bg-rose-50 border-rose-100', 
                            'text-rose-800', 
                            'text-rose-600', 
                            'bg-rose-500', 
                            'alert-circle-outline', 
                            'Atenção', 
                            result.message
                        );
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                } catch (error) {
                    showFeedback(
                        'bg-rose-50 border-rose-100', 
                        'text-rose-800', 
                        'text-rose-600', 
                        'bg-rose-500', 
                        'warning-outline', 
                        'Erro de Conexão', 
                        'Não foi possível se comunicar com o servidor.'
                    );
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            };
        }

        // Função Utilitária de Feedbacks com Cores Soft
        function showFeedback(bgColor, textColor, msgColor, iconBg, iconName, title, message) {
            const el = document.getElementById('formFeedback');
            if (!el) return;

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
