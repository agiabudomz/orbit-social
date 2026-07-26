<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Nova Publicação - Orbit</title>
    <?php include 'config/head.php'; ?>
</head>
<body class="bg-slate-100 min-h-screen font-['Poppins',sans-serif]">

    <?php include 'components/header.php'; ?>

    <div class="max-w-2xl mx-auto px-4 font-['Poppins']">
        <!-- Header da Página -->
        <div class="mt-10 flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 leading-tight">Criar Nova Publicação</h1>
                    <p class="text-xs text-slate-400 font-normal">Compartilhe novidades, conteúdos e atualizações no WeShare</p>
                </div>
            </div>

            <!-- Botão Voltar -->
            <a href="<?= url('/feed') ?>"
            class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-100 text-slate-600 font-medium text-xs rounded-full hover:bg-slate-50 shadow-sm transition-all">
                <ion-icon name="arrow-back-outline" class="text-base"></ion-icon>
                <span class="hidden sm:inline">Voltar ao Feed</span>
            </a>
        </div>

        <!-- Formulário de Publicação (WeShare Soft-Card UI) -->
        <form id="newpostForm" class="bg-white p-6 md:p-8 rounded-2xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.03)] space-y-6">

            <!-- Campo Título -->
            <div class="space-y-2 group">
                <div class="flex items-center justify-between px-1">
                    <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                        <ion-icon name="text-outline" class="text-blue-600 text-sm"></ion-icon>
                        <span>Título da Publicação</span>
                    </label>
                    <span id="titleCounter" class="text-[11px] font-medium text-slate-400 transition-colors">0 / 255</span>
                </div>
                <div class="relative">
                    <input type="text" id="titleInput" name="title" maxlength="255" required
                        class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-medium placeholder:text-slate-400 transition-all outline-none"
                        placeholder="Digite um título atrativo...">
                </div>
            </div>

            <!-- Campo Conteúdo -->
            <div class="space-y-2 group">
                <div class="flex items-center gap-1.5 px-1">
                    <ion-icon name="document-text-outline" class="text-blue-600 text-sm"></ion-icon>
                    <label class="text-xs font-semibold text-slate-700">Conteúdo da Publicação</label>
                </div>
                <div class="relative">
                    <textarea name="content" rows="7" required
                            class="w-full p-4 bg-slate-50/80 border border-slate-200/60 rounded-xl focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 text-slate-800 text-sm font-normal leading-relaxed placeholder:text-slate-400 transition-all outline-none resize-none"
                            placeholder="Escreva aqui o conteúdo do seu post..."></textarea>
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
                    <ion-icon name="paper-plane-outline" class="text-sm"></ion-icon>
                    <span>Publicar Agora</span>
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
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 0.8s linear infinite;
        }
    </style>

    <!-- JavaScript da Tela -->
    <script>
        // Contador de caracteres do Título
        const titleInput = document.getElementById('titleInput');
        const titleCounter = document.getElementById('titleCounter');

        titleInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            titleCounter.innerText = `${currentLength} / 255`;

            if (currentLength >= 250) {
                titleCounter.className = 'text-[11px] font-semibold text-rose-500';
            } else {
                titleCounter.className = 'text-[11px] font-medium text-slate-400';
            }
        });

        // Envio do Formulário via AJAX
        document.getElementById('newpostForm').onsubmit = async function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmit');
            const feedback = document.getElementById('formFeedback');
            const originalHTML = btn.innerHTML;

            // Estado de Carregamento
            btn.disabled = true;
            btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div><span>Enviando...</span>';
            feedback.classList.add('hidden');

            const formData = new FormData(this);

            try {
                const response = await fetch('<?= url("/api/newpost") ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showFeedback('bg-emerald-50 border-emerald-100', 'text-emerald-800', 'text-emerald-600', 'bg-emerald-500', 'checkmark-circle-outline', 'Sucesso!', result.message);

                    const redirectUrl = result.data?.redirect || '<?= url("/feed") ?>';
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1000);

                } else {
                    showFeedback('bg-rose-50 border-rose-100', 'text-rose-800', 'text-rose-600', 'bg-rose-500', 'alert-circle-outline', 'Atenção', result.message);
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (error) {
                showFeedback('bg-rose-50 border-rose-100', 'text-rose-800', 'text-rose-600', 'bg-rose-500', 'warning-outline', 'Erro de Conexão', 'Não foi possível enviar a publicação.');
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
        }
    </script>
</body>
</html>
