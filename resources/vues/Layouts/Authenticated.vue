<template>
  <div>
    <instant-sidebar></instant-sidebar>
    <div class="relative md:ml-64 bg-blueGray-100">
      <div class="relative bg-blue-300 md:pt-32 pb-12 pt-12">
        <instant-navbar>
          <template #navbar-page-title>
              <slot name="page-title" />
          </template>
        </instant-navbar>
      </div>
      <div class="px-4 md:px-10 mx-auto w-full -m-24">
        <div class="flex flex-wrap mt-4">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      date: new Date().getFullYear()
    }
  }
};
</script>
