import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast, POSITION } from 'vue-toastification';

export default function useFlashNotifications() {
    const page = usePage();
    const toast = useToast();

    watch(
        () => page.props.flash.success,
        (newValue) => {
            if (newValue) {
                toast.success(newValue, {
                    position: POSITION.TOP_CENTER,
                    draggable: true,
                });

                page.props.flash.success = null;
            }
        }
    );

    watch(
        () => page.props.flash.error,
        (newValue) => {
            if (newValue) {
                toast.error(newValue, {
                    position: POSITION.TOP_CENTER,
                    draggable: true,
                });

                page.props.flash.error = null;
            }
        }
    );

    watch(
        () => page.props.flash.warning,
        (newValue) => {
            if (newValue) {
                toast.warning(newValue, {
                    position: POSITION.TOP_CENTER,
                    draggable: true,
                });

                page.props.flash.warning = null;
            }
        }
    );
}
