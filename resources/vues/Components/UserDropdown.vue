<template>
  <div>
    <a class="text-blueGray-500 block" href="#pablo" v-on:click="toggleDropdown($event)" ref="btnDropdownRef">
      <div class="items-center flex">
        <span class="w-12 h-12 text-sm text-white bg-blueGray-200 inline-flex items-center justify-center rounded-full">
          <img class="w-full rounded-full align-middle border-none shadow-lg" :src="$page.props.auth.avatar"/>
        </span>
      </div>
    </a>
    <div ref="popoverDropdownRef" class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg mt-1" v-bind:class="{ hidden: !dropdownPopoverShow, block: dropdownPopoverShow }" style="min-width: 12rem">
      <a href="#pablo" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">
        Action
      </a>
      <a href="#pablo" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">
        Another action
      </a>
      <a href="#pablo" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">
        Something else here
      </a>
      <div class="h-0 my-2 border border-solid border-blueGray-100" />
      <inertia-link :href="route('impersonate.leave')" as="button" class="text-left text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700" v-if="$page.props.impersonating">
        Leave impersonation
      </inertia-link>
      <inertia-link :href="route('logout')" method="post" as="button" class="text-left text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700" v-if="!$page.props.impersonating">
        Log Out
      </inertia-link>
    </div>
  </div>
</template>
<script>
import { createPopper } from "@popperjs/core";

export default {
  data() {
    return {
      dropdownPopoverShow: false
    };
  },
  methods: {
    toggleDropdown: function(event) {
      event.preventDefault();
      if (this.dropdownPopoverShow) {
        this.dropdownPopoverShow = false;
      } else {
        this.dropdownPopoverShow = true;
        createPopper(this.$refs.btnDropdownRef, this.$refs.popoverDropdownRef, {
          placement: "bottom-end"
        });
      }
    }
  }
};
</script>
