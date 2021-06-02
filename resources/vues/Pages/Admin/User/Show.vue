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
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-display-field :data="model.brand_id" id="brand_id" label="Brand" :options="$page.props.brands"/>
                <instant-display-field :data="model.name" id="name" label="Full Name"/>
                <instant-display-field :data="model.email" id="email" label="Email"/>
                <instant-display-field :data="model.timezone" id="timezone" label="Timezone" :options="$page.props.timezones"/>
                <instant-display-field :data="model.type" id="type" label="Type" :options="$page.props.user_types"/>
                <instant-display-field :data="$page.props.user_roles_list" id="roles" label="Roles" type="list"/>
            </div>
        </instant-content-card>
        <instant-other-content-card :model="model">
            <template #append>
                <instant-display-field :form="form" type="json" :html="model.last_activity" id="name" label="Last Activity"/>
            </template>
        </instant-other-content-card>
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
