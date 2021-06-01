<template>
    <form @submit.prevent="onSearch" class="w-full">
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
            onSearch() {
                this.form.post(this.route('audit'), {
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
