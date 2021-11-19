<template>
    <form @submit.prevent="emitter.emit('onSubmitFilter', {form, route: route('versionizer')})" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-date-range-field label="Created At" :form="form.filters" objprop="created_at" id="created_at"/>
            <instant-input-field label="Changes" :form="form.filters" objprop="changes" id="changes" type="text"/>
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
                        changes: '',
                        created_at: '',
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
