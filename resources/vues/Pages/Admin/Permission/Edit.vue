<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('permission')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12 items-center">
            <template #content-title>
                Edit
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-input-field label="Group" :form="form" objprop="group" id="group"/>
                    <instant-multi-rows-input-field label="Permissions" :form="form" objprop="name" id="name"/>
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
                    group: this.model.group,
                    name: this.permissions,
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
            model: Object,
            permissions: Object,
        },

        methods: {
            submit() {
                this.form.put(this.route('permission.edit',[this.model.id]), {
                    // onFinish: () => this.form.reset(['group','name']),
                });
            },
        },
    }
</script>
