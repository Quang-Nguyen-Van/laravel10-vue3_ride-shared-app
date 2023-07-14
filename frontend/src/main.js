import "./assets/main.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router/index";
import App from "./App.vue";

import VueGoogleMaps from "@fawmi/vue-google-maps";

const app = createApp(App);
app.use(router);
app.use(createPinia());

app.use(VueGoogleMaps, {
  load: {
    key: "AIzaSyCAqdwRPpTtDGc6lWZKlSO0EPgkAKRo-8o",
    libraries: "places",
  },
});

app.mount("#app");
