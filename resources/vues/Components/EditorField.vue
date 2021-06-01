<template>
    <div class="px-4 py-5 bg-white sm:p-6">
        <div class="grid grid-cols-6 gap-6">
            <div class="col-span-6">
                <instant-label :for="id" :value="label" class="block text-sm font-medium text-gray-700" />
                <vue-editor toolbar="full" :id="id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="form[objprop]" :disabled="disabled" useCustomImageHandler @image-added="handleImageAdded">
                </vue-editor>
                <instant-input-error :message="$page.props.errors[id]" />
            </div>
        </div>
    </div>
</template>

<script>
    import { VueEditor } from "vue3-editor";
    export default {
        components: {
            VueEditor,
        },
        props: {
            id: String,
            label: String,
            form: Object,
            objprop: String,
            disabled: {
                default: false,
            },
        },
        data () {
            return {

            }
        },
        methods: {
            handleImageAdded: function(file, Editor, cursorLocation, resetUploader) {
                var formData = new FormData();
                formData.append("image", file);
                axios({
                    url: route('editor.upload_image'),
                    method: "POST",
                    data: formData
                })
                .then(result => {
                    let url = result.data.url;
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch(err => {
                    console.log(err);
                });
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
