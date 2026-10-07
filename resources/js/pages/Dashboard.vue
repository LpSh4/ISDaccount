<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
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

const page = usePage();
const user = computed(() => page.props.auth.user);

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
</script>

<template>
    <Head :title="`Hello there, ${user.name}!`" />

    <div class="flex h-[calc(100vh-8rem)] flex-1 flex-col gap-4 overflow-hidden rounded-xl p-4">

        <div class="flex items-center justify-between pb-2">
            <h1 class="text-3xl font-bold tracking-tight">Hello there, {{ user.name }}!</h1>
        </div>

        <!-- I love mihecraf MursuStare -->
        <div
            class="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 bg-sidebar dark:border-sidebar-border"
            :class="isDragging ? 'cursor-grabbing' : 'cursor-grab'"
            @wheel.prevent="handleWheel"
            @mousedown="startDrag"
            @mousemove="doDrag"
            @mouseup="stopDrag"
            @mouseleave="stopDrag"
        >
            <!-- The Moving Surface (4000 by 4000, scalable later ig)-->
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

                <!--
                    CANVAS CONTENTS GO HERE
                    Use absolute positioning (top/left) to place achievements around the board!!!!!!!!!!!!!!!!!!!!!!
                -->

                <div class="absolute top-[1950px] left-[1900px] w-64 rounded-xl border-2 border-primary bg-card p-4 text-card-foreground shadow-xl">
                    <h2 class="font-bold">Start Menu</h2>
                    <p class="text-sm text-muted-foreground mt-1">Drag anywhere to explore</p>
                </div>

                <div class="absolute top-[1750px] left-[1900px] w-64 rounded-xl border border-border bg-card p-4 text-card-foreground shadow-md">
                    <h2 class="font-bold">First Achievement</h2>
                    <p class="text-sm text-muted-foreground mt-1">Found a secret node.</p>
                </div>

                <div class="absolute top-[1830px] left-[2025px] h-[120px] w-1 bg-border"></div>

            </div>
        </div>

        <!-- Footer with fillers -->
        <footer class="mt-auto flex shrink-0 items-center justify-between rounded-xl border border-sidebar-border/70 p-4 text-sm text-muted-foreground dark:border-sidebar-border">
            <div>&copy; 1985 ISD client</div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-foreground transition-colors">GitHub</a> //Place the tg bot here somewhere
                <a href="#" class="hover:text-foreground transition-colors">Documentation</a>
                <a href="#" class="hover:text-foreground transition-colors">Support</a>
            </div>
        </footer>

    </div>
</template>
