<script>
  import {page} from '$app/stores';
  import Carousel from 'svelte-carousel';

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
<svelte:head>
  {#if activity}
    <title>{activity.activityName} - AMOS - Teacher digital Agency</title>
  {:else}
    <title>AMOS - Teacher digital Agency</title>
  {/if}
</svelte:head>
{#if activity}
<div class="flex flex-col items-center p-2 sm:p-4 lg:p-8">
  <h1 class="h1">{activity.activityName}</h1>
  <p class="font-bold">{activity.description}</p>
  <br>
  <br>
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
      <Accordion>
      {#each activity.homePreparation as prep}
        <div class="rounded-container-token variant-filled-secondary m-2 mt-0 !pt-2">

            <AccordionItem>
              <svelte:fragment slot="summary"><span>{prep.title}</span></svelte:fragment>
              <svelte:fragment slot="content">
                {#if prep.warn}<p class="pl-4"><span>Pozor:</span> {prep.warn}</p>{/if}
                {#if prep.note}<p class="pl-4"><span>ps.</span> {prep.note}</p>{/if}
              </svelte:fragment>
            </AccordionItem>

        </div>
      {/each}
    </Accordion>
    </div>

    <div>
      <h2 class="h6">Instrukce</h2>
      <Accordion>
      {#each activity.instructions as instruction}
        <div class="rounded-container-token variant-filled-secondary m-2 mt-0 !pt-2">

            <AccordionItem>
              <svelte:fragment slot="summary"><span>{instruction.title}</span></svelte:fragment>
              <svelte:fragment slot="content">
                {#if instruction.warn}<p class="pl-4"><span>Pozor:</span> {instruction.warn}</p>{/if}
                {#if instruction.note}<p class="pl-4"><span>ps.</span> {instruction.note}</p>{/if}
              </svelte:fragment>
            </AccordionItem>

        </div>
      {/each}
    </Accordion>
    </div>

    <div>
      <h2 class="h6">Agenda</h2>
      <Accordion>
      {#each activity.agenda as agendaItem}
        <div class="rounded-container-token variant-filled-secondary m-2 mt-0 !pt-2">

            <AccordionItem>
              <svelte:fragment slot="summary"><span>{agendaItem.title} ({humanReadableTime(agendaItem.duration)})</span></svelte:fragment>
              <svelte:fragment slot="content">
                {#if agendaItem.description}<p class="pl-4"><span>Pozor:</span> {agendaItem.description}</p>{/if}
              </svelte:fragment>
            </AccordionItem>

        </div>
      {/each}
    </Accordion>
    </div>
<div>
  <h2  class="h6">Odkazy</h2>
  {#each activity.links as link}
    <a class="italic underline text-blue-500" href={link.url}>{link.title}</a><br>
  {/each}
</div>
</div>
</div>
<div class=" p-2 sm:p-4 lg:p-8">
  <center><h2 class="h4">Galerie</h2></center>
  {#each activity.gallery as gallery}
    <div class="p-1 pt-4">
      <h3><span>{gallery.title}:</span></h3>
      <div>
        {#if gallery.images.length === 1}
          <img class="rounded max-h-64 m-1" src={gallery.images[0].highRes} alt={gallery.images[0].title} />
        {:else}
          <Carousel>
            {#each gallery.images as image}
              <div>
                <img class="rounded max-h-64 m-1" src={image.highRes} alt={image.title} />
              </div>
            {/each}
          </Carousel>
        {/if}
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