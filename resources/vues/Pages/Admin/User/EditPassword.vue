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
                    <instant-input-field :form="form" type="password" objprop="password" id="password" label="Password"/>
                    <instant-input-field :form="form" type="password" objprop="password_confirmation" id="password_confirmation" label="Confirm Password"/>
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
                    password: '',
                    password_confirmation: '',
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
                this.form.put(this.route('user.updatePassword',[this.model.id]), {
                    preserveScroll: true,
                    resetOnSuccess: false,
                    // onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
