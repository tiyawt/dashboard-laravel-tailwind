import { createPopper } from "@popperjs/core";
import "./bootstrap";
import Alpine from "alpinejs";
import ApexCharts from "apexcharts";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import { Calendar } from "fullcalendar";

import "./asset-inventory";

window.Alpine = Alpine;
window.createPopper = createPopper;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;

Alpine.start();

window.addEventListener("load", () => {
    if (document.querySelector("#mapOne")) {
        import("./components/map").then((module) => module.initMap());
    }
});
