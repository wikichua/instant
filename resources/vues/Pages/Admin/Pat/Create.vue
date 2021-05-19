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
                    <instant-input-field :form="form" type="text" objprop="name" id="name" label="Name"/>
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
                    name: 'authToken',
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
            user_id: Number,
        },

        methods: {
            submit() {
                this.form.post(this.route('pat.create',[this.$props.user_id]), {
                    preserveScroll: true,
                    resetOnSuccess: false,
                    // onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
