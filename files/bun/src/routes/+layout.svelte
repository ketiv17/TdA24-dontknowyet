<script>
  import "../app.css";
  import {AppShell} from '@skeletonlabs/skeleton';
  import { Toast, getToastStore } from '@skeletonlabs/skeleton';
  import { initializeStores } from '@skeletonlabs/skeleton';
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