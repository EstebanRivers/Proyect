// Navegación optimizada sin dependencias externas
class SecureNavigation {
    constructor() {
        this.cache = new Map();
        this.currentPage = window.location.pathname;
        this.isLoading = false;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.preloadCriticalPages();
        this.updateActiveMenuItem();
    }

    setupEventListeners() {
        // Interceptar clics en enlaces del menú
        document.addEventListener('click', (e) => {
            const link = e.target.closest('.menu a, .brand a');
            if (link && this.shouldIntercept(link)) {
                e.preventDefault();
                this.navigate(link.href);
            }
        });

        // Manejar botón atrás/adelante del navegador
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.page) {
                this.loadPage(e.state.page, false);
            }
        });

        // Precargar al hacer hover
        document.addEventListener('mouseenter', (e) => {
            const link = e.target.closest('.menu a');
            if (link && this.shouldIntercept(link)) {
                this.preloadPage(link.href);
            }
        }, true);
    }

    shouldIntercept(link) {
        // Solo interceptar enlaces internos del menú
        return link.hostname === window.location.hostname && 
               !link.hasAttribute('data-no-intercept') &&
               !link.href.includes('logout');
    }

    async navigate(url) {
        if (this.isLoading || url === window.location.href) return;
        
        this.showLoadingState();
        
        try {
            const content = await this.loadPage(url, true);
            if (content) {
                this.updatePage(content, url);
                this.updateActiveMenuItem();
            }
        } catch (error) {
            console.error('Navigation error:', error);
            // Fallback a navegación normal
            window.location.href = url;
        } finally {
            this.hideLoadingState();
        }
    }

    async loadPage(url, updateHistory = true) {
        // Verificar cache primero
        if (this.cache.has(url)) {
            const cached = this.cache.get(url);
            if (Date.now() - cached.timestamp < 300000) { // 5 minutos
                if (updateHistory) {
                    history.pushState({ page: url }, '', url);
                }
                return cached.content;
            }
        }

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extraer solo el contenido principal
            const mainContent = doc.querySelector('#main-content');
            const title = doc.querySelector('title')?.textContent || '';
            
            if (!mainContent) {
                throw new Error('Invalid page structure');
            }

            const content = {
                main: mainContent.innerHTML,
                title: title,
                url: url
            };

            // Cachear la página
            this.cache.set(url, {
                content: content,
                timestamp: Date.now()
            });

            if (updateHistory) {
                history.pushState({ page: url }, title, url);
            }

            return content;
        } catch (error) {
            console.error('Failed to load page:', error);
            return null;
        }
    }

    updatePage(content, url) {
        // Actualizar contenido principal
        const mainElement = document.querySelector('#main-content');
        if (mainElement) {
            mainElement.innerHTML = content.main;
        }

        // Actualizar título
        document.title = content.title;
        
        // Actualizar URL actual
        this.currentPage = url;

        // Ejecutar scripts si es necesario
        this.executeScripts(mainElement);
    }

    executeScripts(container) {
        const scripts = container.querySelectorAll('script');
        scripts.forEach(script => {
            if (script.src) {
                // Script externo - verificar si ya está cargado
                if (!document.querySelector(`script[src="${script.src}"]`)) {
                    const newScript = document.createElement('script');
                    newScript.src = script.src;
                    document.head.appendChild(newScript);
                }
            } else {
                // Script inline - ejecutar en contexto seguro
                try {
                    new Function(script.textContent)();
                } catch (e) {
                    console.warn('Script execution failed:', e);
                }
            }
        });
    }

    updateActiveMenuItem() {
        // Remover clase active de todos los items
        document.querySelectorAll('.menu li').forEach(li => {
            li.classList.remove('active');
        });

        // Agregar clase active al item actual
        const currentPath = window.location.pathname;
        document.querySelectorAll('.menu a').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                const li = link.closest('li');
                if (li) li.classList.add('active');
            }
        });
    }

    preloadCriticalPages() {
        // Precargar páginas importantes en background
        const criticalPages = [
            '/dashboard',
            '/perfil',
            '/ajustes'
        ];

        setTimeout(() => {
            criticalPages.forEach(page => {
                if (page !== this.currentPage) {
                    this.preloadPage(window.location.origin + page);
                }
            });
        }, 1000);
    }

    async preloadPage(url) {
        if (!this.cache.has(url) && !this.isLoading) {
            try {
                await this.loadPage(url, false);
            } catch (error) {
                // Silenciar errores de precarga
            }
        }
    }

    showLoadingState() {
        this.isLoading = true;
        document.body.style.opacity = '0.95';
        
        // Mostrar indicador de carga sutil
        const indicator = document.createElement('div');
        indicator.id = 'loading-indicator';
        indicator.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #e69a37, transparent);
            background-size: 200% 100%;
            animation: loading 1s infinite;
            z-index: 9999;
        `;
        document.body.appendChild(indicator);

        // Agregar animación CSS si no existe
        if (!document.querySelector('#loading-animation')) {
            const style = document.createElement('style');
            style.id = 'loading-animation';
            style.textContent = `
                @keyframes loading {
                    0% { background-position: -200% 0; }
                    100% { background-position: 200% 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    hideLoadingState() {
        this.isLoading = false;
        document.body.style.opacity = '1';
        
        const indicator = document.querySelector('#loading-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    // Método para limpiar cache si es necesario
    clearCache() {
        this.cache.clear();
    }

    // Método para invalidar cache de una página específica
    invalidatePage(url) {
        this.cache.delete(url);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.secureNav = new SecureNavigation();
});

// Limpiar cache al cerrar sesión
document.addEventListener('beforeunload', () => {
    if (window.secureNav) {
        window.secureNav.clearCache();
    }
});