<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('mailer')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Edit
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-input-field label="Name" :form="form" objprop="name" id="name"/>
                    <instant-input-field label="Domain" :form="form" objprop="domain" id="domain"/>
                    <instant-date-field label="Published" :form="form" objprop="published_at" id="published_at"/>
                    <instant-date-field label="Expired" :form="form" objprop="expired_at" id="expired_at"/>
                    <instant-select-field label="Status" :form="form" objprop="status" id="status" :options="$page.props.status"/>
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
                    domain: this.model.domain,
                    published_at: this.model.published_at,
                    expired_at: this.model.expired_at,
                    status: this.model.status,
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
                this.form.put(this.route('mailer.edit',[this.model.id]), {
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
