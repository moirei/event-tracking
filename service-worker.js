/**
 * Welcome to your Workbox-powered service worker!
 *
 * You'll need to register this file in your web app and you should
 * disable HTTP caching for this file too.
 * See https://goo.gl/nhQhGp
 *
 * The rest of the code is auto-generated. Please don't update this file
 * directly; instead, make changes to your Workbox build configuration
 * and re-run your build process.
 * See https://goo.gl/2aRDsh
 */

importScripts("https://storage.googleapis.com/workbox-cdn/releases/4.3.1/workbox-sw.js");

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

/**
 * The workboxSW.precacheAndRoute() method efficiently caches and responds to
 * requests for URLs in the manifest.
 * See https://goo.gl/S9QRab
 */
self.__precacheManifest = [
  {
    "url": "404.html",
    "revision": "2a2bddda1edc0a9dc0d0a075b31a8eb8"
  },
  {
    "url": "assets/css/0.styles.d2a75f93.css",
    "revision": "bc3f884c8821cd28e4626c378ad9d878"
  },
  {
    "url": "assets/img/copied.26408bed.svg",
    "revision": "26408bed185146a74d6fb7d71b4207e9"
  },
  {
    "url": "assets/img/copy.e3634ccf.svg",
    "revision": "e3634ccf2a60445e59d5f255481010fd"
  },
  {
    "url": "assets/js/10.a52c08da.js",
    "revision": "72b78c5aa4d47d1449f224a162e17ab6"
  },
  {
    "url": "assets/js/11.b0731546.js",
    "revision": "ad1d9edc1e06e8c511e85a96dbea3405"
  },
  {
    "url": "assets/js/12.b88453bd.js",
    "revision": "bb322cae65e5f54327c348ff444d1006"
  },
  {
    "url": "assets/js/13.14dff535.js",
    "revision": "7231ffab7a83f7b7bc50fbba42232d48"
  },
  {
    "url": "assets/js/14.76361f18.js",
    "revision": "8d92d4e7031e210f9d478386368f594e"
  },
  {
    "url": "assets/js/15.10e33b8a.js",
    "revision": "ddf360cca22fdddadc46a3347dcd0bbc"
  },
  {
    "url": "assets/js/16.401f7ee7.js",
    "revision": "eb836272c710ecce67ccc4fa32631165"
  },
  {
    "url": "assets/js/17.5fe309d4.js",
    "revision": "b1ab08df693ac0bedeb1fbb4b3d3bd24"
  },
  {
    "url": "assets/js/18.aa20af4c.js",
    "revision": "4c8783eba480b6a2cf45c949eceda4ad"
  },
  {
    "url": "assets/js/19.a2b778d3.js",
    "revision": "57e8053a9ae1afa6dc01f2697e15789e"
  },
  {
    "url": "assets/js/2.a83daff5.js",
    "revision": "4765d27f8faef75264ed5d8f35144914"
  },
  {
    "url": "assets/js/20.ce26dd90.js",
    "revision": "8b8cf6a597a6d129a1fd9df7230fa4f8"
  },
  {
    "url": "assets/js/21.ec9f04bf.js",
    "revision": "93f30df63bc64e089d3233d78b9cd9cb"
  },
  {
    "url": "assets/js/22.ed88ae50.js",
    "revision": "85361fb198ac9e6eff75ce1866723913"
  },
  {
    "url": "assets/js/23.51b10233.js",
    "revision": "48191d397dbf421ed2dc42a473a4f84f"
  },
  {
    "url": "assets/js/24.57e87653.js",
    "revision": "cdc4f5af1a3a3449c8a21f2f5b5d66f8"
  },
  {
    "url": "assets/js/25.b90e2394.js",
    "revision": "52d424fd9dadb530cd49978784fdfc63"
  },
  {
    "url": "assets/js/26.95ac5ab7.js",
    "revision": "2aab022893379088cf56f600475b7e3c"
  },
  {
    "url": "assets/js/27.92665a4e.js",
    "revision": "e2f09e4499ef583852938868d2a9f22b"
  },
  {
    "url": "assets/js/3.cf152464.js",
    "revision": "4d1cc386d656f7d304aca80615130681"
  },
  {
    "url": "assets/js/4.84ed0150.js",
    "revision": "2298e6838f3345b78dcc924d179029f3"
  },
  {
    "url": "assets/js/5.e9063dd2.js",
    "revision": "6b4a2ee719722e0c19e4d8fbe31fec09"
  },
  {
    "url": "assets/js/6.5e4bcc14.js",
    "revision": "121b1a51fc124d1c3752afda23e308e8"
  },
  {
    "url": "assets/js/7.f20d99fb.js",
    "revision": "1f4ec30b0685898e6c57f689fcf58a7c"
  },
  {
    "url": "assets/js/8.2c37a4d0.js",
    "revision": "401425fc4ec48d1d8d580f4d86384dd7"
  },
  {
    "url": "assets/js/9.feb50405.js",
    "revision": "a3df4990c3c15d64dd753af1cae20774"
  },
  {
    "url": "assets/js/app.4578c0bc.js",
    "revision": "be601e2ed3da8e7e031a403fa46e9887"
  },
  {
    "url": "concepts.html",
    "revision": "b7f06a5b2f3d696838bdf1735e698391"
  },
  {
    "url": "guide/automatic-tracking/tracking-app-events.html",
    "revision": "5223da544b10fedaa3d8c5576890e138"
  },
  {
    "url": "guide/automatic-tracking/tracking-model-events.html",
    "revision": "426327a958274553f559af14c6d65e53"
  },
  {
    "url": "guide/event-mapping/adapters.html",
    "revision": "77a7909b225c2fe8c9177ed2cdce2fa6"
  },
  {
    "url": "guide/event-mapping/global-maps.html",
    "revision": "318683ecea0de98a72bef95088cc928d"
  },
  {
    "url": "guide/hooks.html",
    "revision": "99cf62fcc113d5f8978f7a22d7f5482e"
  },
  {
    "url": "guide/identify-users.html",
    "revision": "00d8894cc94a96b8cdf8d6dcd7e1357c"
  },
  {
    "url": "guide/queues.html",
    "revision": "b202213385b440d625558b504e9d6201"
  },
  {
    "url": "guide/testing.html",
    "revision": "537b5f5fc75f9700a1c9edf6d0f077ea"
  },
  {
    "url": "guide/track-events.html",
    "revision": "7b2ca35e9ebb8ddeb55af85fa5bf0a55"
  },
  {
    "url": "guide/using-enums.html",
    "revision": "3de0fd0e1ee25c65c90182703a245237"
  },
  {
    "url": "icons/apple-touch-icon-152x152.png",
    "revision": "bb5d8a25d314cab9fb7003293e262b7b"
  },
  {
    "url": "icons/msapplication-icon-144x144.png",
    "revision": "7b147426540b00bc662c63140819dac9"
  },
  {
    "url": "index.html",
    "revision": "27087663966fc7229cb2c823b453ea56"
  },
  {
    "url": "installation.html",
    "revision": "0bbbd362a0116a6645c895f2e7f25224"
  },
  {
    "url": "logo.png",
    "revision": "a68c56ae1a0bc32fdcbf4d244b183aef"
  }
].concat(self.__precacheManifest || []);
workbox.precaching.precacheAndRoute(self.__precacheManifest, {});
addEventListener('message', event => {
  const replyPort = event.ports[0]
  const message = event.data
  if (replyPort && message && message.type === 'skip-waiting') {
    event.waitUntil(
      self.skipWaiting().then(
        () => replyPort.postMessage({ error: null }),
        error => replyPort.postMessage({ error })
      )
    )
  }
})
