// Navegación SPA optimizada con sidebar fijo
class EnhancedSPANavigation {
    constructor() {
        this.cache = new Map();
        this.currentPage = window.location.pathname;
        this.isLoading = false;
        this.loadingTimeout = null;
        this.preloadQueue = new Set();
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.preloadCriticalPages();
        this.updateActiveMenuItem();
        this.setupIntersectionObserver();
    }

    setupEventListeners() {
        // Interceptar clics en enlaces del menú
        document.addEventListener('click', (e) => {
            const link = e.target.closest('.menu a, .brand a');
            if (link && this.shouldIntercept(link)) {
                e.preventDefault();
                this.setInmediateActivate(link);
                this.navigate(link.href);
            }
        });

        // Manejar botón atrás/adelante del navegador
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.page) {
                this.loadPage(e.state.page, false);
            }
        });

        // Precargar al hacer hover con debounce
        let hoverTimeout;
        document.addEventListener('mouseenter', (e) => {
            const link = e.target.closest('.menu a');
            if (link && this.shouldIntercept(link)) {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    this.preloadPage(link.href);
                }, 100);
            }
        }, true);

        // Limpiar timeout al salir del hover
        document.addEventListener('mouseleave', (e) => {
            const link = e.target.closest('.menu a');
            if (link) {
                clearTimeout(hoverTimeout);
            }
        }, true);

        // Manejar errores de red
        window.addEventListener('online', () => {
            this.handleNetworkChange(true);
        });

        window.addEventListener('offline', () => {
            this.handleNetworkChange(false);
        });
    }

    setInmediateActivate(link){
        document.querySelectorAll('.menu li').forEach(li => {
            li.classList.remove('spa-activating');
        });

        const li = link.closest('li');
        if (li){
            document.querySelectorAll('.menu li').forEach(other => {
                if (other !== li) other.classList.remove('active');
            });

            li.classList.add('active', 'spa-activating');
        }
    }

    shouldIntercept(link) {
        return link.hostname === window.location.hostname && 
               !link.hasAttribute('data-no-intercept') &&
               !link.href.includes('logout') &&
               !link.href.includes('#');
    }

    async navigate(url) {
        if (this.isLoading || url === window.location.href) return;
        
        // Cancelar navegación anterior si existe
        if (this.loadingTimeout) {
            clearTimeout(this.loadingTimeout);
        }
        
        this.showLoadingState();
        
        try {
            const content = await this.loadPage(url, true);
            if (content) {
                this.updatePage(content, url);
                this.updateActiveMenuItem();
                this.trackPageView(url);
            }
        } catch (error) {
            console.error('Navigation error:', error);
            this.handleNavigationError(error, url);
        } finally {
            this.hideLoadingState();
        }
    }

    async loadPage(url, updateHistory = true) {
        // Verificar cache primero
        const cacheKey = this.getCacheKey(url);
        if (this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < 180000) { // 3 minutos - reducido para mejor UX
                if (updateHistory) {
                    history.pushState({ page: url }, '', url);
                }
                return cached.content;
            }
        }

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000); // 8s timeout - más rápido

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Cache-Control': 'no-cache'
                },
                credentials: 'same-origin',
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const html = await response.text();
            const content = this.parsePageContent(html);
            
            if (!content) {
                throw new Error('Invalid page structure');
            }

            // Cachear la página
            this.cache.set(cacheKey, {
                content: content,
                timestamp: Date.now()
            });

            // Limpiar cache si es muy grande
            if (this.cache.size > 20) {
                this.cleanupCache();
            }

            if (updateHistory) {
                history.pushState({ page: url }, content.title, url);
            }

            return content;
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            throw error;
        }
    }

    parsePageContent(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const mainContent = doc.querySelector('#main-content, .main-content');
        const title = doc.querySelector('title')?.textContent || '';
        
        if (!mainContent) {
            return null;
        }

        return {
            main: mainContent.innerHTML,
            title: title,
            scripts: this.extractScripts(mainContent),
            styles: this.extractStyles(doc)
        };
    }

    extractScripts(container) {
        const scripts = [];
        const scriptElements = container.querySelectorAll('script');
        
        scriptElements.forEach(script => {
            if (script.src) {
                scripts.push({ type: 'external', src: script.src });
            } else if (script.textContent.trim()) {
                scripts.push({ type: 'inline', content: script.textContent });
            }
        });
        
        return scripts;
    }

    extractStyles(doc) {
        const styles = [];
        const styleElements = doc.querySelectorAll('style, link[rel="stylesheet"]');
        
        styleElements.forEach(style => {
            if (style.tagName === 'LINK') {
                styles.push({ type: 'external', href: style.href });
            } else {
                styles.push({ type: 'inline', content: style.textContent });
            }
        });
        
        return styles;
    }

    updatePage(content, url) {
        // Actualizar contenido principal con animación suave
        const mainElement = document.querySelector('#main-content, .main-content');
        if (mainElement) {
            // Fade out
            mainElement.style.opacity = '0';
            mainElement.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                mainElement.innerHTML = content.main;
                
                // Fade in
                mainElement.style.opacity = '1';
                mainElement.style.transform = 'translateY(0)';
                
                // Ejecutar scripts
                this.executeScripts(content.scripts);
                this.loadStyles(content.styles);
                
                // Scroll al top suavemente
                this.scrollToTop();
            }, 150);
        }

        // Actualizar título
        document.title = content.title;
        
        // Actualizar URL actual
        this.currentPage = url;
        
        // Actualizar meta tags si es necesario
        this.updateMetaTags(content);
    }

    executeScripts(scripts) {
        scripts.forEach(script => {
            if (script.type === 'external') {
                // Verificar si ya está cargado
                if (!document.querySelector(`script[src="${script.src}"]`)) {
                    const newScript = document.createElement('script');
                    newScript.src = script.src;
                    newScript.async = true;
                    document.head.appendChild(newScript);
                }
            } else {
                // Script inline - ejecutar en contexto seguro
                try {
                    new Function(script.content)();
                } catch (e) {
                    console.warn('Script execution failed:', e);
                }
            }
        });
    }

    loadStyles(styles) {
        styles.forEach(style => {
            if (style.type === 'external') {
                if (!document.querySelector(`link[href="${style.href}"]`)) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = style.href;
                    document.head.appendChild(link);
                }
            } else {
                // Evitar duplicar estilos inline
                const existingStyle = Array.from(document.querySelectorAll('style'))
                    .find(s => s.textContent === style.content);
                
                if (!existingStyle) {
                    const styleElement = document.createElement('style');
                    styleElement.textContent = style.content;
                    document.head.appendChild(styleElement);
                }
            }
        });
    }

