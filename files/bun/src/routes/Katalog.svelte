<script>
  import { onMount } from 'svelte';
  import { ProgressRadial, Autocomplete, popup, Paginator } from '@skeletonlabs/skeleton';

  let data;

  onMount(async () => {
    let response = await fetch('/api/activity');
    if (response.ok) {
      data = await response.json();
      console.log(data);
    } else {
      page.set({status: response.status, error: {message: response.statusText}});
    }
  });

  let czech = {
    primarySchool: "Základní škola",
    secondarySchool: "Střední škola",
    highSchool: "Vysoká škola",
    Individual: "Individuální", 
    Group: "Skupina",
    All: "Všechny"
  }

  function humanReadableTime(minutes) {
    const hours = (minutes / 60)^0;
    const remainingMinutes = minutes % 60;
    return `${hours}h ${remainingMinutes}min`;
  }
</script>

{#if data}
  <div class="flex flex-col items-center p-3 mx-20">
    {#each data as activity}
      <a href="/activity/{activity.uuid}" class="card card-hover p-8 border-primary-500 border-2 w-full">
        <h2 class="h2">{activity.activityName}</h2>
        <p class="font-bold">{activity.description}</p>
 
        <p>Struktura: {czech[activity.classStructure]}</p>
        <p>Délka: {humanReadableTime(activity.lengthMin)} - {humanReadableTime(activity.lengthMax)}</p>
        <p>Úroveň vzdělání: {#each activity.edLevel as level}{czech[level]+","} {/each}</p>
        <p>Potřebné vybavení: {#each activity.tools as tool}{tool}, {/each}</p>
      </a>
    {/each}
  </div>
{:else}
  <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="btn w-20 m-20 col-span-full"/>
{/if}