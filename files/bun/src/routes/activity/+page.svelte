<script>
  import { onMount } from 'svelte';
  import { ProgressRadial, Autocomplete, popup, Paginator } from '@skeletonlabs/skeleton';
  import {humanReadableTime, czech, search} from '$lib/utils.js';
  import Search from '$lib/search.svelte';
  import { get } from 'svelte/store';

  let error = "";
  let data;

  //call the load when search store changes

    $: {
      if ($search !== undefined && $search !== ''){
        reload();
      }
    }

    async function reload() {
      data = undefined;
      error = "";
      let response = await fetch(`/api/ai/return/`, {
        method: 'POST', 
        headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({prompt: get(search)})
      });
      if (response.ok) {
        data = await response.json();
        console.log(data);
      } else {
        error = "Nic jsme nenašli. Zkuste jiný dotaz.";
      }
    }

    onMount(() => {
      reload();
    });
  </script>

<svelte:head>
  <title>Vzhledávání - AMOS</title>
</svelte:head>

    <h1 class="h1 text-center mt-12 mb-5">Vyhledat Aktivitu</h1>
  <Search />


{#if data}
  <div class="flex flex-col items-center p-3 mx-20">
    {#each data as activity}
      <div>{activity.reason}</div>
      <a href="/activity/{activity.uuid}" class="card card-hover p-8 border-primary-500 border-2 mb-8 w-full mt-4">
        <h2 class="h2">{activity.activityName}</h2>
        <p class="font-bold">{activity.description}</p>
 
        <p>Struktura: {czech[activity.classStructure]}</p>
        <p>Délka: 
          {#if activity.lengthMin === activity.lengthMax}
            {humanReadableTime(activity.lengthMin)}
          {:else}
            {humanReadableTime(activity.lengthMin)} - {humanReadableTime(activity.lengthMax)}
          {/if}
        </p>        <p>Úroveň vzdělání: {#each activity.edLevel as level}{czech[level]+","} {/each}</p>
        <p>Potřebné vybavení: {#each activity.tools as tool}{tool}, {/each}</p>
      </a>
    {/each}
  </div>
{:else}

<div class="flex flex-col items-center">
  <p>{error}</p>

  <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="btn w-20 m-20 col-span-full"/>
</div>

{/if}