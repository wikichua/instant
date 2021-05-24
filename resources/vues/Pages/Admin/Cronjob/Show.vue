<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('cronjob')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Show
            </template>
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-input-field label="Name" :form="form" objprop="name" id="name" disabled/>
                <instant-input-field label="Command" :form="form" objprop="command" id="command" disabled/>
                <instant-select-field label="Timezone" :form="form" objprop="timezone" id="timezone" :options="$page.props.timezones" disabled/>
                <instant-select-field label="Frequency" :form="form" objprop="frequency" id="frequency" :options="$page.props.cronjob_frequencies" disabled/>
                <instant-select-field label="Status" :form="form" objprop="status" id="status" :options="$page.props.report_status" disabled/>
            </div>
        </instant-content-card>
        <instant-other-content-card :model="model" />
    </authenticated-layout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated'
    import InstantMultiValuesField from '@/Pages/Admin/Setting/MultiValuesField'

    export default {
        components: {
            AuthenticatedLayout,
            InstantMultiValuesField,
        },

        data() {
            return {
                form: this.$inertia.form({
                    name: this.model.name,
                    command: this.model.command,
                    timezone: this.model.timezone,
                    frequency: this.model.frequency,
                    status: this.model.status,
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
            model: Object,
            value: String,
            values: Object,
        },

        methods: {
        },
    }
</script>
