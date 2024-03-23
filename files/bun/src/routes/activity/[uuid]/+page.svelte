<script>
  import {page} from '$app/stores';
  import { onMount } from 'svelte';
  import { humanReadableTime, czech } from '$lib/utils.js';
  import { Accordion, AccordionItem } from '@skeletonlabs/skeleton';
  let activity;
  let uuid;
  $: uuid = $page.params.uuid;


  onMount(async () => {
    let response = await fetch(`/api/activity/${uuid}`);
    if (response.ok) {
      activity = await response.json();
      console.log(activity);
    } else {
      page.set({status: response.status, error: {message: response.statusText}});
    }
  });
</script>
{#if activity}
<div class="flex flex-col items-center p-2 sm:p-4 lg:p-8">
  <h1 class="h1">{activity.activityName}</h1>
  <p class="font-bold">{activity.description}</p>
  <div class="grid grid-cols-2 padding">
    <div class="!pt-10">
    <p><span>Struktura:</span> {czech[activity.classStructure]}</p>
    <p><span>Délka:</span> {humanReadableTime(activity.lengthMin)} - {humanReadableTime(activity.lengthMax)}</p>
    <p><span>úroveň vzdělávání:</span> {#each activity.edLevel as a}
      {czech[a]},
      {/each}
    </p>
    <p><span>Potřebné vybavení:</span> {activity.tools.join(', ')}</p>

    </div>
    <div>
      <h2 class="h6">Domácí Příprava</h2>
      {#each activity.homePreparation as prep}
        <div class="rounded-container-token variant-filled-secondary m-2 mt-0 !pt-2">
        <Accordion>
            <AccordionItem >
              <svelte:fragment slot="summary"><span>{prep.title}</span></svelte:fragment>
              <svelte:fragment slot="content">
          <p class="pl-4"><span>Pozor:</span> {prep.warn}</p>
          <p class="pl-4"><span>ps.</span> {prep.note}</p>
        </svelte:fragment>
        </AccordionItem>
        </Accordion>
        </div>
      {/each}
    </div>
  
<div>
  <h2 class="h6">Instrukce</h2>
  {#each activity.instructions as instruction}
  <div class="rounded-container-token variant-filled-secondary m-2 mt-0 !pt-2">
    <Accordion>
        <AccordionItem >
          <svelte:fragment slot="summary"><span>{instruction.title}</span></svelte:fragment>
          <svelte:fragment slot="content">
      <p class="pl-4"><span>Pozor:</span> {instruction.warn}</p>
      <p class="pl-4"><span>ps.</span> {instruction.note}</p>
    </svelte:fragment>
    </AccordionItem>
    </Accordion>
    </div>
  {/each}
</div>
<div>
  <h2  class="h6">Agenda</h2>
  {#each activity.agenda as agendaItem}
  <div class="rounded-container-token variant-filled-secondary m-2 mt-0 !pt-2">
    <Accordion>
        <AccordionItem >
          <svelte:fragment slot="summary"><span>{agendaItem.title} ({humanReadableTime(agendaItem.duration)})</span></svelte:fragment>
          <svelte:fragment slot="content">
      <p class="pl-4"><span>Pozor:</span> {agendaItem.description}</p> 
       </svelte:fragment>
    </AccordionItem>
    </Accordion>
    </div>
  {/each}
</div>
<div>
  <h2  class="h6">Odkazy</h2>
  {#each activity.links as link}
    <a class="italic underline text-blue-500" href={link.url}>{link.title}</a><br>
  {/each}
</div>
</div>
  <h2 class="h4">Galerie</h2>
  {#each activity.gallery as gallery}
    <div class="p-1 pt-4">
      <h3><span>{gallery.title}:</span></h3>
      <div class="flex">
        {#each gallery.images as image}
      <img class="max-h-96 m-1 shadow-2xl" src={image.highRes} alt={image.title} />
        {/each}
    </div>
    </div>
  {/each}
</div>
{:else}
  <p>Loading...</p>
{/if}

<style>
  .padding div {
    @apply p-2 pt-4;
  }
  span {
    @apply font-bold;
  }
</style>