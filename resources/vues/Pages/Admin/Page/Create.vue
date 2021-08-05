<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('page')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Create
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-select-field label="Brand" :form="form" objprop="brand_id" id="brand_id" :options="$page.props.brands" />
                    <instant-select-field label="Status" :form="form" objprop="status" id="status" :options="$page.props.status"/>
                    <instant-button-field>Save</instant-button-field>
                </div>
            </form>
        </instant-content-card>
        <instant-other-content-card model="">
            <template #prepend>
                <instant-date-field label="Published Date" :form="form" objprop="published_at" id="published_at" />
                <instant-date-field label="Expired" :form="form" objprop="expired_at" id="expired_at" />
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
                    name: '',
                    command: '',
                    timezone: '',
                    frequency: '',
                    status: '',
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
        },

        methods: {
            submit() {
                this.form.post(this.route('page.create'), {
                    preserveScroll: true,
                    resetOnSuccess: false,
                    // onFinish: () => this.form.reset(['group','name']),
                })
            },
            swapInputs() {
                this.isMultiple = this.form.multipleTypes;
            }
        },
    }
</script>
