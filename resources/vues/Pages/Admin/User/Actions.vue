<template>
    <div>
        <instant-show-link :href="route('user.show',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.read" />
        <instant-edit-link :href="route('user.edit',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.update" />
        <instant-others-link :href="route('user.editPassword',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.updatePassword" >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </instant-others-link>
        <instant-others-link :href="route('impersonate',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.impersonate" >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
        </instant-others-link>
        <instant-others-link :href="route('pat',[model.id])" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.readPersonalAccessToken" >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
            </svg>
        </instant-others-link>
        <form @submit.prevent="onDelete(model.id)" class="text-indigo-600 hover:text-indigo-900 inline-block" v-if="$page.props.can.delete && model.can.onlyDeleteOtherUser">
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
                this.form.delete(this.route('user.destroy',[id]), {
                    // onFinish: () => this.form.reset(['group','permissions']),
                });
            }
        },
    },
}
</script>

<style lang="css" scoped>
</style>
