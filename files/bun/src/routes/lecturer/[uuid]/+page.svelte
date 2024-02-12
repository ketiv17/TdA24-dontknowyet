<script>
  import {onMount} from 'svelte';
  import {ProgressRadial, Avatar} from '@skeletonlabs/skeleton';
  import {page} from '$app/stores';
  import {fullName, null2string} from '$lib/index.js'

  let data;
  let uuid;
  $: uuid = $page.params.uuid;

  async function fetchData() {
    const response = await fetch('/api/lecturers/'+uuid);
    data = await response.json();
  }

  onMount(() => {
    fetchData();
  });

  const czechFor = {
    "emails": "e-mail",
    "telephone_numbers": "telefon"
  };
</script>

<svelte:head>
  {#if data}  
    <title>{fullName(data)} - Teacher digital Agency</title>
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
    {#if data}
      {#if data.picture_url !== null}
        <Avatar src="{data.picture_url}" class="w-48 self-center m-8" shadow="shadow-2xl" />
      {/if}
      <h1 class="h1 text-center">{fullName(data)}</h1>
      <h4 class="h4 text-center">{null2string(data.location)}</h4>
      <h3 class="h3 text-center">{null2string(data.claim)}</h3>
      <div class="w-full text-center m-2">
        {#if data.tags !== null && data.tags.length !== 0}
          {#each data.tags as tag}
            <span class="badge text-sm rounded-full m-1 variant-filled-secondary">{tag.name}</span>
          {/each}
        {/if}
      </div>
      <article class="blockquote not-italic m-2">{@html null2string(data.bio)}</article>
      <div class="grid sm:grid-cols-2 mx-4">
        <div>Kontakty:
          <ul class="list-disc ml-4">
            {#each ["telephone_numbers", "emails"] as contact_type}
              {#if data.contact[contact_type] !== null && data.contact[contact_type].lenght !== 0}
                <li>{czechFor[contact_type]+":"}
                  {#each data.contact[contact_type] as contact_value}
                    <p class="badge rounded-full variant-ghost-secondary m-1">{contact_value}</p>
                  {/each}
                </li>
              {/if}
            {/each}
          </ul>
        </div>
        <p>Cena za hodinu: {null2string(data.price_per_hour)} Kč/hod</p>
      </div>
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20 self-center"/>
    {/if}
  </div>
</main>