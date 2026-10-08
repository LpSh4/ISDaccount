<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {route} from "ziggy-js";

defineProps<{
    users: Array<{
        id: number;
        name: string;
        surname: string | null;
        lastname: string | null;
        date_of_birth: string | null;
        email: string;
        role: string;
    }>;
}>();

// Modal State
const isModalOpen = ref(false);
const selectedUser = ref<any>(null);
const form = useForm({
    title: '',
    subtitle: '',
    image_url: '',
});

const openModal = (user: any) => {
    selectedUser.value = user;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const submitAchievement = () => {
    if (!selectedUser.value) return;

    form.post(`/admin/users/${selectedUser.value.id}/achievements`, {
        onSuccess: () => {
            isModalOpen.value = false;
        }
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="flex flex-col space-y-6 p-6 max-w-7xl mx-auto">
        <Heading variant="large" title="Admin Dashboard" description="Manage employees and system access." />

        <div class="rounded-xl border border-sidebar-border bg-card text-card-foreground shadow">
            <div class="p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight mb-4">All Users</h3>

                <div class="space-y-4">
                    <div v-for="user in users" :key="user.id" class="flex items-center justify-between rounded-lg border p-4 hover:bg-muted/50">

                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-foreground">
                                    {{ user.name }} {{ user.surname ?? '' }} {{ user.lastname ?? '' }}
                                </span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="user.role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'">
                                    {{ user.role }}
                                </span>
                            </div>
                            <span class="text-sm text-muted-foreground">
                                DOB: {{ user.date_of_birth || 'Not set' }} &bull; {{ user.email }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Button is no longer a filler - triggers the modal below now -->
                            <Button @click="openModal(user)" variant="outline" size="sm">Add Achievement</Button>
                            <Button variant="destructive" size="sm" :disabled="user.role === 'admin'">Delete</Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form for achievements x\\\x -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-card text-card-foreground p-6 rounded-xl shadow-lg w-full max-w-md border border-border">

            <h2 class="text-xl font-bold mb-1">Grant Achievement</h2>
            <p class="text-sm text-muted-foreground mb-6">
                Awarding to {{ selectedUser?.name }} {{ selectedUser?.surname ?? '' }}
            </p>

            <form @submit.prevent="submitAchievement" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="title">Achievement Title</Label>
                    <Input id="title" v-model="form.title" required placeholder="e.g. King of kings" />
                    <span v-if="form.errors.title" class="text-red-500 text-xs">{{ form.errors.title }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="subtitle">Subtitle (Optional)</Label>
                    <Input id="subtitle" v-model="form.subtitle" placeholder="e.g. Give out 10 achievements" />
                    <span v-if="form.errors.subtitle" class="text-red-500 text-xs">{{ form.errors.subtitle }}</span>
                </div>

                <div class="grid gap-2">
                    <Label for="image_url">Image URL</Label>
                    <Input id="image_url" type="url" v-model="form.image_url" placeholder="https://..." />
                    <span v-if="form.errors.image_url" class="text-red-500 text-xs">{{ form.errors.image_url }}</span>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <Button type="button" variant="outline" @click="isModalOpen = false">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">Award</Button>
                </div>
            </form>
        </div>
    </div>
</template>
