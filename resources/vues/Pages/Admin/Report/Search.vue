<template>
    <form @submit.prevent="onSearch" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-input-field label="Name" :form="form.filters" objprop="name" id="name"/>
            <instant-date-range-field label="Report Generated" :form="form.filters" objprop="generated_at" id="generated_at"/>
            <instant-select-field label="Status" :form="form.filters" objprop="status" id="status" :options="$page.props.status"/>
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
                        generated_at: '',
                        status: 'A',
                        name: '',
                    },
                })
            }
        },
        methods: {
            onSearch() {
                this.form.post(this.route('report'), {
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
