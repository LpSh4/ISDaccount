<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth?.user as any;
const activeAchievements = ref<any[]>([]);

onMounted(() => {
    const echo = (window as any).Echo;

    if (user && echo) {
        echo.private(`users.${user.id}`)
            .listen('.achievement.created', (e: any) => {
                console.log("WEBSOCKET EVENT RECEIVED:", e);

                const id = Date.now();

                activeAchievements.value.push({
                    ...e.achievement,
                    unique_id: id
                });

                setTimeout(() => {
                    activeAchievements.value = activeAchievements.value.filter(a => a.unique_id !== id);
                }, 5000);
            });
    }
});
</script>

<template>
    <div class="fixed top-6 left-6 z-[9999] flex flex-col gap-4 pointer-events-none">
        <TransitionGroup
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="-translate-x-[150%] opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-500 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="-translate-x-[150%] opacity-0"
        >
            <div
                v-for="ach in activeAchievements"
                :key="ach.unique_id"
                class="flex w-80 items-center gap-4 rounded-xl border-2 border-[#555] bg-[#212121] p-3 text-white shadow-[8px_8px_0px_rgba(0,0,0,0.5)]"
            >
                <div v-if="ach.image_url" class="h-12 w-12 shrink-0 rounded bg-black/50 overflow-hidden border border-[#555]">
                    <img :src="ach.image_url" class="h-full w-full object-cover" />
                </div>
                <div>
                    <p class="text-xs font-bold text-[#FFFF55] uppercase tracking-wider mb-0.5">Achievement Get!</p>
                    <h3 class="text-base font-medium leading-tight text-white">{{ ach.title }}</h3>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>
