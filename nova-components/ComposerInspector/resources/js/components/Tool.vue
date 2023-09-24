<template>
    <div>
        <h1 class="font-normal text-xl md:text-xl mb-3 flex items-center">{{ __('Composer Installation') }}</h1>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-3 py-2 px-6 dark:divide-gray-700">
            <div
                class="flex flex-col -mx-6 px-6 py-2 space-y-2 md:flex-row @sm/peekable:flex-row @md/modal:flex-row md:py-0 @sm/peekable:py-0 @md/modal:py-0 md:space-y-0 @sm/peekable:space-y-0 @md/modal:space-y-0">
                <div class="md:py-3 @sm/peekable:py-3 @md/modal:py-3">
                    {{ __('Composer integration Help') }}
                </div>
            </div>
            <div class="mb-5">
                <CopyButton
                    @click.prevent.stop="copy(field.value)"
                    v-tooltip="__('Copy to clipboard')"
                    ref="composerConfig"
                    class="p-3 m-0 font-mono text-xs break-words rounded border border-gray-300 border-solid break-all md:break-keep"
                >
                    {{ field.value }}
                </CopyButton>
            </div>
            <div class="mb-5">
                <p>{{ __('the token for authentication in the file auth.json file:') }}</p>
            </div>
            <div class="mb-5">
                <CopyButton
                    v-if="panel.fields[0]?.token"
                    @click.prevent.stop="copy(token.value)"
                    v-tooltip="__('Copy to clipboard')"
                    ref="composerConfig"
                    class="p-3 m-0 font-mono text-xs md:break-words rounded border border-gray-300 border-solid break-all md:break-keep"
                >
                    {{ token.value }}
                </CopyButton>
            </div>
            <ul class="list-decimal mb-5">
                <li>{{ __('Requires composer version 1.10.0 or higher') }}</li>
                <li>{{ __('Make sure that the auth.json file is in .gitignore to prevent credentials from getting into the git history') }}</li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {

    props: ['resourceName', 'resourceId', 'panel'],

    data() {
        return {
            field: {
                value: `composer config repositories.iwaves-packages '{"type": "composer", "url": "${this.panel.fields[0].application_url}"}'`,
                copyable: true,
            },
            token: {
                value: `composer config bearer.packages.shopware.com "${this.panel.fields[0].token}"`,
                copyable: true,
            },
        }
    },

    methods: {
        async copy(text) {
            await navigator.clipboard.writeText(text);
        }
    },
}
</script>
