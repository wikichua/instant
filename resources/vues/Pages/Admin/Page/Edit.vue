<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('page')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Edit
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-input-field label="Name" :form="form" objprop="name" id="name"/>
                    <instant-input-field label="Command" :form="form" objprop="command" id="command"/>
                    <instant-select-field label="Timezone" :form="form" objprop="timezone" id="timezone" :options="$page.props.timezones"/>
                    <instant-select-field label="Frequency" :form="form" objprop="frequency" id="frequency" :options="$page.props.cronjob_frequencies"/>
                    <instant-select-field label="Status" :form="form" objprop="status" id="status" :options="$page.props.report_status"/>
                    <instant-button-field>Save</instant-button-field>
                </div>
            </form>
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
        },

        methods: {
            submit() {
                this.form.put(this.route('page.edit',[this.model.id]), {
                    preserveScroll: true,
                    resetOnSuccess: false,
                    // onFinish: () => this.form.reset(['group','name']),
                })
            },
            swapInputs() {
                this.isMultiple = this.form.multipleTypes;
            }
        },
    }
</script>
