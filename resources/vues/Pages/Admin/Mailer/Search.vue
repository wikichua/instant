<template>
    <form @submit.prevent="emitter.emit('onSubmitFilter', {form, route: route('mailer')})" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-date-range-field label="Created At" :form="form.filters" objprop="created_at" id="created_at"/>
            <instant-input-field label="Subject" :form="form.filters" objprop="subject" id="subject" type="text"/>
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
                        created_at: '',
                        subject: '',
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
