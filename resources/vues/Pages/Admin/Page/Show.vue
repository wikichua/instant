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
                <instant-display-field label="Name" :html="model.name" id="name" />
                <instant-display-field label="Command" :html="model.command" id="command" />
                <instant-display-field label="Timezone" :html="model.timezone" id="timezone" :options="$page.props.timezones" />
                <instant-display-field label="Frequency" :html="model.frequency" id="frequency" :options="$page.props.cronjob_frequencies" />
                <instant-display-field label="Status" :html="model.status" id="status" :options="$page.props.cronjob_status" />
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
