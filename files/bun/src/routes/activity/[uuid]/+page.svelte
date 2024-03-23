<script>
  import {page} from '$app/stores';
  import { onMount } from 'svelte';
  import { humanReadableTime, czech } from '$lib/utils.js';

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
<div>
  <h1>{activity.activityName}</h1>
  <p>{activity.description}</p>
  <p>Class Structure: {activity.classStructure}</p>
  <p>Length: {activity.lengthMin} - {activity.lengthMax}</p>
  <p>Education Level: {activity.edLevel.join(', ')}</p>
  <p>Tools: {activity.tools.join(', ')}</p>
  <h2>Home Preparation</h2>
  {#each activity.homePreparation as prep}
    <div>
      <h3>{prep.title}</h3>
      <p>Warning: {prep.warn}</p>
      <p>Note: {prep.note}</p>
    </div>
  {/each}
  <h2>Instructions</h2>
  {#each activity.instructions as instruction}
    <div>
      <h3>{instruction.title}</h3>
      <p>Warning: {instruction.warn}</p>
      <p>Note: {instruction.note}</p>
    </div>
  {/each}
  <h2>Agenda</h2>
  {#each activity.agenda as agendaItem}
    <div>
      <h3>{agendaItem.title} ({agendaItem.duration})</h3>
      <p>{agendaItem.description}</p>
    </div>
  {/each}
  <h2>Links</h2>
  {#each activity.links as link}
    <a href={link.url}>{link.title}</a>
  {/each}
  <h2>Gallery</h2>
  {#each activity.gallery as gallery}
    <div>
      <h3>{gallery.title}</h3>
      {#each gallery.images as image}
        <img src={image.lowRes} alt={gallery.title} />
      {/each}
    </div>
  {/each}
</div>
{:else}
  <p>Loading...</p>
{/if}