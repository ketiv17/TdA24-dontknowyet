<script>
  import "../app.css";
  import {AppShell, Toast} from '@skeletonlabs/skeleton';
  import Nav from './Nav.svelte'
</script>
<Toast />
<AppShell>
  <svelte:fragment slot="header"><Nav /></svelte:fragment>
  <svelte:fragment slot="sidebarLeft">
  </svelte:fragment>
  <slot />
  <svelte:fragment slot="pageFooter">
    <nav class="variant-filled-primary h-56 mt-28 flex justify-center items-center">
      <img src="/icons/TdA_LOGO/TeacherDigitalAgency_LOGO_colour-white.svg" alt="Teacher digital Agency logo" class="h-1/2 flex-auto">
    </nav>
  </svelte:fragment>
</AppShell>