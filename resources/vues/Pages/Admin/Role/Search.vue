<template>
    <form @submit.prevent="emitter.emit('onSubmitFilter', {form, route: route('role')})" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-date-range-field label="Created At" :form="form.filters" objprop="created_at" id="created_at" />
            <instant-input-field label="Name" :form="form.filters" objprop="name" id="name"/>
            <instant-select-field label="Admin" :form="form.filters" objprop="admin" id="admin" :options="{0: 'No', 1: 'Yes'}"/>
            <instant-button-field>filter</instant-button-field>
        </div>
    </form>
</template>

<script>
    export default {
        data () {
            return {
                form: this.$inertia.form({
                    filters: {
                        admin: 1,
                        created_at: '',
                        name: '',
                    },
                })
            }
        },
        methods: {
        }
    }
</script>

<style lang="css" scoped>
</style>
