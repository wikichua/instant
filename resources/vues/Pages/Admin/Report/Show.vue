<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('report')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12 items-center">
            <template #content-title>
                Show
            </template>
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-content-tabs :models="models" />
            </div>
        </instant-content-card>
        <instant-other-content-card :model="model" />
    </authenticated-layout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated'
    import InstantContentTabs from '@/Pages/Admin/Report/ContentTabs'

    export default {
        components: {
            AuthenticatedLayout,
            InstantContentTabs,
        },

        data() {
            return {
                form: this.$inertia.form()
            }
        },

        props: {
            auth: Object,
            errors: Object,
            model: Object,
            models: Object,
        },

        methods: {
        },
    }
</script>
