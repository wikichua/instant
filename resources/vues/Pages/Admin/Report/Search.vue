<template>
    <form @submit.prevent="onSearch" class="w-full">
        <div class="shadow overflow-hidden sm:rounded-md">
            <!-- <instant-input-field label="Group" :form="form.filters" objprop="group" id="group"/> -->
            <instant-select-field label="Group" :form="form.filters" objprop="group" id="group" :options="$page.props.groups" tags/>
            <instant-input-field label="Permissions" :form="form.filters" objprop="name" id="name"/>
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
