import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';
import northwesternTheme from "@nu-appdev/northwestern-starlight-theme";

// https://astro.build/config
export default defineConfig({
  site: "https://nit-administrative-systems.github.io/SysDev-laravel-soa//",
  integrations: [
    starlight({
      plugins: [northwesternTheme()],
      title: "Northwestern Tools for Laravel",
      editLink: {
        baseUrl: "https://github.com/NIT-Administrative-Systems/SysDev-laravel-soa//edit/develop/docs/",
      },
      sidebar: [
        {
          label: "Overview",
          link: '/'
        },
        {
          label: "Upgrading",
          link: '/upgrading'
        },
        {
          label: "Services",
          autogenerate: { directory: "services" },
        },
      ],
      social: [
        {
          icon: "github",
          label: "GitHub",
          href: "https://github.com/NIT-Administrative-Systems/SysDev-laravel-soa/",
        },
      ],
    }),
  ]
});