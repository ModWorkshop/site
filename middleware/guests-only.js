import { useStore } from '../store';
// Moves users from pages that are for guests only (login & register generally)

export default function({ $pinia, redirect }) {
    const { user } = useStore();
    if (user) {
        redirect('/');
    }
}