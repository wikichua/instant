<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('page')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Show
            </template>
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-display-field label="Brand" :html="model.brand_name" id="brand" />
                <instant-display-field label="Name" :html="model.name" id="name" />
                <instant-display-field label="Locale" :html="model.locale" id="locale" />
                <instant-display-field label="Slug" :html="model.slug" id="slug" />
                <instant-display-field label="Template" :html="model.template" id="template" />
                <instant-display-field label="Status" :html="model.status" id="status" :options="$page.props.status" />
            </div>
        </instant-content-card>
        <instant-other-content-card :model="model">
            <template #prepend>
                <instant-display-field label="Published Date" :html="model.published_at" id="published_at" />
                <instant-display-field label="Expired" :html="model.expired_at" id="expired_at" />
            </template>
        </instant-other-content-card>
    </authenticated-layout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated'

    export default {
        components: {
            AuthenticatedLayout,
        },

        data() {
            return {
            }
        },

        props: {
            auth: Object,
            errors: Object,
            model: Object,
        },

        methods: {
        },
    }
</script>
