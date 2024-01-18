<script>
  import {Avatar} from '@skeletonlabs/skeleton';
  import {textContrast} from '$lib/index.js';

  export let lecturer = {};
  let width;
  let tags = [];
  $: width && formatTags();

  function formatTags () {
    tags = [];
    if (lecturer.tags !== null) {
      lecturer.tags.forEach(tag => {
        tags = [...tags, {"color": tag.color, "name": shortenString(tag.name)}]
      });
    }
  }

  function shortenString (string) {
    let lenght = (width - 6) / 8;
    if (string.length > lenght) {
      return string.slice(0,lenght - 2).concat("...");
    }
    return string;
  }
</script>


<a href="/lecturer/{lecturer.uuid}" class="card card-hover max-w-96 m-2 p-1 min-h-32 variant-ghost-surface rounded-2xl border-2 border-primary-500">
  <div class="card-header flex" bind:clientWidth={width}>
    <Avatar src={lecturer.picture_url} width="w-32" shadow="shadow-2xl" />
    <div class="mx-3">
      <h3 class="h3">{lecturer.title_before+' '+lecturer.first_name+' '+lecturer.middle_name+' '+lecturer.last_name+' '+lecturer.title_after}</h3>
      <h5 class="h5">{lecturer.location+"  |  "+lecturer.price_per_hour+" Kč/hod"}</h5>
    </div>
  </div>
  <p class="text-lg m-1">{lecturer.claim}</p>
  {#if tags.length !== 0}
    {#each tags as tag}
      <span class="badge text-sm rounded-full m-1" style="background-color: {tag.color}; color: {textContrast(tag.color)};">{tag.name}</span>
    {/each}
  {/if}
</a>