<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profile"
            description="Update your personal information"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">First Name</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="given-name"
                    placeholder="First name"
                />
                <InputError class="mt-2" :message="errors.first_name" />
            </div>

            <div class="grid gap-2">
                <Label for="surname">Surname</Label>
                <Input
                    id="surname"
                    class="mt-1 block w-full"
                    name="surname"
                    :default-value="(user.surname == 0 || user.surname === '0') ? '' : (user.surname ?? '')"
                    required
                    autocomplete="middle-name"
                    placeholder="Middle name"
                />
                <InputError class="mt-2" :message="errors.last_name" />
            </div>
            <!-- == AND === OPERANDS ARE TEMPORARY FIXES!!!!!!!!!!!! DONT TOUCH AND DONT BLAME!!!!!!!!!!!!!!1-->
            <div class="grid gap-2">
                <Label for="lastname">Last Name</Label>
                <Input
                    id="lastname"
                    class="mt-1 block w-full"
                    name="lastname"
                    :default-value="(user.lastname == 0 || user.lastname === '0') ? '' : (user.lastname ?? '')"
                    autocomplete="family-name"
                    placeholder="Last name"
                />
                <InputError class="mt-2" :message="errors.last_name" />
            </div>

            <div class="grid gap-2">
                <Label for="date_of_birth">Date of Birth</Label>
                <Input
                    id="date_of_birth"
                    type="date"
                    class="mt-1 block w-full"
                    name="date_of_birth"
                    :default-value="user.date_of_birth"
                    autocomplete="bday"
                />
                <InputError class="mt-2" :message="errors.date_of_birth" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="Email address"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button"
                >Save</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
