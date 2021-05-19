<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('report')">
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
                    <instant-input-field type="number" label="TTL (Seconds)" :form="form" objprop="cache_ttl" id="cache_ttl"/>
                    <instant-multi-rows-input-field label="SQL queries" :form="form" objprop="queries" id="queries" type="textarea"/>
                    <instant-select-field label="Status" :form="form" objprop="status" id="status" :options="status" />
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
                    cache_ttl: this.model.cache_ttl,
                    queries: this.model.queries,
                    status: this.model.status,
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
            model: Object,
            status: Object,
        },

        methods: {
            submit() {
                this.form.put(this.route('report.edit',[this.model.id]), {
                    // onFinish: () => this.form.reset(['group','name']),
                });
            },
        },
    }
</script>
