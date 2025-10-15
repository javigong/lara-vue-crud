<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    create as productsCreate,
    destroy as productsDelete,
    edit as productsEdit,
} from '@/routes/products';
import { type BreadcrumbItem, type Product } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Rocket } from 'lucide-vue-next';

const { products } = defineProps<{
    products: Product[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];

const page = usePage();

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(productsDelete.url(id));
    }
};
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div v-if="page.props.flash?.message">
                <Alert
                    class="mb-4 rounded-lg border-green-500 bg-green-50 text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-50"
                >
                    <Rocket class="h-4 w-4" />
                    <AlertTitle>Success!</AlertTitle>
                    <AlertDescription>
                        {{ page.props.flash?.message }}
                    </AlertDescription>
                </Alert>
            </div>

            <Link :href="productsCreate.url()">
                <Button> Create a Product </Button>
            </Link>

            <div>
                <Table>
                    <TableCaption>A list of your recent products.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]"> ID </TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Price</TableHead>
                            <TableHead>Description</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="product in products" :key="product.id">
                            <TableCell>
                                {{ product.id }}
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ product.name }}
                            </TableCell>
                            <TableCell> {{ product.price }} </TableCell>
                            <TableCell> {{ product.description }} </TableCell>
                            <TableCell class="flex gap-2">
                                <Link :href="productsEdit.url(product.id)">
                                    <Button variant="outline">Edit</Button>
                                </Link>
                                <Button
                                    variant="destructive"
                                    @click="handleDelete(product.id)"
                                    >Delete</Button
                                >
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
