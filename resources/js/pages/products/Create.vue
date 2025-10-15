<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { store as productsStore } from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Create Product',
        href: '/products/create',
    },
];

const form = useForm({
    name: '',
    price: '',
    description: '',
});

const handleSubmit = () => {
    console.log(form.data());
    form.post(productsStore.url());
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <form @submit.prevent="handleSubmit" class="w-8/12 space-y-4">
                <div class="grid gap-2">
                    <Label for="Product Name">Name</Label>
                    <Input
                        v-model="form.name"
                        type="text"
                        placeholder="Product Name"
                    />
                    <div class="text-sm text-red-600" v-if="form.errors.name">
                        {{ form.errors.name }}
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="Product Price">Price</Label>
                    <Input
                        v-model="form.price"
                        type="number"
                        placeholder="Product Price"
                    />
                    <div class="text-sm text-red-600" v-if="form.errors.price">
                        {{ form.errors.price }}
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="Product Description">Description</Label>
                    <Input
                        v-model="form.description"
                        type="text"
                        placeholder="Product Description"
                    />
                    <div
                        class="text-sm text-red-600"
                        v-if="form.errors.description"
                    >
                        {{ form.errors.description }}
                    </div>
                </div>
                <Button type="submit" :disabled="form.processing">Add Product</Button>
            </form>
        </div>
    </AppLayout>
</template>
