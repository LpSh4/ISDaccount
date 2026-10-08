<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps<{
    noFeedingDays?: number;
    noIncidentsDays?: number;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const resetCounter = (key: string) => {
    router.post(`/dashboard/reset-counter/${key}`, {}, {
        preserveScroll: true,
    });
};

// States for draggable achievement menu
const scale = ref(1);
const panX = ref(0);
const panY = ref(0);
const isDragging = ref(false);
const startX = ref(0);
const startY = ref(0);

const handleWheel = (e: WheelEvent) => {
    const zoomSensitivity = 0.05;
    const delta = e.deltaY > 0 ? -zoomSensitivity : zoomSensitivity;
    let newScale = scale.value + delta;
    scale.value = Math.max(0.5, Math.min(newScale, 2.5));
};

const startDrag = (e: MouseEvent) => {
    if ((e.target as HTMLElement).tagName === 'BUTTON') return;
    isDragging.value = true;
    startX.value = e.clientX - panX.value;
    startY.value = e.clientY - panY.value;
};

const doDrag = (e: MouseEvent) => {
    if (!isDragging.value) return;
    panX.value = e.clientX - startX.value;
    panY.value = e.clientY - startY.value;
};

const stopDrag = () => {
    isDragging.value = false;
};
</script>

<template>
    <Head :title="`Hello there, ${user.name}!`" />

    <div class="flex h-[calc(100vh-8rem)] flex-1 flex-col gap-4 overflow-hidden rounded-xl p-4">

        <!-- Header: Заголовок + Счётчики сверху -->
        <div class="flex flex-wrap items-center justify-between gap-4 pb-2">
            <h1 class="text-3xl font-bold tracking-tight">Hello there, {{ user.name }}!</h1>

            <!-- Счётчики -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Days without feeding -->
                <div class="flex items-center gap-3 rounded-xl border border-border bg-card px-4 py-2 text-card-foreground shadow-sm">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">Stats</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="text-xs font-medium text-muted-foreground">Days without feeding</div>
                    </div>
                    <div class="text-2xl font-extrabold text-primary">
                        {{ props.noFeedingDays ?? 0 }}
                    </div>
                    <button
                        type="button"
                        @click="resetCounter('no_feeding')"
                        class="ml-1 rounded-md bg-destructive px-2.5 py-1 text-xs font-medium text-destructive-foreground transition-colors hover:bg-destructive/90 active:scale-95"
                    >
                        Reset
                    </button>
                </div>

                <!-- Days without incidents -->
                <div class="flex items-center gap-3 rounded-xl border border-border bg-card px-4 py-2 text-card-foreground shadow-sm">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">Stats</span>
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="text-xs font-medium text-muted-foreground">Days without incidents</div>
                    </div>
                    <div class="text-2xl font-extrabold text-primary">
                        {{ props.noIncidentsDays ?? 0 }}
                    </div>
                    <button
                        type="button"
                        @click="resetCounter('no_incidents')"
                        class="ml-1 rounded-md bg-destructive px-2.5 py-1 text-xs font-medium text-destructive-foreground transition-colors hover:bg-destructive/90 active:scale-95"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Interactive Canvas -->
        <div
            class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-sidebar dark:border-sidebar-border"
            :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
            @wheel.prevent="handleWheel"
            @mousedown="startDrag"
            @mousemove="doDrag"
            @mouseup="stopDrag"
            @mouseleave="stopDrag"
        >
            <!-- The Moving Surface -->
            <div
                class="absolute origin-center transition-transform duration-75 ease-out"
                :style="{
                    transform: `translate(${panX}px, ${panY}px) scale(${scale})`,
                    width: '4000px',
                    height: '4000px',
                    left: '50%',
                    top: '50%',
                    marginLeft: '-2000px',
                    marginTop: '-2000px'
                }"
            >
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080801a_1px,transparent_1px),linear-gradient(to_bottom,#8080801a_1px,transparent_1px)] bg-[size:40px_40px]"></div>

                <!-- Start Menu & Achievements Nodes -->
                <div class="absolute top-[1950px] left-[1900px] w-64 rounded-xl border-2 border-primary bg-card p-4 text-card-foreground shadow-xl">
                    <h2 class="font-bold">Start Menu</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Drag anywhere to explore</p>
                </div>

                <div class="absolute top-[1750px] left-[1900px] w-64 rounded-xl border border-border bg-card p-4 text-card-foreground shadow-md">
                    <h2 class="font-bold">First Achievement</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Found a secret node.</p>
                </div>

                <div class="absolute top-[1830px] left-[2025px] h-[120px] w-1 bg-border"></div>

            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-auto flex shrink-0 items-center justify-between rounded-xl border border-sidebar-border/70 p-4 text-sm text-muted-foreground dark:border-sidebar-border">
            <div>&copy; 1985 ISD client</div>
            <div class="flex gap-4">
                <a href="https://t.me/Apol_ISD_bot" target="_blank" class="transition-colors hover:text-foreground">Telegram Bot</a>
                <a href="#" class="transition-colors hover:text-foreground">GitHub</a>
                <a href="#" class="transition-colors hover:text-foreground">Documentation</a>
                <a href="#" class="transition-colors hover:text-foreground">Support</a>
            </div>
        </footer>

    </div>
</template>
