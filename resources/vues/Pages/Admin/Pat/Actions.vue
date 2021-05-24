<template>
    <div>
        <form @submit.prevent="onDelete(model.id)" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete">
            <instant-delete-link class="text-indigo-600 hover:text-indigo-900 inline-block" />
        </form>
        <instant-delete-link :href="route('pat.destroy',[$page.props.user_id, model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete"/>
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
}
</script>

<style lang="css" scoped>
</style>
