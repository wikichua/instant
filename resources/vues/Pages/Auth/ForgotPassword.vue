<template>
    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
    </div>

    <instant-validation-errors class="mb-4" />

    <form @submit.prevent="submit">
        <div>
            <instant-label for="email" value="Email" />
            <instant-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <instant-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Email Password Reset Link
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
                    email: ''
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.email'))
            }
        }
    }
</script>
