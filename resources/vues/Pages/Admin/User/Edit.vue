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
                    <instant-select-field :form="form" objprop="brand_id" id="brand_id" label="Brand" :options="$page.props.brands"/>
                    <instant-input-field :form="form" type="text" objprop="name" id="name" label="Full Name"/>
                    <instant-input-field :form="form" type="email" objprop="email" id="email" label="Email"/>
                    <instant-select-field :form="form" objprop="timezone" id="timezone" label="Timezone" :options="$page.props.timezones"/>
                    <instant-select-field :form="form" objprop="type" id="type" label="Type" :options="$page.props.user_types"/>
                    <instant-checkbox-group-field :form="form" objprop="roles" id="roles" label="Roles" :options="$page.props.roles"/>
                    <instant-button-field>Save</instant-button-field>
                </div>
            </form>
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
                form: this.$inertia.form({
                    brand_id: this.model.brand_id,
                    name: this.model.name,
                    email: this.model.email,
                    timezone: this.model.timezone,
                    type: this.model.type,
                    roles: this.$page.props.user_roles,
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
                this.form.put(this.route('user.update',[this.model.id]), {
                    preserveScroll: true,
                    resetOnSuccess: false,
                    // onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
