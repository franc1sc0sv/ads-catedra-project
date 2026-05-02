import './ui/select.js';
import './ui/toast.js';

console.info(
    '[ui] app.js loaded',
    {
        uiSelect: typeof window.uiSelect,
        uiToast: typeof window.uiToast,
        Alpine: typeof window.Alpine,
    }
);

document.addEventListener('alpine:init', () => {
    console.info('[ui] alpine:init fired', { uiSelect: typeof window.uiSelect, uiToast: typeof window.uiToast });
});
