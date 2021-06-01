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
                    <instant-input-field label="Name" :form="form" objprop="mailable" id="mailable"/>
                    <instant-input-field label="Subject" :form="form" objprop="subject" id="subject"/>
                    <instant-textarea-field label="Text Template" :form="form" objprop="text_template" id="text_template"/>
                    <instant-editor-field label="HTML Template" :form="form" objprop="html_template" id="html_template"/>
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
                    mailable: this.model.mailable,
                    subject: this.model.subject,
                    text_template: this.model.text_template,
                    html_template: this.model.html_template,
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
