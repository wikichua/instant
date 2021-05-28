<template>
    <instant-show-link :href="route('versionizer.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
    <instant-patch-link :href="route('versionizer.revert',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.revert && isNull(model.reverted_at)" :confirmed="true" confirm-message="Do your wish to continue revert this?">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
    </instant-patch-link>
    <instant-delete-link :href="route('versionizer.destroy',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete"/>
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
        isNull (value) {
            return _.isNull(value);
        }
    },
}
</script>

<style lang="css" scoped>
</style>
