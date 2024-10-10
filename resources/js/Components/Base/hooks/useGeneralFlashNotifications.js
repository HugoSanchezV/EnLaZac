import useFlashNotificationsComputed from "./useFlashNotificationsComputed";
import useFlashNotificationsWatch from "./useFlashNotificationsWatch";

export default function useGeneralNotifications() {
    useFlashNotificationsComputed();
    useFlashNotificationsWatch();
}