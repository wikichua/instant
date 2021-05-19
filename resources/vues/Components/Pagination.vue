<template>
  <div class="mt-6 mb-1 flex flex-wrap px-3">
    <template v-for="(link, key) in links" :key="key">
      <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm border rounded text-grey" :class="{ 'ml-auto': link.label === 'Next' }">{{ link.label }}</div>
      <button type="button" @click="onPaginating(link.url)" v-else class="mr-1 mb-1 px-4 py-3 text-sm border rounded hover:bg-white focus:border-indigo focus:text-indigo" :class="{ 'bg-white': link.active, 'ml-auto': link.label === 'Next' }">{{ link.label }}</button>
    </template>
  </div>
</template>

<script>
    export default {
        props: {
            links: Array,
        },
        methods: {
            onPaginating (urlString) {
                let url = new URL(urlString);
                let urlParams = new URLSearchParams(url.search.substring(1));
                let data = Object.fromEntries(urlParams);
                console.log(data);
                this.$inertia.visit(url.origin+url.pathname, {
                  method: 'post',
                  data: data,
                  replace: true,
                  preserveState: true,
                  preserveScroll: false,
                  forceFormData: true,
                  only: [],
                  headers: {},
                  errorBag: null,
                  onCancelToken: cancelToken => {},
                  onCancel: () => {},
                  onBefore: visit => {},
                  onStart: visit => {},
                  onProgress: progress => {},
                  onSuccess: page => {},
                  onError: errors => {},
                  onFinish: visit => {},
                });
            }
        }
    }
</script>
