<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Feed - Orbit</title>
    <?php include 'config/head.php'; ?>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col">

    <?php include 'components/header.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="max-w-4xl mx-auto w-full px-4 py-8 flex-1">

        <!-- Callout Banner para Visitantes (WeShare Soft-Card UI) -->
        <?php if (!$isLoggedIn): ?>
            <div class="mb-6 p-5 sm:p-6 bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-5 font-['Poppins'] relative overflow-hidden">
                
                <!-- Conteúdo do Banner (Ícone + Texto) -->
                <div class="flex items-start gap-4">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-800">
                            Junte-se à comunidade Orbit!
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5 leading-relaxed">
                            Crie sua conta para publicar conteúdos, interagir com a rede e expandir sua presença.
                        </p>
                    </div>
                </div>

                <!-- Botão Cadastrar -->
                <a href="<?= function_exists('url') ? url('/register') : '/register' ?>"
                class="w-full md:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs sm:text-sm px-6 py-2.5 rounded-full transition-all shadow-sm active:scale-95 flex items-center justify-center gap-2 flex-shrink-0">
                    <span>Cadastrar Agora</span>
                    <ion-icon name="arrow-forward-outline" class="text-base"></ion-icon>
                </a>

            </div>
        <?php endif; ?>

        <!-- Cabeçalho da Seção de Publicações (WeShare Soft-Card UI) -->
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100 font-['Poppins']">
            <!-- Título & Ícone Soft -->
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/60 flex-shrink-0">
                    <ion-icon name="newspaper-outline" class="text-base sm:text-lg"></ion-icon>
                </div>
                <h2 class="text-sm sm:text-base font-bold text-slate-800 tracking-tight">
                    Publicações Recentes
                </h2>
            </div>

            <!-- Contador de Posts (Soft Pill Badge) -->
            <span id="postsCount" class="inline-flex items-center text-xs font-medium text-slate-500 bg-slate-100/80 px-3 py-1 rounded-full border border-slate-200/50">
                Carregando...
            </span>
        </div>

        <!-- CONTAINER RENDERIZADO VIA JAVASCRIPT -->
        <div id="postsContainer" class="space-y-6">
            <!-- O script injeta o Skeleton Loading e posteriormente os posts aqui -->
        </div>

    </main>
    
    <!-- FOOTER -->
    <?php include 'components/footer.php'; ?>

   <!-- LÓGICA DE JAVASCRIPT (INFINITE SCROLL) -->
