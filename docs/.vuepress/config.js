module.exports = {
    base: '/SysDev-laravel-soa/',
    title: 'Northwestern Tools for Laravel',
    description: 'Enhance Laravel with easy access to popular Northwestern APIs & webSSO/Duo multi-factor authentication.',
    dest: '.build',

    themeConfig: {
        repo: 'NIT-Administrative-Systems/SysDev-laravel-soa',
        docsDir: '',
        docsBranch: 'docs',
        editLinks: true,
        editLinkText: 'Edit Page',
        lastUpdated: true,

        // https://github.com/algolia/docsearch-configs/blob/master/configs/sysdev-laravel-soa.json
        algolia: {
            apiKey: '3920f359705e7cb88057f4702554314a',
            indexName: 'sysdev-laravel-soa',
        },

        sidebar: [{
                title: 'Getting Started',
                collapsable: false,
                children: [
                    ['/', 'Installation'],
                    'upgrading',
                ],
            },

            {
                title: 'Services',
                collapsable: false,
                children: [
                    'websso',
                    'eventhub',
                    'directory-search',
                ],
            },
        ],
    },
}