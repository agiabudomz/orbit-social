<!-- FOOTER -->
<footer class="bg-white border-t border-slate-200/80 pt-10 pb-8 mt-16 font-poppins">
<div class="max-w-4xl mx-auto px-4">
    
    <!-- Seção Superior: Identidade e Links -->
    <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-6 pb-8 border-b border-slate-100 text-center md:text-left">
        
        <!-- Branding -->
        <div class="flex flex-col items-center md:items-start gap-2 max-w-xs">
            <div class="flex items-center gap-2.5">
                <img src="<?= url('assets/img/orbit_logo.png') ?>" alt="Orbit" class="h-7 w-auto object-contain">
                <span class="font-bold text-lg text-slate-800 tracking-tight">Orbit</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">Conectando ideias e pessoas em um só lugar.</p>
        </div>

        <!-- Navegação Rápida -->
        <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-xs font-medium text-slate-500">
            <a href="#" class="hover:text-blue-600 transition-colors">Sobre</a>
            <a href="#" class="hover:text-blue-600 transition-colors">Termos</a>
            <a href="#" class="hover:text-blue-600 transition-colors">Privacidade</a>
            <a href="#" class="hover:text-blue-600 transition-colors">Diretrizes</a>
            <a href="#" class="hover:text-blue-600 transition-colors">Suporte</a>
        </div>

    </div>

    <!-- Seção Inferior: Status + Copyright -->
    <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-400">
        
        <!-- Indicator de Status dos Servidores -->
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px]">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <!-- Sistemas operacionais -->
        </div>

        <!-- Copyright -->
        <p>&copy; <?= date('Y') ?> Orbit Inc. Todos os direitos reservados.</p>
        
    </div>

</div>
</footer>