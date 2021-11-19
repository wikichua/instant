<template>
    <form @submit.prevent="emitter.emit('onSubmitFilter', {form, route: route('failedjob')})" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-date-range-field label="Failed At" :form="form.filters" objprop="failed_at" id="failed_at"/>
            <instant-input-field label="Queue" :form="form.filters" objprop="queue" id="queue" type="text"/>
            <instant-input-field label="Exception" :form="form.filters" objprop="exception" id="exception" type="text"/>
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
                        failed_at: '',
                        queue: '',
                        exception: '',
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
