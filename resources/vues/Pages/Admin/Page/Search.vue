<template>
    <form @submit.prevent="onSearch" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-date-range-field label="Created At" :form="form.filters" objprop="created_at" id="created_at"/>
            <instant-input-field label="Name" :form="form.filters" objprop="name" id="name" type="text"/>
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
                        name: '',
                    },
                })
            }
        },
        methods: {
            onSearch() {
                this.form.post(this.route('page'), {
                    replace: true,
                    preserveState: true,
                    preserveScroll: false,
                    forceFormData: true,
                    onFinish: () => this.emitter.emit('toggleModal'),
                });
            },
        }
    }
</script>

<style lang="css" scoped>
</style>
