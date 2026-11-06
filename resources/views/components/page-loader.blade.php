<!-- UNCART Global Brand Preloader & Navigation Progress Bar -->
<div id="uncart-global-preloader" 
     class="fixed inset-0 z-[9999999] flex flex-col items-center justify-center bg-[#0E0716] select-none"
     aria-label="Loading UNCART Platform">
    
    <div class="relative flex flex-col items-center text-center p-6" data-preloader-content>
        <!-- Glowing Brand Star Emblem -->
        <div class="relative w-16 h-16 flex items-center justify-center mb-4">
            <div class="absolute inset-0 bg-gradient-to-tr from-[#F59E0B]/30 to-[#8E44AD]/40 rounded-2xl blur-xl animate-preloader-aura"></div>
            <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-b from-[#2A143A] to-[#140A1C] border border-[#F59E0B]/30 flex items-center justify-center shadow-2xl shadow-[#F59E0B]/20">
                <svg class="w-7 h-7 text-[#F59E0B] drop-shadow-[0_0_8px_rgba(245,158,11,0.5)]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L14.2 9.8L22 12L14.2 14.2L12 22L9.8 14.2L2 12L9.8 9.8L12 2Z" />
                </svg>
            </div>
        </div>

        <!-- Typography -->
        <div class="flex items-center gap-1 text-white font-black tracking-[0.2em] text-xl font-sans uppercase">
            <span>UN</span><span class="text-[#F59E0B]">CART</span>
        </div>
        <p class="text-[10px] font-mono tracking-[0.3em] uppercase text-slate-400 mt-1 opacity-80">
            Precision Commerce
        </p>

        <!-- Shimmering Micro-Progress Line -->
        <div class="w-36 h-1 bg-white/10 rounded-full overflow-hidden mt-5 relative">
            <div class="uncart-shimmer-bar h-full rounded-full bg-gradient-to-r from-[#F59E0B] via-[#D0A9E4] to-[#F59E0B]"></div>
        </div>
    </div>
</div>

<!-- Top Navigation Progress Indicator Bar -->
<div id="uncart-top-progress" 
     class="fixed top-0 left-0 h-[3px] w-0 z-[99999999] pointer-events-none opacity-0 transition-all duration-200"
     style="background: linear-gradient(90deg, #F59E0B, #8E44AD, #F59E0B); box-shadow: 0 0 10px rgba(245,158,11,0.8), 0 0 4px rgba(142,68,173,0.6);"
     aria-hidden="true">
</div>

<style>
    @keyframes uncartShimmerAnim {
        0% { transform: translateX(-120%); }
        100% { transform: translateX(250%); }
    }
    .uncart-shimmer-bar {
        width: 50%;
        animation: uncartShimmerAnim 1.1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        will-change: transform;
    }
    @keyframes uncartAuraAnim {
        0%, 100% { opacity: 0.35; transform: scale(0.95); }
        50% { opacity: 0.85; transform: scale(1.12); }
    }
    .animate-preloader-aura {
        animation: uncartAuraAnim 2s ease-in-out infinite;
    }
    @media (prefers-reduced-motion: reduce) {
        #uncart-global-preloader { display: none !important; }
        #uncart-top-progress { display: none !important; }
    }
</style>

<script>
    (function() {
        const preloader = document.getElementById('uncart-global-preloader');
        const progressBar = document.getElementById('uncart-top-progress');

        // 1. Dismiss Splash Preloader smoothly
        const dismissPreloader = () => {
            if (!preloader || preloader.dataset.dismissed) return;
            preloader.dataset.dismissed = 'true';
            preloader.style.transition = 'opacity 300ms cubic-bezier(0.16, 1, 0.3, 1), transform 300ms cubic-bezier(0.16, 1, 0.3, 1)';
            preloader.style.opacity = '0';
            preloader.style.transform = 'scale(1.02)';
            preloader.style.pointerEvents = 'none';
            setTimeout(() => {
                if (preloader.parentNode) preloader.parentNode.removeChild(preloader);
            }, 320);
        };

        if (document.readyState === 'complete') {
            setTimeout(dismissPreloader, 80);
        } else {
            window.addEventListener('load', () => setTimeout(dismissPreloader, 100));
            // Strict safety fallback timeout: guarantees page is interactive within 550ms max
            setTimeout(dismissPreloader, 550);
        }

        // 2. Global Progress Bar API
        let progressTimer = null;
        let currentProgress = 0;

        window.startLoader = function() {
            if (!progressBar) return;
            if (progressTimer) clearInterval(progressTimer);
            progressBar.style.opacity = '1';
            currentProgress = 15;
            progressBar.style.width = currentProgress + '%';

            progressTimer = setInterval(() => {
                if (currentProgress < 85) {
                    currentProgress += Math.random() * 15;
                    progressBar.style.width = Math.min(85, currentProgress) + '%';
                }
            }, 180);
        };

        window.stopLoader = function() {
            if (!progressBar) return;
            if (progressTimer) clearInterval(progressTimer);
            progressBar.style.width = '100%';
            setTimeout(() => {
                progressBar.style.opacity = '0';
                setTimeout(() => {
                    progressBar.style.width = '0%';
                    currentProgress = 0;
                }, 220);
            }, 150);
        };

        // 3. Attach Top Loader to Internal Link Clicks for Instant Feedback
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link) return;
            const href = link.getAttribute('href');
            if (
                !href ||
                href.startsWith('#') ||
                href.startsWith('javascript:') ||
                href.startsWith('mailto:') ||
                href.startsWith('tel:') ||
                link.getAttribute('target') === '_blank' ||
                link.hasAttribute('download') ||
                e.metaKey || e.ctrlKey || e.shiftKey || e.altKey
            ) {
                return;
            }

            try {
                const url = new URL(link.href, window.location.origin);
                if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                    window.startLoader();
                }
            } catch (err) {}
        });

        // 4. Attach Top Loader to Form Submissions (checkout, cart, search, login, etc.)
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form && !form.hasAttribute('target')) {
                window.startLoader();
            }
        });

        // 5. Reset on Page Show / Back-Forward Cache
        window.addEventListener('pageshow', (e) => {
            dismissPreloader();
            window.stopLoader();
        });
    })();
</script>
