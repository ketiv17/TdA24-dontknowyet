<script>
    import "../app.css";
    import {AppShell} from '@skeletonlabs/skeleton';
    import Nav from './Nav.svelte'
    import Footer from './Footer.svelte'
</script>
<AppShell>
    <svelte:fragment slot="header"><Nav /></svelte:fragment>
    <slot />
    <svelte:fragment slot="pageFooter"><Footer /></svelte:fragment>
</AppShell>