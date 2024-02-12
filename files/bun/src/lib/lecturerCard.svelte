<script>
  import {Avatar} from '@skeletonlabs/skeleton';
  import {fullName, null2string} from '$lib/index.js';

  export let lecturer = {};
  export let selectedTags = [];
  let width;
  let tags = [];
  $: { lecturer, width, formatTags() };

  function formatTags () {
    tags = [];
    if (lecturer.tags !== null) {
      lecturer.tags.forEach(tag => {
        tags = [...tags, {"uuid": tag.uuid,"name": shortenString(tag.name)}]
      });
    }
  }

  function shortenString (string) {
    let lenght = ((width - 6) / 9) ^ 0; // ^0 rounds the number down
    if (string.length > lenght) {
      return string.slice(0,lenght - 2).concat("...");
    }
    return string;
  }

  function isSelected (tag) {
    return selectedTags.includes(tag.uuid);
  }

</script>


<a href="/lecturer/{lecturer.uuid}" class="card card-hover max-w-96 w-full m-2 p-1 variant-ghost-surface rounded-2xl border-2 border-primary-500">
  <div class="card-header flex" bind:clientWidth={width}>
    {#if lecturer.picture_url !== null}
      <Avatar src={lecturer.picture_url} width="w-32" shadow="shadow-2xl" />
    {/if}
    <div class="ml-5">
      <h3 class="h3">{fullName(lecturer)}</h3>
      <h5 class="h5">{null2string(lecturer.location)+"  |  "+null2string(lecturer.price_per_hour)+" Kč/hod"}</h5>
    </div>
  </div>
  <p class="text-lg m-1 mt-3">{null2string(lecturer.claim)}</p>
  {#if tags.length !== 0}
    {#each tags as tag}
      <span class="badge text-sm rounded-full m-1 variant-filled-{isSelected(tag) ? "tertiary" : "surface"} border-2 border-tertiary-500">{tag.name}</span>
    {/each}
  {/if}
</a>