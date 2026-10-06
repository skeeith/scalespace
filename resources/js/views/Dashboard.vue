<script setup>
const stats = [
    {
        label: 'Total Users',
        value: '12,480',
        change: '+12.5% from last month',
        icon: 'users',
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
        positive: true
    },
    {
        label: 'Active Users',
        value: '9,842',
        change: '+8.2% from last month',
        icon: 'check',
        iconClass: 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/40 dark:text-cyan-400',
        positive: true
    },
    {
        label: 'Roles',
        value: '24',
        change: '4 added this month',
        icon: 'settings',
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
        positive: false
    },
    {
        label: 'Permissions',
        value: '86',
        change: 'System access controls',
        icon: 'lock',
        iconClass: 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/40 dark:text-cyan-400',
        positive: false
    }
]

const recentUsers = [
    {
        initials: 'JD',
        name: 'John Doe',
        email: 'john@example.com',
        status: 'Active',
        statusClass: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400',
        avatarClass: 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
    },
    {
        initials: 'AS',
        name: 'Alice Smith',
        email: 'alice@example.com',
        status: 'Active',
        statusClass: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400',
        avatarClass: 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/40 dark:text-cyan-400'
    },
    {
        initials: 'MW',
        name: 'Michael Wong',
        email: 'michael@example.com',
        status: 'Pending',
        statusClass: 'bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400',
        avatarClass: 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
    }
]

const quickActions = [
    {
        title: 'Add User',
        description: 'Create a new account',
        icon: 'plus',
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
    },
    {
        title: 'Manage Roles',
        description: 'Configure access roles',
        icon: 'settings',
        iconClass: 'bg-cyan-50 text-cyan-600 dark:bg-cyan-950/40 dark:text-cyan-400'
    },
    {
        title: 'Permissions',
        description: 'Manage system permissions',
        icon: 'lock',
        iconClass: 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400'
    }
]
</script>

<template>
    <main class="w-full px-4 py-5 sm:px-6 sm:py-6 lg:px-7 lg:py-7">
        <div class="w-full space-y-6 lg:space-y-7">
            <!-- Welcome Banner -->
            <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-7 text-white shadow-lg shadow-blue-500/10 sm:px-7 sm:py-8 lg:px-7 lg:py-7">
                <div class="max-w-4xl">
                    <p class="text-xs font-semibold tracking-wide text-blue-100 sm:text-sm">
                        Welcome back
                    </p>

                    <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
                        Administration Dashboard
                    </h1>

                    <p class="mt-2 text-sm text-blue-50 sm:text-base">
                        Manage users, roles, permissions, and monitor your platform from one central location.
                    </p>
                </div>
            </section>

            <!-- Statistics -->
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article v-for="stat in stats" :key="stat.label" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-950">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500 sm:text-sm dark:text-slate-400">
                                {{ stat.label }}
                            </p>

                            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                                {{ stat.value }}
                            </p>
                        </div>

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" :class="stat.iconClass">
                            <svg v-if="stat.icon === 'users'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <svg v-else-if="stat.icon === 'check'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <svg v-else-if="stat.icon === 'settings'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06-1.42 1.42-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21h-2v-.08a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06-1.42-1.42.06-.06A1.65 1.65 0 0 0 9.6 15a1.65 1.65 0 0 0-1.51-1H8v-2h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06 1.42-1.42.06.06a1.65 1.65 0 0 0 1.82.33 1.65 1.65 0 0 0 1-1.51V6h2v.08a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06 1.42 1.42-.06.06A1.65 1.65 0 0 0 19.4 10c.14.61.69 1 1.31 1H21v2h-.29a1.65 1.65 0 0 0-1.31 2Z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="5" y="11" width="14" height="10" rx="2" />
                                <path d="M8 11V8a4 4 0 0 1 8 0v3" stroke-linecap="round" />
                            </svg>
                        </div>
                    </div>

                    <p class="mt-5 text-[11px] font-medium sm:text-xs" :class="stat.positive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'">
                        {{ stat.change }}
                    </p>
                </article>
            </section>

            <!-- Recent Users + Quick Actions -->
            <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                <!-- Recent Users -->
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2 dark:border-slate-800 dark:bg-slate-950">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6 dark:border-slate-800">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 sm:text-base dark:text-white">
                                Recent Users
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                Latest registered accounts
                            </p>
                        </div>

                        <button type="button" class="text-xs font-semibold text-blue-600 transition hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            View all
                        </button>
                    </div>

                    <div>
                        <div v-for="(user, index) in recentUsers" :key="user.email" class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-6" :class="index !== recentUsers.length - 1 ? 'border-b border-slate-200 dark:border-slate-800' : ''">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-xs font-bold" :class="user.avatarClass">
                                    {{ user.initials }}
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-xs font-semibold text-slate-900 sm:text-sm dark:text-white">
                                        {{ user.name }}
                                    </p>
                                    <p class="truncate text-[11px] text-slate-500 sm:text-xs dark:text-slate-400">
                                        {{ user.email }}
                                    </p>
                                </div>
                            </div>

                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold sm:text-[11px]" :class="user.statusClass">
                                {{ user.status }}
                            </span>
                        </div>
                    </div>
                </article>

                <!-- Quick Actions -->
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-slate-800 dark:bg-slate-950">
                    <div class="mb-4">
                        <h2 class="text-sm font-bold text-slate-900 sm:text-base dark:text-white">
                            Quick Actions
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            Common administration tasks
                        </p>
                    </div>

                    <div class="space-y-2.5">
                        <button v-for="action in quickActions" :key="action.title" type="button" class="flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left transition duration-200 hover:border-blue-200 hover:bg-blue-50/50 dark:border-slate-800 dark:bg-slate-950 dark:hover:border-blue-900 dark:hover:bg-blue-950/20">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl" :class="action.iconClass">
                                <svg v-if="action.icon === 'plus'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                                </svg>

                                <svg v-else-if="action.icon === 'settings'" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06-1.42 1.42-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21h-2v-.08a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06-1.42-1.42.06-.06A1.65 1.65 0 0 0 9.6 15a1.65 1.65 0 0 0-1.51-1H8v-2h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06 1.42-1.42.06.06a1.65 1.65 0 0 0 1.82.33 1.65 1.65 0 0 0 1.82-.33l.06-.06 1.42 1.42-.06.06A1.65 1.65 0 0 0 19.4 10c.14.61.69 1 1.31 1H21v2h-.29a1.65 1.65 0 0 0-1.31 2Z" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                                <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="5" y="11" width="14" height="10" rx="2" />
                                    <path d="M8 11V8a4 4 0 0 1 8 0v3" stroke-linecap="round" />
                                </svg>
                            </span>

                            <span class="min-w-0">
                                <span class="block truncate text-xs font-semibold text-slate-900 sm:text-sm dark:text-white">
                                    {{ action.title }}
                                </span>
                                <span class="block truncate text-[10px] text-slate-500 sm:text-xs dark:text-slate-400">
                                    {{ action.description }}
                                </span>
                            </span>
                        </button>
                    </div>
                </article>
            </section>
        </div>
    </main>
</template>
