import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast, POSITION } from 'vue-toastification';

export default function useFlashNotifications() {
    const page = usePage();
    const toast = useToast();


    const successMessage = computed(() => page.props.flash.success);

    if (successMessage.value) {
        toast.success(successMessage.value, {
            position: POSITION.TOP_CENTER,
            draggable: true,
        });

        page.props.flash.success = null;
        return
    }


    const errorMessage = computed(() => page.props.flash.error);

    if (errorMessage.value) {
        toast.error(errorMessage.value, {
            position: POSITION.TOP_CENTER,
            draggable: true,
        });

        page.props.flash.error = null;
        return
    }


    const warningMessage = computed(() => page.props.flash.warning);

    if (warningMessage.value) {
        toast.warning(warningMessage.value, {
            position: POSITION.TOP_CENTER,
            draggable: true,
        });
        page.props.flash.warning = null;
        return
    }
}
