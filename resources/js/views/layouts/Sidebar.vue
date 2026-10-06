<script setup>
import { onMounted, ref, watch } from 'vue'

const props = defineProps({
    isDesktop: {
        type: Boolean,
        default: false,
    },
    desktopOpen: {
        type: Boolean,
        default: true,
    },
    mobileOpen: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close-mobile'])

const STORAGE_KEY = 'scalespace-sidebar-open-menus'

const defaultOpenMenus = {
    inventory: true,
    purchasing: false,
    sales: false,
    logistics: false,
    management: false,
    accounting: false,
}

const openMenus = ref({ ...defaultOpenMenus })

const menuGroups = [
    {
        key: 'inventory',
        label: 'Inventory',
        icon: ['M4.5 8.25 12 4.5l7.5 3.75v10.5H4.5V8.25Z', 'M8.25 19V9.75h7.5V19', 'M9.75 13.5h4.5'],
        items: [
            { label: 'Brands', icon: ['M5.25 6.75h13.5v11.5a1.25 1.25 0 0 1-1.25 1.25H6.5a1.25 1.25 0 0 1-1.25-1.25V6.75Z', 'M7.5 6.75V4.5h9v2.25', 'M8.25 11.25h7.5', 'M8.25 14.25h4.5'] },
            { label: 'Measuring Masses', icon: ['M4.5 18h15', 'M6 18 9.75 6h4.5L18 18', 'M8.25 13.5h7.5'] },
            { label: 'Unit of Measurements', icon: ['M4.5 6h15', 'M4.5 18h15', 'M7.5 6v3', 'M10.5 6v1.5', 'M13.5 6v3', 'M16.5 6v1.5', 'M7.5 15v3', 'M10.5 16.5v1.5', 'M13.5 15v3', 'M16.5 16.5v1.5'] },
            { label: 'Item Classifications', icon: ['M5.25 5.25h5.25v5.25H5.25z', 'M13.5 5.25h5.25v5.25H13.5z', 'M5.25 13.5h5.25v5.25H5.25z', 'M13.5 13.5h5.25v5.25H13.5z'] },
            { label: 'Item Types', icon: ['M5.25 5.25h13.5v13.5H5.25z', 'M8.25 8.25h7.5v7.5h-7.5z'] },
            { label: 'Items', icon: ['M5.25 5.25h13.5v13.5H5.25z', 'M8.25 8.25h7.5', 'M8.25 11.25h7.5', 'M8.25 14.25h5.25'] },
            { label: 'Stocks', icon: ['M4.5 7.5h15v11.25h-15z', 'M7.5 7.5V5.25h9V7.5', 'M4.5 11.25h15', 'M10.5 11.25v7.5'] },
        ],
    },
    {
        key: 'purchasing',
        label: 'Purchasing',
        icon: ['M4.5 6h15v12.75h-15V6Z', 'M4.5 9h15', 'M7.5 5.25 6 3.75', 'M16.5 5.25 18 3.75', 'M8.25 12.75h3', 'M8.25 15.75h6.75'],
        items: [
            { label: 'Purchase Item Prices', icon: ['M4.5 5.25h15v13.5h-15z', 'M8.25 9h7.5', 'M8.25 12h5.25', 'M8.25 15h3'] },
            { label: 'Purchase Orders', icon: ['M5.25 4.5h10.5l3 3v12H5.25z', 'M15.75 4.5v3h3', 'M8.25 11.25h7.5', 'M8.25 14.25h4.5'] },
            { label: 'Receive Orders', icon: ['M4.5 7.5h10.5v10.5H4.5z', 'M15 11.25h3l1.5 1.5v5.25H15', 'M7.5 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z', 'M17.25 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z'] },
            { label: 'Bills', icon: ['M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z', 'M8.25 8.25h7.5', 'M8.25 11.25h7.5', 'M8.25 14.25h4.5'] },
            { label: 'Bill Payments', icon: ['M4.5 6.75h15v10.5h-15z', 'M4.5 9.75h15', 'M8.25 14.25h3', 'M15.75 14.25h.01'] },
        ],
    },
    {
        key: 'sales',
        label: 'Sales & Services',
        icon: ['M5.25 6.75h13.5v12H5.25z', 'M8.25 6.75V5.25a1.5 1.5 0 0 1 1.5-1.5h4.5a1.5 1.5 0 0 1 1.5 1.5v1.5', 'M5.25 10.5h13.5', 'M9 14.25h6'],
        items: [
            { label: 'Sales Item Prices', icon: ['M4.5 6.75h15v12h-15z', 'M7.5 6.75V5.25h9v1.5', 'M8.25 11.25h7.5', 'M8.25 14.25h4.5'] },
            { label: 'Service Prices', icon: ['M12 4.5v15', 'M15.75 7.5c-.75-1.5-2.25-2.25-3.75-2.25-2.25 0-3.75 1.5-3.75 3 0 4.5 7.5 2.25 7.5 6 0 1.5-1.5 3-3.75 3-1.5 0-3-.75-3.75-2.25'] },
            { label: 'Services', icon: ['M14.25 5.25a3.75 3.75 0 0 0-5.1 5.1L4.5 15v4.5H9l4.65-4.65a3.75 3.75 0 0 0 5.1-5.1l-2.1 2.1-2.4-2.4 2.1-2.1Z'] },
            { label: 'Invoices', icon: ['M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z', 'M8.25 8.25h7.5', 'M8.25 11.25h7.5', 'M8.25 14.25h4.5'] },
            { label: 'Invoice Payments', icon: ['M4.5 6.75h15v10.5h-15z', 'M4.5 9.75h15', 'M8.25 14.25h3', 'M15 14.25l1.5 1.5 2.25-3'] },
        ],
    },
    {
        key: 'logistics',
        label: 'Logistics',
        icon: ['M4.5 5.25h9v13.5h-9z', 'M13.5 9.75h3l3 3v6h-6', 'M7.5 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z', 'M17.25 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z'],
        items: [
            { label: 'Stock Requests', icon: ['M6 4.5h12v15H6z', 'M9 8.25h6', 'M9 11.25h6', 'M9 14.25h3'] },
            { label: 'Stock Transfers', icon: ['M5.25 8.25h10.5', 'M13.5 5.25l3 3-3 3', 'M18.75 15.75H8.25', 'M10.5 12.75l-3 3 3 3'] },
            { label: 'Stock Receives', icon: ['M4.5 7.5h10.5v10.5H4.5z', 'M15 11.25h3l1.5 1.5v5.25H15', 'M7.5 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z', 'M17.25 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z', 'M7.5 10.5l1.5 1.5 3-3'] },
            { label: 'Stock Returns', icon: ['M18.75 8.25H8.25', 'M10.5 5.25l-3 3 3 3', 'M5.25 15.75h10.5', 'M13.5 12.75l3 3-3 3'] },
        ],
    },
    {
        key: 'management',
        label: 'Management',
        icon: ['M8.25 10.5a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z', 'M2.25 20.25a6 6 0 0 1 12 0', 'M15.75 10.5a3 3 0 1 0 0-6', 'M15.75 13.5a5.25 5.25 0 0 1 6 5.25'],
        items: [
            { label: 'Warehouses', icon: ['M3.75 9.75 12 4.5l8.25 5.25v9.75H3.75z', 'M8.25 19.5v-6h7.5v6', 'M9.75 10.5h.01', 'M14.25 10.5h.01'] },
            { label: 'Branches', icon: ['M12 21a6.75 6.75 0 1 0 0-13.5A6.75 6.75 0 0 0 12 21Z', 'M12 10.5v3', 'M10.5 12h3', 'M12 4.5v3'] },
            { label: 'Departments', icon: ['M4.5 5.25h6v6h-6z', 'M13.5 5.25h6v6h-6z', 'M4.5 14.25h6v6h-6z', 'M13.5 14.25h6v6h-6z'] },
            { label: 'Contacts', icon: ['M12 11.25a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z', 'M5.25 19.5a6.75 6.75 0 0 1 13.5 0', 'M18 5.25l1.5 1.5 2.25-2.25'] },
            { label: 'Users', icon: ['M9 10.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z', 'M3.75 19.5a5.25 5.25 0 0 1 10.5 0', 'M16.5 10.5a2.25 2.25 0 1 0 0-4.5', 'M16.5 13.5a4.5 4.5 0 0 1 3.75 3.75'] },
            { label: 'Roles', icon: ['M12 3.75 19.5 7.5v4.5c0 4.5-3 7.5-7.5 8.25-4.5-.75-7.5-3.75-7.5-8.25V7.5L12 3.75Z', 'm9 12 2 2 4-4'] },
            { label: 'Permissions', icon: ['M8.25 10.5V8.25a3.75 3.75 0 1 1 7.5 0v2.25', 'M6.75 10.5h10.5v8.25H6.75z', 'M12 13.5v2.25'] },
        ],
    },
    {
        key: 'accounting',
        label: 'Accounting',
        icon: ['M6 3.75h12a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 1-1.5-1.5V5.25A1.5 1.5 0 0 1 6 3.75Z', 'M8.25 7.5h7.5', 'M8.25 11.25h7.5', 'M8.25 15h4.5'],
        items: [
            { label: 'Accounting Profiles', icon: ['M5.25 4.5h13.5v15H5.25z', 'M8.25 8.25h7.5', 'M8.25 11.25h7.5', 'M8.25 14.25h4.5'] },
            { label: 'Cost Centers', icon: ['M4.5 6.75h15v10.5h-15z', 'M8.25 10.5h2.25v3H8.25z', 'M13.5 10.5h2.25v3H13.5z'] },
            { label: 'Account Types', icon: ['M5.25 5.25h13.5v13.5H5.25z', 'M8.25 8.25h3v3h-3z', 'M12.75 8.25h3v3h-3z', 'M8.25 12.75h3v3h-3z', 'M12.75 12.75h3v3h-3z'] },
            { label: 'Accounts', icon: ['M6 4.5h12a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 1-1.5-1.5V6A1.5 1.5 0 0 1 6 4.5Z', 'M8.25 8.25h7.5', 'M8.25 11.25h4.5', 'M8.25 14.25h6'] },
            { label: 'Journals', icon: ['M6 4.5h12v15H6z', 'M9 8.25h6', 'M9 11.25h6', 'M9 14.25h3'] },
            { label: 'Vouchers', icon: ['M5.25 6.75h13.5v10.5H5.25z', 'M8.25 10.5h7.5', 'M8.25 13.5h4.5'] },
            { label: 'Groups', icon: ['M7.5 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z', 'M16.5 10.5a2.25 2.25 0 1 0 0-4.5', 'M3.75 19.5a5.25 5.25 0 0 1 10.5 0', 'M15 13.5a4.5 4.5 0 0 1 5.25 4.5'] },
            { label: 'Industries', icon: ['M4.5 19.5V9.75L12 5.25l7.5 4.5v9.75', 'M8.25 19.5v-6h7.5v6', 'M9.75 10.5h.01', 'M14.25 10.5h.01'] },
            { label: 'Recurring Charge Invoices', icon: ['M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z', 'M8.25 8.25h7.5', 'M8.25 11.25h7.5', 'M14.25 14.25a2.25 2.25 0 1 1-3.75 1.65', 'M14.25 14.25h-2.25', 'M14.25 14.25v-2.25'] },
            { label: 'Charge Invoices', icon: ['M6 3.75h12v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z', 'M8.25 8.25h7.5', 'M8.25 11.25h4.5', 'M8.25 14.25h6'] },
            { label: 'Charge Invoice Payments', icon: ['M4.5 6.75h15v10.5h-15z', 'M4.5 9.75h15', 'M8.25 14.25h3', 'M14.25 14.25l1.5 1.5 2.25-2.25'] },
        ],
    },
]

onMounted(() => {
    try {
        const saved = localStorage.getItem(STORAGE_KEY)

        if (saved) {
            const parsed = JSON.parse(saved)

            if (parsed && typeof parsed === 'object') {
                openMenus.value = {
                    ...defaultOpenMenus,
                    ...parsed,
                }
            }
        }
    } catch (error) {
        console.warn('Unable to restore sidebar state:', error)
    }
})

watch(
    openMenus,
    (value) => {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(value))
        } catch (error) {
            console.warn('Unable to save sidebar state:', error)
        }
    },
    { deep: true }
)

