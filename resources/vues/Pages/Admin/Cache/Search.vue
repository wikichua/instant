<template>
    <form @submit.prevent="emitter.emit('onSubmitFilter', {form, route: route('cache')})" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-input-field label="Key" :form="form.filters" objprop="key" id="key" type="text"/>
            <instant-select-field label="Tags" :form="form.filters" objprop="tags" id="tags" type="text" tags/>
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
                        key: '',
                        tags: [],
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
