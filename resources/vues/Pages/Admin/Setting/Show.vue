<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('setting')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Show
            </template>
            <div class="shadow overflow-hidden sm:rounded-md">
                <instant-display-field label="Key" :data="model.key" id="key"/>
                <instant-display-field label="Apply Encryption" :data="model.protected" id="protected" :options="[{ label: 'Yes', value: 1 }, { label: 'No', value: 0 }]" />
                <instant-display-field label="Multiple values" :data="model.multipleTypes" id="multipleTypes" :options="[{ label: 'Yes', value: 1 }, { label: 'No', value: 0 }]"/>
                <instant-display-field label="Value" :data="model.value" id="value" type="json"/>
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
