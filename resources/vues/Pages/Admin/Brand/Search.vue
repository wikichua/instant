<template>
    <form @submit.prevent="onSearch" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-input-field label="Name" :form="form.filters" objprop="name" id="name" type="text"/>
            <instant-input-field label="Domain" :form="form.filters" objprop="domain" id="domain" type="text"/>
            <instant-date-range-field label="Published At" :form="form.filters" objprop="published_at" id="published_at"/>
            <instant-date-range-field label="Expired At" :form="form.filters" objprop="expired_at" id="expired_at"/>
            <instant-select-field label="Status" :form="form.filters" objprop="status" id="status" :options="$page.props.status" tags/>
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
                        name: '',
                        domain: '',
                        published_at: '',
                        expired_at: '',
                        status: [''],
                    },
                })
            }
        },
        methods: {
            onSearch() {
                this.form.post(this.route('cronjob'), {
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
