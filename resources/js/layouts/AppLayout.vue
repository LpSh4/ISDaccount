<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import Achievement from '@/components/Achievement.vue';
import type { BreadcrumbItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Achievement />
        <slot />
    </AppLayout>
</template>