const toggleMenu = (menu) => {
    const isCurrentlyOpen = openMenus.value[menu]

    Object.keys(openMenus.value).forEach((key) => {
        openMenus.value[key] = false
    })

    openMenus.value[menu] = !isCurrentlyOpen
}

const navigate = () => {
    if (!props.isDesktop) {
        emit('close-mobile')
    }
}

const closeMobile = () => {
    if (!props.isDesktop) {
        emit('close-mobile')
    }
}
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white shadow-xl transition-transform duration-300 ease-out dark:border-slate-700/80 dark:bg-slate-950 lg:shadow-none" :class="props.isDesktop ? (props.desktopOpen ? 'translate-x-0' : '-translate-x-full') : (props.mobileOpen ? 'translate-x-0' : '-translate-x-full')" aria-label="Main navigation">
        <div class="box-border flex h-[80px] shrink-0 items-center border-b border-slate-200 px-5 dark:border-slate-700/80">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-cyan-400 text-white shadow-lg shadow-blue-500/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 5h5v5H5zM14 5h5v5h-5zM5 14h5v5H5zM14 14h5v5h-5z" fill="currentColor" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-bold tracking-tight text-slate-900 dark:text-white">ERP System</div>
                    <div class="truncate text-xs text-slate-500 dark:text-slate-300">Administration Dashboard</div>
                </div>
            </div>

            <button type="button" class="ml-auto flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white" aria-label="Close navigation" title="Close navigation" @click="closeMobile">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-4" aria-label="Sidebar navigation">
            <div class="space-y-2">
                <div class="px-3 pb-2 pt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-400">Dashboards</div>

                <a href="#" class="group flex h-[48px] items-center gap-3 rounded-xl bg-blue-50 px-3 text-sm font-semibold text-blue-700 transition-colors dark:bg-blue-500/15 dark:text-blue-300" @click.prevent="navigate">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-300">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z" fill="currentColor" />
                        </svg>
                    </span>
                    <span class="flex-1">Overview</span>
                </a>

                <div v-for="group in menuGroups" :key="group.key">
                    <button type="button" class="group flex h-[48px] w-full items-center gap-3 rounded-xl px-3 text-sm font-semibold text-slate-900 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-white dark:hover:bg-slate-900 dark:hover:text-white" :class="openMenus[group.key] ? 'bg-slate-100/80 text-slate-900 dark:bg-slate-900 dark:text-white' : ''" :aria-expanded="openMenus[group.key]" @click="toggleMenu(group.key)">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition-colors group-hover:bg-white group-hover:text-blue-600 dark:bg-slate-950 dark:text-slate-300 dark:group-hover:bg-slate-800 dark:group-hover:text-blue-300" :class="openMenus[group.key] ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-300' : ''">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path v-for="(path, index) in group.icon" :key="index" :d="path" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="flex-1 text-left">{{ group.label }}</span>
                        <svg class="h-4 w-4 shrink-0 text-slate-600 transition-transform duration-200 dark:text-slate-300" :class="openMenus[group.key] ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div class="grid transition-[grid-template-rows] duration-300 ease-out" :class="openMenus[group.key] ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
                        <div class="min-h-0 overflow-hidden">
                            <div class="ml-4 border-l border-slate-200 py-1.5 pl-2 dark:border-slate-700/80">
                                <a v-for="item in group.items" :key="item.label" href="#" class="group/item flex min-w-0 items-center gap-2 rounded-lg px-2 py-2 text-sm text-slate-800 transition-colors hover:bg-slate-100 hover:text-blue-600 dark:text-white dark:hover:bg-slate-900 dark:hover:text-blue-300" @click.prevent="navigate">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md text-slate-400 transition-colors group-hover/item:text-blue-500 dark:text-slate-300 dark:group-hover/item:text-blue-300">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path v-for="(path, index) in item.icon" :key="index" :d="path" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span class="min-w-0 flex-1 truncate">{{ item.label }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="shrink-0 border-t border-slate-200 p-3 dark:border-slate-700/80">
            <div class="rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-950">
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">System Online</span>
                </div>
            </div>
        </div>
    </aside>
</template>
