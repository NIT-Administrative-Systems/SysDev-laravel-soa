import { defineUserConfig } from 'vuepress';
import defaultTheme from '@vuepress/theme-default';
import { viteBundler } from '@vuepress/bundler-vite'
import { searchProPlugin } from 'vuepress-plugin-search-pro';
import { mdEnhancePlugin } from 'vuepress-plugin-md-enhance';

export default defineUserConfig({
    title: 'Northwestern Tools for Laravel',
    description: 'Enhance Laravel with easy access to popular Northwestern APIs & webSSO/Duo multi-factor authentication.',
    head: [
        ['link', { href: 'https://common.northwestern.edu/v8/icons/favicon-16.png', rel: 'icon', sizes: '16x16', type: 'image/png' }],
        ['link', { href: 'https://common.northwestern.edu/v8/icons/favicon-32.png', rel: 'icon', sizes: '32x32', type: 'image/png' }],
        ['link', { href: 'https://use.fontawesome.com/releases/v6.4.0/css/all.css', rel: 'stylesheet'}],
    ],
    pagePatterns: ['**/*.md', '!**/README.md', '!.vuepress', '!node_modules'],
    base: '/SysDev-laravel-soa/',

    bundler: viteBundler({
        viteOptions: {},
        vuePluginOptions: {},
    }),

    theme: defaultTheme({
        repo: 'NIT-Administrative-Systems/SysDev-laravel-soa',
        docsBranch: 'develop',
        docsDir: 'docs',
        editLink: true,
        editLinkText: 'Edit Page',
        lastUpdated: true,
        sidebarDepth: 1,

        sidebar: [
            {
                text: 'Getting Started',
                collapsible: false,
                children: [
                    { text: 'Introduction', link: '/' },
                    { text: 'Upgrading', link: '/upgrading' },
                ],
            },

            {
                text: 'Services',
                collapsible: false,
                children: [
                    { text: 'WebSSO', link: '/websso' },
                    { text: 'EventHub', link: '/eventhub' },
                    { text: 'Directory Search', link: '/directory-search' },
                ],
            }
        ],
    }),
    plugins: [
        searchProPlugin({
            indexContent: true,
            searchDelay: 500,
            autoSuggestions: false,
        }),
        mdEnhancePlugin({
            tabs: true,
            footnote: true,
            mark: true,
            include: true,
        })
    ],
});