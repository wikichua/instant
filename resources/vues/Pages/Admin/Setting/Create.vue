<template>
    <authenticated-layout>
        <template #page-title>
            <inertia-link class="text-white text-sm uppercase hidden lg:inline-block font-semibold" :href="route('setting')">
                {{ $page.props.moduleName }}
            </inertia-link>
        </template>
        <instant-content-card class="w-full xl:w-8/12">
            <template #content-title>
                Create
            </template>
            <form @submit.prevent="submit">
                <div class="shadow overflow-hidden sm:rounded-md">
                    <instant-input-field label="Key" :form="form" objprop="key" id="key"/>
                    <instant-checkbox-field label="Apply Encryption" :form="form" objprop="protected" id="protected" :option="{ label: 'Yes', value: 1 }" />
                    <instant-checkbox-field label="Multiple values" :form="form" objprop="multipleTypes" id="multipleTypes" :option="{ label: 'Yes', value: 1 }" @change="swapInputs"/>
                    <instant-textarea-field label="Value" :form="form" objprop="value" id="value" v-if="!isMultiple"/>
                    <instant-multi-values-field label="Values" :form="form" objprop="values" id="values" v-if="isMultiple"/>
                    <instant-button-field>Save</instant-button-field>
                </div>
            </form>
        </instant-content-card>
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
                isMultiple: false,
                form: this.$inertia.form({
                    key: '',
                    protected: false,
                    multipleTypes: false,
                    value: '',
                    values: [{
                        key: '',
                        value: '',
                    }],
                })
            }
        },

        props: {
            auth: Object,
            errors: Object,
        },

        methods: {
            submit() {
                this.form.post(this.route('setting.create'), {
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
