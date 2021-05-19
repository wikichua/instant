<template>
    <instant-validation-errors class="mb-4" />

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
    </div>

    <form @submit.prevent="submit">

        <div class="mt-4">
            <instant-label for="password" value="Password" />
            <instant-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" />
        </div>

        <div class="flex items-center justify-end mt-4">

            <instant-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Log in
            </instant-button>
        </div>
    </form>
</template>

<script>
    import GuestLayout from "@/Layouts/Guest"

    export default {
        layout: GuestLayout,

        props: {
            auth: Object,
            errors: Object,
            status: String,
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: ''
                })
            }
        },

        methods: {
            submit() {
                this.form
                    .transform(data => ({
                        ... data,
                    }))
                    .post(this.route('reauth'), {
                        onFinish: () => this.form.reset('password'),
                    })
            }
        }
    }
</script>