<script>
    // Configurações globais
    const API_URL = '<?= function_exists("url") ? url("/api/posts") : "/api/posts" ?>';
    const NEWPOST_URL = '<?= function_exists("url") ? url("/newpost") : "/newpost" ?>';
    const IS_LOGGED_IN = <?= $isLoggedIn ? 'true' : 'false' ?>;

    // Estado do Infinite Scroll
    let currentPage = 1;
    const limit = 5;
    let isLoading = false;
    let hasMore = true;
    let observer = null;

    document.addEventListener('DOMContentLoaded', () => {
        setupSentinel();
        fetchAndRenderPosts();
    });

    /**
     * Prepara a div sentinela e o IntersectionObserver
     */
    function setupSentinel() {
        const container = document.getElementById('postsContainer');
        if (!container) return;

        // Cria a div sentinela para detecção do scroll ao final da página
        let sentinel = document.getElementById('scrollSentinel');
        if (!sentinel) {
            sentinel = document.createElement('div');
            sentinel.id = 'scrollSentinel';
            sentinel.className = 'py-4 text-center';
            container.after(sentinel);
        }

        // Observer que dispara quando a sentinela entra na tela
        observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !isLoading && hasMore) {
                fetchAndRenderPosts();
            }
        }, { rootMargin: '200px' }); // Dispara 200px antes do final visível

        observer.observe(sentinel);
    }

    /**
     * Busca os posts paginados na API
     */
    async function fetchAndRenderPosts() {
        if (isLoading || !hasMore) return;

        isLoading = true;
        const container = document.getElementById('postsContainer');
        const sentinel = document.getElementById('scrollSentinel');
        const countElement = document.getElementById('postsCount');

        // Se for a primeira página, exibe os skeletons. Nas próximas, o spinner inferior.
        if (currentPage === 1) {
            container.innerHTML = getSkeletonHTML(3);
        } else if (sentinel) {
            sentinel.innerHTML = getBottomLoaderHTML();
        }

        try {
            const response = await fetch(`${API_URL}?page=${currentPage}&limit=${limit}`);
            const result = await response.json();

            if (result.success) {
                const posts = result.data?.posts || [];
                hasMore = result.data?.has_more ?? false;

                // Atualiza o contador de postagens no topo
                if (countElement && result.data?.total !== undefined) {
                    countElement.innerText = `${result.data.total} publicação(ões)`;
                }

                // Limpa skeletons na primeira carga
                if (currentPage === 1) {
                    container.innerHTML = '';
                    if (posts.length === 0) {
                        container.innerHTML = getEmptyStateHTML();
                        if (sentinel) sentinel.innerHTML = '';
                        return;
                    }
                }

                // Renderiza (anexa) os posts sem sobrescrever os anteriores
                appendPosts(posts, container);

                // Incrementa a página para a próxima chamada
                currentPage++;

                // Limpa o loader da sentinela se não houver mais posts
                if (sentinel) {
                    sentinel.innerHTML = !hasMore && container.children.length > 0 
                        ? '<p class="text-xs text-slate-400 py-4 font-medium">Você chegou ao fim do feed.</p>' 
                        : '';
                }

            } else {
                if (currentPage === 1) {
                    renderErrorState(container, result.message || 'Erro ao carregar as publicações.');
                }
            }
        } catch (error) {
            console.error('Erro no carregamento do feed:', error);
            if (currentPage === 1) {
                renderErrorState(container, 'Não foi possível conectar ao servidor. Verifique sua conexão.');
            }
        } finally {
            isLoading = false;
        }
    }

    /**
     * Anexa novos cards de post ao container
     */
    function appendPosts(posts, container) {
        const html = posts.map(post => createPostCardHTML(post)).join('');
        container.insertAdjacentHTML('beforeend', html);
    }

    /**
     * Card de Post
     */
    function createPostCardHTML(post) {
        const authorName = post.author_name || 'Usuário Anônimo';
        const authorUsername = post.author_username || 'usuario';
        const authorInitial = authorName.charAt(0).toUpperCase();

        const avatarHTML = (post.author_avatar && post.author_avatar.trim() !== '')
            ? `<img src="${escapeHtml(post.author_avatar)}" alt="${escapeHtml(authorName)}" class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100">`
            : `<div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-semibold text-sm flex items-center justify-center border border-blue-100/60">${authorInitial}</div>`;

        const formattedContent = escapeHtml(post.content).replace(/\n/g, '<br>');

        return `
            <article class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 font-['Poppins'] overflow-hidden mb-5">
                <div class="p-4 sm:p-5 pb-3 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative flex-shrink-0">
                            ${avatarHTML}
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-xs sm:text-sm font-semibold text-slate-800 hover:text-blue-600 transition-colors cursor-pointer truncate leading-snug">
                                ${escapeHtml(authorName)}
                            </h3>
                            <span class="text-[11px] font-normal text-slate-400 block truncate">
                                @${escapeHtml(authorUsername)}
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium block mt-0.5">
                                ${formatDate(post.created_at)}
                            </span>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-50 transition-colors flex-shrink-0">
                        <ion-icon name="ellipsis-horizontal" class="text-lg block"></ion-icon>
                    </button>
                </div>

                <div class="px-5 py-2 space-y-2">
                    ${post.title ? `<h2 class="text-sm sm:text-base font-bold text-slate-800 leading-snug">${escapeHtml(post.title)}</h2>` : ''}
                    <div class="text-xs sm:text-sm text-slate-600 font-normal leading-relaxed">
                        ${formattedContent}
                    </div>
                </div>

                <div class="px-5 py-3 mt-2 border-t border-slate-100/60 flex items-center justify-between text-xs text-slate-500">
                    <div class="flex items-center gap-1 sm:gap-2">
                        <button type="button" class="group flex items-center gap-1.5 px-3 py-1.5 rounded-full hover:bg-rose-50 hover:text-rose-600 transition-all font-medium text-slate-500">
                            <ion-icon name="heart-outline" class="text-base group-hover:scale-110 transition-transform"></ion-icon>
                            <span class="text-xs">Curtir</span>
                        </button>
                        <button type="button" class="group flex items-center gap-1.5 px-3 py-1.5 rounded-full hover:bg-blue-50 hover:text-blue-600 transition-all font-medium text-slate-500">
                            <ion-icon name="chatbubble-outline" class="text-base group-hover:scale-110 transition-transform"></ion-icon>
                            <span class="text-xs">Comentar</span>
                        </button>
                    </div>
                    <button type="button" class="group p-2 rounded-full hover:bg-slate-100 hover:text-slate-800 transition-all" title="Compartilhar">
                        <ion-icon name="share-social-outline" class="text-base group-hover:rotate-12 transition-transform block"></ion-icon>
                    </button>
                </div>
            </article>
        `;
    }

    /**
     * Indicador de carregamento no rodape (para paginas adicionais)
     */
    function getBottomLoaderHTML() {
        return `
            <div class="flex items-center justify-center gap-2 py-4 text-xs font-semibold text-slate-400">
                <span class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></span>
                Carregando mais publicações...
            </div>
        `;
    }

    function getEmptyStateHTML() {
        const actionButton = IS_LOGGED_IN
            ? `<a href="${NEWPOST_URL}" class="inline-block bg-blue-600 text-white text-xs font-bold px-6 py-3 rounded-full hover:bg-blue-700 transition-colors mt-2">
                Criar Primeiro Post
               </a>`
            : '';

        return `
            <div class="bg-white border border-slate-100 rounded-2xl p-12 text-center space-y-4">
                <div class="w-12 h-12 bg-slate-50 text-slate-400 flex items-center justify-center mx-auto rounded-full">
                    <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Nenhuma publicação encontrada</h4>
                    <p class="text-xs text-slate-400 mt-1">Seja o primeiro a compartilhar um conteúdo!</p>
                </div>
                ${actionButton}
            </div>
        `;
    }

    function getSkeletonHTML(count = 3) {
        let html = '';
        for (let i = 0; i < count; i++) {
            html += `
                <div class="bg-white border border-slate-100 rounded-2xl p-6 space-y-4 animate-pulse mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-200 rounded-full"></div>
                        <div class="space-y-2">
                            <div class="w-32 h-3 bg-slate-200 rounded"></div>
                            <div class="w-20 h-2 bg-slate-200 rounded"></div>
                        </div>
                    </div>
                    <div class="w-2/3 h-4 bg-slate-200 rounded"></div>
                    <div class="space-y-2">
                        <div class="w-full h-3 bg-slate-200 rounded"></div>
                        <div class="w-4/5 h-3 bg-slate-200 rounded"></div>
                    </div>
                </div>
            `;
        }
        return html;
    }

    function renderErrorState(container, message) {
        container.innerHTML = `
            <div class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center space-y-3">
                <div class="w-10 h-10 bg-red-100 text-red-600 flex items-center justify-center mx-auto rounded-full">
                    <ion-icon name="alert-circle-outline" class="text-2xl"></ion-icon>
                </div>
                <p class="text-xs font-bold text-red-700">${escapeHtml(message)}</p>
                <button onclick="currentPage=1; fetchAndRenderPosts()"
                        class="px-5 py-2.5 bg-red-600 text-white text-xs font-bold rounded-full hover:bg-red-700 transition-colors">
                    Tentar Novamente
                </button>
            </div>
        `;
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;

        return date.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
</script>
</body>
</html>
