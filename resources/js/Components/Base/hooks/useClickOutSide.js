export default {
    beforeMount(el, binding) {
        el.clickOutsideEvent = (event) => {
            // Si el clic ocurre dentro del elemento o en sus hijos, no cierra el dropdown
            if (el.contains(event.target)) {
                return;
            }
            // Ejecuta el método asociado solo si el clic es fuera del dropdown
            binding.value(event);
        };
        document.addEventListener("click", el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener("click", el.clickOutsideEvent);
    },
};

