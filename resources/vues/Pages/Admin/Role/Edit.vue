<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('role')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12 items-center">
            <template #content-title>
                Edit
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-input-field label="Name" :form="form" objprop="name" id="name"/>
                    <instant-select-field label="Admin" :form="form" objprop="admin" id="admin" :options="[{ value: 0, label:'No'},{ value: 1 , label:'Yes' }]"/>
                    <instant-checkbox-group-field label="Permissions" :form="form" objprop="permissions" id="permissions" :options="$page.props.group_permissions" :selected="$page.props.selected_permissions" grouped/>
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
                    admin: this.model.admin,
                    permissions: this.selected_permissions,
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
            model: Object,
            selected_permissions: Object,
        },

        methods: {
            submit() {
                this.form.put(this.route('role.edit',[this.model.id]), {
                    // onFinish: () => this.form.reset(['group','name']),
                });
            },
        },
    }
</script>
