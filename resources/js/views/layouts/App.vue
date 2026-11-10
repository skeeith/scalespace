<script setup>
    import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
    import Sidebar from '@/views/layouts/Sidebar.vue';
    import Navbar from '@/views/layouts/Navbar.vue';
    // import Users from '@/views/Users.vue'
    import Index from '@/views/users-new/Index.vue';

    const DESKTOP_BREAKPOINT = 1024;

    const theme = ref('dark');
    const desktopSidebarOpen = ref(true);
    const mobileSidebarOpen = ref(false);
    const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : DESKTOP_BREAKPOINT);

    const isDesktop = computed(() => windowWidth.value >= DESKTOP_BREAKPOINT);

    function updateWindowWidth() {
        windowWidth.value = window.innerWidth;

        if (isDesktop.value) {
            mobileSidebarOpen.value = false;
            document.body.classList.remove('overflow-hidden');
        }
    }

    function setTheme(value) {
        theme.value = value

        if (value === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        }

        localStorage.setItem('scalespace-theme', value);
    }

    function toggleTheme() {
        setTheme(theme.value === 'dark' ? 'light' : 'dark');
    }

    function toggleDesktopSidebar() {
        if (! isDesktop.value) {
            return;
        }

        desktopSidebarOpen.value = ! desktopSidebarOpen.value;
    }

    function openMobileSidebar() {
        if (isDesktop.value) {
            return
        }

        mobileSidebarOpen.value = true
    }

    function closeMobileSidebar() {
        mobileSidebarOpen.value = false
    }

    function toggleMobileSidebar() {
        if (isDesktop.value) {
            return
        }

        mobileSidebarOpen.value = !mobileSidebarOpen.value
    }

    function handleNavigation() {
        if (!isDesktop.value) {
            closeMobileSidebar()
        }
    }

    function handleLogout() {
        console.log('Logout clicked')

    // Replace this with your Laravel logout implementation.
    // Example:
    // router.post('/logout')
    }

    function handleKeydown(event) {
        if (event.key !== 'Escape') {
            return
        }

        if (mobileSidebarOpen.value) {
            closeMobileSidebar()
        }
    }

    watch(mobileSidebarOpen, (open) => {
        if (open && !isDesktop.value) {
            document.body.classList.add('overflow-hidden')
        } else {
            document.body.classList.remove('overflow-hidden')
        }
    })

    watch(isDesktop, (desktop) => {
        if (desktop) {
            mobileSidebarOpen.value = false
            document.body.classList.remove('overflow-hidden')
        }
    })

    onMounted(() => {
        const savedTheme = localStorage.getItem('scalespace-theme')

        if (savedTheme === 'light' || savedTheme === 'dark') {
            setTheme(savedTheme)
        } else {
            setTheme('dark')
        }

        updateWindowWidth()

        window.addEventListener('resize', updateWindowWidth)
        document.addEventListener('keydown', handleKeydown)
    })

    onBeforeUnmount(() => {
        window.removeEventListener('resize', updateWindowWidth)
        document.removeEventListener('keydown', handleKeydown)
        document.body.classList.remove('overflow-hidden')
    })
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
        <Transition name="overlay">
            <div v-if="mobileSidebarOpen && !isDesktop" class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden" aria-hidden="true" @click="closeMobileSidebar"></div>
        </Transition>

        <Sidebar :desktop-open="desktopSidebarOpen" :mobile-open="mobileSidebarOpen" :is-desktop="isDesktop" @close-mobile="closeMobileSidebar" @navigate="handleNavigation" />

        <div class="min-h-screen transition-[padding] duration-300 ease-out" :class="isDesktop && desktopSidebarOpen ? 'lg:pl-72' : 'lg:pl-0'">
            <Navbar :desktop-sidebar-open="desktopSidebarOpen" :mobile-sidebar-open="mobileSidebarOpen" :theme="theme" :is-desktop="isDesktop" @toggle-desktop-sidebar="toggleDesktopSidebar" @toggle-mobile-sidebar="toggleMobileSidebar" @toggle-theme="toggleTheme" @logout="handleLogout" />

            <main class="w-full">
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component"/>
                    </transition>
                </router-view>
            </main>
        </div>
    </div>
</template>

<style>
    @import "tailwindcss";

    @custom-variant dark (&:where(.dark, .dark *));

    html {
        min-height: 100%;
    }

    body {
        min-height: 100vh;
        margin: 0;
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    button,
    a {
        -webkit-tap-highlight-color: transparent;
    }

    .overlay-enter-active,
    .overlay-leave-active {
        transition: opacity 300ms ease;
    }

    .overlay-enter-from,
    .overlay-leave-to {
        opacity: 0;
    }
</style>
