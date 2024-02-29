<script>
  import {ProgressRadial, Avatar, getModalStore} from '@skeletonlabs/skeleton';
  import {page} from '$app/stores';
  import {fullName, null2string} from '$lib/string.js'
  import Reservation from './Reservation.svelte';

  export let data;
  let uuid;
  $: uuid = $page.params.uuid;

  const czechFor = {
    "emails": "e-mail",
    "telephone_numbers": "telefon"
  };
  const uriof = {
    "emails": "mailto:",
    "telephone_numbers": "tel:"
  }

  const modalStore = getModalStore();
  function reservation() {
		const c = { ref: Reservation, props: {uuid: uuid} };
		const modal = {
			type: 'component',
			component: c,
			title: 'Rezervace lekce',
			body: fullName(data),
			response: (r) => console.log('response:', r)
		};
		modalStore.trigger(modal);
	}
</script>

<svelte:head>
  {#if data.lecturer}  
    <title>{fullName(data.lecturer)} - Teacher digital Agency</title>
  {:else}
    <title>Teacher digital Agency</title>
  {/if}
</svelte:head>

<main class="flex justify-center">
  <div class="flex flex-col gap-y-5 xl:w-1/2 lg:w-8/12 md:w-10/12 w-full mt-12">
    <ol class="breadcrumb text-xs p-2">
      <li class="crumb"><a href="/" class="anchor">Domů</a></li>
      <li class="crumb" aria-hidden>›</li>
      <li class="crumb">{uuid}</li>
    </ol>
    {#if data.lecturer}
      {#if data.lecturer.picture_url !== null}
        <Avatar src="{data.lecturer.picture_url}" class="w-48 self-center m-8" shadow="shadow-2xl" />
      {/if}
      <h1 class="h1 text-center">{fullName(data.lecturer)}</h1>
      <h4 class="h4 text-center">{null2string(data.lecturer.location)}</h4>
      <h3 class="h3 text-center">{null2string(data.lecturer.claim)}</h3>
      <div class="w-full text-center m-2">
        {#if data.lecturer.tags !== null && data.lecturer.tags.length !== 0}
          {#each data.lecturer.tags as tag}
            <span class="badge text-sm rounded-full m-1 variant-filled-tertiary">{tag.name}</span>
          {/each}
        {/if}
      </div>
      <article class="blockquote not-italic m-2">{@html null2string(data.lecturer.bio)}</article>
      <div class="grid sm:grid-cols-2 mx-4">
        <div>Kontakty:
          <ul class="list-disc ml-4">
            {#each ["telephone_numbers", "emails"] as contact_type}
              {#if data.lecturer.contact[contact_type] !== null && data.lecturer.contact[contact_type].lenght !== 0}
                <li>{czechFor[contact_type]+":"}
                  {#each data.lecturer.contact[contact_type] as contact_value}
                    <a href="{uriof[contact_type]+contact_value}" class="badge rounded-full variant-ghost-secondary m-1">{contact_value}</a>
                  {/each}
                </li>
              {/if}
            {/each}
          </ul>
        </div>
        <div>
          <p>Cena za hodinu: {null2string(data.lecturer.price_per_hour)} Kč/hod</p>
          <button class="btn btn-md variant-filled-tertiary m-2" on:click={() => reservation()}>Rezervace</button>
        </div>
      </div>
      <div>
        
      </div>
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20 self-center"/>
    {/if}
  </div>
</main>