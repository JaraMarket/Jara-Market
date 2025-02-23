import Vue from "vue";
import SettingsManagement from "./components/SettingsManagement.vue";

new Vue({
    el: "#settings-management-app",
    components: { SettingsManagement },
});

new Vue({
    el: "#report-generation-app",
    components: { ReportGeneration },
});
