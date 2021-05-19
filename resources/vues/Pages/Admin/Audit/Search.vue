<template>
    <form @submit.prevent="onSearch" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <instant-input-field label="Created At" :form="form.filters" objprop="created_at" id="created_at" type="date"/>
            <instant-input-field label="Name" :form="form.filters" objprop="name" id="name" type="text"/>
            <instant-select-field label="Group" :form="form.filters" objprop="group" id="group" :options="$page.props.groups" tags/>
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
                        group: [''],
                        name: '',
                        created_at: '',
                    },
                })
            }
        },
        methods: {
            onSearch() {
                this.form.post(this.route('role'), {
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
