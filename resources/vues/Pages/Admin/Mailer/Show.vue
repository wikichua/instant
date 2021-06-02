<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('mailer')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Show
            </template>
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-display-field label="Name" :data="model.mailable" id="mailable"/>
                <instant-display-field label="Subject" :data="model.subject" id="subject"/>
                <instant-display-field label="Text Template" :data="model.text_template" id="text_template"/>
                <instant-display-field label="HTML Template" :data="model.html_template" id="html_template"/>
            </div>
        </instant-content-card>
        <instant-other-content-card :model="model" />
    </authenticated-layout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated'
    import InstantMultiValuesField from '@/Pages/Admin/Setting/MultiValuesField'

    export default {
        components: {
            AuthenticatedLayout,
            InstantMultiValuesField,
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
