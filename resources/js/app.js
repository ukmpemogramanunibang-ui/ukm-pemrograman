import './bootstrap';
import 'flowbite';
import Swiper from 'swiper/bundle';
import Swal from 'sweetalert2';
import 'animate.css';
import { Howl } from 'howler';
import { Html5Qrcode } from 'html5-qrcode';
import axios from 'axios';
import dayjs from 'dayjs';
import { Chart } from 'chart.js/auto';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';

// ✅ import device helper (auto-init)
import deviceHelper, { attachSwipe, isTouch, getBreakpoint } from './device-helper';

// Init AOS
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 700,
        easing: 'ease-in-out',
        once: false,
        mirror: true,
        disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    });
});

// Global
window.Alpine = Alpine;
window.AOS = AOS;
window.Swiper = Swiper;
window.Swal = Swal;
window.Howl = Howl;
window.Html5Qrcode = Html5Qrcode;
window.axios = axios;
window.dayjs = dayjs;
window.Chart = Chart;
window.deviceHelper = deviceHelper;
window.attachSwipe = attachSwipe;
window.isTouch = isTouch;
window.getBreakpoint = getBreakpoint;

Alpine.start();
AOS.init();