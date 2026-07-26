<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Gerar Conteúdo com IA - Orbit</title>
    <?php include 'config/head.php'; ?>
</head>
<body class="bg-slate-100 min-h-screen font-['Poppins',sans-serif]">

    <?php include 'components/header.php'; ?>

    <div class="max-w-2xl mx-auto px-4 font-['Poppins']">
        <!-- Header da Página -->
        <div class="mt-10 flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <!-- <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">
                    <ion-icon name="sparkles-outline"></ion-icon>
                </div> -->
                <div>
                    <h1 class="text-lg font-bold text-slate-900 leading-tight flex items-center gap-2">
                        <span>Gerar Posts com IA</span>
                        <span class="bg-blue-100/80 text-blue-700 text-[10px] font-semibold px-2.5 py-0.5 rounded-full border border-blue-200/50">AI</span>
                    </h1>
                    <p class="text-xs text-slate-400 font-normal">Crie publicações de alto engajamento em segundos</p>
                </div>
            </div>

            <!-- Botão Voltar -->
            <a href="<?= url('/feed') ?>"
            class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-100 text-slate-600 font-medium text-xs rounded-full hover:bg-slate-50 shadow-sm transition-all">
                <ion-icon name="arrow-back-outline" class="text-base"></ion-icon>
                <span class="hidden sm:inline">Voltar ao Feed</span>
            </a>
        </div>

        <!-- Formulário de Geração por IA (WeShare Soft-Card UI) -->
        <form id="aiGenerateForm" class="bg-white p-6 md:p-8 rounded-2xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.03)] space-y-6">

            <!-- Banner do Assistente -->
            <div class="bg-slate-900 text-white p-4 rounded-xl shadow-sm flex items-start gap-3.5 border border-slate-800">
                <!-- <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <ion-icon name="sparkles-sharp" class="text-lg"></ion-icon>
                </div> -->
                <div class="text-xs space-y-1">
                    <h4 class="font-bold text-blue-400">Assistente Inteligente</h4>
                    <p class="text-slate-300 font-normal leading-relaxed">
                        Preencha o nicho e os parâmetros abaixo. A IA criará o título, o texto completo e agendará as publicações automaticamente no seu feed.
                    </p>
                </div>
            </div>

            <!-- Campo Nicho / Tema -->
            <div class="space-y-2 group">
                <div class="flex items-center justify-between px-1">
                    <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                        <ion-icon name="bulb-outline" class="text-blue-600 text-sm"></ion-icon>
                        <span>Nicho ou Tema do Conteúdo</span>
                    </label>
                    <span class="text-[11px] font-medium text-red-500">* Obrigatório</span>
                </div>
                <div class="relative">
                    <input type="text" id="nichoInput" name="nicho" required
                        class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="Ex: Marketing para restaurantes, Dicas de programação, Fitness...">
                </div>

                <!-- Sugestões de Nicho Rápido -->
                <div class="pt-1 flex items-center gap-2 flex-wrap">
                    <span class="text-[11px] font-medium text-slate-400 mr-1">Exemplos:</span>
                    <button type="button" onclick="setNicho('E-commerce e Vendas Online')" class="text-xs font-medium bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-600 px-3 py-1 rounded-full transition-all">#Ecommerce</button>
                    <button type="button" onclick="setNicho('Tecnologia e Inovação')" class="text-xs font-medium bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-600 px-3 py-1 rounded-full transition-all">#Tecnologia</button>
                    <button type="button" onclick="setNicho('Empreendedorismo e Negócios')" class="text-xs font-medium bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-600 px-3 py-1 rounded-full transition-all">#Negócios</button>
                    <button type="button" onclick="setNicho('Saúde e Bem-estar')" class="text-xs font-medium bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-600 px-3 py-1 rounded-full transition-all">#Saúde</button>
                </div>
            </div>

            <!-- Seção de Parâmetros (2 Colunas) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">

                <!-- Tom de Voz -->
                <div class="space-y-2">
                    <div class="flex items-center gap-1.5 px-1">
                        <ion-icon name="chatbubbles-outline" class="text-blue-600 text-sm"></ion-icon>
                        <label class="text-xs font-semibold text-slate-700">Tom de Voz</label>
                    </div>
                    <div class="relative flex items-center">
                        <select name="tom_de_voz"
                                class="w-full pl-4 pr-10 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium transition-all outline-none appearance-none cursor-pointer">
                            <option value="engajador" selected>Engajador e Dinâmico</option>
                            <option value="profissional">Profissional e Corporativo</option>
                            <option value="descontraido">Descontraído e Divertido</option>
                            <option value="persuasivo">Persuasivo (Foco em Vendas)</option>
                            <option value="educativo">Educativo e Didático</option>
                            <option value="inspiracional">Inspiracional e Motivacional</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="chevron-down-outline" class="text-base"></ion-icon>
                        </div>
                    </div>
                </div>

                <!-- Quantidade de Posts -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-1.5">
                            <ion-icon name="layers-outline" class="text-blue-600 text-sm"></ion-icon>
                            <label class="text-xs font-semibold text-slate-700">Quantidade de Posts</label>
                        </div>
                        <span id="qtdDisplay" class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-100">1 Post</span>
                    </div>
                    <div class="relative flex items-center bg-slate-50/80 border border-slate-200/60 rounded-xl px-4 py-3.5 h-[46px]">
                        <input type="range" id="qtdInput" name="quantidade" min="1" max="10" value="1"
                            class="w-full h-2 bg-slate-200 accent-blue-600 rounded-lg cursor-pointer">
                    </div>
                </div>

            </div>

            <!-- Botões de Ação -->
            <div class="pt-2 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="<?= url('/feed') ?>"
                class="px-5 py-2.5 bg-slate-100 text-slate-600 font-medium text-xs rounded-full hover:bg-slate-200/70 transition-colors">
                    Cancelar
                </a>

                <button type="submit" id="btnSubmit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-6 py-2.5 rounded-full transition-all shadow-sm flex items-center justify-center gap-2 active:scale-[0.98] cursor-pointer">
                    <!-- <ion-icon name="sparkles-outline" class="text-sm"></ion-icon> -->
                    <span id="btnText">Gerar Publicações</span>
                </button>
            </div>

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
    </div>

    <!-- FOOTER -->
    <?php include 'components/footer.php'; ?>
    
    <!-- Estilos de Animação -->
    <style>
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 0.8s linear infinite;
        }
    </style>

    <!-- JavaScript da Tela -->
    <script>
        // Atualiza a exibição visual do contador de posts
        const qtdInput = document.getElementById('qtdInput');
        const qtdDisplay = document.getElementById('qtdDisplay');

        qtdInput.addEventListener('input', function() {
            const val = this.value;
            qtdDisplay.innerText = `${val} ${val == 1 ? 'Post' : 'Posts'}`;
        });

        // Preenche o Nicho via tags de atalho
        function setNicho(valor) {
            const input = document.getElementById('nichoInput');
            input.value = valor;
            input.focus();
        }

        // Envio do Formulário para o Endpoint /api/ai/generate
        document.getElementById('aiGenerateForm').onsubmit = async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmit');
            const btnText = document.getElementById('btnText');
            const feedback = document.getElementById('formFeedback');
            const originalHTML = btn.innerHTML;

            // Ativa Estado de Carregamento
            btn.disabled = true;
            btn.classList.add('opacity-90', 'cursor-not-allowed');
            btn.innerHTML = `
                <div class="w-4 h-4 border-2 border-white border-t-transparent animate-spin relative z-10"></div>
                <span class="text-xs uppercase tracking-[0.15em] relative z-10">Gerando Conteúdo com IA...</span>
            `;
            feedback.classList.add('hidden');

            const formData = new FormData(this);

            try {
                const response = await fetch('<?= url("/api/ai/generate") ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showFeedback(
                        'bg-blue-50 border-blue-200',
                        'text-blue-600',
                        'text-blue-900',
                        'bg-blue-600',
                        'checkmark-circle',
                        'Sucesso!',
                        result.message
                    );

                    // Redirecionamento após o sucesso
                    const redirectUrl = result.data?.redirect || '<?= url("/feed") ?>';
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1200);

                } else {
                    showFeedback(
                        'bg-red-50 border-red-200',
                        'text-red-600',
                        'text-red-900',
                        'bg-red-600',
                        'alert-circle',
                        'Atenção',
                        result.message || 'Não foi possível gerar as publicações.'
                    );
                    btn.disabled = false;
                    btn.classList.remove('opacity-90', 'cursor-not-allowed');
                    btn.innerHTML = originalHTML;
                }

            } catch (error) {
                showFeedback(
                    'bg-red-50 border-red-200',
                    'text-red-600',
                    'text-red-900',
                    'bg-red-600',
                    'warning',
                    'Erro de Conexão',
                    'Falha ao comunicar com o servidor da IA. Tente novamente.'
                );
                btn.disabled = false;
                btn.classList.remove('opacity-90', 'cursor-not-allowed');
                btn.innerHTML = originalHTML;
            }
        };

        // Função para Renderizar a Caixa de Feedback
        function showFeedback(bgColor, textColor, msgColor, iconBg, iconName, title, message) {
            const el = document.getElementById('formFeedback');

            el.className = `${bgColor} p-4 flex items-center gap-4 border transition-all`;
            document.getElementById('feedbackTitle').className = `text-[10px] font-black uppercase tracking-widest leading-none mb-1 ${textColor}`;
            document.getElementById('feedbackMessage').className = `text-xs font-bold leading-tight ${msgColor}`;
            document.getElementById('feedbackIconContainer').className = `w-10 h-10 text-white flex items-center justify-center flex-shrink-0 shadow-md ${iconBg}`;

            document.getElementById('feedbackIcon').setAttribute('name', iconName);
            document.getElementById('feedbackTitle').innerText = title;
            document.getElementById('feedbackMessage').innerText = message;

            el.classList.remove('hidden');
        }
    </script>
</body>
</html>