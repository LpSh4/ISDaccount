<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {computed, onMounted, ref} from 'vue';
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

const tgprops = defineProps<{
    noFeedingDays?: number;
    noIncidentsDays?: number;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user as any);

const resetCounter = (key: string) => {
    router.post(`/dashboard/reset-counter/${key}`, {}, {
        preserveScroll: true,
    });
};

//States for draggable achievment menu
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
//read
const props = defineProps<{
    achievements: Array<{
        id: number;
        title: string;
        subtitle: string | null;
        image_url: string | null;
        pivot?: {
            created_at: string;
        };
    }>;
}>();
const startDrag = (e: MouseEvent) => {
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

const canvasNodes = ref<any[]>([]);
const connections = ref<any[]>([]);

onMounted(() => {
    const nodes: any[] = [];
    const lines: any[] = [];
    const occupied = new Set<string>();

    const rootX = 2000;
    const rootY = 2000;
    const spacingX = 350;
    const spacingY = 250;

    const directions = [
        { dx: 0, dy: -spacingY },
        { dx: spacingX, dy: 0 },
        { dx: 0, dy: spacingY },
        { dx: -spacingX, dy: 0 }
    ];

    if (props.achievements && props.achievements.length > 0) {
        const rootAch = props.achievements[0];
        nodes.push({ ...rootAch, x: rootX, y: rootY });
        occupied.add(`${rootX},${rootY}`);
        const remaining = props.achievements.slice(1);

        remaining.forEach((ach) => {
            let placed = false;
            const shuffledNodes = [...nodes].sort(() => 0.5 - Math.random());

            for (const parent of shuffledNodes) {
                if (placed) break;

                const shufDirs = [...directions].sort(() => 0.5 - Math.random());

                for (const dir of shufDirs) {
                    const newX = parent.x + dir.dx;
                    const newY = parent.y + dir.dy;
                    const coordKey = `${newX},${newY}`;

                    if (!occupied.has(coordKey)) {
                        nodes.push({ ...ach, x: newX, y: newY });
                        occupied.add(coordKey);

                        lines.push({
                            x1: parent.x + 128,
                            y1: parent.y + 64,
                            x2: newX + 128,
                            y2: newY + 64
                        });

                        placed = true;
                        break;
                    }
                }
            }
        });
    }

    canvasNodes.value = nodes;
    connections.value = lines;
});
</script>

<template>
    <Head :title="`Hello there, ${user.name}!`" />

    <div class="flex h-[calc(100vh-8rem)] flex-1 flex-col gap-4 overflow-hidden rounded-xl p-4">

        <div class="flex items-center justify-between pb-2">
            <h1 class="text-3xl font-bold tracking-tight">Hello there, {{ user.name }}!</h1>
        </div>

        <div
            class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-sidebar dark:border-sidebar-border"
            :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
            @wheel.prevent="handleWheel"
            @mousedown="startDrag"
            @mousemove="doDrag"
            @mouseup="stopDrag"
            @mouseleave="stopDrag"
        >
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

                <svg class="absolute inset-0 h-full w-full pointer-events-none">
                    <line
                        v-for="(line, index) in connections"
                        :key="'line-'+index"
                        :x1="line.x1" :y1="line.y1" :x2="line.x2" :y2="line.y2"
                        stroke="currentColor"
                        stroke-width="2"
                        class="text-border"
                    />
                </svg>
                <div
                    v-for="(node, index) in canvasNodes"
                    :key="node.id"
                    class="group absolute w-64 rounded-xl border bg-card p-3 text-card-foreground shadow-xl flex items-center gap-4 transition-all hover:scale-105 z-10"
                    :style="{ top: node.y + 'px', left: node.x + 'px' }"
                    :class="index === 0 ? 'border-primary border-2' : 'border-border'"
                >
                    <!-- Hover Date Tooltip -->
                    <div v-if="node.pivot" class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 whitespace-nowrap rounded bg-foreground px-2 py-1 text-xs text-background opacity-0 transition-opacity group-hover:opacity-100">
                        Acquired: {{ new Date(node.pivot.created_at).toLocaleDateString() }}
                    </div>

                    <div v-if="node.image_url" class="h-16 w-16 shrink-0 rounded-md overflow-hidden bg-muted">
                        <img :src="node.image_url" class="h-full w-full object-cover" alt="Achievement Icon" />
                    </div>

                    <!-- Text Content -->
                    <div class="flex-1">
                        <h2 class="font-bold leading-tight">{{ node.title }}</h2>
                        <p class="text-sm text-muted-foreground mt-0.5">{{ node.subtitle }}</p>
                    </div>
                </div>

                <div class="absolute top-[1950px] left-[2250px] w-64 rounded-xl border border-border bg-card p-4 text-card-foreground shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Stats</span>
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <h2 class="font-bold mt-1">Days without feeding</h2>
                    <div class="my-3 text-4xl font-extrabold text-primary">
                        {{ tgprops.noFeedingDays ?? 0 }}
                    </div>
                    <button
                        type="button"
                        @click="resetCounter('no_feeding')"
                        class="w-full rounded-lg bg-destructive px-3 py-1.5 text-xs font-medium text-destructive-foreground transition-colors hover:bg-destructive/90 active:scale-95"
                    >
                        Reset Counter
                    </button>
                </div>

                <div class="absolute top-[2150px] left-[2250px] w-64 rounded-xl border border-border bg-card p-4 text-card-foreground shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Stats</span>
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <h2 class="font-bold mt-1">Days without incidents</h2>
                    <div class="my-3 text-4xl font-extrabold text-primary">
                        {{ tgprops.noIncidentsDays ?? 0 }}
                    </div>
                    <button
                        type="button"
                        @click="resetCounter('no_incidents')"
                        class="w-full rounded-lg bg-destructive px-3 py-1.5 text-xs font-medium text-destructive-foreground transition-colors hover:bg-destructive/90 active:scale-95"
                    >
                        Reset Counter
                    </button>
                </div>

                <div class="absolute top-[1990px] left-[2156px] h-0.5 w-[94px] bg-border"></div>
                <div class="absolute top-[1990px] left-[2202px] h-[200px] w-0.5 bg-border"></div>
                <div class="absolute top-[2190px] left-[2202px] h-0.5 w-[48px] bg-border"></div>

            </div>
        </div>

        <!-- Footer with fillers -->
        <footer class="mt-auto flex shrink-0 items-center justify-between rounded-xl border border-sidebar-border/70 p-4 text-sm text-muted-foreground dark:border-sidebar-border">
            <div>&copy; 1985 ISD client</div>
            <div class="flex gap-4">
                <a href="https://t.me/Apol_ISD_bot" target="_blank" class="hover:text-foreground transition-colors">Telegram Bot</a>
                <a href="#" class="hover:text-foreground transition-colors">GitHub</a>
                <a href="#" class="hover:text-foreground transition-colors">Documentation</a>
                <a href="#" class="hover:text-foreground transition-colors">Support</a>
            </div>
        </footer>

    </div>
</template>
