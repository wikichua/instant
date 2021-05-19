<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('permission')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12 items-center">
            <template #content-title>
                Show
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-input-field label="Group" :form="form" objprop="group" id="group" disabled/>
                    <instant-multi-rows-input-field label="Permissions" :form="form" objprop="name" id="name" disabled/>
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
        },
    }
</script>