updateActiveMenuItem() {
    const currentPath = new URL(window.location.href).pathname.replace(/\/+$/, '') || '/';
    // console.log('updateActiveMenuItem ->', currentPath);

    document.querySelectorAll('.menu a').forEach(link => {
        const li = link.closest('li');
        if (!li) return;

        const href = link.getAttribute('href') || '#';
        let linkPath = '/';
        try {
            linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
        } catch (e) {
            linkPath = href.replace(/\/+$/, '') || '/';
        }

        // coincidencia: igualdad o prefijo (pero no '/' como prefijo agresivo)
        const isMatch = (linkPath === currentPath) || (linkPath !== '/' && currentPath.startsWith(linkPath + '/')) || (linkPath !== '/' && currentPath === linkPath);

        if (isMatch) {
            if (!li.classList.contains('active')) {
                li.classList.add('active');
            }
            // limpiar marcador temporal si existía
            if (li.classList.contains('spa-activating')) {
                li.classList.remove('spa-activating');
            }
        } else {
            // Solo quitar si no fue marcado como "spa-activating" por el click reciente
            if (!li.classList.contains('spa-activating')) {
                li.classList.remove('active');
            }
        }
    });
}



    preloadCriticalPages() {
        const criticalPages = [
            '/dashboard',
            '/perfil',
            '/ajustes'
        ];

        // Precargar después de que la página esté completamente cargada
        setTimeout(() => {
            criticalPages.forEach(page => {
                if (page !== this.currentPage) {
                    this.preloadQueue.add(window.location.origin + page);
                }
            });
            this.processPreloadQueue();
        }, 2000);
    }

    async processPreloadQueue() {
        for (const url of this.preloadQueue) {
            if (!this.cache.has(this.getCacheKey(url))) {
                try {
                    await this.preloadPage(url);
                    // Pequeña pausa entre precargas para no saturar
                    await new Promise(resolve => setTimeout(resolve, 100));
                } catch (error) {
                    console.debug('Preload failed for:', url);
                }
            }
            this.preloadQueue.delete(url);
        }
    }

    async preloadPage(url) {
        if (!this.cache.has(this.getCacheKey(url)) && !this.isLoading) {
            try {
                await this.loadPage(url, false);
            } catch (error) {
                // Silenciar errores de precarga
            }
        }
    }

    showLoadingState() {
        this.isLoading = true;
        
        // Agregar clase de loading al body
        document.body.classList.add('navigation-loading');
        
        // Mostrar indicador de carga en la barra superior
        this.createLoadingIndicator();
        
        // Timeout de seguridad
        this.loadingTimeout = setTimeout(() => {
            this.hideLoadingState();
        }, 15000);
    }

    createLoadingIndicator() {
        // Remover indicador existente
        const existing = document.querySelector('#spa-loading-indicator');
        if (existing) existing.remove();
        
        const indicator = document.createElement('div');
        indicator.id = 'spa-loading-indicator';
        indicator.innerHTML = `
            <div class="loading-bar"></div>
            <div class="loading-text">Cargando...</div>
        `;
        document.body.appendChild(indicator);
    }

    hideLoadingState() {
        this.isLoading = false;
        document.body.classList.remove('navigation-loading');
        
        const indicator = document.querySelector('#spa-loading-indicator');
        if (indicator) {
            indicator.style.opacity = '0';
            setTimeout(() => indicator.remove(), 300);
        }
        
        if (this.loadingTimeout) {
            clearTimeout(this.loadingTimeout);
            this.loadingTimeout = null;
        }
    }

    handleNavigationError(error, url) {
        console.error('Navigation failed:', error);
        
        // Mostrar mensaje de error al usuario
        this.showErrorMessage('Error al cargar la página. Reintentando...');
        
        // Reintentar una vez
        setTimeout(() => {
            window.location.href = url;
        }, 2000);
    }

    showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'spa-error-message';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.style.opacity = '0';
            setTimeout(() => errorDiv.remove(), 300);
        }, 3000);
    }

    handleNetworkChange(isOnline) {
        if (!isOnline) {
            this.showErrorMessage('Conexión perdida. Algunas funciones pueden no estar disponibles.');
        } else {
            // Limpiar cache al reconectar
            this.cache.clear();
        }
    }

    setupIntersectionObserver() {
        // Observer para lazy loading de contenido
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Precargar páginas relacionadas cuando el usuario está cerca
                        const link = entry.target.querySelector('a');
                        if (link && this.shouldIntercept(link)) {
                            this.preloadPage(link.href);
                        }
                    }
                });
            }, { threshold: 0.1 });
        }
    }

    scrollToTop() {
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    trackPageView(url) {
        // Aquí puedes agregar analytics si es necesario
        console.debug('Page view:', url);
    }

    updateMetaTags(content) {
        // Actualizar meta tags dinámicamente si es necesario
        // Por ejemplo, meta description, og tags, etc.
    }

    getCacheKey(url) {
        return new URL(url).pathname;
    }

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    cleanupCache() {
        // Mantener solo las 15 páginas más recientes
        const entries = Array.from(this.cache.entries())
            .sort((a, b) => b[1].timestamp - a[1].timestamp)
            .slice(0, 15);
        
        this.cache.clear();
        entries.forEach(([key, value]) => {
            this.cache.set(key, value);
        });
    }

    // Métodos públicos para control externo
    clearCache() {
        this.cache.clear();
    }

    invalidatePage(url) {
        this.cache.delete(this.getCacheKey(url));
    }

    prefetchPage(url) {
        this.preloadPage(url);
    }

    getCurrentPage() {
        return this.currentPage;
    }

    isPageCached(url) {
        return this.cache.has(this.getCacheKey(url));
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.spaNav = new EnhancedSPANavigation();
    
    // Exponer métodos útiles globalmente
    window.navigateTo = (url) => window.spaNav.navigate(url);
    window.prefetchPage = (url) => window.spaNav.prefetchPage(url);
});

// Limpiar al cerrar
window.addEventListener('beforeunload', () => {
    if (window.spaNav) {
        window.spaNav.clearCache();
    }
});

// Manejar errores globales
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
});

window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled promise rejection:', e.reason);
});