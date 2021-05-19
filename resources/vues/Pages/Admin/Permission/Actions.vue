<template>
    <div>
        <instant-show-link :href="route('permission.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
        <instant-edit-link :href="route('permission.edit',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.update" />
        <form @submit.prevent="onDelete(model.id)" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete">
            <instant-delete-link class="text-indigo-600 hover:text-indigo-900 inline-block" />
        </form>
    </div>
</template>

<script>
export default {
    props: {
        model: Object,
    },
    data() {
        return {
            form: this.$inertia.form()
        }
    },
    methods: {
        onDelete(id) {
            if (confirm('Do you wish to continue remove this?')) {
                // this.$inertia.delete(this.route('permission.destroy',[id]));
                this.form.delete(this.route('permission.destroy',[id]), {
                    // onFinish: () => this.form.reset(['group','permissions']),
                });
            }
        },
    },
}
</script>

<style lang="css" scoped>
</style>
