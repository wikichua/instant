<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('failedjob')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Show
            </template>
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-display-field type="text" :html="model.id" id="id" label="ID"/>
                <instant-display-field type="text" :html="model.queue" id="queue" label="Queue"/>
                <instant-display-field type="json" :html="model.payload" id="payload" label="Payload"/>
                <instant-display-field type="json" :html="model.exception" id="exception" label="Exception"/>
            </div>
        </instant-content-card>
        <instant-other-content-card :model="model" />
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
