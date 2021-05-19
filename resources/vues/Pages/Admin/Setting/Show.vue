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
                <instant-input-field label="Key" :form="form" objprop="key" id="key" disabled/>
                <instant-checkbox-field label="Apply Encryption" :form="form" objprop="protected" id="protected" :option="{ label: 'Yes', value: 1 }"  disabled/>
                <instant-checkbox-field label="Multiple values" :form="form" objprop="multipleTypes" id="multipleTypes" :option="{ label: 'Yes', value: 1 }" @change="swapInputs" disabled/>
                <instant-textarea-field label="Value" :form="form" objprop="value" id="value" v-if="!isMultiple" disabled/>
                <instant-multi-values-field label="Values" :form="form" objprop="values" id="values" v-if="isMultiple" disabled/>
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
                isMultiple: this.model.multipleTypes,
                form: this.$inertia.form({
                    key: this.model.key,
                    protected: this.model.protected == 1 ? true : this.model.protected,
                    multipleTypes: this.model.multipleTypes,
                    value: this.value,
                    values: this.values,
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
            model: Object,
            value: String,
            values: Object,
        },

        methods: {
        },
    }
</script>
