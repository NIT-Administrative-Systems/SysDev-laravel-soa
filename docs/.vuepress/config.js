module.exports = {
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