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
                    <instant-select-field :form="form" objprop="brand_id" id="brand_id" label="Brand" :options="$page.props.brands"/>
                    <instant-input-field :form="form" type="text" objprop="name" id="name" label="Full Name"/>
                    <instant-input-field :form="form" type="email" objprop="email" id="email" label="Email"/>
                    <instant-input-field :form="form" type="password" objprop="password" id="password" label="Password"/>
                    <instant-input-field :form="form" type="password" objprop="password_confirmation" id="password_confirmation" label="Confirm Password"/>
                    <instant-select-field :form="form" objprop="timezone" id="timezone" label="Timezone" :options="$page.props.timezones"/>
                    <instant-select-field :form="form" objprop="type" id="type" label="Type" :options="$page.props.user_types"/>
                    <instant-checkbox-group-field :form="form" objprop="roles" id="roles" label="Roles" :options="$page.props.roles"/>
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
                    brand_id: '',
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    timezone: '',
                    type: '',
                    roles: {},
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
        },

        methods: {
            submit() {
                this.form.post(this.route('user.create'), {
                    preserveScroll: true,
                    resetOnSuccess: false,
                    // onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
