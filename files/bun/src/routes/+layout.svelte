<script>
    import "../app.css";
    import { computePosition, autoUpdate, offset, shift, flip, arrow } from '@floating-ui/dom'; // import popup
    storePopup.set({ computePosition, autoUpdate, offset, shift, flip, arrow }); // set stores for popup
    import {AppShell, Toast, getToastStore, initializeStores, storePopup} from '@skeletonlabs/skeleton';
    initializeStores();
    import Nav from './Nav.svelte'
    import Footer from './Footer.svelte'
</script>
<Toast />
<AppShell>
    <svelte:fragment slot="header"><Nav /></svelte:fragment>
    <slot />
    <svelte:fragment slot="pageFooter"><Footer /></svelte:fragment>
</AppShell>