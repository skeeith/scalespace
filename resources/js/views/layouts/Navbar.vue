<script setup>
    import { onBeforeUnmount, onMounted, ref } from 'vue'

    const props = defineProps({
        desktopSidebarOpen: {
            type: Boolean,
            default: true
        },
        mobileSidebarOpen: {
            type: Boolean,
            default: false
        },
        theme: {
            type: String,
            default: 'dark'
        },
        isDesktop: {
            type: Boolean,
            default: true
        }
    })

    const emit = defineEmits(['toggle-desktop-sidebar', 'toggle-mobile-sidebar', 'toggle-theme', 'logout'])

    const userMenuOpen = ref(false)
    const userMenu = ref(null)
    const userMenuToggle = ref(null)

    function toggleUserMenu() {
        userMenuOpen.value = !userMenuOpen.value
    }

    function closeUserMenu() {
        userMenuOpen.value = false
    }

    function handleOutsideClick(event) {
        if (!userMenuOpen.value) {
            return
        }

        if (userMenu.value?.contains(event.target) || userMenuToggle.value?.contains(event.target)) {
            return
        }

        closeUserMenu()
    }

    function handleKeydown(event) {
        if (event.key === 'Escape') {
            closeUserMenu()
        }
    }

    onMounted(() => {
        document.addEventListener('click', handleOutsideClick)
        document.addEventListener('keydown', handleKeydown)
    })

    onBeforeUnmount(() => {
        document.removeEventListener('click', handleOutsideClick)
        document.removeEventListener('keydown', handleKeydown)
    })
</script>

<template>
    <header class="sticky top-0 z-30 box-border min-h-[80px] border-b border-slate-200 bg-white/95 backdrop-blur-xl transition-colors duration-300 dark:border-slate-800 dark:bg-slate-950/95">
        <div class="flex min-h-[80px] w-full flex-wrap items-center justify-between gap-x-4 gap-y-2 px-4 py-2 sm:flex-nowrap sm:gap-4 sm:px-6 sm:py-0 lg:px-8">
            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-3 sm:flex-nowrap">
                <button type="button" class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition-[margin,transform,background-color,border-color,color] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:flex dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-blue-950/40 dark:hover:text-blue-400" :class="desktopSidebarOpen ? '' : 'ml-5'" :aria-expanded="desktopSidebarOpen" :aria-label="desktopSidebarOpen ? 'Close sidebar' : 'Open sidebar'" :title="desktopSidebarOpen ? 'Close sidebar' : 'Open sidebar'" @click="emit('toggle-desktop-sidebar')">
                    <svg class="h-5 w-5 transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" :class="desktopSidebarOpen ? 'rotate-0' : 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-blue-700 dark:hover:bg-blue-950/40 dark:hover:text-blue-400" :aria-expanded="mobileSidebarOpen" aria-label="Open sidebar" title="Open sidebar" @click="emit('toggle-mobile-sidebar')">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <div class="flex min-w-0 items-center gap-3 overflow-hidden transition-[max-width,transform,opacity] duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" :class="isDesktop && desktopSidebarOpen ? 'pointer-events-none max-w-0 -translate-x-3 opacity-0' : 'max-w-[360px] translate-x-0 opacity-100'">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-cyan-400 text-white shadow-lg shadow-blue-500/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 5h5v5H5zM14 5h5v5h-5zM5 14h5v5H5zM14 14h5v5h-5z" fill="currentColor" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold tracking-tight text-slate-900 dark:text-white">
                            Scalespace Inc.
                        </div>
                        <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                            Administration Dashboard
                        </div>
                    </div>
                </div>

                <div class="hidden h-8 w-px shrink-0 bg-slate-200 sm:block dark:bg-slate-800"></div>

                <nav class="order-last flex min-w-0 basis-full items-center gap-2 pt-1 text-base sm:order-none sm:basis-auto sm:pt-0 sm:text-lg" aria-label="Breadcrumb">
                    <a href="#" class="shrink-0 text-slate-400 transition hover:text-blue-600 dark:text-slate-500 dark:hover:text-blue-400">
                        Management
                    </a>

                    <svg class="h-3.5 w-3.5 shrink-0 text-slate-300 dark:text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    <span class="min-w-0 break-words font-medium text-slate-700 dark:text-slate-300">
                        Users
                    </span>
                </nav>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <button type="button" class="relative flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Notifications">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10 21h4" stroke-linecap="round" />
                    </svg>
                    <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full bg-cyan-500"></span>
                </button>

                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white" :aria-label="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'" @click="emit('toggle-theme')">
                    <svg v-if="theme === 'dark'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke-linecap="round" />
                    </svg>
                    <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5 8.5 8.5 0 1 0 20.5 14.5Z" />
                    </svg>
                </button>

                <div class="relative">
                    <button ref="userMenuToggle" type="button" class="flex items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:bg-slate-800" aria-label="Open user menu" :aria-expanded="userMenuOpen" @click.stop="toggleUserMenu">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 text-xs font-bold text-white">
                            PP
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-xs font-semibold text-slate-900 dark:text-white">Gether Kestrel B. Medel</span>
                            <span class="block text-[11px] text-slate-500 dark:text-slate-400">Super Administrator</span>
                        </span>
                        <svg class="hidden h-4 w-4 text-slate-400 transition-transform sm:block" :class="userMenuOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <Transition name="dropdown">
                        <div v-if="userMenuOpen" ref="userMenu" class="absolute right-0 top-full mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white p-1 shadow-xl dark:border-slate-800 dark:bg-slate-900">
                            <button type="button" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800" @click="closeUserMenu">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 21a8 8 0 0 1 16 0" stroke-linecap="round" />
                                </svg>
                                Profile
                            </button>

                            <button type="button" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30" @click="closeUserMenu(); emit('logout')">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M10 17l5-5-5-5M15 12H3" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M21 19V5a2 2 0 0 0-2-2h-7" stroke-linecap="round" />
                                </svg>
                                Logout
                            </button>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
    .dropdown-enter-active,
    .dropdown-leave-active {
        transition: opacity 150ms ease, transform 150ms ease;
    }

    .dropdown-enter-from,
    .dropdown-leave-to {
        opacity: 0;
        transform: translateY(-4px);
    }
</style>
