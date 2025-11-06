/**
 * Script para controle do menu lateral (sidebar)
 * Toggle: abre/fecha ao clicar no botão
 * Fecha automaticamente ao clicar fora
 * Destaca automaticamente o item ativo baseado na URL
 */
document.addEventListener('DOMContentLoaded', function() {
    
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    console.log('Menu Toggle:', menuToggle);
    console.log('Sidebar:', sidebar);
    
    if (menuToggle && sidebar) {
        
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Evita que o clique propague
            sidebar.classList.toggle('hidden');
            
        
            console.log('Sidebar classes:', sidebar.className);
        });
        
        // Fecha a sidebar ao clicar fora (apenas em mobile)
      
    }
    
    // Adiciona classe 'active' no item do menu baseado na URL atual
    const menuItems = document.querySelectorAll('.sidebar .menu li');
    const currentPath = window.location.pathname;
    
    menuItems.forEach(function(item) {
        const link = item.querySelector('a');
        if (link) {
            const href = link.getAttribute('href');
            
            // Verifica se o href corresponde ao caminho atual
            if (href && currentPath.includes(href) && href !== '#') {
                // Remove active de todos os items primeiro
                menuItems.forEach(mi => mi.classList.remove('active'));
                // Adiciona active no item atual
                item.classList.add('active');
            }
        }
    });
});

