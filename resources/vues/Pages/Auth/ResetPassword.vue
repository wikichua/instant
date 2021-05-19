<template>
    <instant-validation-errors class="mb-4" />

    <form @submit.prevent="submit">
        <div>
            <instant-label for="email" value="Email" />
            <instant-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus autocomplete="username" />
        </div>

        <div class="mt-4">
            <instant-label for="password" value="Password" />
            <instant-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
        </div>

        <div class="mt-4">
            <instant-label for="password_confirmation" value="Confirm Password" />
            <instant-input id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <instant-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Reset Password
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
            email: String,
            errors: Object,
            token: String,
        },

        data() {
            return {
                form: this.$inertia.form({
                    token: this.token,
                    email: this.email,
                    password: '',
                    password_confirmation: '',
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.update'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
            }
        }
    }
</script>
