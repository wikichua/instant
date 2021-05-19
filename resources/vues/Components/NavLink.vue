<template>
    <li class="items-center">
        <inertia-link :href="href" :class="classes">
            <svg xmlns="http://www.w3.org/2000/svg" class="inline-flex h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
            <slot />
        </inertia-link>
      </li>
</template>

<script>
    export default {
        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                            ? 'text-pink-500 hover:text-pink-600 text-sm uppercase py-2 font-bold block'
                            : 'text-blueGray-700 hover:text-blueGray-500 text-sm block py-2 no-underline font-semibold'
            }
        }
    }
</script>
