<script>
    import {AppBar} from '@skeletonlabs/skeleton'
</script>

<AppBar gridColumns="grid-cols-3" slotDefault="place-self-center" slotTrail="place-content-end" padding="p-0" class="h-20" slotLead="h-20">
	<svelte:fragment slot="lead">
        <a href="/" class="btn btn-lg h-full">
            <img src="/icons/TdA_LOGO/TeacherDigitalAgency_LOGO_white.svg" alt="Home" class="h-3/4">
        </a>
    </svelte:fragment>
	<svelte:fragment slot="trail">
        <button class="btn-icon btn-icon-lg mr-2 variant-filled-secondary">
            <img src="/icons/TdA_ikony/SVG/TdA_ikony_nastaveni_white.svg" alt="status" class="h-1/2">
        </button>
    </svelte:fragment>
</AppBar>