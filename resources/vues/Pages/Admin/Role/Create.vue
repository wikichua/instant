<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('role')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12 items-center">
            <template #content-title>
                Create
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-input-field label="Name" :form="form" objprop="name" id="name"/>
                    <instant-select-field label="Admin" :form="form" objprop="admin" id="admin" :options="{ 0: 'No', 1: 'Yes'}"/>
                    <instant-checkbox-group-field label="Permissions" :form="form" objprop="permissions" id="permissions" :options="$page.props.group_permissions" grouped/>
                    <instant-button-field>Save</instant-button-field>
                </div>
            </form>
        </instant-content-card>
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
                    name: '',
                    admin: 0,
                    permissions: {},
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
        },

        methods: {
            submit() {
                this.form.post(this.route('role.create'), {
                    preserveScroll: true,
                    resetOnSuccess: false,
                    // onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
