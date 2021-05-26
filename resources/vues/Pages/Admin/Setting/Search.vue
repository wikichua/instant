<template>
    <form @submit.prevent="onSearch" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-date-range-field label="Created At" :form="form.filters" objprop="created_at" id="created_at"/>
            <instant-input-field label="Key" :form="form.filters" objprop="key" id="key"/>
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
                        created_at: '',
                    },
                })
            }
        },
        methods: {
            onSearch() {
                this.form.post(this.route('setting'), {
                    forceFormData: true,
                    onFinish: () => this.emitter.emit('toggleModal'),
                });
            },
        }
    }
</script>

<style lang="css" scoped>
</style>
