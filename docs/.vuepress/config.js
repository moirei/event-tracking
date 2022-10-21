module.exports = {
  title: "Event Tracking",
  description:
    "Send application events to analytics services and data-warehouse.",
  base: "/event-tracking/",
  theme: "vt",
  themeConfig: {
    enableDarkMode: true,
    logo: "/logo.png",
    repo: "moirei/event-tracking",
    repoLabel: "Github",
    docsRepo: "moirei/event-tracking",
    docsDir: "docs",
    docsBranch: "master",
    sidebar: [
      {
        title: "Get started",
        sidebarDepth: 1, // optional, defaults to 1
        children: ["/installation", "/concepts"],
      },
      {
        title: "Track events",
        path: "/guide/track-events",
      },
      {
        title: "Identify users",
        path: "/guide/identify-users",
      },
      {
        title: "Using enums",
        path: "/guide/using-enums",
      },
      {
        title: "Automatic tracking",
        children: [
          ["/guide/automatic-tracking/tracking-app-events", "Track App Events"],
          [
            "/guide/automatic-tracking/tracking-model-events",
            "Track Model Events",
          ],
        ],
      },
      {
        title: "Event mapping",
        children: [
          "/guide/event-mapping/adapters",
          "/guide/event-mapping/global-maps",
        ],
      },
      {
        title: "Hooks",
        path: "/guide/hooks",
      },
      {
        title: "Queues",
        path: "/guide/queues",
      },
    ],
    nav: [
      { text: "Guide", link: "/guide/track-events" },
      {
        text: "Github",
        link: "https://github.com/moirei/event-tracking",
        target: "_self",
      },
      // { text: 'External', link: 'https://moirei.com', target:'_self' },
    ],
  },
  head: [
    ["link", { rel: "icon", href: "/logo.png" }],
    // ['link', { rel: 'manifest', href: '/manifest.json' }],
    ["meta", { name: "theme-color", content: "#3eaf7c" }],
    ["meta", { name: "apple-mobile-web-app-capable", content: "yes" }],
    [
      "meta",
      { name: "apple-mobile-web-app-status-bar-style", content: "black" },
    ],
    [
      "link",
      { rel: "apple-touch-icon", href: "/icons/apple-touch-icon-152x152.png" },
    ],
    // ['link', { rel: 'mask-icon', href: '/icons/safari-pinned-tab.svg', color: '#3eaf7c' }],
    [
      "meta",
      {
        name: "msapplication-TileImage",
        content: "/icons/msapplication-icon-144x144.png",
      },
    ],
    ["meta", { name: "msapplication-TileColor", content: "#000000" }],
  ],
  plugins: [
    "@vuepress/register-components",
    "@vuepress/active-header-links",
    "@vuepress/pwa",
    "seo",
  ],
};
