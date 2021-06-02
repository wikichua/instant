<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('role')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12 items-center">
            <template #content-title>
                Show
            </template>
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-display-field type="text" :html="model.brand.name" id="brand" label="Brand"/>
                <instant-display-field type="text" :html="model.user.name" id="user" label="User"/>
                <instant-display-field type="text" :html="model.model_id" id="model_id" label="Model ID"/>
                <instant-display-field type="text" :html="model.model_class" id="model_class" label="Model Class"/>
                <instant-display-field type="text" :html="model.message" id="message" label="Message"/>
                <instant-display-field type="json" :html="model.data" id="data" label="Data"/>
                <instant-display-field type="json" :html="model.agents" id="agents" label="Agents"/>
                <instant-display-field type="json" :html="model.iplocation" id="iplocation" label="Iplocation"/>
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
                form: this.$inertia.form({
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

        },
    }
</script>
