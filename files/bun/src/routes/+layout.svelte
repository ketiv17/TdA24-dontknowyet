<script>
  import "../app.css";
  import { computePosition, autoUpdate, offset, shift, flip, arrow } from '@floating-ui/dom'; // import popup
  storePopup.set({ computePosition, autoUpdate, offset, shift, flip, arrow }); // set stores for popup
  import {AppShell, initializeStores, storePopup} from '@skeletonlabs/skeleton';
  initializeStores();
  import Nav from './Nav.svelte'
  import {onMount} from 'svelte';
  import {checkLogin} from '$lib/login.js';

  onMount(() => {
    fetchauth();
  });

  async function fetchauth() {
    const response = await fetch('/api/login/auth');
    checkLogin(response);
  }
</script>
<AppShell>
  <svelte:fragment slot="header"><Nav /></svelte:fragment>
  <!--<svelte:fragment slot="sidebarLeft"></svelte:fragment>-->
  <slot />
  <svelte:fragment slot="pageFooter">
    <nav class="variant-filled-primary h-56 mt-28 flex justify-center items-center">
      <img src="/icons/TdA_LOGO/TeacherDigitalAgency_LOGO_colour-white.svg" alt="Teacher digital Agency logo" class="h-1/2 flex-auto">
    </nav>
  </svelte:fragment>
</AppShell>