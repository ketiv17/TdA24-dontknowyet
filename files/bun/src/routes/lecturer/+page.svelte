<script>
  import {onMount} from 'svelte';
  import {ProgressRadial} from '@skeletonlabs/skeleton'
  import {Avatar} from '@skeletonlabs/skeleton'
  let data;

  async function fetchData() {
    const response = await fetch('/api/lecturers/index2.php');
    data = await response.json();
  }

  onMount(() => {
    fetchData();
  });

  function contrast(colorhex) {
    colorhex = colorhex.replace("#", "");
    var r = parseInt(colorhex.substr(0,2),16);
    var g = parseInt(colorhex.substr(2,2),16);
    var b = parseInt(colorhex.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    return (yiq >= 128) ? 'black' : 'white';
  }
</script>
<p class="h1 text-center m-10 mt-20">Katalog Lektorů</p>
<main class="flex justify-center">
  <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 sm:min-w-10/12">
    <hr class="col-span-full">
    {#if data}
      {#each data as lecturer}
        <a href="/lecturer/{lecturer.uuid}" class="card card-hover max-w-96 m-2 p-1 min-h-32 variant-ghost-surface rounded-2xl border-2 border-primary-500">
          <div class="card-header flex">
            <Avatar src={lecturer.picture_url} width="w-32" shadow="shadow-2xl" />
            <div class="mx-3">
              <h3 class="h3">{lecturer.title_before+' '+lecturer.first_name+' '+lecturer.middle_name+' '+lecturer.last_name+' '+lecturer.title_after}</h3>
              <h5 class="h5">{lecturer.location}</h5>
            </div>
          </div>
          <p class="text-lg m-1">{lecturer.claim}</p>
          {#each lecturer.tags as tag}
            <span class="badge text-sm rounded-full m-1" style="background-color: {tag.color}; color: {contrast(tag.color)};">{tag.name}</span>
          {/each}
        </a>
      {/each}
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20"/>
    {/if}
  </div>
</main>
