<script>
  import {AppBar, popup} from '@skeletonlabs/skeleton'

  const popupSet = {
    event: 'click',
	  target: 'popupClick',
	  placement: 'bottom',
    middleware: {
      offset: {
        mainAxis: 10,
        crossAxis: -75
      }
    }
  };
</script>

<AppBar gridColumns="grid-cols-3" slotDefault="place-self-center" slotTrail="place-content-end" padding="p-0" class="h-20" slotLead="h-20" shadow="shadow-3xl" background="bg-primary-500">
	<svelte:fragment slot="lead">
    <a href="/" class="btn btn-lg h-full">
      <img src="/icons/TdA_LOGO/TeacherDigitalAgency_LOGO_colour-white.svg" alt="Home" class="h-3/4">
    </a>
  </svelte:fragment>
	<svelte:fragment slot="trail">
    <button class="btn-icon btn-icon-lg mr-2 variant-filled-secondary" use:popup={popupSet}>
      <img src="/icons/TdA_ikony/SVG/TdA_ikony_nastaveni_white.svg" alt="status" class="h-1/2">
    </button>
    <div class="card variant-filled-secondary m-5" data-popup="popupClick">
      <a href="/login/" class="btn">login</a>
    </div>
  </svelte:fragment>
</AppBar>